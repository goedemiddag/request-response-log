<?php

namespace Goedemiddag\RequestResponseLog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use RuntimeException;

abstract class LogModel extends Model
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $connection = config('request-response-log.database.connection');
        if (empty($connection)) {
            throw new RuntimeException('Database connection for the request/response log is not set in the configuration.');
        }

        $this->setConnection($connection);

        $model = class_basename($this);
        $table = config(sprintf('request-response-log.database.%s_table', Str::snake($model)));
        if (empty($table)) {
            throw new RuntimeException(sprintf('Table name for %s is not set in the configuration.', $model));
        }

        $this->setTable($table);
    }
}
