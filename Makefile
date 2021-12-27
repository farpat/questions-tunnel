include .env

PRIMARY_COLOR   		= \033[0;34m
PRIMARY_COLOR_BOLD   	= \033[1;34m
SUCCESS_COLOR   		= \033[0;32m
SUCCESS_COLOR_BOLD   	= \033[1;32m
DANGER_COLOR    		= \033[0;31m
DANGER_COLOR_BOLD    	= \033[1;31m
WARNING_COLOR   		= \033[0;33m
WARNING_COLOR_BOLD   	= \033[1;33m
NO_COLOR      			= \033[m

.DEFAULT_GOAL   = help

php_test = docker-compose -f docker-compose.test.yaml exec php php

isDocker := $(shell docker info > /dev/null 2>&1 && echo 1)
ifeq ($(isDocker), 1)
	php := docker-compose run --rm php php
	mariadb := docker-compose exec mariadb mysql -psecret -e
	bash := docker-compose run --rm php zsh
	composer := docker-compose run --rm php composer
	npm := docker-compose run --rm asset_dev_server npm
	npx := docker-compose run --rm asset_dev_server npx
else
	php := php
	composer := composer
endif

node_modules: package.json
	@$(npm) install

vendor: composer.json
	@$(composer) install

.PHONY: install
install: vendor node_modules ## Install the composer dependencies and npm dependencies

.PHONY: update
update: ## Update the composer dependencies and npm dependencies
	@$(composer) update
	@npm run update
	@$(npm) install

.PHONY: clean
clean: ## Remove cache
	@echo "$(DANGER_COLOR)Clearing Symfony cache...$(NO_COLOR)"
	@$(php) bin/console cache:pool:clear --quiet cache.app

.PHONY: help
help: ## Display this help
	@awk 'BEGIN {FS = ":.*##"; } /^[a-zA-Z_-]+:.*?##/ { printf "$(PRIMARY_COLOR_BOLD)%-15s$(NO_COLOR) %s\n", $$1, $$2 }' $(MAKEFILE_LIST) | sort

.PHONY: dev
dev: ## Run development servers
	@docker-compose up -d
	@echo "Dev server launched on http://localhost:$(DOCKER_APP_PORT)"
	@echo "Mail server launched on http://localhost:1080"
	@echo "Asset dev server launched on http://localhost:$(DOCKER_ASSET_DEV_SERVER_PORT)"

.PHONY: stop-dev
stop-dev: ## Stop development servers
	@docker-compose down
	@echo "Dev server stopped: http://localhost:$(DOCKER_APP_PORT)"
	@echo "Mail server stopped: http://localhost:1080"
	@echo "Asset dev server stopped: http://localhost:$(DOCKER_ASSET_DEV_SERVER_PORT)"

.PHONY: build
build: install ## Build assets projects for production
	@rm -rf ./public/assets/*
	@$(npm) run build

.PHONY: migrate
migrate: clean ## Refresh database by running new migrations
	@echo "$(PRIMARY_COLOR)Migrating database...$(NO_COLOR)"
	@$(php) bin/console doctrine:migrations:migrate --no-interaction
	@$(php) bin/console app:create-admin-user test@email.com
	@$(php) bin/console doctrine:fixtures:load --no-interaction

.PHONY: purge-database
purge-database: ## Purge dev database (CLEAN_MIGRATIONS=0[default] : remove migrations and make:migration)
	@$(php) bin/console doctrine:database:drop --force --if-exists
	@$(php) bin/console doctrine:database:create
ifdef CLEAN_MIGRATIONS
	@rm -rf migrations/*
	@$(php) bin/console make:migration
endif

.PHONY: bash
bash: ## Run bash in PHP container
	@$(bash)

.PHONY: lint-js
lint-js: ## Lint JavaScript and fix auto
	@$(npx) prettier-standard --lint --changed "assets/**/*.{js,scss,jsx}" --fix

.PHONY: lint-php
lint-php: ## Lint PHP
	@$(php) -d memory_limit=-1 vendor/bin/phpstan analyze

.PHONY: lint-php-generate-baseline
lint-php-generate-baseline: ## Baseline regeneration (PHPStan)
	@$(php) -d memory_limit=-1 vendor/bin/phpstan analyze --generate-baseline

.PHONY: run-command
run-command: ## Run command in PHP container (COMMAND="command to run")
	docker-compose run --rm php $(COMMAND)

.PHONY: test
test: ## Run tests
	@docker-compose -f docker-compose.test.yaml up -d
	@$(php_test) bin/console doctrine:database:drop --env=test --force --if-exists
	@$(php_test) bin/console doctrine:database:create --env=test
	@$(php_test) bin/console doctrine:migrations:migrate --no-interaction --quiet --env=test -vvv
	@$(php_test) bin/console doctrine:fixtures:load --no-interaction --quiet --env=test
	@$(php_test) bin/phpunit --testdox
	@$(php_test) bin/console doctrine:database:drop --env=test --force --quiet
	@docker-compose -f docker-compose.test.yaml down
