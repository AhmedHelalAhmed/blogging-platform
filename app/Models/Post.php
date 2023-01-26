<?php

namespace App\Models;

use App\enums\SortByPublicationDateEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Stevebauman\Purify\Facades\Purify;

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
            get: fn ($value) => Purify::clean($value),
            set: fn ($value) => strip_tags(htmlspecialchars_decode(Purify::clean($value))),
        );
    }

    /**
     * @return Attribute
     */
    protected function description(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Purify::clean($value),
            set: fn ($value) => strip_tags(htmlspecialchars_decode(Purify::clean($value))),
        );
    }
}
