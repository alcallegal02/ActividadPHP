# Usar la imagen oficial de Apache con PHP
FROM php:8.4-apache

# Instalar extensiones necesarias
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Configura Apache
RUN a2enmod rewrite