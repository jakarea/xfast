<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Video\MultiStepsVideos;
use App\Http\Controllers\Controller;

use App\Http\Requests\Front\VideoRequest;
use App\Models\Picture;
use App\Models\Post;
use App\Http\Resources\PictureResource;
use App\Http\Resources\EntityCollection;
use Illuminate\Http\Request;

class VideoController extends BaseController
{
    use MultiStepsVideos;

    /**
     * List videos
     *
     * @queryParam embed string The list of the video relationships separated by commas for Eager Loading. Example: null
     * @queryParam postId int List of videos related to a listing (using the listing ID). Example: 1
     * @queryParam latest boolean Get only the first video after ordering (as object instead of collection). Possible value: 0 or 1. Example: 0
     * @queryParam sort string The sorting parameter (Order by DESC with the given column. Use "-" as prefix to order by ASC). Possible values: position, created_at. Example: -position
     * @queryParam perPage int Items per page. Can be defined globally from the admin settings. Cannot be exceeded 100. Example: 2
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        $embed = explode(',', request()->query('embed'));

        $videos = Picture::query();

        if (in_array('post', $embed)) {
            $videos->with('post');
        }

        if (request()->filled('postId')) {
            $videos->where('post_id', request()->query('postId'));
        }

        // Sorting
        $videos = $this->applySorting($videos, ['position', 'created_at']);

        if (request()->query('latest') == 1) {
            $video = $videos->first();

            abort_if(empty($video), 404, t('video_not_found'));

            $resource = new PictureResource($video);

            return apiResponse()->withResource($resource);
        } else {
            $videos = $videos->paginate($this->perPage);

            $videos = setPaginationBaseUrl($videos);

            $resourceCollection = new EntityCollection(class_basename($this), $videos);

            $message = ($videos->count() <= 0) ? t('no_videos_found') : null;

            return apiResponse()->withCollection($resourceCollection, $message);
        }
    }

    /**
     * Get video
     *
     * @queryParam embed string The list of the video relationships separated by commas for Eager Loading. Example: null
     *
     * @urlParam id int required The video's ID. Example: 298
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id): \Illuminate\Http\JsonResponse
    {
        $embed = explode(',', request()->query('embed'));

        $video = Picture::query();

        if (in_array('post', $embed)) {
            $video->with('post');
        }

        $video = $video->where('id', $id)
            ->where('mime_type','video')->first();

        abort_if(empty($video), 404, t('video_not_found'));

        $resource = new PictureResource($video);

        return apiResponse()->withResource($resource);
    }

    /**
     * Store video
     *
     * @authenticated
     * @header Authorization Bearer {YOUR_AUTH_TOKEN}
     *
     * @bodyParam country_code string required The code of the user's country. Example: US
     * @bodyParam post_id int required The post's ID. Example: 2
     * @bodyParam videos file[] The video files to upload.
     *
     * @param \App\Http\Requests\Front\VideoRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(VideoRequest $request): \Illuminate\Http\JsonResponse
    {
        // Check if the form type is 'Single-Step Form'
        $isSingleStepFormEnabled = (config('settings.single.publication_form_type') == '2');
        if ($isSingleStepFormEnabled) {
            abort(404);
        }

        return $this->multiStepsVideosStore($request);
    }

    /**
     * Reorder videos
     *
     * @authenticated
     * @header Authorization Bearer {YOUR_AUTH_TOKEN}
     * @header X-Action bulk
     *
     * @bodyParam post_id int required The post's ID. Example: 2
     * @bodyParam body string required Encoded json of the new videos' positions array [['id' => 2, 'position' => 1], ['id' => 1, 'position' => 2], ...]
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function reorder(): \Illuminate\Http\JsonResponse
    {
        // Check if the form type is 'Single-Step Form'
        $isSingleStepFormEnabled = (config('settings.single.publication_form_type') == '2');
        if ($isSingleStepFormEnabled) {
            abort(404);
        }

        return $this->reorderMultiStepsVideos();
    }

    /**
     * Delete video
     *
     * @authenticated
     * @header Authorization Bearer {YOUR_AUTH_TOKEN}
     *
     * @bodyParam post_id int required The post's ID. Example: 2
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        // Check if the form type is 'Single-Step Form'
        $isSingleStepFormEnabled = (config('settings.single.publication_form_type') == '2');
        if ($isSingleStepFormEnabled) {
            abort(404);
        }

        return $this->deleteMultiStepsVideo($id);
    }

}