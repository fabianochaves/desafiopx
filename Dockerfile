# Usar uma imagem base com PHP 8.1 e Apache
FROM php:8.1-apache

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    curl \
    git \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    default-mysql-client \
    libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Habilitar o módulo rewrite do Apache
RUN a2enmod rewrite

# Instalar o Composer
RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

# Definir DocumentRoot como /var/www/html/public (caso você use public/)
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# Substituir VirtualHost
COPY apache/vhost.conf /etc/apache2/sites-available/000-default.conf

#copiar o .htaccess
COPY ./public/.htaccess /var/www/html/public/.htaccess

# Definir o diretório de trabalho
WORKDIR /var/www/html

# Expôr a porta 80 para o Apache
EXPOSE 80

# Comando de inicialização padrão
CMD ["apache2-foreground"]