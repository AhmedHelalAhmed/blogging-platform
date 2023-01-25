<?php

namespace App\Services;

use App\enums\SortByPublicationDateEnum;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

/**
 * This service used to cache welcome page that is available for visitors in redis
 * To reduce the hit of database since we expect millions of users
 */
class CachingPostService
{
    const EXPIRY_TIME = 3600;// one hour as it mentioned the blogger generates between 2 and 3 posts an hour
    const POST_KEY_PREFIX = 'posts';
    const PUBLISHED_AT_SORT_KEY_PREFIX = '_sort_';
    const PAGE_KEY_PREFIX = '_page_';
    const FIRST_PAGE = 1;

    private $cacheKey = '';

    /**
     * @return LengthAwarePaginator
     */
    public function getFromCache(): LengthAwarePaginator
    {
        return Cache::get($this->cacheKey);
    }

    /**
     * @param int $pageNumber
     * @param array $filters
     * @return bool
     */
    public function existsInCache(int $pageNumber, array $filters): bool
    {
        // We don't need to cache the dashboard of the login user it will not have heavy traffic
        if ($this->isTheCallComesFromLoginUserDashboard($filters)) {
            return false;
        }

        // We shall have a lot of traffic here coming from visitors so we shall cache this in redis to reduce database hits
        $this->setCacheKey($pageNumber, $filters);
        return Cache::has($this->cacheKey);
    }

    /**
     * @param LengthAwarePaginator $posts
     * @return void
     */
    public function cache(LengthAwarePaginator $posts):void
    {
        Cache::put($this->cacheKey, $posts, self::EXPIRY_TIME);
    }

    /**
     * @param int $pageNumber
     * @param array $filters
     * @return void
     */
    public function setCacheKey(int $pageNumber, array $filters):void
    {
        $this->cacheKey = self::POST_KEY_PREFIX . self::PAGE_KEY_PREFIX . $pageNumber . self::PUBLISHED_AT_SORT_KEY_PREFIX . ($filters['sort']['published_at'] ?? SortByPublicationDateEnum::OLD_TO_NEW->value);
    }

    /**
     * Here we invalidate the first page when user so that when the user create a post
     * He can see this post
     * @return void
     */
    public function invalidateFirstPageInCache():void
    {
        foreach (SortByPublicationDateEnum::cases() as $sortOption) {
            Cache::forget(
                self::POST_KEY_PREFIX . self::PAGE_KEY_PREFIX . self::FIRST_PAGE . self::PUBLISHED_AT_SORT_KEY_PREFIX . $sortOption->value
            );

        }
    }

    /**
     * @param array $filters
     * @return bool
     */
    public function isTheCallComesFromLoginUserDashboard(array $filters):bool
    {
        return Arr::has($filters, 'filter.authorId');
    }
}
