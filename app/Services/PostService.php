<?php

namespace App\Services;

use App\enums\SortByPublicationDate;
use App\Models\Post;

class PostService
{
    /**
     * @return mixed
     */
    public function getAll(array $filters)
    {
        return Post::when($filters['sort']['published_at'] ?? SortByPublicationDate::OLD_TO_NEW->value, function ($query, $direction) {
            $query->sortByPublishedAt($direction);
        })
            ->when($filters['filter']['authorId'] ?? SortByPublicationDate::OLD_TO_NEW->value, function ($query, $authorId) {
                $query->author($authorId);
            })
            ->paginate(10)
            ->withQueryString();
    }

    /**
     * this is for store post
     * @return void
     */
    public function store($data): bool
    {
        return boolval(Post::create($data));
    }

}
