<?php

namespace App\Transformer;

use App\Models\User;
use Carbon\Carbon;
use Stevebauman\Purify\Facades\Purify;

class PostAPITransformer
{
    public static function transform($post)
    {
        return [
            'title' => Purify::clean($post['title']),
            'description' => strip_tags(htmlspecialchars_decode(Purify::clean($post['description']))),
            'published_at' => strip_tags(htmlspecialchars_decode(Carbon::parse($post['publishedAt']))),
            'user_id' => User::ADMIN_USER_ID,
        ];
    }
}
