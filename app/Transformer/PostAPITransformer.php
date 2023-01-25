<?php

namespace App\Transformer;

use App\Models\User;
use Carbon\Carbon;

class PostAPITransformer
{
    public static function transform($post)
    {
        return [
            'title' => $post['title'],
            'description' => $post['description'],
            'published_at' => Carbon::parse($post['publishedAt']),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'user_id' => User::ADMIN_USER_ID,
        ];
    }
}
