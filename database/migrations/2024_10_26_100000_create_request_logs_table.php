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

        Schema::create($requestLogTable, function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('flow');
            $table->string('vendor');
            $table->string('method')->default('GET');
            $table->json('headers')->nullable();
            $table->string('base_uri');
            $table->string('path')->default('/');
            $table->json('query_parameters')->nullable();
            $table->json('body')->nullable();
            $table->string('request_identifier')->nullable()->unique();
            $table->timestamps();

            $table->index(['flow', 'vendor', 'path']);
        });
    }
};
