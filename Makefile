.PHONY: help install bootstrap up down restart logs bash db-shell redis-shell \
	composer@install composer@update composer@require composer@require-dev composer@dump \
	npm@install npm@dev npm@build npm@watch \
	artisan@* code@pint code@pint-fix code@rector code@rector-dry code@stan code@test code@coverage code@check \
	dev@tools dev@init

DOCKER_COMPOSE = docker-compose
DOCKER_EXEC = $(DOCKER_COMPOSE) exec
DOCKER_EXEC_APP = $(DOCKER_EXEC) app

help: ## Afficher cette aide
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

# Docker commands
up: ## Démarrer les conteneurs
	$(DOCKER_COMPOSE) up -d

down: ## Arrêter les conteneurs
	$(DOCKER_COMPOSE) down

restart: ## Redémarrer les conteneurs
	$(DOCKER_COMPOSE) restart

logs: ## Afficher les logs (usage: make logs [s=service])
	@if [ -z "$(s)" ]; then \
		$(DOCKER_COMPOSE) logs -f; \
	else \
		$(DOCKER_COMPOSE) logs -f $(s); \
	fi

bash: ## Ouvrir un shell dans le conteneur app
	$(DOCKER_EXEC_APP) bash

db-shell: ## Ouvrir un shell PostgreSQL
	$(DOCKER_EXEC) postgres psql -U $${DB_USERNAME} $${DB_DATABASE}

redis-shell: ## Ouvrir un shell Redis
	$(DOCKER_EXEC) redis redis-cli -a $${REDIS_PASSWORD}

# Bootstrap - installation complète
bootstrap: ## Bootstrapper le projet complet (installation, migrations, clé, etc.)
	@echo "🚀 Bootstrapping du projet Solar System Mining..."
	@make up
	@echo "📦 Installation des dépendances PHP..."
	@make composer@install
	@echo "🔑 Génération de la clé d'application..."
	@make artisan@key
	@echo "🗄️ Configuration de la base de données..."
	@make artisan@migrate
	@echo "📄 Installation des outils de développement..."
	@make dev@tools
	@echo "🎨 Installation des dépendances frontend..."
	@make npm@install
	@echo "✅ Projet initialisé avec succès!"

# Installation
install: ## Installer toutes les dépendances
	@make composer@install
	@make npm@install
	@make artisan@key

# Composer commands
composer@install: ## Exécuter composer install
	$(DOCKER_EXEC_APP) composer install

composer@update: ## Exécuter composer update
	$(DOCKER_EXEC_APP) composer update

composer@require: ## Ajouter une dépendance (usage: make composer@require p=package-name)
	$(DOCKER_EXEC_APP) composer require $(p)

composer@require-dev: ## Ajouter une dépendance de développement (usage: make composer@require-dev p=package-name)
	$(DOCKER_EXEC_APP) composer require --dev $(p)

composer@dump: ## Exécuter composer dump-autoload
	$(DOCKER_EXEC_APP) composer dump-autoload

# NPM commands
npm@install: ## Installer les dépendances npm
	$(DOCKER_EXEC_APP) npm install

npm@dev: ## Compiler les assets pour le développement
	$(DOCKER_EXEC_APP) npm run dev

npm@build: ## Compiler les assets pour la production
	$(DOCKER_EXEC_APP) npm run build

npm@watch: ## Compiler les assets et surveiller les changements
	$(DOCKER_EXEC_APP) npm run watch

# Artisan commands
artisan@%: ## Exécuter n'importe quelle commande artisan (usage: make artisan@migrate, make artisan@route:list, etc.)
	$(DOCKER_EXEC_APP) php artisan $(subst artisan@,,$@) $(args)

artisan@tinker: ## Ouvrir une session Tinker
	$(DOCKER_EXEC_APP) -it php artisan tinker

artisan@migrate: ## Exécuter les migrations
	$(DOCKER_EXEC_APP) php artisan migrate $(args)

artisan@fresh: ## Rafraîchir la base de données
	$(DOCKER_EXEC_APP) php artisan migrate:fresh $(args)

artisan@seed: ## Alimenter la base de données
	$(DOCKER_EXEC_APP) php artisan db:seed $(args)

artisan@key: ## Générer la clé d'application
	$(DOCKER_EXEC_APP) php artisan key:generate

artisan@make: ## Créer un fichier Laravel (usage: make artisan@make t=controller n=UserController)
	$(DOCKER_EXEC_APP) php artisan make:$(t) $(n)

artisan@route: ## Lister les routes de l'application
	$(DOCKER_EXEC_APP) php artisan route:list

artisan@clear: ## Vider les caches
	$(DOCKER_EXEC_APP) php artisan optimize:clear

artisan@model: ## Afficher le schéma d'un modèle (usage: make artisan@model m=User)
	$(DOCKER_EXEC_APP) php artisan model:show $(m)

# Development tools
dev@tools: ## Installer les outils de développement dans le projet
	$(DOCKER_EXEC_APP) composer require --dev \
		laravel/pint \
		phpstan/phpstan \
		rector/rector \
		squizlabs/php_codesniffer \
		friendsofphp/php-cs-fixer

dev@init: ## Initialiser les fichiers de configuration pour les outils de développement
	@echo "Initialisation des fichiers de configuration..."
	@if [ ! -f "phpstan.neon" ]; then \
		echo "parameters:\n  level: 5\n  paths:\n    - app\n    - tests" > phpstan.neon; \
		echo "✓ phpstan.neon créé"; \
	fi
	@if [ ! -f "rector.php" ]; then \
		$(DOCKER_EXEC_APP) ./vendor/bin/rector init; \
		echo "✓ rector.php créé"; \
	fi
	@echo "✅ Configuration des outils terminée!"

# Code quality
code@pint: ## Vérifier le style de code avec Pint
	$(DOCKER_EXEC_APP) ./vendor/bin/pint --test

code@pint-fix: ## Corriger le style de code avec Pint
	$(DOCKER_EXEC_APP) ./vendor/bin/pint

code@rector: ## Appliquer des refactorisations avec Rector
	$(DOCKER_EXEC_APP) ./vendor/bin/rector process

code@rector-dry: ## Afficher les refactorisations possibles sans les appliquer
	$(DOCKER_EXEC_APP) ./vendor/bin/rector --dry-run

code@stan: ## Exécuter l'analyse statique avec PHPStan
	$(DOCKER_EXEC_APP) php -d memory_limit=1G ./vendor/bin/phpstan analyse --configuration=phpstan.neon

code@test: ## Exécuter les tests
	$(DOCKER_EXEC_APP) php artisan test $(args)

code@coverage: ## Exécuter les tests avec couverture
	$(DOCKER_EXEC_APP) XDEBUG_MODE=coverage php artisan test --coverage

code@check: ## Exécuter toutes les vérifications (pint, stan, rector, tests)
	@echo "🔍 Exécution des vérifications..."
	@make code@pint
	@make code@stan
	@make code@rector-dry
	@make code@test
	@echo "✅ Toutes les vérifications sont passées!"

# Default target
.DEFAULT_GOAL := help
