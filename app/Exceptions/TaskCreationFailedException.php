<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class TaskCreationFailedException extends HttpException
{
    public function __construct()
    {
        parent::__construct(500, 'Failed to create task.');
    }
}
