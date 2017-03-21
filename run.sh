composer install
php app/console cache:clear
php app/console assets:install
php app/console assetic:dump
phpunit -c app/
php app/console server:run
