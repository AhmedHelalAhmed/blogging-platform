<?php

namespace App\Services;

use App\Models\ExternalPostsIds;
use App\Models\Post;
use App\Transformer\PostAPITransformer;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * To import posts from API every hour
 */
class PostImportingService
{
    /**
     * @return void
     *
     * @throws \Exception
     */
    public function __invoke()
    {
        $posts = collect($this->getFeeds());
        if ($posts->isEmpty()) {
            return;
        }
        $existsPosts = ExternalPostsIds::getByIds($posts->pluck('id'));
        $newPosts = $posts->filter(function ($post) use ($existsPosts) {
            return ! in_array($post['id'], $existsPosts);
        });

        if ($newPosts->isNotEmpty()) {
            Post::insert($newPosts->map(function ($post) {
                return PostAPITransformer::transform($post);
            })->toArray());
            ExternalPostsIds::insert($newPosts->map(function ($post) {
                return ['external_id' => $post['id']];
            })->toArray());

            // If we have new posts we need to invalidate the cache
            app(CachingPostService::class)->invalidateFirstPageInCache();
            // currently it's based on published_at that comes from API
            //? do we need to sort the imported posts based on published_at comes from API or based on created_at on the system
        }
    }

    /**
     * Get data from API
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getFeeds(): array
    {
        $response = Http::get(config('app.feed'));
        if ($response->failed()) {
            Log::error('Can not get feeds from external api', ['response' => $response]);
            throw new \Exception('error in external feed api');
        }

        return $response->json('articles');
    }
}
