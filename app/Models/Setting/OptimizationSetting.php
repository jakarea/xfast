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

namespace App\Models\Setting;

class OptimizationSetting
{
	public static function getValues($value, $disk)
	{
		if (empty($value)) {
			
			$value['cache_driver'] = 'file';
			$value['cache_expiration'] = '86400';
			$value['memcached_servers_1_host'] = '127.0.0.1';
			$value['memcached_servers_1_port'] = '11211';
			$value['redis_client'] = 'predis';
			$value['redis_cluster'] = 'predis';
			$value['redis_host'] = '127.0.0.1';
			$value['redis_password'] = null;
			$value['redis_port'] = '6379';
			$value['redis_database'] = '0';
			$value['lazy_loading_activation'] = '0';
			$value['minify_html_activation'] = '0';
			
		} else {
			
			if (!array_key_exists('cache_driver', $value)) {
				$value['cache_driver'] = 'file';
			}
			if (!array_key_exists('cache_expiration', $value)) {
				$value['cache_expiration'] = '86400';
			}
			if (!array_key_exists('memcached_servers_1_host', $value)) {
				$value['memcached_servers_1_host'] = '127.0.0.1';
			}
			if (!array_key_exists('memcached_servers_1_port', $value)) {
				$value['memcached_servers_1_port'] = '11211';
			}
			if (!array_key_exists('redis_client', $value)) {
				$value['redis_client'] = 'predis';
			}
			if (!array_key_exists('redis_cluster', $value)) {
				$value['redis_cluster'] = 'predis';
			}
			if (!array_key_exists('redis_host', $value)) {
				$value['redis_host'] = '127.0.0.1';
			}
			if (!array_key_exists('redis_password', $value)) {
				$value['redis_password'] = null;
			}
			if (!array_key_exists('redis_port', $value)) {
				$value['redis_port'] = '6379';
			}
			if (!array_key_exists('redis_database', $value)) {
				$value['redis_database'] = '0';
			}
			if (!array_key_exists('lazy_loading_activation', $value)) {
				$value['lazy_loading_activation'] = '0';
			}
			if (!array_key_exists('minify_html_activation', $value)) {
				$value['minify_html_activation'] = '0';
			}
			
		}
		
		// During the Cache variable updating from the Admin panel,
		// Check if the /.env file's cache configuration variables are different to the DB value,
		// If so, then display the right value from the /.env file.
		if (is_array($value)) {
			if (str_contains(currentRouteAction(), 'Admin\SettingController@edit')) {
				if (array_key_exists('cache_driver', $value) && getenv('CACHE_STORE')) {
					if ($value['cache_driver'] != env('CACHE_STORE')) {
						$value['cache_driver'] = env('CACHE_STORE');
					}
				}
				if (array_key_exists('memcached_servers_1_host', $value) && getenv('MEMCACHED_SERVER_1_HOST')) {
					if ($value['memcached_servers_1_host'] != env('MEMCACHED_SERVER_1_HOST')) {
						$value['memcached_servers_1_host'] = env('MEMCACHED_SERVER_1_HOST');
					}
				}
				if (array_key_exists('memcached_servers_1_port', $value) && getenv('MEMCACHED_SERVER_1_PORT')) {
					if ($value['memcached_servers_1_port'] != env('MEMCACHED_SERVER_1_PORT')) {
						$value['memcached_servers_1_port'] = env('MEMCACHED_SERVER_1_PORT');
					}
				}
				if (array_key_exists('redis_client', $value) && getenv('REDIS_CLIENT')) {
					if ($value['redis_client'] != env('REDIS_CLIENT')) {
						$value['redis_client'] = env('REDIS_CLIENT');
					}
				}
				if (array_key_exists('redis_cluster', $value) && getenv('REDIS_CLUSTER')) {
					if ($value['redis_cluster'] != env('REDIS_CLUSTER')) {
						$value['redis_cluster'] = env('REDIS_CLUSTER');
					}
				}
				if (array_key_exists('redis_host', $value) && getenv('REDIS_HOST')) {
					if ($value['redis_host'] != env('REDIS_HOST')) {
						$value['redis_host'] = env('REDIS_HOST');
					}
				}
				if (array_key_exists('redis_password', $value) && getenv('REDIS_PASSWORD')) {
					if ($value['redis_password'] != env('REDIS_PASSWORD')) {
						$value['redis_password'] = env('REDIS_PASSWORD');
					}
				}
				if (array_key_exists('redis_port', $value) && getenv('REDIS_PORT')) {
					if ($value['redis_port'] != env('REDIS_PORT')) {
						$value['redis_port'] = env('REDIS_PORT');
					}
				}
				if (array_key_exists('redis_database', $value) && getenv('REDIS_DB')) {
					if ($value['redis_database'] != env('REDIS_DB')) {
						$value['redis_database'] = env('REDIS_DB');
					}
				}
			}
		}
		
		return $value;
	}
	
