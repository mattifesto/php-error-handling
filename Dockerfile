FROM php:cli



# Debian - Install PHP Composer (MC v1)
# https://hub.docker.com/_/composer/

COPY --from=composer /usr/bin/composer /usr/bin/composer



# Debian - Install Git (MC v2)

RUN apt-get update
RUN apt-get install -y git



# Debian - Install ack (MC v1)

RUN apt-get update
RUN apt-get install -y ack



# Install Node.js (MC v2)

RUN apt-get update
RUN apt-get install -y nodejs
RUN apt-get install -y npm



# Install JSHint (MC v1)

RUN npm install -g jshint



CMD ["sh", "-c", "sleep infinity"]
