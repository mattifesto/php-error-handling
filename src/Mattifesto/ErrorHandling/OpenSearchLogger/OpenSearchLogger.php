<?php

namespace Mattifesto\ErrorHandling;

use \DateTime;
use \DateTimeZone;
use \Throwable;
use \Mattifesto\ErrorHandling\ExceptionRenderer;

final class OpenSearchLogger
{
    /**
     * @param string $indexNameArgument
     *
     * @return void
     */
    private static function ensureOpenSearchIndex(
        string $indexNameArgument
    ): void {
        $openSearchClient = self::getOpenSearchClient();

        $indexAlreadyExists = $openSearchClient->indices()->exists(
            [
                'index' => $indexNameArgument
            ]
        );

        if ($indexAlreadyExists) {
            return;
        }

        $openSearchClient->indices()->create(
            [
                'index' => $indexNameArgument,
                'body' =>
                [
                    'settings' =>
                    [
                        'index' =>
                        [
                            'number_of_shards' => 1
                        ]
                    ]
                ]
            ]
        );
    }



    /**
     * @return ?\OpenSearch\Client
     */
    private static function getOpenSearchClient(): ?\OpenSearch\Client
    {
        static $openSearchClient = null;

        if ($openSearchClient === null) {
            $region = getenv('AWS_DEFAULT_REGION');

            if (!empty($region)) {
                $openSearchClient = (new ClientBuilder())
                    ->setHosts(
                        [
                            self::getOpenSearchEndpoint()
                        ]
                    )
                    ->setSigV4Region($region)
                    ->setSigV4Service('aoss')
                    ->setSigV4CredentialProvider(true)
                    ->build();
            } else {
                $openSearchClient =
                    (new ClientBuilder())
                    ->setHosts(
                        [
                            self::getOpenSearchEndpoint()
                        ]
                    )
                    ->setBasicAuthentication(
                        self::getOpenSearchUsername(),
                        self::getOpenSearchPassword()
                    )
                    ->setSSLVerification(false) // For testing only. Use certificate for validation
                    ->build();
            }
        }

        return $openSearchClient;
    }



    /**
     * @return string
     *
     * If there is no value set an empty string is returned.
     */
    private static function getOpenSearchPassword(): string
    {
        static $openSearchURL = null;

        if ($openSearchURL === null) {
            $openSearchURL = getenv('MATTIFESTO_OPEN_SEARCH_PASSWORD');

            if ($openSearchURL === false) {
                $openSearchURL = '';
            }
        }

        return $openSearchURL;
    }



    /**
     * @return string
     *
     * If there is no value set an empty string is returned.
     */
    private static function getOpenSearchURL(): string
    {
        static $openSearchURL = null;

        if ($openSearchURL === null) {
            $openSearchURL = getenv('MATTIFESTO_OPEN_SEARCH_URL');

            if ($openSearchURL === false) {
                $openSearchURL = '';
            }
        }

        return $openSearchURL;
    }



    /**
     * @return string
     *
     * If there is no value set an empty string is returned.
     */
    private static function getOpenSearchUsername(): string
    {
        static $openSearchURL = null;

        if ($openSearchURL === null) {
            $openSearchURL = getenv('MATTIFESTO_OPEN_SEARCH_USERNAME');

            if ($openSearchURL === false) {
                $openSearchURL = '';
            }
        }

        return $openSearchURL;
    }



    /**
     * @param string $debugMessageArgument
     *
     * @return string
     */
    public static function logDebugMessage(
        string $debugMessageArgument
    ): string {
        $response = OpenSearchLogger::sendToOpenSearch($debugMessageArgument);

        return $response;
    }



    /**
     * @param Throwable $throwableArgument
     *
     * @return string
     */
    public static function logThrowable(
        Throwable $throwableArgument
    ): string {
        $response = self::sendToOpenSearch(
            ExceptionRenderer::exceptionToText($throwableArgument)
        );

        return $response;
    }



    /**
     * @param string $messageArgument
     *
     * @return string
     *
     * If OpenSearch credentials are not available, an empty string is returned.
     */
    private static function sendToOpenSearch(
        string $messageArgument
    ): string {
        $openSearchIndex = 'logs2';
        $openSearchURL = self::getOpenSearchURL();
        $openSearchUsername = self::getOpenSearchUsername();
        $openSearchPassword = self::getOpenSearchPassword();

        if ($openSearchURL === '' || $openSearchUsername === '' || $openSearchPassword === '') {
            return '';
        }

        $microtime = microtime(true);
        $timestamp = floor($microtime);
        $micro_seconds = sprintf("%06d", ($microtime - $timestamp) * 1000000);
        $date = new DateTime(date('Y-m-d H:i:s.' . $micro_seconds, $timestamp));
        $date->setTimezone(new DateTimeZone("America/Los_Angeles"));

        $iso8601Timestamp = $date->format("Y-m-d\TH:i:s.uP"); // ISO 8601 format with microseconds

        self::ensureOpenSearchIndex($openSearchIndex);

        $openSearchClient = self::getOpenSearchClient();

        $result = $openSearchClient->create(
            [
                'index' => $openSearchIndex,
                'body' =>
                [
                    'message' => $messageArgument,
                    'timestamp' => $iso8601Timestamp
                ]
            ]
        );

        return json_encode($result);

        /*
        $ch = curl_init();

        // Set the cURL options
        curl_setopt($ch, CURLOPT_URL, "{$openSearchURL}/logs1/_doc/");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification (you might want to set it to true in production)
        curl_setopt($ch, CURLOPT_USERPWD, "{$openSearchUsername}:{$openSearchPassword}");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POST, true);

        // Set the POST data
        $data = array(
            "message" => $messageArgument,
            "timestamp" => $iso8601Timestamp
        );

        $data_string = json_encode($data);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);

        // Execute the cURL request
        $response = curl_exec($ch);

        // Check for errors
        if (curl_errno($ch)) {
            $method = __METHOD__;
            error_log("curl error in {$method}(): " . curl_error($ch));
        }

        // Close cURL session
        curl_close($ch);

        // Display the response
        return $response;
        */
    }
}
