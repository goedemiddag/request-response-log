<?php

namespace Goedemiddag\RequestResponseLog\Contracts;

interface BacktraceResolver
{
    public function get(): array;
}
