<?php

namespace Goedemiddag\RequestResponseLog\Helpers;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use JsonException;

class Sanitizer
{
    public static function sanitizeBody(string $body, ?string $vendor = null): string|array
    {
        // Try to parse the content as JSON to see if we can filter the sensitive data from it
        try {
            $payload = json_decode(
                json: $body,
                associative: true,
                flags: JSON_THROW_ON_ERROR,
            );

            // When the result is an array, filter the sensitive data from it
            if (is_array($payload)) {
                return self::filterSensitiveData(
                    array: $payload,
                    vendor: $vendor,
                );
            }

            return $payload;
        } catch (JsonException) {
            return $body;
        }
    }

    /**
     * Filters out sensitive data from the array before it is being sent to the configured error reporting tool. Always
     * run array data through this method before logging!
     */
    public static function filterSensitiveData(array $array = [], ?string $vendor = null, string $mask = '********'): array
    {
        $sensitiveFields = config('request-response-log.security.sensitive_fields', []);
        $sensitiveFieldsPerVendor = Arr::get(config('request-response-log.security.sensitive_fields_per_vendor', []), $vendor, []);

        foreach ($array as $key => $value) {
            $searchKey = Str::lower($key);

            $hasGenericSensitiveKey = in_array($searchKey, $sensitiveFields, true);
            $hasVendorSensitiveKey = in_array($searchKey, $sensitiveFieldsPerVendor, true);

            // When the key is sensitive, mask the value
            if ($hasGenericSensitiveKey || $hasVendorSensitiveKey) {
                $array[$key] = $mask;

                continue;
            }

            // When the value is an array, recursively check the array for sensitive data
            if (is_array($value)) {
                $array[$key] = self::filterSensitiveData(
                    array: $value,
                    vendor: $vendor,
                    mask: $mask,
                );
            }
        }

        return $array;
    }
}
