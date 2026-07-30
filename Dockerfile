FROM php:8.4-fpm

# Install system dependencies & PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libjpeg-dev \
    libfreetype6-dev \
    nodejs \
    npm \
    supervisor \
    cron \
    nginx \
    gettext-base \
    && apt-get clean && rm -rf /var/cache/apt/archives

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application files
COPY . .

# Install PHP dependencies
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Install and build frontend assets
RUN npm install && npm run build

# Create storage link
RUN php artisan storage:link || true

# Setup supervisor (manages php-fpm + nginx + cron)
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Nginx: remove default site, install our templated vhost
RUN rm -f /etc/nginx/sites-enabled/default /etc/nginx/conf.d/default.conf
COPY docker/nginx/app.conf.template /etc/nginx/conf.d/app.conf.template

# Entrypoint: renders the nginx config with Railway's $PORT at container start
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Setup cron for scheduler
RUN echo "* * * * * cd /var/www && php artisan schedule:run >> /dev/null 2>&1" | crontab -

# Permissions + log dirs
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache \
    && mkdir -p /var/log/supervisor

EXPOSE 8080

ENTRYPOINT ["/entrypoint.sh"]