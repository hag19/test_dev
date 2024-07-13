FROM php:apache

# Enable necessary Apache modules
RUN docker-php-ext-install pdo pdo_mysql
RUN apt-get update && apt-get install -y libpq-dev libzip-dev unzip && \
    docker-php-ext-install pdo pdo_mysql && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite
COPY . /var/www/html/
EXPOSE 8090:80
