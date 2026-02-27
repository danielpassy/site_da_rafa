FROM php:7.3-apache

# Install required PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable mysqli extension explicitly
RUN docker-php-ext-enable mysqli

# Enable Apache mod_rewrite for WordPress permalinks
RUN a2enmod rewrite

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html

# Copy custom PHP configuration
COPY php.ini /usr/local/etc/php/
COPY uploads.ini /usr/local/etc/php/conf.d/

EXPOSE 80