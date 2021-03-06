<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Carbon;

class SecurityHistoricalDatum extends Model
{
    const FREQUENCY_DAILY = 'daily';
    const FREQUENCY_WEEKLY = 'weekly';
    const FREQUENCY_MONTHLY = 'monthly';
    const FREQUENCY_QUARTERLY = 'quarterly';
    const FREQUENCY_YEARLY = 'yearly';

    const TAG_CLOSE_PRICE = 'close_price';
    const TAG_ADJ_CLOSE_PRICE = 'adj_close_price';

    protected $fillable = [
        'tag',
        'frequency',
        'date',
        'value',
    ];

    public static $frequencyDisplayPriority = [
        self::FREQUENCY_DAILY,
        self::FREQUENCY_WEEKLY,
        self::FREQUENCY_MONTHLY,
        self::FREQUENCY_QUARTERLY,
        self::FREQUENCY_YEARLY,
    ];

    public static function frequencies(): array
    {
        return self::$frequencyDisplayPriority;
    }

    public static function tags(): array
    {
        return [
            self::TAG_CLOSE_PRICE,
            self::TAG_ADJ_CLOSE_PRICE,
        ];
    }

    public function security(): Relation
    {
        return $this->belongsTo(Security::class);
    }

    public function scopeOfTag($query, string $tag): Builder
    {
        return $query->where('tag', $tag);
    }

    public function scopeOfFrequency($query, string $frequency): Builder
    {
        return $query->where('frequency', $frequency);
    }

    public function scopeBetweenDate($query, string $start, string $end): Builder
    {
        return $query->whereBetween('date', [$start, $end]);
    }

    public static function supportTags(): array
    {
        return self::query()
            ->distinct()
            ->get('tag')
            ->pluck('tag')
            ->toArray();
    }

    public static function supportFrequencies(): array
    {
        return self::query()
            ->distinct()
            ->get('frequency')
            ->sort(function ($first, $second) {
                $firstIndex = array_search($first->frequency, self::$frequencyDisplayPriority);
                $secondIndex = array_search($second->frequency, self::$frequencyDisplayPriority);
                return $firstIndex - $secondIndex;
            })
            ->pluck('frequency')
            ->toArray();
    }
}
