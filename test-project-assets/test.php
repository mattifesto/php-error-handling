<?php

use \Mattifesto\ErrorHandling\ExceptionRenderer;

require('vendor/autoload.php');

echo "test 1\n";

try {
    function1();
} catch (Throwable $throwable) {
    $text = ExceptionRenderer::exceptionToText($throwable);

    echo "$text\n\n";
}

/**
 * @return void
 */
function function1(): void
{
    function2();
}



/**
 * @return void
 */
function function2(): void
{
    try {
        $randomInt = random_int(0, PHP_INT_MAX);

        throw new InvalidArgumentException(
            "this exception was thrown in function2()"
        );
    } catch (Throwable $throwable) {
        function2ErrorHandler($throwable);
    }
}



/**
 * @param Throwable $throwableArgument
 * @return void
 */
function function2ErrorHandler(
    Throwable $throwableArgument
): void {
    try {
        throw new RuntimeException(
            "this exception was thrown in function2ErrorHandler()",
            0,
            $throwableArgument
        );
    } catch (Throwable $throwable) {
        function2ErrorHandlerErrorHandler($throwable);
    }
}



/**
 * @param Throwable $throwableArgument
 * @return void
 */
function function2ErrorHandlerErrorHandler(
    Throwable $throwableArgument
): void {
    throw new RuntimeException(
        "this exception was thrown in function2ErrorHandlerErrorHandler()",
        0,
        $throwableArgument
    );
}

echo "test 2\n";

try {
    throw new Exception("this is a single exception test");
} catch (Throwable $throwable) {
    $text = ExceptionRenderer::exceptionToText($throwable);

    echo "$text\n\n";
}
