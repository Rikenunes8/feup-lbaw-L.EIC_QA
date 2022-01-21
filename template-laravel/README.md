# Docker command

## Run Locally

`docker-compose up`

`php artisan db:seed` - Reset database

`php artisan migrate` - Establish migration

`php artisan queue:work` - Starts queue work

`php artisan schedule:work` - Starts calling schedule function regularly

`php artisan serve` - Init server


## docker_run.sh

~~~bash
#!/bin/bash
set -e

cd /var/www; php artisan config:cache

# Add cron job into cronfile
echo "* * * * * cd /var/www && php artisan schedule:run >> /dev/null 2>&1" >> cronfile

# Install cron job
crontab cronfile

# Remove temporary file
rm cronfile

env >> /var/www/.env
php-fpm8.0 -D

php artisan queue:work &
# Start cron
cron
nginx -g "daemon off;"
~~~


# Final URL

L.EIC Q&A - http://lbaw2185.lbaw.fe.up.pt/
Source Code - https://git.fe.up.pt/lbaw/lbaw2122/lbaw2185.git


# User Credentials

| Tipo              | Email                   | Password |
| ----------------- | ----------------------- | -------- |
| Admin             | jfcunha@fe.up.qa.pt     | U2e_PZwP |
| Teacher           | tbs@fe.up.qa.pt         | Dfx3L$nA |
| Teacher           | ssn@fe.up.qa.pt         | #pX8-wMM |
| Student           | up201906852@fe.up.qa.pt | @K4Agr6a |
| Student (blocked) | up201823452@fe.up.qa.pt | H9V@Dvjh |

