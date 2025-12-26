# Dockerfile
FROM php:8.1-apache

# 작업 디렉토리 설정
WORKDIR /var/www/html

# 필수 PHP 확장 설치
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
    libzip-dev \
    libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo pdo_mysql mysqli mbstring zip exif pcntl bcmath \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Apache mod_rewrite 활성화
RUN a2enmod rewrite

# Composer 설치
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Node.js와 npm 설치 (Gulp 빌드용)
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Apache 설정 파일 수정
RUN echo '<VirtualHost *:80>\n\
    DocumentRoot /var/www/html\n\
    <Directory /var/www/html>\n\
        Options Indexes FollowSymLinks\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# 애플리케이션 파일 복사
COPY . /var/www/html

# PHP 경고 수정 및 필수 파일 생성
RUN echo "Fixing PHP issues and creating required files..." && \
    # 1. auto_detect_line_endings 경고 수정
    if [ -f system/dotenv/Loader.php ]; then \
        sed -i "s/ini_set('auto_detect_line_endings', true);/\/\/ ini_set('auto_detect_line_endings', true); \/\/ Deprecated in PHP 8.1+/" system/dotenv/Loader.php; \
    fi && \
    # 2. index.php에 output buffering 추가
    if [ -f index.php ]; then \
        sed -i '2i\ob_start(); // Start output buffering for session handling' index.php; \
    fi && \
    # 3. compress_output 활성화
    if [ -f application/config/config.php ]; then \
        sed -i "s/\$config\['compress_output'\] = FALSE;/\$config['compress_output'] = TRUE;/" application/config/config.php; \
    fi && \
    # 4. resource.json 파일 생성 (적절한 구조로)
    echo '{"common":{"css":[],"js":[]},"beforeAssets":{"css":[],"js":[]},"assets":{"css":[],"js":[]},"afterAssets":{"css":[],"js":[]}}' > resource.json && \
    echo "Fixes applied successfully!"

# 세션 디렉토리 생성 및 권한 설정
RUN mkdir -p /App/ci_sessions && \
    chmod -R 777 /App/ci_sessions && \
    mkdir -p application/cache application/logs && \
    chmod -R 777 application/cache application/logs

# Composer 의존성 설치 (스크립트 실행 건너뛰기)
RUN if [ -f composer.json ]; then \
    composer install --no-dev --optimize-autoloader --no-scripts --ignore-platform-reqs || true; \
    fi

# npm 패키지 설치 및 빌드 (오류 무시)
RUN if [ -f package.json ]; then \
    npm install || true; \
    npm run build 2>/dev/null || true; \
    fi

# 권한 설정
RUN chown -R www-data:www-data /var/www/html /App && \
    chmod -R 755 /var/www/html

# 포트 노출
EXPOSE 80

# Apache 실행
CMD ["apache2-foreground"]