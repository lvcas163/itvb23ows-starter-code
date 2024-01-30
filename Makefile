

start_php:
	php -S 0.0.0.0:8000 -t hive/src/

start_docker:
	docker compose up -d --build

stop_docker:
	docker compose down
