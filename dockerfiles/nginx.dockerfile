FROM nginx:stable-alpine3.23-perl

# para el usuario y grupo
ARG UID
ARG GID

ENV UID=${UID}
ENV GID=${GID}

# se elimina grupo que no se necesita porque alpine ya lo tiene
RUN delgroup dialout

# se genera el grupo de usuario y se crea el usuario
RUN addgroup -g ${GID} --system hikey
RUN adduser -G hikey --system -D -s /bin/sh -u ${UID} userp

# se ejecuta 'sed' para que busque y edite lo que hay dentro del archivo
RUN sed -i "s/user nginx/user hikey/g" /etc/nginx/nginx.conf

# añadir archivo de configuracion de la carpeta de nginx
ADD ./nginx/default.conf /etc/nginx/conf.d/

# se genera nuevo directorio
RUN mkdir -p /var/www/html
