<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $responseLogTable = config('request-response-log.database.response_log_table');
        if (empty($responseLogTable)) {
            throw new RuntimeException('Response log table name is not set in the configuration.');
        }

        Schema::create($responseLogTable, function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('request_log_id');
            $table->boolean('success')->default(false);
            $table->unsignedSmallInteger('status_code')->nullable();
            $table->string('reason_phrase')->nullable();
            $table->json('headers')->nullable();
            $table->json('body')->nullable();
            $table->timestamps();

            $table
                ->foreign('request_log_id')
                ->references('id')
                ->on('request_logs')
                ->cascadeOnDelete();
        });
    }
};
