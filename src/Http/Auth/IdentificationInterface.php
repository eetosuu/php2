<?php

namespace Geekbrains\Php2\Http\Auth;

use Geekbrains\Php2\Blog\User;
use Geekbrains\Php2\Http\Request;

interface IdentificationInterface
{
    public function user(Request $request): User;
}