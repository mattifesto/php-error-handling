<?php

use Mattifesto\ErrorHandling\ExceptionRenderer;
use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\JsonFormatter;

require('vendor/autoload.php');

// app step 1: the first thing needed is to get all errors and exceptions handled, and help would be appreciated

// Mattifesto\ErrorHandling\Router::routeErrorsAndExceptionsToHandlers();


// setting the time zone will format the dates in the log file in west coast time
date_default_timezone_set('America/Los_Angeles');

announce("test 1");

$logFile = '/var/logs/php-error-handling-test/current.log';

try {
    // create a log channel
    $logger = new Logger('php-error-handling-test');

    $streamHandler = new StreamHandler($logFile, Level::Debug);

    $jsonFormatter = new JsonFormatter();

    $streamHandler->setFormatter($jsonFormatter);

    $logger->pushHandler($streamHandler);

    function1();
} catch (Throwable $throwable) {
    $text = ExceptionRenderer::exceptionToText($throwable);

    $logger->error($text);

    echo file_get_contents($logFile);
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



announce('test 2');

try {
    throw new Exception("this is a single exception test");
} catch (Throwable $throwable) {
    $text = ExceptionRenderer::exceptionToText($throwable);

    echo "$text\n\n";
}



function announce(string $message): void
{
    echo <<<EOT

        ----------------------------------------
        $message
        ----------------------------------------



        EOT;
}
