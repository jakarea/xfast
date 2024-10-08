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
use App\Models\Scopes\ActiveScope;
use App\Models\Traits\Common\AppendsTrait;
use App\Models\Traits\PageTrait;
use App\Observers\PageObserver;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Http\Controllers\Web\Admin\Panel\Library\Traits\Models\Crud;
use App\Http\Controllers\Web\Admin\Panel\Library\Traits\Models\SpatieTranslatable\HasTranslations;

#[ObservedBy([PageObserver::class])]
#[ScopedBy([ActiveScope::class])]
class Page extends BaseModel
{
	use Crud, AppendsTrait, HasFactory, Sluggable, SluggableScopeHelpers, HasTranslations;
	use PageTrait;
	
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'pages';
	
	/**
	 * @var array<int, string>
	 */
	protected $appends = ['picture_url'];
	
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
	protected $fillable = [
		'parent_id',
		'type',
		'name',
		'slug',
		'picture',
		'title',
		'content',
		'external_link',
		'name_color',
		'title_color',
		'target_blank',
		'seo_title',
		'seo_description',
		'seo_keywords',
		'excluded_from_footer',
		'active',
		'lft',
		'rgt',
		'depth',
	];
	
	/**
	 * @var array<int, string>
	 */
	public array $translatable = ['name', 'title', 'content', 'seo_title', 'seo_description', 'seo_keywords'];
	
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
	public function parent()
	{
		return $this->belongsTo(Page::class, 'parent_id');
	}
	
	/*
	|--------------------------------------------------------------------------
	| SCOPES
	|--------------------------------------------------------------------------
	*/
	public function scopeType(Builder $builder, $type): Builder
	{
		return $builder->where('type', $type)->orderByDesc('id');
	}
	
	/*
	|--------------------------------------------------------------------------
	| ACCESSORS | MUTATORS
	|--------------------------------------------------------------------------
	*/
	protected function name(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				if (isset($this->attributes['name']) && !isJson($this->attributes['name'])) {
					return $this->attributes['name'];
				}
				
				return $value;
			},
		);
	}
	
	protected function title(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				if (isset($this->attributes['title']) && !isJson($this->attributes['title'])) {
					return $this->attributes['title'];
				}
				
				return $value;
			},
			set: function ($value) {
				if (!empty($value)) {
					$this->attributes['title'] = $value;
				} else {
					$this->attributes['title'] = $this->name;
				}
			},
		);
	}
	
	protected function content(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				if (isset($this->attributes['content']) && !isJson($this->attributes['content'])) {
					return $this->attributes['content'];
				}
				
				return $value;
			},
		);
	}
	
	protected function picture(): Attribute
	{
		return Attribute::make(
			get: function ($value, $attributes) {
				if (empty($value)) {
					if (isset($attributes['picture'])) {
						$value = $attributes['picture'];
					}
				}
				
				if (empty($value)) {
					return null;
				}
				
				$disk = StorageDisk::getDisk();
				if (!$disk->exists($value)) {
					$value = null;
				}
				
				return $value;
			},
		);
	}
	
	protected function pictureUrl(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				if (empty($this->picture)) {
					return null;
				}
				
				return imgUrl($this->picture, 'bg-header');
			},
		);
	}
	
	/*
	|--------------------------------------------------------------------------
	| OTHER PRIVATE METHODS
	|--------------------------------------------------------------------------
	*/
}
