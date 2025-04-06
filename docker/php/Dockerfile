# Базовый образ
FROM php:7.4-fpm

# Установка зависимостей
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    cron \
    nano

# Установка PHP расширений
RUN docker-php-ext-install pdo_mysql zip gd mbstring xml bcmath intl opcache

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

# Копирование файлов проекта
COPY ./src /var/www/html

# Копируем crontab-файл
COPY ./docker/crontab /etc/cron.d/mycron

# Даем права на crontab-файл
RUN chmod 0644 /etc/cron.d/mycron

# Добавляем cron в автозапуск
RUN crontab /etc/cron.d/mycron

# Копируем entrypoint скрипт
COPY ./docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh

# Делаем entrypoint скрипт исполняемым
RUN chmod +x /usr/local/bin/entrypoint.sh

# Указываем entrypoint
CMD ["entrypoint.sh"]