FROM php:7.4-apache

# Install system dependencies including PostgreSQL
RUN apt-get update && \
    apt-get install -y --no-install-recommends --allow-downgrades \
    libpng16-16 \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    zlib1g-dev \
    libonig-dev \
    libpq-dev \
    zip \
    unzip || true

# Configure GD extension with PNG and JPEG support
RUN docker-php-ext-configure gd --with-freetype --with-jpeg || \
    docker-php-ext-configure gd --with-freetype-dir=/usr --with-jpeg-dir=/usr

# Install PHP extensions (PostgreSQL instead of MySQL)
RUN docker-php-ext-install pdo_pgsql pgsql mbstring exif pcntl bcmath gd

ENV TZ=Asia/Jakarta
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN echo "date.timezone = Asia/Jakarta" > /usr/local/etc/php/conf.d/timezone.ini

# Clean up
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy existing application directory permissions
COPY --chown=www-data:www-data . /var/www/html

# Change Apache document root
RUN sed -i 's!/var/www/html!/var/www/html!g' /etc/apache2/sites-available/000-default.conf

# Expose port 80
EXPOSE 80

CMD ["apache2-foreground"]
