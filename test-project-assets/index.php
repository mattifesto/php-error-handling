<?php

require 'vendor/autoload.php';

use \Mattifesto\ErrorHandling\ExceptionRenderer;
use \Mattifesto\ErrorHandling\OpenSearchLogger;

$timezone = new DateTimeZone('America/Los_Angeles');
$date = new DateTime('now', $timezone);

?>
<!doctype html>
<html lang="en">

<head>
    <title>PHP Error Handling Tests</title>
</head>

<body>
    <p><?= $date->format('c') ?></p>
    <?php

    try {
        function1();
    } catch (Throwable $throwable) {
        echo '<pre>';
        echo htmlspecialchars(
            ExceptionRenderer::exceptionToText($throwable)
        );
        echo '</pre>';

        $result = OpenSearchLogger::logThrowable($throwable);

        echo "<pre>result: {$result}</pre>";
    }

    ?>
</body>

</html>
<?php

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
            "first exception: {$randomInt}"
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
    $randomInt = random_int(0, PHP_INT_MAX);

    throw new RuntimeException(
        "an exception occurred in function2(): {$randomInt}",
        0,
        $throwableArgument
    );
}
