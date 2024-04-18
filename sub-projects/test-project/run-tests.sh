#!/usr/bin/env sh

# Get the current date and time in the format YYYY-MM-DD-HHMMSS
DOCKER_TAG_BUILD_DATE=$(TZ=America/Los_Angeles date "+%Y%m%dT%H%M%S")


echo "\n-----\nrun-test.sh: composer update\n-----\n"
composer update
if [ $? -ne 0 ]; then
    echo "composer update failed"
    exit 1
fi


echo "\n-----\nrun-test.sh: docker build\n-----\n"
docker build \
    --target test-app \
    --tag php-error-handling-test:$DOCKER_TAG_BUILD_DATE \
    --tag php-error-handling-test:latest \
    .
if [ $? -ne 0 ]; then
    echo "docker build failed"
    exit 1
fi


echo "\n-----\nrun-test.sh: docker compose up\n-----\n"
docker compose up --build --detach --remove-orphans
if [ $? -ne 0 ]; then
    echo "docker build failed"
    exit 1
fi


echo "\n-----\nrun-test.sh: docker compose run --rm test-app php test.php\n-----\n"
docker compose run --rm test-app php test.php
    if [ $? -ne 0 ]; then
    echo "docker compose run --rm test-app php test.php"
    exit 1
fi
