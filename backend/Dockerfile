FROM php:8.2-apache

# Install extensions
RUN docker-php-ext-install pdo pdo_mysql

# Enable Apache rewrite module
RUN a2enmod rewrite

# Copy source code
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html

RUN chown -R www-data:www-data /var/www/html
