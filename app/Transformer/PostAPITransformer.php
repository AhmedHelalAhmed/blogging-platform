<?php

namespace App\Transformer;

use Carbon\Carbon;

class PostAPITransformer
{
    const ADMIN_USER_ID = 1;

    public static function transform($post)
    {
        return [
            'external_id' => $post['id'],
            'title' => $post['title'],
            'description' => $post['description'],
            'published_at' => Carbon::parse($post['publishedAt']),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'user_id' => self::ADMIN_USER_ID
        ];
    }
}
