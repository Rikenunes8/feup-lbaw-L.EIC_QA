#!/bin/bash

# Stop execution if a step fails
set -e

IMAGE_NAME=git.fe.up.pt:5050/lbaw/lbaw2122/lbaw2185 # Replace with your group's image name

# Ensure that dependencies are available
composer install
php artisan clear-compiled
php artisan optimize

php artisan migrate
php artisan queue:work --daemon > /dev/null 2>&1 &
php artisan schedule:work --daemon > /dev/null 2>&1 &

docker build -t $IMAGE_NAME .
docker push $IMAGE_NAME
