<?php

namespace Goedemiddag\RequestResponseLog\Support\BacktraceResolvers;

use Goedemiddag\RequestResponseLog\Contracts\BacktraceResolver;

/**
 * This resolver is used when the backtrace is not needed or should be ignored to limit performance and storage needs.
 */
class IgnoredResolver implements BacktraceResolver
{
    public function get(): array
    {
        return [];
    }
}
