.PHONY: help install composer@install composer@update code@pint code@pint-fix code@rector code@rector-dry code@stan code@test code@coverage code@check

help: ## Display this help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

install: ## Install all dependencies
	@make composer@install

composer@install: ## Run composer install
	docker-compose exec app composer install

composer@update: ## Run composer update
	docker-compose exec app composer update

composer@require: ## Add a dependency (usage: make composer@require p=package-name)
	docker-compose exec app composer require $(p)

composer@require-dev: ## Add a dev dependency (usage: make composer@require-dev p=package-name)
	docker-compose exec app composer require --dev $(p)

composer@dump: ## Run composer dump-autoload
	docker-compose exec app composer dump-autoload

code@pint: ## Check code style with Pint
	docker-compose exec app pint --test

code@pint-fix: ## Fix code style with Pint
	docker-compose exec app pint

code@rector: ## Apply refactorings with Rector
	docker-compose exec app rector process

code@rector-dry: ## Show possible refactorings without applying them
	docker-compose exec app rector --dry-run --config=rector.php

code@stan: ## Run static analysis with PHPStan
	docker-compose exec app php -d memory_limit=1G /root/.composer/vendor/bin/phpstan analyse --configuration=phpstan.neon

code@test: ## Run tests
	docker-compose exec app php artisan test

code@coverage: ## Run tests with coverage
	docker-compose exec app php artisan test --coverage

code@check: ## Run all checks (pint, stan, rector, tests)
	@echo "🔍 Running checks..."
	@make code@pint
	@make code@stan
	@make code@rector-dry
	@make code@test
	@echo "✅ All checks passed!"

