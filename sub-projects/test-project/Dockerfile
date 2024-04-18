FROM fluent/fluent-bit AS fluent-bit

COPY fluent-bit.conf /fluent-bit/etc/fluent-bit.conf



FROM php:8.1-cli AS test-app

WORKDIR /usr/src/php-error-handling-tests

COPY vendor vendor

COPY test.php .
