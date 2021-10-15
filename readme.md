##Set up environment

`cp .env.example .env`

`cd docker && cp .env.example .env`

`sh scripts/run.sh`

##Using:
Put a file in root directory and run the command:

`docker-compose -p symfony-test exec php-fpm bash -c "bin/console app:product:import:csv file.csv"`

If you want to run the command in test mode, add `-t` after command name.
Detailed log stores in `var/log/import.log`
