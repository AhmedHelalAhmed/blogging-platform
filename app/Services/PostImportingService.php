<?php

namespace App\Services;

use App\Models\ExternalPostsIds;
use App\Models\Post;
use App\Transformer\PostAPITransformer;
use App\Validators\PostValidator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

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

        $posts = $posts->filter(function ($post) {
            try {
                app(PostValidator::class)->validate($post);

                return true;
            } catch (ValidationException $exception) {
                Log::error('[import-posts-api] Error in importing: '.$exception->getMessage(), [
                    'exception' => $exception,
                    'post' => $post,
                ]);

                return false;
            }
        });

        $existsPosts = ExternalPostsIds::getByIds($posts->pluck('id'));
        $newPosts = $posts->filter(fn ($post) => ! in_array($post['id'], $existsPosts));
        if ($newPosts->isNotEmpty()) {
            Post::insert(
                $newPosts->map(function ($post) {
                    return PostAPITransformer::transform($post);
                })->toArray()
            );
            ExternalPostsIds::insert($newPosts->map(fn ($post) => ['external_id' => $post['id']])->toArray());

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
        try {
            $response = Http::get(config('app.feed'));
            if ($response->failed()) {
                Log::error('[import-posts-api] Can not get feeds from external api', ['response' => $response]);
                throw new \Exception('error in external feed api');
            }

            return $response->json('articles');
        } catch (\Exception $exception) {
            Log::error('[import-posts-api] Failed to get posts external api'.$exception->getMessage(), [
                'exception' => $exception,
            ]);
        }

        return [];
    }
}
