FROM php:8.2-apache

# Dépendances système
RUN apt-get update && apt-get install -y \
    unzip \
    libzip-dev \
    && rm -rf /var/lib/apt/lists/*

# Extensions PHP
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    zip

# Apache
RUN a2enmod rewrite

# DocumentRoot = public/
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/apache2.conf \
    /etc/apache2/conf-available/*.conf

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Composer
COPY composer.json composer.lock ./
RUN composer install --no-interaction --prefer-dist

# Projet
COPY . .

EXPOSE 80