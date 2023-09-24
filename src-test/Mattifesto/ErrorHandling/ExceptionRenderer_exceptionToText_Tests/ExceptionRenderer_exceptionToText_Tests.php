<?php

namespace Mattifesto\ErrorHandling;

final class
ExceptionRenderer_exceptionToText_Tests
{
    public static function
    Test_run()
    : void
    {
        throw new Exception('foo');
    }
}
