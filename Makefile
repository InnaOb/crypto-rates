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

## —— Docker ——————————————————————————————————————————————————————————————————

.PHONY: build
build:
	@$(DOCKER_COMPOSE) build --pull --no-cache

.PHONY: up
up:
	@$(DOCKER_COMPOSE) up --detach

.PHONY: down
down:
	@$(DOCKER_COMPOSE) down --remove-orphans

.PHONY: start
start: build up

.PHONY: stop
stop:
	@$(DOCKER_COMPOSE) stop

.PHONY: restart
restart: down up

.PHONY: logs
logs:
	@$(DOCKER_COMPOSE) logs --tail=50 --follow

.PHONY: logs-php
logs-php:
	@$(DOCKER_COMPOSE) logs --tail=50 --follow $(PHP_SERVICE_NAME)

.PHONY: logs-scheduler
logs-scheduler:
	@$(DOCKER_COMPOSE) logs --tail=50 --follow $(SCHEDULER_SERVICE)

## —— Composer ————————————————————————————————————————————————————————————————

.PHONY: composer
composer:
	@$(eval c ?=)
	@$(DOCKER_EXEC_IT) composer $(c)

.PHONY: composer-install
composer-install:
	@$(COMPOSER) install --no-interaction --optimize-autoloader

## —— Symfony —————————————————————————————————————————————————————————————————

.PHONY: symfony
symfony:
	@$(eval c ?=)
	@$(SYMFONY) $(c)

.PHONY: cache-clear
cache-clear:
	@$(SYMFONY) cache:clear

## —— Doctrine ————————————————————————————————————————————————————————————————

.PHONY: migration-create
migration-create:
	@$(SYMFONY) doctrine:migrations:diff

.PHONY: migration-migrate
migration-migrate:
	@$(SYMFONY) doctrine:migrations:migrate --no-interaction

.PHONY: migration-status
migration-status:
	@$(SYMFONY) doctrine:migrations:status

## —— Tests ———————————————————————————————————————————————————————————————————

.PHONY: test
test:
	@$(PHP) vendor/bin/phpunit --configuration phpunit.dist.xml

## —— Documentation ———————————————————————————————————————————————————————————

.PHONY: swagger
swagger:
	@$(SYMFONY) nelmio:apidoc:dump --format=yaml > documentation/api.yaml
	@echo "Swagger YAML generated → documentation/api.yaml"

## —— Cleanup —————————————————————————————————————————————————————————————————

.PHONY: clean
clean:
	@rm -rf var/cache/* var/log/* vendor/
	@echo "Cleaned cache, logs and vendor"

.PHONY: cache-remove
cache-remove:
	@rm -rf var/cache/* var/log/*
