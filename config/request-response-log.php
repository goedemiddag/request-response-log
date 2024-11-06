<?php

return [
    // The number of days to keep the request logs. Request logs older than the provided days here will be pruned. Set
    // to `null` to keep the logs indefinitely.
    'prune_after_days' => 30,

    'security' => [
        // For security reason we should not log sensitive fields. These fields will be checked case-insensitive.
        'sensitive_fields' => [
            'password',
            'access_token',
            'refresh_token',
            'token',
            'apipassword',
            'client_secret',
            'two_factor_secret',
            'cookie',
            'authorization',
            'php-auth-user',
            'php-auth-pw',
            'php-auth-digest',
        ],

        // For security reason we should not log sensitive fields for certain vendors. These fields will be checked
        // case-insensitive.
        'sensitive_fields_per_vendor' => [],
    ],

    'database' => [
        // The database connection to use for storing the request and response logs, this can be any connection defined
        // in `config/database.php` and different from the application's main database connection. Defaults to the
        // application's main database connection.
        'connection' => env('DB_CONNECTION', 'sqlite'),

        // The table name where to model should store the request logs
        'request_log_table' => 'request_logs',

        // The table name where to model should store the response logs
        'response_log_table' => 'response_logs',
    ],
];
