<?php
/*
 * LaraClassifier - Classified Ads Web Application
 * Copyright (c) BeDigit. All Rights Reserved
 *
 * Website: https://laraclassifier.com
 * Author: BeDigit | https://bedigit.com
 *
 * LICENSE
 * -------
 * This software is furnished under a license and may be used and copied
 * only in accordance with the terms of such license and with the inclusion
 * of the above copyright notice. If you Purchased from CodeCanyon,
 * Please read the full License from here - https://codecanyon.net/licenses/standard
 */

namespace App\Models;

use App\Helpers\Files\Storage\StorageDisk;
use App\Models\Scopes\LocalizedScope;
use App\Models\Scopes\ActiveScope;
use App\Models\Traits\Common\AppendsTrait;
use App\Models\Traits\PictureTrait;
use App\Observers\PictureObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Http\Controllers\Web\Admin\Panel\Library\Traits\Models\Crud;

#[ObservedBy([PictureObserver::class])]
#[ScopedBy([ActiveScope::class, LocalizedScope::class])]
class Picture extends BaseModel
{
	use Crud, AppendsTrait, HasFactory;
	use PictureTrait;
	
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'pictures';
	
	/**
	 * @var array<int, string>
	 */
	protected $appends = ['filename_url', 'filename_url_small', 'filename_url_medium', 'filename_url_large'];
	
	/**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array<int, string>
	 */
	protected $guarded = ['id'];
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<int, string>
	 */
	protected $fillable = ['post_id', 'filename', 'mime_type', 'position', 'active'];
	
	/*
	|--------------------------------------------------------------------------
	| FUNCTIONS
	|--------------------------------------------------------------------------
	*/
	/**
	 * Get the attributes that should be cast.
	 *
	 * @return array<string, string>
	 */
	protected function casts(): array
	{
		return [
			'created_at' => 'datetime',
			'updated_at' => 'datetime',
		];
	}
	
	/*
	|--------------------------------------------------------------------------
	| RELATIONS
	|--------------------------------------------------------------------------
	*/
	public function post()
	{
		return $this->belongsTo(Post::class, 'post_id');
	}
	
	/*
	|--------------------------------------------------------------------------
	| SCOPES
	|--------------------------------------------------------------------------
	*/
	
	/*
	|--------------------------------------------------------------------------
	| ACCESSORS | MUTATORS
	|--------------------------------------------------------------------------
	*/
	protected function filename(): Attribute
	{
		return Attribute::make(
			get: function ($value, $attributes) {
				if (empty($value)) {
					if (isset($attributes['filename'])) {
						$value = $attributes['filename'];
					}
				}
				
				// OLD PATH
				$value = $this->getFilenameFromOldPath($value);
				
				// NEW PATH
				$disk = StorageDisk::getDisk();
				if (empty($value) || !$disk->exists($value)) {
					$value = config('larapen.media.picture');
				}
				
				return $value;
			},
		);
	}
	
	protected function filenameUrl(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				return $this->getFilenameUrl();
			},
		);
	}
	
	protected function filenameUrlSmall(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				return $this->getFilenameUrl('picture-sm');
			},
		);
	}
	
	protected function filenameUrlMedium(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				return $this->getFilenameUrl('picture-md');
			},
		);
	}
	
	protected function filenameUrlLarge(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				return $this->getFilenameUrl('picture-lg');
			},
		);
	}
	
	protected function mimeType(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				if (!empty($value)) {
					return $value;
				}
				
				$mimeType = null;
				
				try {
					// Storage Disk Init.
					$disk = StorageDisk::getDisk();
					if (!empty($this->filename) && $disk->exists($this->filename)) {
						$filePath = $disk->path($this->filename);
						$mimeType = mime_content_type($filePath);
					}
				} catch (\Throwable $e) {
				}
				
				if (empty($mimeType)) {
					$mimeTypes = [
						'jpeg' => 'image/jpeg',
						'jpg'  => 'image/jpeg',
						'png'  => 'image/png',
						'gif'  => 'image/gif',
						'webp' => 'image/webp',
					];
					
					$extension = file_extension($this->filename);
					
					if (isset($mimeTypes[$extension])) {
						$mimeType = $mimeTypes[$extension];
					}
				}
				
				if (empty($mimeType)) {
					$mimeType = 'image/jpeg';
				}
				
				return $mimeType;
			},
		);
	}
	
	/*
	|--------------------------------------------------------------------------
	| OTHER PRIVATE METHODS
	|--------------------------------------------------------------------------
	*/
	private function getFilenameFromOldPath($value): ?string
	{
		// Fix path
		$oldBase = 'pictures/';
		$newBase = 'files/';
		if (str_contains($value, $oldBase)) {
			$value = $newBase . last(explode($oldBase, $value));
		}
		
		return $value;
	}
	
	private function getFilenameUrl($size = null): string
	{
		// Default URL
		$defaultFilenameUrl = imgUrl(config('larapen.media.picture'));
		
		// Get saved URL
		$filenameUrl = null;
		if (!empty($this->filename)) {
			$disk = StorageDisk::getDisk();
			if ($disk->exists($this->filename)) {
				$filenameUrl = !empty($size) ? imgUrl($this->filename, $size) : imgUrl($this->filename);
			}
		}
		
		return !empty($filenameUrl) ? $filenameUrl : $defaultFilenameUrl;
	}
}
