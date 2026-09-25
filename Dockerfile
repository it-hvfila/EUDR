FROM php:8.2-fpm-alpine

# ติดตั้งส่วนขยายที่จำเป็นสำหรับ Laravel และ Composer
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    git

RUN docker-php-ext-install pdo pdo_mysql bcmath gd

# ติดตั้ง Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# ก๊อปปี้ไฟล์โปรเจกต์ทั้งหมดเข้าตู้
COPY . .

# สั่งติดตั้ง Dependencies ของ Laravel
RUN composer install --no-dev --optimize-autoloader --no-interaction

# ตั้งสิทธิ์โฟลเดอร์ Storage และ Cache ให้ Laravel ทำงานได้
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 80

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=80"]
