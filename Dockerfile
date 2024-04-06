FROM php:cli AS development-environment



# Debian - Install PHP Composer (Mattifesto 2024-03-14T17:33:00-07:00)
# https://hub.docker.com/_/composer/

RUN apt-get update
RUN apt-get install -y zlib1g-dev libzip-dev unzip
RUN docker-php-ext-install zip
COPY --from=composer /usr/bin/composer /usr/bin/composer



# Debian - Install Git (MC v2)

RUN apt-get update
RUN apt-get install -y git



# Debian - Install ack (MC v1)

RUN apt-get update
RUN apt-get install -y ack



# Debian - Install Docker (2024-02-26)
# https://docs.docker.com/engine/install/debian/

# Add Docker's official GPG key:
RUN apt-get update
RUN apt-get install -y ca-certificates curl
RUN install -m 0755 -d /etc/apt/keyrings
RUN curl -fsSL https://download.docker.com/linux/debian/gpg -o /etc/apt/keyrings/docker.asc
RUN chmod a+r /etc/apt/keyrings/docker.asc

# Add the repository to Apt sources:
RUN echo \
  "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.asc] https://download.docker.com/linux/debian \
  $(. /etc/os-release && echo "$VERSION_CODENAME") stable" | \
  tee /etc/apt/sources.list.d/docker.list > /dev/null
RUN apt-get update

RUN apt-get install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin



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
