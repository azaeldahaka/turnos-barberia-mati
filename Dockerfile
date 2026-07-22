FROM php:8.4-cli

# Instalar dependencias del sistema y extensiones de PHP requeridas para PostgreSQL/Supabase
RUN apt-get update && apt-get install -y \
    libpq-dev \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_pgsql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar directorio de trabajo
WORKDIR /app

# Copiar los archivos del proyecto
COPY . .

# Instalar dependencias de Laravel
RUN composer install --optimize-autoloader --no-dev

# Exponer el puerto
EXPOSE $PORT

# Iniciar servidor nativo de Laravel
CMD php artisan serve --host=0.0.0.0 --port=$PORT
