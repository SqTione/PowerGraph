FROM php:7.4-fpm

# Установка зависимостей
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git

# Установка PHP расширений
RUN docker-php-ext-install pdo_mysql zip gd mbstring xml

# Установка Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Установка рабочей директории
WORKDIR /var/www/html

# Копирование файлов проекта
COPY ./src /var/www/html

# Установка зависимостей через Composer
RUN composer install --no-dev --prefer-dist

# Запуск PHP-FPM
CMD ["php-fpm"]