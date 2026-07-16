FROM php:8.4-fpm-alpine3.23

# para el usuario y grupo
ARG UID
ARG GID

ENV UID=${UID}
ENV GID=${GID}

# se genera el directorio de html donde estará nuestra config
RUN mkdir -p /var/www/html
WORKDIR /var/www/html

# instalación de composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# se elimina grupo que no se necesita porque alpine ya lo tiene
RUN delgroup dialout

# se genera el grupo de usuario y se crea el usuario
RUN addgroup -g ${GID} --system hikey
RUN adduser -G hikey --system -D -s /bin/sh -u ${UID} userp

# se ejecuta 'sed' para que busque y edite lo que hay dentro del archivo
# cambia al usuario por defecto de php-fpm que es www-data por los que se definieron anteriormente
RUN sed -i "s/user = www-data/user = userp/g" /usr/local/etc/php-fpm.d/www.conf
RUN sed -i "s/group = www-data/group = hikey/g" /usr/local/etc/php-fpm.d/www.conf

# agrega una nueva linea para mostrar los errores que pueda haber
RUN echo "php_admin_flag[log_errors] = on" >> /usr/local/etc/php-fpm.d/www.conf

# instalación de extension que ejecutara las extensiones de php
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS

# instalación de extension de php para BD
RUN docker-php-ext-install pdo pdo_mysql

# instalación de dependencias para correr gd y zip
RUN apk add libpng-dev \
    && apk add freetype-dev \
    && apk add jpeg-dev \
    && apk add libzip-dev \
    && apk add oniguruma-dev \
    && apk add libxml2-dev \
    && apk add linux-headers \
    && apk add nodejs npm

# instalación de extensiones de php
RUN docker-php-ext-install mbstring \
    && docker-php-ext-install bcmath \
    && docker-php-ext-install xml \
    && docker-php-ext-install ctype \
    && docker-php-ext-install sockets \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && docker-php-ext-install exif \
    && docker-php-ext-install zip

# instalación de redis
RUN pecl install redis \
    && docker-php-ext-enable redis

# se quitan las dependencias que ya no se necesiten
RUN apk del .build-deps

# se entra con el usuario previamente creado
USER userp

CMD ["php-fpm", "-y", "/usr/local/etc/php-fpm.conf", "-R"]
