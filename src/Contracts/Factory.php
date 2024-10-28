<?php

namespace Goedemiddag\RequestResponseLog\Contracts;

use Illuminate\Database\Eloquent\Model;

interface Factory
{
    public function build(): Model;
}
