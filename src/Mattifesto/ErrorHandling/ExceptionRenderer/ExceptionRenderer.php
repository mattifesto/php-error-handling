<?php

namespace Mattifesto\ErrorHandling;

use Throwable;

final class
ExceptionRenderer
{
    /**
     * @return string
     */
    static function
    exceptionToText(
        Throwable $throwable
    ): string
    {
        $arrayOfCalls = $throwable->getTrace();
        $arrayOfText = [];

        foreach(
            $arrayOfCalls as $call
        ) {
            $file = $call['file'] ?? '';
            $line = $call['line'] ?? '';
            $class = $call['class'] ?? '';
            $function = $call['function'];

            if ($class !== '')
            {
                $function = "{$class}::{$function}";
            }

            $function = "{$function}()";

            $text = <<<EOT

            {$file}
            line {$line}
            {$function} was called

            EOT;

            array_push(
                $arrayOfText,
                $text
            );
        }

        $arrayOfText = array_reverse($arrayOfText);

        $message =
        $throwable->getMessage();

        $file =
        $throwable->getFile();

        $line =
        $throwable->getLine();

        $text =
        <<<EOT

        an exception was thrown in the file: {$file}
        on line: {$line}
        with the message: {$message}

        EOT;

        array_push(
            $arrayOfText,
            $text
        );

        $textContent =
        implode("\n", $arrayOfText);

        return $textContent;
    }
    // exceptionToText()
}
