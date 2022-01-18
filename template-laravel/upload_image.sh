#!/bin/bash

# Stop execution if a step fails
set -e

IMAGE_NAME=git.fe.up.pt:5050/lbaw/lbaw2122/lbaw2185 # Replace with your group's image name

# Ensure that dependencies are available
composer require laravel/socialite
composer install
php artisan clear-compiled
php artisan optimize

php artisan migrate

docker build -t $IMAGE_NAME .
docker push $IMAGE_NAME
