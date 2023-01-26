<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Pagination\LengthAwarePaginator;

class PostService
{
    /**
     * @var CachingPostService
     */
    private CachingPostService $cachingPostService;

    /**
     * @param  CachingPostService  $cachingPostService
     */
    public function __construct(CachingPostService $cachingPostService)
    {
        $this->cachingPostService = $cachingPostService;
    }

    /**
     * @param  array  $filters
     * @param  int  $pageNumber
     * @return LengthAwarePaginator
     */
    public function getAll(array $filters, int $pageNumber): LengthAwarePaginator
    {
        if ($this->cachingPostService->existsInCache($pageNumber, $filters)) {
            return $this->cachingPostService->getFromCache();
        }

        $posts = Post::getAll($filters);

        if ($posts->isNotEmpty()) {
            $this->cachingPostService->cache($posts);
        }

        return $posts;
    }

    /**
     * This is for store post
     *
     * @return void
     */
    public function store($data): bool
    {
        $status = boolval(Post::query()->create($data));

        if ($status) {
            $this->cachingPostService->invalidateFirstPageInCache();
        }

        return $status;
    }
}
