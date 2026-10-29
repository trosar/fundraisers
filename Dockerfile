FROM php:8.4-cli

# Install system dependencies required for extensions if needed, and the PDO MySQL extension
RUN docker-php-ext-install pdo_mysql

# Set the working directory first so relative paths match
WORKDIR /var/www/html

# Create the images directories and set strict permissions (Avoid 777 in production if possible)
RUN mkdir -p ms/images fs/images && chmod -R 755 ms/images fs/images

# Copy all application files
COPY . .

# Expose Railway's standard port
EXPOSE 8080

CMD ["php", "-S", "0.0.0.0:8080"]

