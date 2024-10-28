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
 * @property RequestFlow $flow
 * @property string $vendor
 * @property string $method
 * @property array $headers
 * @property string $base_uri
 * @property string $path
 * @property array $query_parameters
 * @property mixed $body
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
        $config = config('request-response-log.prune_after_days');

        // When the config is set to null, we don't want to prune anything so return a query that will never give any
        // results.
        if ($config === null) {
            return static::query()->whereNull('created_at');
        }

        return static::query()->where('created_at', '<=', Carbon::now()->subDays());
    }
}
