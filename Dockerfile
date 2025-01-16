FROM php:8.2-fpm-alpine

## Install dependencies
RUN apk add --no-cache curl tini bash \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    freetype-dev \
    zip \
    unzip \
    git \
    openssh \
    oniguruma-dev \
    tree \
    libpq-dev \
    nodejs \
    npm

SHELL ["/bin/bash", "-l", "-c"]
# SHELL /bin/bash

RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp
    # && docker-php-ext-install gd intl gettext
RUN docker-php-ext-install pdo_pgsql gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# RUN apk add --no-cache $PHPIZE_DEPS \
#     && docker-php-ext-install pdo_mysql \
#     && docker-php-ext-install mysqli \
#     && docker-php-ext-install tokenizer \
#     && docker-php-ext-install json \
#     && docker-php-ext-install xml \
#     && docker-php-ext-install ctype \
#     && docker-php-ext-install session \
#     && docker-php-ext-install pdo \
#     && docker-php-ext-install pdo_mysql \
#     && docker-php-ext-install pdo_sqlite \
#     && docker-php-ext-install pdo_pgsql \
#     && docker-php-ext-install pdo_dblib \
#     && docker-php-ext-install pdo_oci \
#     && docker-php-ext-install pdo_sqlsrv \
#     && docker-php-ext-install pdo_firebird \
#     && docker-php-ext-install pdo_odbc

WORKDIR /app

ENV PATH=$PATH:/app/node_modules/.bin

RUN npm install -g yarn
RUN git config --global --add safe.directory "/app"

COPY . .

ENV COMPOSER_ALLOW_SUPERUSER=1
# RUN composer install --no-dev --no-interaction --no-progress --no-suggest --optimize-autoloader
RUN composer install
RUN yarn install

RUN yarn build

EXPOSE 8001
EXPOSE 5173
RUN echo "alias pa='php artisan'" >> ~/.bashrc

RUN chmod +x /app/entrypoint.sh

# CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
CMD ["/app/entrypoint.sh"]
