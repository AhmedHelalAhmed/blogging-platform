<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ExternalPostsIds extends Model
{
    public $timestamps = false;
    protected $fillable = ['external_id'];
    use HasFactory;


    /**
     * @param Collection $externalIds
     * @return array
     */
    public static function getByIds(Collection $externalIds): array
    {
        return self::whereIn('external_id', $externalIds)->pluck('external_id')->toArray();
    }
}
