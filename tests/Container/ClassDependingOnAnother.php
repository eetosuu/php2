<?php

namespace Geekbrains\Php2\UnitTests\Container;

class ClassDependingOnAnother
{
    public function __construct(private SomeClassWithoutDependencies $one,
                                private SomeClassWithParameter       $two,)
    {
    }
}