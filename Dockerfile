# Base image
FROM php:8.2-fpm

# Set working directory
WORKDIR /ecommerce-api-main/app

# Install dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    && docker-php-ext-install zip pdo_mysql

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy application files
COPY . /app

# Set permissions
RUN chown -R www-data:www-data /app \
    && chmod -R 755 /app/storage

# Install Laravel dependencies
RUN composer install

# Generate application key
RUN php artisan key:generate

# laravel passport
RUN php artisan passport:install --uuids

# seed
RUN php artisan db:seed

# Expose port 443
EXPOSE 443

# Start PHP-FPM and nginx
CMD ["php-fpm"]
