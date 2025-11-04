# PHP + Composer + Node + PNPM + Redis image
FROM php:8.3-fpm

# Install system dependencies, PHP extensions, and Redis
RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        wget \
        gnupg \
        supervisor \
        curl \
        zip \
        unzip \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        libonig-dev \
        libxml2-dev \
        libzip-dev \
        libssl-dev \
        build-essential \
        pkg-config \
        dos2unix \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*


# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Install PHP dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Install Node.js 22.x
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    && corepack enable \
    && corepack prepare pnpm@latest --activate \
    && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Copy package files and install JS dependencies via PNPM
COPY package*.json pnpm-lock.yaml ./
RUN CI=true pnpm install --frozen-lockfile


# Build frontend assets
RUN pnpm run build

# Fix permissions
RUN chown -R www-data:www-data /var/www/html

# Expose PHP-FPM
EXPOSE 9000 5173

# Supervisor and entrypoint
COPY supervisord.conf /etc/supervisord.conf
COPY entrypoint.sh /entrypoint.sh

# Normalize line endings and set execute permissions
RUN dos2unix /entrypoint.sh && chmod +x /entrypoint.sh


# Entrypoint
ENTRYPOINT ["/entrypoint.sh"]
