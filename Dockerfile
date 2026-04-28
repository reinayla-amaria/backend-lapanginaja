FROM php:8.2-fpm-alpine

# Install dependencies sistem yang dibutuhin aja
RUN apk add --no-cache \
    nginx \
    nodejs \
    npm \
    curl \
    zip \
    unzip \
    git \
    supervisor \
    libpng-dev \
    libzip-dev \
    oniguruma-dev \
    libxml2-dev

# Install ekstensi PHP (Khusus MySQL & dependensi standar Laravel)
RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    opcache \
    zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# --- OPTIMASI CACHING ---
# Copy file package manager duluan
COPY composer.json composer.lock package.json package-lock.json* ./

# Install dependency backend & frontend
# (composer install tanpa script & autoloader dulu biar cache jalan)
RUN composer install --no-scripts --no-autoloader --no-dev
RUN npm install

# Baru copy sisa semua source code project lu
COPY . .

# Build frontend & dump-autoload composer
RUN npm run build
RUN composer dump-autoload --optimize

# Set permission folder Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Copy konfigurasi Nginx dan Supervisor
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]