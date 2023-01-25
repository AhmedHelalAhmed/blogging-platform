<?php

namespace App\Models;

use App\enums\SortByPublicationDateEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
