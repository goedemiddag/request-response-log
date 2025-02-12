<?php

namespace Goedemiddag\RequestResponseLog\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $request_log_id
 * @property bool $success
 * @property int $status_code
 * @property string $reason_phrase
 * @property array $headers
 * @property mixed $body
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property RequestLog $request
 */
class ResponseLog extends LogModel
{
    use HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'request_log_id',
        'success',
        'status_code',
        'reason_phrase',
        'headers',
        'body',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'success' => 'boolean',
        'headers' => 'array',
        'body' => 'json',
    ];

    /**
     * The response belongs to a request.
     */
    public function request(): BelongsTo
    {
        return $this->belongsTo(RequestLog::class, 'request_log_id');
    }
}
