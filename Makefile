.PHONY: help install composer@install composer@update code@pint code@pint-fix code@rector code@rector-dry code@stan code@test code@coverage code@check

help: ## Affiche cette aide
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

install: ## Installe toutes les dépendances
	@make composer@install

composer@install: ## Lance composer install
	docker compose exec app composer install

composer@update: ## Lance composer update
	docker compose exec app composer update

composer@require: ## Ajoute une dépendance (usage: make composer@require p=nom-package)
	docker compose exec app composer require $(p)

composer@require-dev: ## Ajoute une dépendance de dev (usage: make composer@require-dev p=nom-package)
	docker compose exec app composer require --dev $(p)

composer@dump: ## Lance composer dump-autoload
	docker compose exec app composer dump-autoload

code@pint: ## Vérifie le style du code avec Pint
	docker compose run --rm pint --test

code@pint-fix: ## Corrige le style du code avec Pint
	docker compose run --rm pint

code@rector: ## Applique les refactorisations avec Rector
	docker compose run --rm rector process

code@rector: ## Montre les refactorisations possibles sans les appliquer
	docker compose run --rm rector --dry-run

code@stan: ## Lance l'analyse statique avec PHPStan
	docker compose run --rm phpstan analyse

code@test: ## Lance les tests
	docker compose exec app php artisan test

code@coverage: ## Lance les tests avec coverage
	docker compose exec app php artisan test --coverage

code@check: ## Lance toutes les vérifications (pint, stan, rector, tests)
	@echo "🔍 Lancement des vérifications..."
	@make code@pint
	@make code@stan
	@make code@rector
	@make code@test
	@echo "✅ Toutes les vérifications sont passées !"


