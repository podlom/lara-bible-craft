FROM php:8.2-fpm

# Встановлення системних залежностей і PHP-розширень
RUN apt-get update && apt-get install -y \
    git curl zip unzip libonig-dev libxml2-dev libzip-dev libpng-dev \
    default-mysql-client \
    # Node.js (для npm / vite / tailwind)
    && curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd

# Встановлення Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html