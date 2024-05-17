# Build and start dev application.
build:
	docker compose -f docker-compose.yml -f docker-compose.dev.yml up -d --build

# Start dev application.
prod:
	docker compose -f docker-compose.yml up -d

# Start dev application.
dev:
	docker compose -f docker-compose.yml -f docker-compose.dev.yml up -d

# Stop dev application.
down:
	docker compose -f docker-compose.yml -f docker-compose.dev.yml down

# Enter application bash
ssh:
	docker compose exec -it symfony bash

# Enter database bash
ssh-db:
	docker compose exec -it database bash
