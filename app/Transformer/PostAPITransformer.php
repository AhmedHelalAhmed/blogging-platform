<?php

namespace App\Transformer;

use App\Models\User;
use App\Services\TextInputFilterService;
use Carbon\Carbon;

class PostAPITransformer
{
    public static function transform($post)
    {
        return [
            'title' => TextInputFilterService::storeFilter($post['title']),
            'description' => TextInputFilterService::storeFilter($post['description']),
            'published_at' => Carbon::parse($post['publishedAt']),
            'user_id' => User::ADMIN_USER_ID,
        ];
    }
}
