<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class TaskDependencyExistException extends HttpException
{
    public function __construct()
    {
        parent::__construct(500, 'Task dependency already exists.');
    }
}
