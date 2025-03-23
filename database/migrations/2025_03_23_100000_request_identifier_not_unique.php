<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $requestLogTable = config('request-response-log.database.request_log_table');
        if (empty($requestLogTable)) {
            throw new RuntimeException('Request log table name is not set in the configuration.');
        }

        Schema::table($requestLogTable, function (Blueprint $table) {
            $table->dropUnique(['request_identifier']);
        });
    }
};
