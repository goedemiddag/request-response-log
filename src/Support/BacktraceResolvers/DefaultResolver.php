<?php

namespace Goedemiddag\RequestResponseLog\Support\BacktraceResolvers;

use Goedemiddag\RequestResponseLog\Contracts\BacktraceResolver;

/**
 * This resolver uses the default PHP debug_backtrace function to retrieve the backtrace.
 */
class DefaultResolver implements BacktraceResolver
{
    public function get(): array
    {
        return debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
    }
}
