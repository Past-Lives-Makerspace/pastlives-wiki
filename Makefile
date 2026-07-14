COMPOSE_FILE := docker-compose-development.yml
IMAGE_NAME   := pastlives-wiki

.DEFAULT_GOAL := help

.PHONY: help build up down update-db clean

help: ## Print this help message
	@grep -E '^[a-zA-Z_-]+:.*## ' $(MAKEFILE_LIST) \
		| awk 'BEGIN {FS = ":.*## "}; {printf "  \033[36m%-10s\033[0m %s\n", $$1, $$2}'

build: ## Build the Docker image
	docker build -t $(IMAGE_NAME) .

up: ## Start the development environment (docker-compose-development.yml)
	docker compose -f $(COMPOSE_FILE) up --build -d

down: ## Stop and remove the development environment containers
	docker compose -f $(COMPOSE_FILE) down

update-db: ## Initialize or upgrade the MediaWiki database schema and ensure sysop account exists
	@echo "Checking if schema needs initialization..."
	@docker compose -f $(COMPOSE_FILE) exec db psql -U mediawiki_user -d mediawiki \
	    -c "CREATE SCHEMA IF NOT EXISTS mediawiki AUTHORIZATION mediawiki_user;" -q
	@if docker compose -f $(COMPOSE_FILE) exec db psql -U mediawiki_user -d mediawiki \
	    -tAc "SELECT 1 FROM information_schema.tables WHERE table_schema='mediawiki' AND table_name='revision'" \
	    | grep -q 1; then \
	    echo "Schema exists — running updater..."; \
	    docker compose -f $(COMPOSE_FILE) exec wiki php maintenance/run.php update --quick; \
	else \
	    echo "Empty database — running installer..."; \
	    docker compose -f $(COMPOSE_FILE) exec wiki bash -c " \
	        mv /var/www/html/LocalSettings.php /var/www/html/LocalSettings.php.bak && \
	        php maintenance/run.php install \
	            --dbtype postgres --dbserver db --dbname mediawiki \
	            --dbuser mediawiki_user --dbpass localdevpassword \
	            --dbschema mediawiki --dbport 5432 \
	            --server 'http://localhost:8080' --scriptpath '' \
	            --pass 'adminpassword' 'PastLives' 'admin' && \
	        mv /var/www/html/LocalSettings.php.bak /var/www/html/LocalSettings.php"; \
	fi
	@echo "Ensuring sysop account exists..."
	@docker compose -f $(COMPOSE_FILE) exec wiki php maintenance/run.php createAndPromote \
	    --force --bureaucrat admin adminpassword

clean: ## Remove the built image and prune dangling build cache
	docker compose -f $(COMPOSE_FILE) down --rmi local --volumes --remove-orphans
	docker image rm -f $(IMAGE_NAME) 2>/dev/null || true
	docker builder prune -f
