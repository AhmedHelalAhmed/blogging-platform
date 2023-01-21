<?php

namespace App\Services;

use App\Models\Post;
use App\Transformer\PostAPITransformer;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PostImportingService
{
    public function __invoke()
    {
        $posts = collect($this->getFeeds());
        if ($posts->isEmpty()) {
            return;
        }
        $existsPosts = Post::getImportedPosts($posts->pluck('id'));
        $newPosts = $posts->filter(function ($post) use ($existsPosts) {
            return !in_array($post['id'],$existsPosts);
        })->map(function ($post) {
            return PostAPITransformer::transform($post);
        });
        if ($newPosts->isNotEmpty()) {
            Post::insert($newPosts->toArray());
        }
    }

    public function getFeeds()
    {
        $response = Http::get(config('app.feed'));
        if ($response->failed()) {
            Log::error('Can not get feeds from external api', ['response' => $response]);
            throw new \Exception('error in external feed api');
        }

        return $response->json('articles');
    }

}
