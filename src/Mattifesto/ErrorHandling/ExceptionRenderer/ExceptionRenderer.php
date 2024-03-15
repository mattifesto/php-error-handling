<?php

namespace Mattifesto\ErrorHandling;

use Throwable;

final class ExceptionRenderer
{
    /**
     * @return string
     */
    public static function exceptionToText(
        Throwable $outermostThrowableArgument
    ): string {
        $chronilogicalArrayOfThrowables = array();
        $chronilogicalArrayOfExceptionTexts = array();

        $currentThrowable = $outermostThrowableArgument;

        while ($currentThrowable !== null) {
            array_unshift(
                $chronilogicalArrayOfThrowables,
                $currentThrowable
            );

            $currentThrowable = $currentThrowable->getPrevious();
        }

        $errorIndex = 0;

        foreach ($chronilogicalArrayOfThrowables as $currentThrowable) {
            $errorText = ExceptionRenderer::singleThrowableToText(
                $currentThrowable
            );

            $errorText = <<<EOT
            ---------- error index {$errorIndex} ----------

            {$errorText}
            EOT;

            array_push(
                $chronilogicalArrayOfExceptionTexts,
                $errorText
            );

            $errorIndex += 1;
        }

        $outermostMessage = $outermostThrowableArgument->getMessage();

        $exceptionText =
        "error: \"{$outermostMessage}\"\n\n\n" .
        implode(
            "\n\n\n",
            $chronilogicalArrayOfExceptionTexts
        );

        return $exceptionText;
    }



    /**
     * @return string
     */
    private static function singleThrowableToText(
        Throwable $throwable
    ): string {
        $arrayOfCalls = $throwable->getTrace();
        $arrayOfText = [];

        foreach ($arrayOfCalls as $call) {
            $file = $call['file'] ?? '';
            $line = $call['line'] ?? '';
            $class = $call['class'] ?? '';
            $function = $call['function'];

            if ($class !== '') {
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

        $throwableClassName = get_class($throwable);
        $message = $throwable->getMessage();
        $file = $throwable->getFile();
        $line = $throwable->getLine();

        $text = <<<EOT
        {$file}
        line {$line}
        a throwable with the class {$throwableClassName} was thrown
        "{$message}"
        EOT;

        array_push(
            $arrayOfText,
            $text
        );

        $textContent = implode("\n\n", $arrayOfText);

        return $textContent;
    }
}
