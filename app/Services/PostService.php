<?php

namespace App\Services;

use App\enums\SortByPublicationDateEnum;
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
        $posts = Post::select(['title', 'description', 'published_at'])
            ->when($filters['sort']['published_at'] ?? SortByPublicationDateEnum::OLD_TO_NEW->value, function ($query, $direction) {
                $query->sortByPublishedAt($direction);
            })
            ->when($filters['filter']['authorId'] ?? SortByPublicationDateEnum::OLD_TO_NEW->value, function ($query, $authorId) {
                $query->author($authorId);
            })
            ->orderBy('published_at', 'desc')
            ->paginate(Post::PAGE_SIZE)
            ->withQueryString();

        $this->cachingPostService->cache($posts);

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
