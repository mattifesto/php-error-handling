FROM php:8.0-cli

WORKDIR /usr/src/php-error-handling-tests

COPY vendor vendor

COPY test.php .

CMD ["php", "test.php"]
