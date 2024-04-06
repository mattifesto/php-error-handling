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

        $throwableIndex = 0;
        $throwableCount = count($chronilogicalArrayOfThrowables);

        while ($throwableIndex < $throwableCount) {
            $currentThrowable = $chronilogicalArrayOfThrowables[$throwableIndex];

            $errorText = ExceptionRenderer::singleThrowableToText(
                $currentThrowable
            );

            if ($throwableIndex === ($throwableCount - 1)) {
                $title = "final exception of {$throwableCount}";
            } else if ($throwableIndex === 0) {
                $title = "first exception of {$throwableCount}";
            } else {
                $throwableNumber = $throwableIndex + 1;
                $title = "exception {$throwableNumber} of {$throwableCount}";
            }

            $errorText = <<<EOT
            ---------- {$title} ----------

            {$errorText}
            EOT;

            array_push(
                $chronilogicalArrayOfExceptionTexts,
                $errorText
            );

            $throwableIndex += 1;
        }

        $outermostMessage = $outermostThrowableArgument->getMessage();

        $exceptionText =
            "final exception message: \"{$outermostMessage}\"\n\n\n" .
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
        $reversedArrayOfCalls = array_reverse($arrayOfCalls);
        $arrayOfText = [];
        $callIndex = 0;
        $callCount = count($reversedArrayOfCalls);

        while ($callIndex < $callCount) {
            $call = $reversedArrayOfCalls[$callIndex];
            $callIndexAsString = sprintf('%02d', $callIndex);

            $file = $call['file'] ?? '';
            $line = $call['line'] ?? '';
            $class = $call['class'] ?? '';
            $function = $call['function'];

            if ($class !== '') {
                $function = "{$class}::{$function}";
            }

            $function = "{$function}()";

            $text = <<<EOT
            {$callIndexAsString}: {$function} was called
                in {$file}
                on line {$line}
            EOT;

            array_push(
                $arrayOfText,
                $text
            );

            $callIndex += 1;
        }

        $throwableClassName = get_class($throwable);
        $message = $throwable->getMessage();
        $file = $throwable->getFile();
        $line = $throwable->getLine();

        $text = <<<EOT
            "{$message}"
            this {$throwableClassName} was thrown
            in {$file}
            on line {$line}
        EOT;

        array_push(
            $arrayOfText,
            $text
        );

        $textContent = implode("\n\n", $arrayOfText);

        return $textContent;
    }
}
