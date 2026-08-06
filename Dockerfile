FROM php:8.2-cli

# Install the PDO MySQL extension
RUN docker-php-ext-install pdo_mysql

# Create the images directories and set permissions
RUN mkdir -p /var/www/html/ms/images /var/www/html/fs/images && chmod -R 777 /var/www/html/ms/images /var/www/html/fs/images

# Set the working directory
WORKDIR /var/www/html

# Copy all application files
COPY . .

# Expose Railway's standard port
EXPOSE 8080

CMD ["php", "-S", "0.0.0.0:8080"]