FROM php:8.3-apache

# Install all necessary system libraries for Laravel & Composer
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install and enable every standard PHP extension required by Laravel packages
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql mbstring zip exif pcntl bcmath xml

# Enable Apache Mod Rewrite for Laravel routing
RUN a2enmod rewrite

# Grab the official Composer binary
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working environment directory
WORKDIR /var/www/html

# Copy your codebase into the container
COPY . .

# Force a clean dependency download while completely ignoring lock limitations and dev environments
RUN composer update --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts --ignore-platform-reqs

# Set correct storage and cache permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Direct Apache to point directly to Laravel's public directory
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

EXPOSE 80
CMD ["apache2-foreground"]
