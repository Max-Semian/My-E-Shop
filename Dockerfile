FROM php:8.1-apache

# Install required PHP extensions for WordPress
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libicu-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        gd \
        mysqli \
        pdo \
        pdo_mysql \
        zip \
        exif \
        intl \
        opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite for WordPress permalinks
RUN a2enmod rewrite

# Set recommended PHP configuration for WordPress
RUN echo "upload_max_filesize = 64M" > /usr/local/etc/php/conf.d/wordpress.ini \
    && echo "post_max_size = 64M" >> /usr/local/etc/php/conf.d/wordpress.ini \
    && echo "memory_limit = 256M" >> /usr/local/etc/php/conf.d/wordpress.ini \
    && echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/wordpress.ini

# Allow .htaccess overrides
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

WORKDIR /var/www/html
