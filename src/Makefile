up:
	docker compose up -d

down:
	docker compose down

ps:
	docker compose ps

init:
	docker compose exec app bash -c "npm ci"
	docker compose exec app bash -c "composer install"
	make migrate

build:
	docker compose build

exec-app:
	docker compose exec app bash