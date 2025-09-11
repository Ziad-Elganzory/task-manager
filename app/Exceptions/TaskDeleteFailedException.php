<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class TaskDeleteFailedException extends HttpException
{
    public function __construct()
    {
        parent::__construct(500, 'Failed to delete task.');
    }
}
