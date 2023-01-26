<?php

namespace App\Models;

use App\enums\SortByPublicationDateEnum;
use App\Services\TextInputFilterService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class Post extends Model
{
    const PAGE_SIZE = 10;

    const LIMIT_LENGTH_FOR_DESCRIPTION = 300;

    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'published_at',
        'user_id',
    ];

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @param  Builder  $query
     * @param  int  $value
     * @return void
     */
    public function scopeSortByPublishedAt(Builder $query, int $value)
    {
        $query->orderBy('published_at', SortByPublicationDateEnum::changeValueToDirection($value));
    }

    /**
     * @param  Builder  $query
     * @param  int  $value
     * @return void
     */
    public function scopeAuthor(Builder $query, int $authorId)
    {
        $query->where('user_id', $authorId);
    }

    /**
     * Get the user's first name.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function publishedAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Carbon::parse($value)->diffForHumans(),
        );
    }

    /**
     * @return Attribute
     */
    protected function title(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => TextInputFilterService::displayFilter($value),
            set: fn ($value) => TextInputFilterService::storeFilter($value),
        );
    }

    /**
     * @return Attribute
     */
    protected function description(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Str::limit(TextInputFilterService::displayFilter($value), self::LIMIT_LENGTH_FOR_DESCRIPTION, '...'),
            set: fn ($value) => TextInputFilterService::storeFilter($value),
        );
    }

    /**
     * @param  array  $filters
     * @return mixed
     */
    public static function getAll(array $filters): LengthAwarePaginator
    {
        return Post::query()->select(['title', 'description', 'published_at'])
            ->when(Arr::get($filters, 'sort.published_at', SortByPublicationDateEnum::getDefaultSort()), function ($query, $direction) {
                $query->sortByPublishedAt($direction);
            })
            ->when(Arr::get($filters, 'filter.authorId'), function ($query, $authorId) {
                $query->author($authorId);
            })
            ->paginate(Post::PAGE_SIZE)
            ->withQueryString();
    }
}
