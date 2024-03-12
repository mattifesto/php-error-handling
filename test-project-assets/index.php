<?php

require 'vendor/autoload.php';

use \Mattifesto\ErrorHandling\ExceptionRenderer;

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
        throw new Exception("test exception");
    } catch (Throwable $throwable) {
        echo '<pre>';
        echo htmlspecialchars(
            ExceptionRenderer::exceptionToText($throwable)
        );
        echo '</pre>';
    }

    ?>
</body>

</html>
