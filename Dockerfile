FROM php:8.0-fpm

ENV DOCKER=true

RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    git \
    default-mysql-client \
    && docker-php-ext-install pdo pdo_mysql zip

COPY php-fpm.conf /usr/local/etc/php-fpm.d/www.conf

WORKDIR /var/www

COPY composer.lock composer.json ./
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install

COPY . .
COPY init.sh /usr/local/bin/init.sh
RUN chmod +x /usr/local/bin/init.sh

RUN chown -R www-data:www-data /var/www

EXPOSE 9000

CMD ["sh", "-c", "/usr/local/bin/init.sh && php-fpm"]
