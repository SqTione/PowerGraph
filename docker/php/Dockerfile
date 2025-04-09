# Базовый образ
FROM php:7.4-fpm

# Установка зависимостей
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    librabbitmq-dev \
    build-essential \
    gcc \
    make \
    autoconf \
    zip \
    unzip \
    git \
    cron \
    nano && \
    docker-php-ext-install pdo_mysql zip gd mbstring xml bcmath intl opcache


# Установка зависимостей для AMQP
RUN apt-get update && apt-get install -y \
    librabbitmq-dev \
    build-essential \
    gcc \
    make \
    autoconf

# Установка расширения AMQP через PECL
RUN pecl install amqp && docker-php-ext-enable amqp

# Установка Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Установка рабочей директории
WORKDIR /var/www/html

# Копируем основновное приложение
COPY ./src .

# Копируем crontab-файл
COPY ./docker/crontab /etc/cron.d/mycron

# Даем права на crontab-файл
RUN chmod 0644 /etc/cron.d/mycron

# Добавляем cron в автозапуск
RUN crontab /etc/cron.d/mycron

# Создаем необходимые директории, если они отсутствуют
RUN mkdir -p /var/www/html/protected/runtime /var/www/html/assets && \
    chown -R www-data:www-data /var/www/html/protected /var/www/html/assets && \
    chmod -R 775 /var/www/html/protected/runtime /var/www/html/assets

# Установка зависимостей Composer
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Запуск PHP FPM
CMD ["php-fpm"]