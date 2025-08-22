# Dockerfile

FROM php:8.2-fpm

# Expose port 80
EXPOSE 80

# Allow compose to run as root
ENV COMPOSER_ALLOW_SUPERUSER=1

# Install system dependencies and clean apt cache after installation
RUN apt update \
    && apt install -y \
        libpq-dev \
        libzip-dev \
        libonig-dev \
        libxml2-dev \
        libssl-dev \
        libpng-dev \
        zlib1g-dev \
        libjpeg-dev \
        libfreetype6-dev \
        git \
        curl \
        unzip \
        supervisor \
        nginx \
        nano \
        htop \
    && apt clean \
    && apt autoclean \
    && apt autoremove

# Install PHP extensions
RUN docker-php-ext-install \
    -j$(nproc) \
    pdo \
    pdo_pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip

# Install composer
# RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer --version=2.6.5
COPY --from=composer:2.6.5 \
    /usr/bin/composer \
    /usr/local/bin/composer

# Install prerequisites
RUN #pecl install redis \
#    && docker-php-ext-enable redis

# Install Swoole extension
RUN #pecl install swoole && docker-php-ext-enable swoole

# # Add user for laravel application
# RUN groupadd -g 1000 www
# RUN useradd -u 1000 -ms /bin/bash -g www www

# Change current working dir
WORKDIR /var/www/html

RUN rm ./index.nginx-debian.html

# Copy configuration files
COPY ./conf/supervisord/000-supervisord.conf        /etc/supervisor/supervisord.conf
COPY ./conf/supervisord/001-laravel-horizon.conf    /etc/supervisor/conf.d/laravel-horizon.conf
# COPY ./conf/supervisord/001-nginx.conf              /etc/supervisor/conf.d/nginx.conf
COPY ./conf/php.ini                                 /usr/local/etc/php/
COPY ./conf/nginx.conf                              /etc/nginx/sites-available/default

COPY artisan \
    composer.json \
    composer.lock \
    ./

# Install requirements (step 1, for caching purposes)
RUN composer install \
    --download-only \
    --no-interaction

# Copy all files and set permissions
COPY --chown=www-data:www-data . .

# Install requirements (step 2, for full installation)
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction

# Create folders
RUN mkdir -p \
    /var/www/html/storage \
    /var/www/html/bootstrap/cache

# Clear caches
RUN ./artisan config:clear \
    && ./artisan route:clear \
    && ./artisan view:clear \
    && ./artisan event:clear

# # Change ownership
# RUN chown -R www-data:www-data /var/www/html \
#     && chmod -R 775 /var/www/html/

# Change ownership
# RUN chmod -R 775 /var/www/html/
RUN #ls /var/www/html/ | grep -xvF vendor | xargs -I{} chmod -R 775 {}


# # Change current user to www
# USER www

# Start run script
ENTRYPOINT ["/bin/bash", "./run.sh"]
