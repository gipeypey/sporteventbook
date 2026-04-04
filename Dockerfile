# Dockerfile for SportEventBook
FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    zip \
    unzip \
    nodejs \
    npm \
    default-mysql-client

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl && \
    pecl install redis && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy existing application code
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Build assets
RUN npm install && npm run build

# Generate optimized autoloaders
RUN composer dump-autoload

# Create system user to run Composer and Artisan commands
RUN useradd -G www-data,root -u 1337 -d /home/laravel laravel
RUN mkdir -p /home/laravel/.composer && \
    chown -R laravel:laravel /home/laravel

# Create storage directories
RUN mkdir -p storage/app/public/assets/images/events && \
    mkdir -p storage/app/public/assets/images/event-prizes && \
    mkdir -p storage/logs && \
    mkdir -p bootstrap/cache && \
    chown -R laravel:laravel storage && \
    chmod -R 775 storage && \
    chmod -R 775 bootstrap/cache

# Expose port 9000
EXPOSE 9000

# Set user
USER laravel

# Run artisan optimize
RUN php artisan event:cache && php artisan route:cache && php artisan view:cache

CMD ["php-fpm"]