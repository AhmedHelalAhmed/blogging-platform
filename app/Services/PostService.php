<?php

namespace App\Services;

use App\enums\SortByPublicationDateEnum;
use App\Models\Post;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

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
            ->when(Arr::get($filters, 'sort.published_at', SortByPublicationDateEnum::NEW_TO_OLD->value), function ($query, $direction) {
                $query->sortByPublishedAt($direction);
            })
            ->when(Arr::get($filters, 'filter.authorId'), function ($query, $authorId) {
                $query->author($authorId);
            })
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
