# Use official PHP with Apache
FROM php:8.2-apache

# Enable mod_rewrite (needed for frameworks like Laravel)
RUN a2enmod rewrite

# Install common PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Copy all project files to Apache's web root
COPY . /var/www/html/

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expose port 80
EXPOSE 80