	public static function setValues($value, $setting)
	{
		return $value;
	}
	
	public static function getFields($diskName)
	{
		$fields = [
			[
				'name'  => 'caching_system_sep',
				'type'  => 'custom_html',
				'value' => trans('admin.caching_system_sep_value'),
			],
			[
				'name'              => 'cache_driver',
				'label'             => trans('admin.cache_driver_label'),
				'type'              => 'select2_from_array',
				'options'           => [
					'file'      => 'File (Default)',
					'array'     => 'None',
					'database'  => 'Database',
					'apc'       => 'APC',
					'memcached' => 'Memcached',
					'redis'     => 'Redis',
				],
				'hint'              => trans('admin.cache_driver_hint'),
				'wrapperAttributes' => [
					'class' => 'col-md-6',
				],
			],
			[
				'name'              => 'cache_expiration',
				'label'             => trans('admin.cache_expiration_label'),
				'type'              => 'number',
				'hint'              => trans('admin.cache_expiration_hint'),
				'wrapperAttributes' => [
					'class' => 'col-md-6',
				],
			],
			[
				'name'  => 'cache_driver_info_sep',
				'type'  => 'custom_html',
				'value' => trans('admin.cache_driver_info'),
			],
			[
				'name'  => 'webp_format_title',
				'type'  => 'custom_html',
				'value' => trans('admin.webp_format_title'),
			],
			[
				'name'  => 'webp_format',
				'label' => trans('admin.webp_format_label'),
				'type'  => 'checkbox_switch',
				'hint'  => trans('admin.webp_format_hint'),
			],
			[
				'name'  => 'memcached_sep',
				'type'  => 'custom_html',
				'value' => trans('admin.memcached_sep_value'),
			],
			[
				'name'              => 'memcached_persistent_id',
				'label'             => trans('admin.memcached_persistent_id_label'),
				'type'              => 'text',
				'hint'              => trans('admin.memcached_persistent_id_hint'),
				'wrapperAttributes' => [
					'class' => 'col-md-6',
				],
			],
			[
				'name'  => 'separator_clear_1',
				'type'  => 'custom_html',
				'value' => '<div style="clear: both;"></div>',
			],
			[
				'name'              => 'memcached_sasl_username',
				'label'             => trans('admin.memcached_sasl_username_label'),
				'type'              => 'text',
				'hint'              => trans('admin.memcached_sasl_username_hint'),
				'wrapperAttributes' => [
					'class' => 'col-md-6',
				],
			],
			[
				'name'              => 'memcached_sasl_password',
				'label'             => trans('admin.memcached_sasl_password_label'),
				'type'              => 'text',
				'hint'              => trans('admin.memcached_sasl_password_hint'),
				'wrapperAttributes' => [
					'class' => 'col-md-6',
				],
			],
			[
				'name'  => 'memcached_servers_sep',
				'type'  => 'custom_html',
				'value' => trans('admin.memcached_servers_sep_value'),
			],
			[
				'name'              => 'memcached_servers_1_host',
				'label'             => trans('admin.memcached_servers_host_label', ['num' => 1]),
				'type'              => 'text',
				'hint'              => trans('admin.memcached_servers_host_hint'),
				'wrapperAttributes' => [
					'class' => 'col-md-6',
				],
			],
			[
				'name'              => 'memcached_servers_1_port',
				'label'             => trans('admin.memcached_servers_port_label', ['num' => 1]),
				'type'              => 'number',
				'hint'              => trans('admin.memcached_servers_port_hint'),
				'wrapperAttributes' => [
					'class' => 'col-md-6',
				],
			],
			[
				'name'              => 'memcached_servers_2_host',
				'label'             => trans('admin.memcached_servers_host_label', ['num' => 2]) . ' (' . trans('admin.Optional') . ')',
				'type'              => 'text',
				'wrapperAttributes' => [
					'class' => 'col-md-6',
				],
			],
			[
				'name'              => 'memcached_servers_2_port',
				'label'             => trans('admin.memcached_servers_port_label', ['num' => 2]) . ' (' . trans('admin.Optional') . ')',
				'type'              => 'number',
				'wrapperAttributes' => [
					'class' => 'col-md-6',
				],
			],
			[
				'name'              => 'memcached_servers_3_host',
				'label'             => trans('admin.memcached_servers_host_label', ['num' => 3]) . ' (' . trans('admin.Optional') . ')',
				'type'              => 'text',
				'wrapperAttributes' => [
					'class' => 'col-md-6',
				],
			],
			[
				'name'              => 'memcached_servers_3_port',
				'label'             => trans('admin.memcached_servers_port_label', ['num' => 3]) . ' (' . trans('admin.Optional') . ')',
				'type'              => 'number',
				'wrapperAttributes' => [
					'class' => 'col-md-6',
				],
			],
			[
				'name'  => 'redis_sep',
				'type'  => 'custom_html',
				'value' => trans('admin.redis_sep_value'),
			],
			[
				'name'              => 'redis_client',
				'label'             => trans('admin.redis_client_label'),
				'type'              => 'select2_from_array',
				'options'           => [
					'predis'   => 'Predis',
					'phpredis' => 'PhpRedis',
				],
				'hint'              => trans('admin.redis_client_hint'),
				'wrapperAttributes' => [
					'class' => 'col-md-6',
				],
			],
			[
				'name'  => 'separator_clear_2',
				'type'  => 'custom_html',
				'value' => '<div style="clear: both;"></div>',
			],
			[
				'name'              => 'redis_cluster_activation',
				'label'             => trans('admin.redis_cluster_activation_label'),
				'type'              => 'checkbox_switch',
				'hint'              => trans('admin.redis_cluster_activation_hint'),
				'wrapperAttributes' => [
					'class' => 'col-md-6',
				],
			],
			[
				'name'              => 'redis_cluster',
				'label'             => trans('admin.redis_cluster_label'),
				'type'              => 'select2_from_array',
				'options'           => [
					'predis' => 'Predis',
					'redis'  => 'Redis',
				],
				'hint'              => trans('admin.redis_cluster_hint'),
				'wrapperAttributes' => [
					'class' => 'col-md-6',
				],
			],
			[
				'name'  => 'separator_clear_3',
				'type'  => 'custom_html',
				'value' => '<div style="clear: both;"></div>',
			],
			[
				'name'  => 'redis_server_sep',
				'type'  => 'custom_html',
				'value' => trans('admin.redis_server_sep_value'),
			],
			[
				'name'              => 'redis_host',
				'label'             => trans('admin.redis_host_label'),
				'type'              => 'text',
				'hint'              => trans('admin.redis_host_hint'),
				'wrapperAttributes' => [
					'class' => 'col-md-6',
				],
			],
			[
				'name'              => 'redis_password',
				'label'             => trans('admin.redis_password_label'),
				'type'              => 'text',
				'hint'              => trans('admin.redis_password_hint'),
				'wrapperAttributes' => [
					'class' => 'col-md-6',
				],
			],
			[
				'name'              => 'redis_port',
				'label'             => trans('admin.redis_port_label'),
				'type'              => 'number',
				'hint'              => trans('admin.redis_port_hint'),
				'wrapperAttributes' => [
					'class' => 'col-md-6',
				],
			],
			[
				'name'              => 'redis_database',
				'label'             => trans('admin.redis_database_label'),
				'type'              => 'text',
				'hint'              => trans('admin.redis_database_hint'),
				'wrapperAttributes' => [
					'class' => 'col-md-6',
				],
			],
			[
				'name'  => 'redis_clusters_sep',
				'type'  => 'custom_html',
				'value' => trans('admin.redis_clusters_sep_value'),
			],
			[
				'name'              => 'redis_cluster_1_host',
				'label'             => trans('admin.redis_cluster_host_label', ['num' => 1]),
				'type'              => 'text',
				'hint'              => trans('admin.redis_cluster_host_hint'),
				'wrapperAttributes' => [
					'class' => 'col-md-6',
				],
			],
			[
				'name'              => 'redis_cluster_1_password',
				'label'             => trans('admin.redis_cluster_password_label', ['num' => 1]),
				'type'              => 'text',
				'hint'              => trans('admin.redis_cluster_password_hint'),
				'wrapperAttributes' => [
					'class' => 'col-md-6',
				],
			],
			[
				'name'              => 'redis_cluster_1_port',
				'label'             => trans('admin.redis_cluster_port_label', ['num' => 1]),
				'type'              => 'number',
				'hint'              => trans('admin.redis_cluster_port_hint'),
				'wrapperAttributes' => [
					'class' => 'col-md-6',
				],
			],
			[
				'name'              => 'redis_cluster_1_database',
				'label'             => trans('admin.redis_cluster_database_label', ['num' => 1]),
				'type'              => 'text',
				'hint'              => trans('admin.redis_cluster_database_hint'),
				'wrapperAttributes' => [
					'class' => 'col-md-6',
				],
			],
			[
				'name'              => 'redis_cluster_2_host',
				'label'             => trans('admin.redis_cluster_host_label', ['num' => 2]) . ' (' . trans('admin.Optional') . ')',
				'type'              => 'text',
				'wrapperAttributes' => [
					'class' => 'col-md-6',
				],
			],
			[
				'name'              => 'redis_cluster_2_password',
				'label'             => trans('admin.redis_cluster_password_label', ['num' => 2]) . ' (' . trans('admin.Optional') . ')',
				'type'              => 'text',
				'wrapperAttributes' => [
					'class' => 'col-md-6',
				],
			],
			[
				'name'              => 'redis_cluster_2_port',
				'label'             => trans('admin.redis_cluster_port_label', ['num' => 2]) . ' (' . trans('admin.Optional') . ')',
				'type'              => 'number',
				'wrapperAttributes' => [
					'class' => 'col-md-6',
				],
			],
			[
				'name'              => 'redis_cluster_2_database',
				'label'             => trans('admin.redis_cluster_database_label', ['num' => 2]) . ' (' . trans('admin.Optional') . ')',
				'type'              => 'text',
				'wrapperAttributes' => [
					'class' => 'col-md-6',
				],
			],
			[
				'name'              => 'redis_cluster_3_host',
				'label'             => trans('admin.redis_cluster_host_label', ['num' => 3]) . ' (' . trans('admin.Optional') . ')',
				'type'              => 'text',
				'wrapperAttributes' => [
					'class' => 'col-md-6',
				],
			],
			[
				'name'              => 'redis_cluster_3_password',
				'label'             => trans('admin.redis_cluster_password_label', ['num' => 3]) . ' (' . trans('admin.Optional') . ')',
				'type'              => 'text',
				'wrapperAttributes' => [
					'class' => 'col-md-6',
				],
			],
			[
				'name'              => 'redis_cluster_3_port',
				'label'             => trans('admin.redis_cluster_port_label', ['num' => 3]) . ' (' . trans('admin.Optional') . ')',
				'type'              => 'number',
				'wrapperAttributes' => [
					'class' => 'col-md-6',
				],
			],
			[
				'name'              => 'redis_cluster_3_database',
				'label'             => trans('admin.redis_cluster_database_label', ['num' => 3]) . ' (' . trans('admin.Optional') . ')',
				'type'              => 'text',
				'wrapperAttributes' => [
					'class' => 'col-md-6',
				],
			],
			[
				'name'  => 'lazy_loading_sep',
				'type'  => 'custom_html',
				'value' => trans('admin.lazy_loading_sep_value'),
			],
			[
				'name'  => 'lazy_loading_activation',
				'label' => trans('admin.lazy_loading_activation_label'),
				'type'  => 'checkbox_switch',
				'hint'  => trans('admin.lazy_loading_activation_hint'),
			],
			[
				'name'  => 'minify_html_sep',
				'type'  => 'custom_html',
				'value' => trans('admin.minify_html_sep_value'),
			],
			[
				'name'  => 'minify_html_activation',
				'label' => trans('admin.minify_html_activation_label'),
				'type'  => 'checkbox_switch',
				'hint'  => trans('admin.minify_html_activation_hint'),
			],
		];
		
		return $fields;
	}
}
