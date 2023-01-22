<?php

namespace App\Models;

use App\enums\SortByPublicationDate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class Post extends Model
{
    const PAGE_SIZE = 10;
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
        'external_id',
    ];

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @param Builder $query
     * @param int $value
     * @return void
     */
    public function scopeSortByPublishedAt(Builder $query, int $value)
    {
        $query->orderBy('published_at', SortByPublicationDate::changeValueToDirection($value));
    }

    /**
     * @param Builder $query
     * @param int $value
     * @return void
     */
    public function scopeAuthor(Builder $query, int $authorId)
    {
        $query->where('user_id', $authorId);
    }

    /**
     * @param Collection $externalIds
     * @return array
     */
    public static function getImportedPosts(Collection $externalIds): array
    {
        return self::whereIn('external_id', $externalIds)->pluck('external_id')->toArray();
    }
}
