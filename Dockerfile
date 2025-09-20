FROM php:8.2-cli

# Install system dependencies and mysqli extension
RUN apt-get update && apt-get install -y \
        default-mysql-client \
        libmariadb-dev \
    && docker-php-ext-install mysqli \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html
COPY . .

EXPOSE 10000
CMD ["php", "-S", "0.0.0.0:10000"]
