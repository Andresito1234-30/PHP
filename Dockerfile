FROM php:8.2-apache


# Instalar extensiones necesarias para MySQL

RUN docker-php-ext-install mysqli pdo pdo_mysql


# (Opcional) habilitar mod_rewrite de Apache

RUN a2enmod rewrite


# Copiar el código al contenedor

WORKDIR /var/www/html

COPY . .