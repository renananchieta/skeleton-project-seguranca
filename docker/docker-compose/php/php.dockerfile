FROM php:8.2-fpm

# Arguments defined in docker-compose.yml
ARG user
ARG uid

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libonig-dev \
    libpq-dev \
    libcurl4-openssl-dev \
    unzip \
    vim \
    firebird-dev \
    libfbclient2 \
    libib-util \
    && apt-get clean

# Install PHP extensions
RUN docker-php-ext-install curl pgsql pdo_pgsql pdo_mysql mbstring exif pcntl bcmath

# Install Firebird PDO extension
RUN docker-php-ext-install pdo_firebird

# Install Xdebug
RUN pecl install xdebug-3.2.2 \
    && docker-php-ext-enable xdebug

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Install redis
RUN pecl install -o -f redis \
    &&  rm -rf /tmp/pear \
    &&  docker-php-ext-enable redis

# Set working directory
RUN chown -R $user:www-data /var/www

WORKDIR /var/www

USER $user
