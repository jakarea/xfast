<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessAccount extends Model
{
    protected $fillable = [
        'user_id',
        'business_name',
        'category',
        'business_title',
        'location',
        'phone1',
        'phone2',
        'whatsapp',
        'email',
        'website',
        'product_services',
        'business_description',
        'logo',
        'cover_photo',
        'company_images',
        'company_videos',
        'social_media_links',
    ];

    protected $casts = [
        'company_images' => 'array',
        'company_videos' => 'array',
        'social_media_links' => 'array',
    ];
}
