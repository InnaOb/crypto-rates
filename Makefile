.DEFAULT_GOAL := help
SHELL := /bin/bash

CONTAINER_NAME     := crypto
PHP_SERVICE_NAME   := php
SCHEDULER_SERVICE  := scheduler
WORKING_DIR        := /var/www/app
DOCKER_COMPOSE     := docker compose
DOCKER_EXEC        := $(DOCKER_COMPOSE) exec -T -w $(WORKING_DIR) $(PHP_SERVICE_NAME)
DOCKER_EXEC_IT     := $(DOCKER_COMPOSE) exec -w $(WORKING_DIR) $(PHP_SERVICE_NAME)
PHP                := $(DOCKER_EXEC) php
COMPOSER           := $(DOCKER_EXEC) composer
SYMFONY            := $(PHP) bin/console

## —— Help ————————————————————————————————————————————————————————————————————

.PHONY: help
help: ## Show this help screen
	@printf "\033[33mUsage:\033[0m\n  make [target]\n\n\033[33mTargets:\033[0m\n"
	@grep -E '^[-a-zA-Z0-9_\.\/]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[32m%-25s\033[0m %s\n", $$1, $$2}'

## —— Docker ——————————————————————————————————————————————————————————————————

.PHONY: build
build: ## Build containers without cache
	@$(DOCKER_COMPOSE) build --pull --no-cache

.PHONY: up
up: ## Start all containers in background
	@$(DOCKER_COMPOSE) up --detach

.PHONY: down
down: ## Stop and remove containers
	@$(DOCKER_COMPOSE) down --remove-orphans

.PHONY: start
start: build up ## Build and start all containers

.PHONY: stop
stop: ## Stop containers without removing
	@$(DOCKER_COMPOSE) stop

.PHONY: restart
restart: down up ## Restart all containers

.PHONY: logs
logs: ## Follow all container logs
	@$(DOCKER_COMPOSE) logs --tail=50 --follow

.PHONY: logs-php
logs-php: ## Follow php container logs
	@$(DOCKER_COMPOSE) logs --tail=50 --follow $(PHP_SERVICE_NAME)

.PHONY: logs-scheduler
logs-scheduler: ## Follow scheduler container logs
	@$(DOCKER_COMPOSE) logs --tail=50 --follow $(SCHEDULER_SERVICE)

## —— Composer ————————————————————————————————————————————————————————————————

.PHONY: composer
composer: ## Run composer command, e.g.: make composer c='require vendor/pkg'
	@$(eval c ?=)
	@$(DOCKER_EXEC_IT) composer $(c)

.PHONY: composer-install
composer-install: ## Install dependencies from composer.lock
	@$(COMPOSER) install --no-interaction --optimize-autoloader

## —— Symfony —————————————————————————————————————————————————————————————————

.PHONY: symfony
symfony: ## Run Symfony console command, e.g.: make symfony c=about
	@$(eval c ?=)
	@$(SYMFONY) $(c)

.PHONY: cache-clear
cache-clear: ## Clear Symfony cache
	@$(SYMFONY) cache:clear

## —— Doctrine ————————————————————————————————————————————————————————————————

.PHONY: migration-create
migration-create: ## Generate a new migration based on entity changes
	@$(SYMFONY) doctrine:migrations:diff

.PHONY: migration-migrate
migration-migrate: ## Run pending migrations
	@$(SYMFONY) doctrine:migrations:migrate --no-interaction

.PHONY: migration-status
migration-status: ## Show migration status
	@$(SYMFONY) doctrine:migrations:status

## —— Tests ———————————————————————————————————————————————————————————————————

.PHONY: test
test: ## Run all unit tests
	@$(PHP) vendor/bin/phpunit --configuration phpunit.dist.xml

.PHONY: test-unit
test-unit: ## Run unit test suite
	@$(PHP) vendor/bin/phpunit --configuration phpunit.dist.xml --testsuite unit

## —— Documentation ———————————————————————————————————————————————————————————

.PHONY: swagger
swagger: ## Generate Swagger YAML documentation
	@$(SYMFONY) nelmio:apidoc:dump --format=yaml > documentation/api.yaml
	@echo "Swagger YAML generated → documentation/api.yaml"

## —— Cleanup —————————————————————————————————————————————————————————————————

.PHONY: clean
clean: ## Remove cache, logs and vendor
	@rm -rf var/cache/* var/log/* vendor/
	@echo "Cleaned cache, logs and vendor"

.PHONY: cache-remove
cache-remove: ## Remove local cache and log directories
	@rm -rf var/cache/* var/log/*
