<?php

require 'vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\Credentials\CredentialProvider;
use \Mattifesto\ErrorHandling\ExceptionRenderer;
use \Mattifesto\ErrorHandling\OpenSearchLogger;

$arrayOfTests =
    [
        [
            'title' => 'AWS Connection',
            'callable' => 'runAWSConnectionTest',
        ],
        [
            'title' => 'exceptionToText',
            'callable' => 'runExceptionToTextTest',
        ]
    ];

?>
<!doctype html>
<html lang="en">

<head>
    <title>PHP Error Handling Tests</title>
</head>

<body>
    <h1>PHP Error Handling Tests</h1>

    <?php

    for ($testIndex = 0; $testIndex < count($arrayOfTests); $testIndex++) {
        $test = $arrayOfTests[$testIndex];
        $title = $test['title'];
        $titleAsHTML = htmlspecialchars($title);
        $callable = $test['callable'];

        echo "<h2>Test Index {$testIndex}: {$titleAsHTML}</h2>";

        try {
            $result = $callable();

            if (!is_string($result)) {
                $result = json_encode($result, JSON_PRETTY_PRINT);
            }

            $resultAsHTML = htmlspecialchars($result);

            echo "<pre>{$resultAsHTML}</pre>";
        } catch (Throwable $throwable) {
            echo "<h3>An unexpected exception occurred while running this test.</h3>";
            echo '<pre>';
            echo htmlspecialchars(
                ExceptionRenderer::exceptionToText($throwable)
            );
            echo '</pre>';
        }
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



function runAWSConnectionTest(): string
{
    $resultAsText = '';

    $arrayOfAWSEnvironmentVariables =
        [
            'AWS_ACCESS_KEY_ID',
            'AWS_SECRET_ACCESS_KEY',
            'AWS_DEFAULT_REGION',
        ];

    foreach ($arrayOfAWSEnvironmentVariables as $awsEnvironmentVariable) {
        $variableIsSet = !empty(getenv($awsEnvironmentVariable));

        $resultAsText .=
            "{$awsEnvironmentVariable} is set: " .
            ($variableIsSet ? 'yes' : 'no') .
            PHP_EOL;
    }

    // Use environment variables for AWS credentials
    $provider = CredentialProvider::env();
    $provider = CredentialProvider::memoize($provider);

    // Get the default region from environment variables
    $region = getenv('AWS_DEFAULT_REGION');

    // Create an S3 client with the region
    $s3 = new S3Client([
        'version'     => '2006-03-01',
        'region'      => $region,
        'credentials' => $provider,
    ]);

    // Try to list buckets to confirm the connection
    $buckets = $s3->listBuckets();
    $resultAsText .= "Successfully connected to AWS. Here are your S3 buckets:\n";

    foreach ($buckets['Buckets'] as $bucket) {
        $resultAsText .= "- {$bucket['Name']}\n";
    }

    return $resultAsText;
}



function runExceptionToTextTest(): string
{
    $resultAsText = "This test creates a complex exception and uses exceptionToText to render it as text. Here is the text generated:\n\n";

    try {
        function1();
    } catch (Throwable $throwable) {
        $resultAsText .= ExceptionRenderer::exceptionToText($throwable) . "\n\n";

        $result = OpenSearchLogger::logThrowable($throwable);

        $resultAsText .= "result: {$result}\n";

        return $resultAsText;
    }

    $resultAsText .= 'failed because no exception was thrown\n';

    return $resultAsText;
}
