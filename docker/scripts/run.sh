docker network create golden-gate 2> /dev/null

docker-compose -p symfony-test --env-file ./.env up -d --build --force-recreate --remove-orphans || exit

docker-compose -p symfony-test exec mysql bash -c "mysql -uroot -proot -e \"CREATE DATABASE IF NOT EXISTS master CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;\""

docker-compose -p symfony-test exec php-fpm bash -c "cd /var/www && COMPOSER_MEMORY_LIMIT=-1 composer install" || exit

docker-compose -p symfony-test exec php-fpm bash -c "bin/console doctrine:migration:migrate" || exit
