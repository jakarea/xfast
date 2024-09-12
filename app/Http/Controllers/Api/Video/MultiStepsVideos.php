<?php

namespace App\Http\Controllers\Api\Video;

use App\Helpers\Files\Upload;
use App\Http\Requests\Front\PhotoRequest;
use App\Http\Requests\Front\VideoRequest;
use App\Http\Resources\PictureResource;
use App\Http\Resources\PostResource;
use App\Models\Picture;
use App\Models\Post;
use App\Models\Scopes\ActiveScope;
use App\Models\Scopes\ReviewedScope;
use App\Models\Scopes\VerifiedScope;

trait MultiStepsVideos
{
    /**
     * Store Videos (from Multi Steps Form)
     *
     * @param \App\Http\Requests\Front\VideoRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function multiStepsVideosStore(VideoRequest $request): \Illuminate\Http\JsonResponse
    {
        // Get customized request variables
        $countryCode = $request->input('country_code', config('country.code'));
        $postId = $request->input('post_id');

        $authUser = null;
        if (auth('sanctum')->check()) {
            $authUser = auth('sanctum')->user();
        }

        $post = null;
        if (!empty($authUser) && !empty($postId)) {
            $post = Post::query()
                ->withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])
                ->inCountry($countryCode)
                ->where('user_id', $authUser->id)
                ->where('id', $postId)
                ->first();
        }

        if (empty($post)) {
            return apiResponse()->notFound(t('post_not_found'));
        }

        $videos = Picture::where('post_id', $post->id)
            ->where('mime_type', 'video');

        // Get default/global video limit
        $defaultVideosLimit = (int)config('settings.single.videos_limit', 3);

        // Get video limit
        $countExistingVideos = $videos->count();
        $videosLimit = $defaultVideosLimit - $countExistingVideos;

        if ($videosLimit > 0) {
            // Get videos initial position
            $latestPosition = $videos->orderByDesc('position')->first();
            $initialPosition = (!empty($latestPosition) && (int)$latestPosition->position > 0) ? (int)$latestPosition->position : 0;
            $initialPosition = ($countExistingVideos >= $initialPosition) ? $countExistingVideos : $initialPosition;

            // Save all videos
            $videos = [];
            $files = $request->file('videos');
            if (is_array($files) && count($files) > 0) {
                foreach ($files as $key => $file) {
                    if (empty($file)) {
                        continue;
                    }

                    // Delete old video if new file is uploaded
                    $videoPosition = $initialPosition + (int)$key + 1;
                    $video = Picture::query()
                        ->where('post_id', $post->id)
                        ->where('id', $key)
                        ->first();
                    if (!empty($video)) {
                        $videoPosition = $video->position;
                        $video->delete();
                    }

                    // Post Video in the database
                    $video = new Picture([
                        'post_id' => $post->id,
                        'filename' => null,
                        'mime_type' => null,
                        'position' => $videoPosition,
                    ]);

                    // Upload Video
                    $destPath = 'files/' . strtolower($post->country_code) . '/' . $post->id;
                    $video->filename = Upload::file($destPath, $file, null, true);  // Video-specific upload method
                    $video->mime_type = 'video';

                    if (!empty($video->filename)) {
                        $video->save();
                    }

                    $videos[] = (new PictureResource($video));

                    // Check the video limit
                    if ($key >= ($videosLimit - 1)) {
                        break;
                    }
                }
            }

            if (!empty($videos)) {
                $data = [
                    'success' => true,
                    'message' => t('The videos have been updated'),
                    'result' => $videos,
                ];
            } else {
                $data = [
                    'success' => false,
                    'message' => t('error_found'),
                    'result' => null,
                ];
            }
        } else {
            $data = [
                'success' => false,
                'message' => t('videos_limit_reached'),
                'result' => null,
            ];
        }

        $extra = [];
        $extra['post']['result'] = (new PostResource($post))->toArray($request);

        // User should he go on Payment page or not?
        $extra['steps']['payment'] = false;
        $extra['nextStepLabel'] = t('Done');

        if (doesRequestIsFromWebApp()) {
            // Get the FileInput plugin's data
            $fileInput = [];
            $fileInput['initialPreview'] = [];
            $fileInput['initialPreviewConfig'] = [];

            $videos = collect($videos);
            if ($videos->count() > 0) {
                foreach ($videos as $video) {
                    if (empty($video->filename)) {
                        continue;
                    }

                    // Get Deletion Url
                    $initialPreviewConfigUrl = url('posts/' . $post->id . '/videos/' . $video->id . '/delete');

                    $videoSize = (isset($this->disk) && $this->disk->exists($video->filename))
                        ? (int)$this->disk->size($video->filename)
                        : 0;

                    // Build Bootstrap-FileInput plugin's parameters
                    $fileInput['initialPreview'][] = imgUrl($video->filename, 'video-md');  // Adjust for video URL
                    $fileInput['initialPreviewConfig'][] = [
                        'caption' => basename($video->filename),
                        'size' => $videoSize,
                        'url' => $initialPreviewConfigUrl,
                        'key' => $video->id,
                        'extra' => ['id' => $video->id],
                    ];
                }
            }
            $extra['fileInput'] = $fileInput;
        }

        $data['extra'] = $extra;

        return apiResponse()->json($data);
    }

    /**
     * Delete a Video (from Multi Steps Form)
     *
     * @param $videoId
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteMultiStepsVideo($videoId): \Illuminate\Http\JsonResponse
    {
        // Get customized request variables
        $postId = request()->input('post_id');

        $authUser = null;
        if (auth('sanctum')->check()) {
            $authUser = auth('sanctum')->user();
        }

        // Get Post
        $post = null;
        if (!empty($authUser) && !empty($postId)) {
            $post = Post::query()
                ->withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])
                ->where('user_id', $authUser->id)
                ->where('id', $postId)
                ->first();
        }

        if (empty($post)) {
            return apiResponse()->notFound(t('post_not_found'));
        }

        $videos = Picture::query()->withoutGlobalScopes([ActiveScope::class])->where('post_id', $postId);

        if ($videos->count() <= 0) {
            return apiResponse()->forbidden();
        }

        if ($videos->count() == 1) {
            if (config('settings.single.video_mandatory')) {
                return apiResponse()->forbidden(t('the_latest_video_removal_text'));
            }
        }

        $videos = $videos->get();
        foreach ($videos as $video) {
            if ($video->id == $videoId) {
                $video->delete();
                break;
            }
        }

        $message = t('The video has been deleted');

        return apiResponse()->success($message);
    }

    /**
     * Reorder Videos - Bulk Update
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function reorderMultiStepsVideos(): \Illuminate\Http\JsonResponse
    {
        // Get customized request variables
        $postId = request()->input('post_id');

        if (request()->header('X-Action') != 'bulk') {
            return apiResponse()->unauthorized();
        }

        $bodyJson = request()->input('body');
        if (!isJson($bodyJson)) {
            return apiResponse()->error('Invalid JSON format for the "body" field.');
        }

        $bodyArray = json_decode($bodyJson);
        if (!is_array($bodyArray) || empty($bodyArray)) {
            return apiResponse()->noContent();
        }

        $authUser = null;
        if (auth('sanctum')->check()) {
            $authUser = auth('sanctum')->user();
        }

        $videos = [];
        foreach ($bodyArray as $item) {
            if (!isset($item->id) || !isset($item->position)) {
                continue;
            }
            if (empty($item->id) || !is_numeric($item->position)) {
                continue;
            }

            $video = null;
            if (!empty($authUser) && !empty($postId)) {
                $video = Picture::where('id', $item->id)
                    ->whereHas('post', fn($query) => $query->where('user_id', $authUser->id))
                    ->first();
            }

            if (!empty($video)) {
                $video->position = $item->position;
                $video->save();

                $videos[] = (new PictureResource($video));
            }
        }

        // Get endpoint output data
        $data = [
            'success' => !empty($videos),
            'message' => !empty($videos) ? t('Your video has been reorder successfully') : null,
            'result' => !empty($videos) ? $videos : null,
        ];

        return apiResponse()->json($data);
    }
}