#!/usr/bin/env sh

TEST_HTML_DIRECTORY='build-test-image-html'

if [ -d "$TEST_HTML_DIRECTORY" ]; then
    read -p "Directory '$TEST_HTML_DIRECTORY' already exists. Do you want to replace it? (y/N) " answer
    case $answer in
        [Yy]* ) rm -rf $TEST_HTML_DIRECTORY; mkdir $TEST_HTML_DIRECTORY;;
        * ) echo "Creation of the '$TEST_HTML_DIRECTORY' was cancelled."; exit;;
    esac
else
    mkdir $TEST_HTML_DIRECTORY
fi

# Get the current date and time in the format YYYY-MM-DD-HHMMSS
DOCKER_TAG_BUILD_DATE=$(TZ=America/Los_Angeles date "+%Y%m%dT%H%M%S")

# Copy all files from 'test-project-assets' to 'test-project'
cp -r test-project-assets/* $TEST_HTML_DIRECTORY/

(
    cd $TEST_HTML_DIRECTORY

    composer update
)


docker build \
    --target php-error-handling-test-image \
    --tag php-error-handling-test:$DOCKER_TAG_BUILD_DATE \
    --tag php-error-handling-test:latest \
    .

docker compose -f run-tests-docker-compose.yaml up -d
