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



# Install Node.js (Mattifesto v3)
#
# 2024-02-21
#
# On the following page, Node.js recommends using the NodeSource repository to
# install Node.js.
#
# https://nodejs.org/en/download/package-manager#debian-and-ubuntu-based-linux-distributions
#
# NodeSource had some issues in the recent past where their installation
# instructions were too complex and difficult to use in a Dockerfile. They have
# fixed this and say, "Installation Scripts: Back by popular demand, the
# installation scripts have returned and are better than ever. See the
# installation instructions below for details on how to use them."
#
# The command below comes from this page:
#
# https://github.com/nodesource/distributions?tab=readme-ov-file#debian-and-ubuntu-based-distributions

RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - &&\
    apt-get install -y nodejs



# Install JSHint (MC v1)

RUN npm install -g jshint



CMD ["sh", "-c", "sleep infinity"]
