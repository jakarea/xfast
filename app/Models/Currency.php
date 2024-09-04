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

use App\Models\Traits\Common\AppendsTrait;
use App\Models\Traits\CurrencyTrait;
use App\Observers\CurrencyObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Http\Controllers\Web\Admin\Panel\Library\Traits\Models\Crud;

#[ObservedBy([CurrencyObserver::class])]
class Currency extends BaseModel
{
	use Crud, AppendsTrait, HasFactory;
	use CurrencyTrait;
	
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'currencies';
	
	/**
	 * The primary key for the model.
	 *
	 * @var string
	 */
	protected $primaryKey = 'code';
	
	/**
	 * The "type" of the primary key ID.
	 *
	 * @var string
	 */
	protected $keyType = 'string';
	public $incrementing = false;
	
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
		'code',
		'name',
		'symbol',
		'html_entities',
		'in_left',
		'decimal_places',
		'decimal_separator',
		'thousand_separator',
	];
	
	/**
	 * @param array $attributes
	 */
	public function __construct(array $attributes = [])
	{
		if (config('plugins.currencyexchange.installed')) {
			$this->fillable[] = 'rate';
		}
		
		parent::__construct($attributes);
	}
	
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
	
	protected static function boot()
	{
		parent::boot();
		
		Currency::observe(CurrencyObserver::class);
	}
	
	/*
	|--------------------------------------------------------------------------
	| RELATIONS
	|--------------------------------------------------------------------------
	*/
	public function countries()
	{
		return $this->hasMany(Country::class, 'currency_code', 'code');
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
	protected function id(): Attribute
	{
		return Attribute::make(
			get: fn($value) => $this->attributes['code'] ?? $value,
		);
	}
	
	protected function symbol(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				if (trim($value) == '') {
					if (isset($this->attributes['symbol'])) {
						$value = $this->attributes['symbol'];
					}
				}
				if (trim($value) == '') {
					if (isset($this->attributes['html_entities'])) {
						$value = $this->attributes['html_entities'];
					}
				}
				if (trim($value) == '') {
					if (isset($this->attributes['code'])) {
						$value = $this->attributes['code'];
					}
				}
				
				return $value;
			},
		);
	}
	
	/*
	|--------------------------------------------------------------------------
	| OTHER PRIVATE METHODS
	|--------------------------------------------------------------------------
	*/
}
