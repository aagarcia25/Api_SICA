# Usa una imagen base moderna con Apache y PHP preinstalados
FROM php:8.1-apache

# Ajuste de trabajo
WORKDIR /var/www/html

# Instala dependencias necesarias
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    wget \
    curl \
    libxrender1 \
    libxtst6 \
    libxi6 \
    fontconfig \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Copiar el archivo OpenJDK al contenedor
COPY openlogic-openjdk-8u432-b06-linux-x64.tar.gz /tmp/openjdk8.tar.gz

# Extraer e instalar OpenJDK 8
RUN mkdir -p /usr/local/java && \
    tar -xzf /tmp/openjdk8.tar.gz -C /usr/local/java && \
    mv /usr/local/java/openlogic-openjdk-8u432-b06-linux-x64 /usr/local/java/jdk8 && \
    rm /tmp/openjdk8.tar.gz

# Configurar JAVA_HOME y actualizar PATH
ENV JAVA_HOME=/usr/local/java/jdk8
ENV PATH=$JAVA_HOME/bin:$PATH

# Configurar el entorno gráfico en modo headless
ENV JAVA_OPTS="-Djava.awt.headless=true"

# Descargar e instalar libpng15
RUN wget http://download.sourceforge.net/libpng/libpng-1.5.30.tar.gz && \
    tar -xzvf libpng-1.5.30.tar.gz && \
    cd libpng-1.5.30 && \
    ./configure --prefix=/usr/local && \
    make && \
    make install && \
    echo "/usr/local/lib" > /etc/ld.so.conf.d/local.conf && \
    ldconfig && \
    cd .. && \
    rm -rf libpng-1.5.30 libpng-1.5.30.tar.gz

# Crear directorio de cache para fontconfig y dar permisos
RUN mkdir -p /var/cache/fontconfig && \
    chmod -R 777 /var/cache/fontconfig

# Descargar e instalar la fuente Arial
RUN mkdir -p /usr/share/fonts/truetype/msttcorefonts && \
    wget https://github.com/gasharper/linux-fonts/raw/master/arial.ttf -O /usr/share/fonts/truetype/msttcorefonts/Arial.ttf && \
    fc-cache -fv

# Habilita módulos necesarios de Apache
RUN a2enmod rewrite

# Configuración de Apache para Laravel
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf
RUN a2ensite 000-default.conf

# Copia y ajusta los archivos de Composer
COPY composer.json composer.lock ./
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php && \
    php -r "unlink('composer-setup.php');" && \
    mv composer.phar /usr/local/bin/composer

# Define variables de entorno para Composer
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_PROCESS_TIMEOUT=2000

# Instala dependencias de Composer
RUN composer install --prefer-dist --no-scripts --no-dev --optimize-autoloader

# Copia el código de la aplicación
COPY . .

# Genera la clave de la aplicación y publica configuraciones
RUN cp .env.example .env && \
    php artisan key:generate && \
    php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider" --tag=config && \
    php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider" --tag=config

# Ajustes de permisos
RUN chown -R www-data:www-data public && \
    chmod -R 775 storage && \
    chmod -R ugo+rw storage && \
    chmod -R ugo+rw bootstrap && \
    chown -R www-data:www-data storage bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache

# Configuración de PHP
RUN echo "memory_limit = 512M" >> /usr/local/etc/php/php.ini && \
    echo "max_execution_time = 300" >> /usr/local/etc/php/php.ini && \
    echo "upload_max_filesize = 100M" >> /usr/local/etc/php/php.ini && \
    echo "post_max_size = 512M" >> /usr/local/etc/php/php.ini

EXPOSE 80
CMD ["apache2-foreground"]
