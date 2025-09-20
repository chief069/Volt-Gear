# Use official PHP image with Debian (so we can install extensions)
FROM php:8.2-cli

# Install mysqli extension
RUN docker-php-ext-install mysqli

# Set working directory
WORKDIR /var/www/html

# Copy all project files
COPY . .

# Expose port 10000
EXPOSE 10000

# Start PHP built-in server
CMD ["php", "-S", "0.0.0.0:10000"]
