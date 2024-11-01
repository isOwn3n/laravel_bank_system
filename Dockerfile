FROM ghcr.io/parmincloud/containers/php:php8.3-cli

COPY composer.* /var/www/html/
COPY package.* /var/www/html/
# RUN npm install

ENV DOCUMENT_ROOT=/var/www/html/public

RUN composer install --no-scripts --no-autoloader --no-interaction --prefer-dist

COPY . .

RUN composer dump-autoload --optimize

CMD ["php", "artisan", "octane:start", "--port=8000", "--workers=$(nproc)", "--host=0.0.0.0"]
