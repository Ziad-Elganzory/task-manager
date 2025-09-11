<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class TaskDependencyNotFound extends HttpException
{
    public function __construct()
    {
        parent::__construct(404, 'Task dependency not found.');
    }
}
