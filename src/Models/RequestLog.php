<?php

namespace Goedemiddag\RequestResponseLog\Models;

use Goedemiddag\RequestResponseLog\Enums\RequestFlow;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * @property string $id
 * @property RequestFlow $flow
 * @property string $vendor
 * @property string $method
 * @property array $headers
 * @property string $base_uri
 * @property string $path
 * @property array $query_parameters
 * @property mixed $body
 * @property mixed $backtrace
 * @property string $request_identifier
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Collection<ResponseLog> $responses
 */
class RequestLog extends LogModel
{
    use HasUuids;
    use Prunable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'flow',
        'vendor',
        'method',
        'headers',
        'base_uri',
        'path',
        'query_parameters',
        'body',
        'backtrace',
        'request_identifier',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'flow' => RequestFlow::class,
        'headers' => 'array',
        'query_parameters' => 'array',
        'body' => 'json',
        'backtrace' => 'json',
    ];

    /**
     * The request has zero or more responses.
     */
    public function responses(): HasMany
    {
        return $this->hasMany(ResponseLog::class);
    }

    public function prunable(): Builder
    {
        $amountOfDaysToKeepLogs = config('request-response-log.prune_after_days');

        // When the config is set to null, we don't want to prune anything so return a query that will never return any
        // results as prunable requires a query to be returned.
        if ($amountOfDaysToKeepLogs === null) {
            return static::query()->whereNull('created_at');
        }

        $createdAtLimit = Carbon::today()->subDays($amountOfDaysToKeepLogs);

        return static::query()->where('created_at', '<=', $createdAtLimit);
    }
}
