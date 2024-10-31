# Use the official PHP 8.1 image
FROM php:8.1-cli

# Set working directory
WORKDIR /var/www/html

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip

# Install Xdebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Configure Xdebug for code coverage
RUN echo "xdebug.mode=coverage" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy project files
COPY . .
