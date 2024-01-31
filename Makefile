

start_php:
	php -c /home/lucas/PycharmProjects/itvb23ows-starter-code/php.ini -S 0.0.0.0:8000 -t hive/src/

start_docker:
	docker compose up -d --build

stop_docker:
	docker compose down
