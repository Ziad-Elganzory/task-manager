<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class CompleteDependencyException extends HttpException
{
    public function __construct()
    {
        parent::__construct(500, 'Cannot complete this task until all dependencies are completed.');
    }
}
