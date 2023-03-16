<?php

namespace Geekbrains\Php2\Blog\Exceptions;


use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends AppException implements NotFoundExceptionInterface
{

}