FROM php:latest
WORKDIR /app
COPY . .
CMD ["php", "-S", "0.0.0.0:8000", "-t", "/app/public", "/app/router.php"]
EXPOSE 8000
