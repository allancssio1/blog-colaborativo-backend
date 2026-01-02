# Multi-stage build para otimizar a imagem final
FROM php:8.2-fpm-alpine AS base

# Instalar dependências do sistema e extensões PHP necessárias
RUN apk add --no-cache \
    mysql-client \
    curl \
    zip \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_mysql

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Definir diretório de trabalho
WORKDIR /var/www/html

# Copiar arquivos de configuração do Composer
COPY composer.json composer.lock ./

# Instalar dependências do Composer (otimizado para produção)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# Copiar o código da aplicação
COPY . .

# Criar diretório de logs e ajustar permissões
RUN mkdir -p /var/log/php \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expor porta 8000
EXPOSE 8000

# Usar usuário não-root
USER www-data

# Comando para iniciar o servidor PHP
CMD ["php", "-S", "0.0.0.0:8000", "-t", "src/Infra/Http"]

# Stage de desenvolvimento (opcional)
FROM base AS development

USER root

# Instalar dependências de desenvolvimento
RUN composer install --optimize-autoloader --no-interaction --no-progress

# Instalar Xdebug para debugging
RUN apk add --no-cache $PHPIZE_DEPS \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

USER www-data
