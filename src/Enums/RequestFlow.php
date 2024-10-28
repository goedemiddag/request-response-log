<?php

namespace Goedemiddag\RequestResponseLog\Enums;

enum RequestFlow: string
{
    case Incoming = 'in';
    case Outgoing = 'out';
}
