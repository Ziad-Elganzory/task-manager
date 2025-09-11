<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class CircularDependencyException extends HttpException
{
    public function __construct()
    {
        parent::__construct(500, 'Circular dependency detected.');
    }
}
