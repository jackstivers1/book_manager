FROM php:8.2-apache

# Enable Apache mod_rewrite (optional, but harmless)
RUN a2enmod rewrite

# Install PostgreSQL PDO driver (for Render Postgres)
RUN apt-get update \
  && apt-get install -y libpq-dev \
  && docker-php-ext-install pdo pdo_pgsql \
  && rm -rf /var/lib/apt/lists/*

# Copy your app into Apache's web root
COPY . /var/www/html/

# Permissions (usually not needed, but safe)
RUN chown -R www-data:www-data /var/www/html
