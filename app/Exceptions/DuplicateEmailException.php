<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class DuplicateEmailException extends HttpException
{
    public function __construct()
    {
        parent::__construct(400, 'The email has already been taken.');
    }
}
