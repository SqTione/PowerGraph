set -e

# Создаем необходимые директории, если они отсутствуют
mkdir -p /var/www/html/protected/runtime
mkdir -p /var/www/html/assets

# Устанавливаем права доступа
chown -R www-data:www-data /var/www/html/protected/runtime
chown -R www-data:www-data /var/www/html/assets
chmod -R 775 /var/www/html/protected/runtime
chmod -R 775 /var/www/html/assets

# Запускаем cron
cron

# Запускаем PHP-FPM
exec php-fpm