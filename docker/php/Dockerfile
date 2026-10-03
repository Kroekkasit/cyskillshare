FROM php:8.2-apache

# System deps + PHP extensions required by CySkillShare
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libzip-dev \
        unzip \
    && docker-php-ext-install -j$(nproc) pdo_mysql \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

# Document root must be /public (not the project root)
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Allow .htaccess overrides in the public directory
RUN printf '<Directory /var/www/html/public>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>\n' > /etc/apache2/conf-available/cyskillshare.conf \
    && a2enconf cyskillshare

WORKDIR /var/www/html

# Writable storage for logs/uploads (www-data)
RUN mkdir -p /var/www/html/storage/logs /var/www/html/storage/uploads \
    && chown -R www-data:www-data /var/www/html/storage

COPY docker/php/entrypoint.sh /usr/local/bin/cyskillshare-entrypoint.sh
RUN chmod +x /usr/local/bin/cyskillshare-entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/cyskillshare-entrypoint.sh"]
