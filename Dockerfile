FROM php:8.2-apache

# 1. Instalar dependencias necesarias para Laravel y PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_pgsql

# 2. Instalar Composer (el gestor de paquetes de PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3. Configurar carpeta de trabajo
WORKDIR /var/www/html

# 4. Copiar todos los archivos de tu proyecto al servidor
COPY . .

# 5. Instalar las librerías de Laravel
RUN composer install --no-dev --optimize-autoloader

# 6. Dar permisos a las carpetas de almacenamiento (clave para que no de error 500)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 7. Configurar Apache para que apunte a la carpeta 'public'
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# 8. Activar módulo rewrite de Apache
RUN a2enmod rewrite

# 9. Exponer el puerto 80 (el estándar web)
EXPOSE 80
