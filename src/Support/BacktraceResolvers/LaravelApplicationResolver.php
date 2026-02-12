<?php

namespace Goedemiddag\RequestResponseLog\Support\BacktraceResolvers;

use Goedemiddag\RequestResponseLog\Contracts\BacktraceResolver;

/**
 * This resolver uses the default PHP debug_backtrace function to retrieve the backtrace.
 */
class LaravelApplicationResolver implements BacktraceResolver
{
    public function __construct(
        private readonly bool $includeIndex = false,
        private readonly bool $includeVendor = false,
        private readonly bool $includeMiddleware = false,
        private readonly bool $includePipeline = false,
        private readonly bool $includeRouting = false,
    ) {
    }

    public function get(): array
    {
        $basePath = realpath(base_path()) . DIRECTORY_SEPARATOR;
        $vendorPath = $basePath . 'vendor' . DIRECTORY_SEPARATOR;
        $indexFile = $basePath . 'public' . DIRECTORY_SEPARATOR . 'index.php';

        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        $stackFrames = [];
        foreach ($trace as $stackFrame) {
            // Many internal frames have no file/class, so skip those
            if (!isset($stackFrame['file'])) {
                continue;
            }

            // Determine the real path of the file, and skip if it cannot be resolved
            $file = realpath($stackFrame['file']);
            if ($file === false) {
                continue;
            }

            // Exclude the vendor directory if not including vendor frames
            if (!$this->includeVendor && str_starts_with($file, $vendorPath)) {
                continue;
            }

            // Exclude the index.php file if not including it
            if (!$this->includeIndex && $file === $indexFile) {
                continue;
            }

            $class = $stackFrame['class'] ?? null;

            // Exclude traces without class reference
            if ($class === null) {
                continue;
            }

            // Exclude Laravel middleware if not including middleware frames
            if (!$this->includeMiddleware && str_starts_with($class, 'App\\Http\\Middleware\\')) {
                continue;
            }

            // Exclude Laravel pipeline frames if not including routing frames
            if (! $this->includePipeline && str_starts_with($class, 'Illuminate\\Pipeline\\')) {
                continue;
            }

            // Exclude Laravel routing frames if not including routing frames
            if (! $this->includeRouting && (
                str_starts_with($class, 'Illuminate\\Routing\\Middleware\\') ||
                str_starts_with($class, 'Illuminate\\Routing\\Pipeline\\')
            )) {
                continue;
            }

            $stackFrames[] = $stackFrame;
        }

        return $stackFrames;
    }
}
