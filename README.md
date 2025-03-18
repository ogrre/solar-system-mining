# Solar System Mining - Laravel Project

This repository contains a Laravel application for a Solar System Mining project. The development environment is containerized using Docker to ensure consistency across different development machines and streamline the deployment process.

## Table of Contents

- [Prerequisites](#prerequisites)
- [Quick Start](#quick-start)
- [Development Setup](#development-setup)
- [Docker Environment](#docker-environment)
- [Common Commands](#common-commands)
- [Adding Dependencies](#adding-dependencies)
- [Development Tools](#development-tools)
- [Running Tests](#running-tests)
- [Code Quality Tools](#code-quality-tools)
- [CI/CD Pipeline](#cicd-pipeline)
- [Production Deployment](#production-deployment)
- [Environment Variables](#environment-variables)
- [Troubleshooting](#troubleshooting)

## Prerequisites

Before you begin, ensure you have the following tools installed on your local machine:

- Docker and Docker Compose
- Git
- Make (optional, but recommended for using the Makefile commands)

## Quick Start

To bootstrap the entire project with a single command:

```bash
# Clone the repository
git clone https://your-repository-url.git
cd solar-system-mining

# Copy environment file
cp .env.example .env

# Bootstrap the entire project (starts Docker, installs dependencies, sets up database)
make bootstrap
```

This command will:
1. Start all Docker containers
2. Install PHP dependencies
3. Generate application key
4. Run database migrations
5. Install development tools (Laravel Pint, PHPStan, etc.)
6. Install frontend dependencies

## Development Setup

If you prefer a step-by-step approach:

1. Clone the repository:
   ```bash
   git clone https://your-repository-url.git
   cd solar-system-mining
   ```

2. Create environment file:
   ```bash
   cp .env.example .env
   ```

3. Build and start the Docker containers:
   ```bash
   make up
   # or without Make
   docker-compose up -d
   ```

4. Install PHP dependencies:
   ```bash
   make composer@install
   # or without Make
   docker-compose exec app composer install
   ```

5. Generate application key:
   ```bash
   make artisan@key
   # or without Make
   docker-compose exec app php artisan key:generate
   ```

6. Run database migrations:
   ```bash
   make artisan@migrate
   # or without Make
   docker-compose exec app php artisan migrate
   ```

7. Install development tools:
   ```bash
   make dev@tools
   ```

8. Initialize configuration files for dev tools:
   ```bash
   make dev@init
   ```

9. Install frontend dependencies (if needed):
   ```bash
   make npm@install
   # or without Make
   docker-compose exec app npm install
   ```

Your application should now be running at http://localhost (or the port specified in your .env file).

## Docker Environment

The Docker environment includes the following services:

- **app**: PHP 8.3 with Laravel application
- **nginx**: Web server
- **postgres**: PostgreSQL database
- **redis**: Redis server for caching and queues
- **adminer**: Database management tool
- **kafka**: Kafka message broker
- **zookeeper**: Required for Kafka
- **kafka-ui**: UI for Kafka management
- **mailhog**: For email testing (optional, starts with profile)
- **queue**: For queue workers (optional, starts with profile)
- **node**: For dedicated npm tasks (optional, starts with profile)

### Service Access

- **Web application**: http://localhost (or custom port from .env)
- **Database admin**: http://localhost:8080 (or custom port from .env)
- **Kafka UI**: http://localhost:8081 (or custom port from .env)
- **MailHog**: http://localhost:8025 (when started with profile)

## Common Commands

The Makefile provides shortcuts for common tasks:

### Docker Management

```bash
# Start the containers
make up

# Stop the containers
make down

# Restart the containers
make restart

# View logs
make logs
# For a specific service
make logs s=app
```

### Laravel Artisan Commands

```bash
# Run any Artisan command
make artisan@command args="--option1 --option2"

# Run migrations
make artisan@migrate

# Fresh migrations
make artisan@fresh

# Generate controller
make artisan@make t=controller n=UserController

# List routes
make artisan@route

# Clear caches
make artisan@clear

# Enter Tinker
make artisan@tinker
```

### Composer Commands

```bash
# Install dependencies
make composer@install

# Update dependencies
make composer@update

# Add a package
make composer@require p=vendor/package-name

# Add a dev package
make composer@require-dev p=vendor/package-name
```

### NPM Commands

```bash
# Install dependencies
make npm@install

# Run development build
make npm@dev

# Run production build
make npm@build

# Watch for changes
make npm@watch
```

### Database Access

```bash
# Access PostgreSQL shell
make db-shell

# Access Redis shell
make redis-shell
```

## Adding Dependencies

### PHP Dependencies

```bash
# Add a production dependency
make composer@require p=package-name

# Add a development dependency
make composer@require-dev p=package-name
```

### JavaScript Dependencies

```bash
# Add a production dependency
make npm@install
docker-compose exec app npm install package-name --save

# Add a development dependency
make npm@install
docker-compose exec app npm install package-name --save-dev
```

## Development Tools

The project includes several development tools installed locally in the project's `vendor/bin` directory:

- **Laravel Pint**: PHP code style fixer
- **PHPStan**: Static analysis tool
- **Rector**: Automated code refactoring tool
- **PHP_CodeSniffer**: Detects violations of coding standards
- **PHP-CS-Fixer**: Fixes PHP coding standards issues

Install all development tools:

```bash
make dev@tools
```

Initialize configuration files for these tools:

```bash
make dev@init
```

## Running Tests

```bash
# Run all tests
make code@test

# Run specific test
make code@test args="--filter=TestClassName"

# Run tests with coverage
make code@coverage
```

## Code Quality Tools

The project includes several code quality tools that can be run through the Makefile:

```bash
# Check code style with Laravel Pint
make code@pint

# Fix code style with Laravel Pint
make code@pint-fix

# Static analysis with PHPStan
make code@stan

# Code refactoring suggestions with Rector
make code@rector-dry

# Apply Rector suggestions
make code@rector

# Run all code quality checks
make code@check
```

## CI/CD Pipeline

The project includes configuration for a CI/CD pipeline that:

1. Runs all tests and code quality checks
2. Builds Docker images for production
3. Deploys to the appropriate environment based on the branch

See the CI/CD configuration in the `.github/workflows` or `.gitlab-ci.yml` file for details.

## Production Deployment

### Preparing for Production

1. Build production Docker image:
   ```bash
   docker build -t solar-system-mining:production --target production .
   ```

2. Configure production environment variables:
   ```bash
   # Ensure these are set for production
   APP_ENV=production
   APP_DEBUG=false
   APP_USER=1000:1000  # Run as non-root user
   FIX_PERMISSIONS=true
   ```

3. Optimize the application:
   ```bash
   docker-compose exec app php artisan optimize
   docker-compose exec app php artisan route:cache
   docker-compose exec app php artisan view:cache
   docker-compose exec app php artisan config:cache
   ```

### Deployment Options

#### Using Docker Compose

For simple deployments, you can use Docker Compose in production:

```bash
# Start production environment
APP_ENV=production APP_USER=1000:1000 docker-compose up -d
```

#### Using Kubernetes

For more robust deployments, consider using Kubernetes:

1. Ensure your Kubernetes cluster is configured
2. Apply Kubernetes configuration:
   ```bash
   kubectl apply -f kubernetes/
   ```

#### Using Docker Swarm

For mid-sized deployments, Docker Swarm can be a good option:

```bash
# Initialize swarm if not already done
docker swarm init

# Deploy stack
docker stack deploy -c docker-compose.prod.yml solar-system-mining
```

## Environment Variables

Key environment variables to configure:

| Variable | Description | Default |
|----------|-------------|---------|
| `NGINX_PORT` | Port for web access | 80 |
| `DB_CONNECTION` | Database driver | pgsql |
| `DB_HOST` | Database hostname | postgres |
| `DB_PORT` | Database port | 5432 |
| `DB_DATABASE` | Database name | laravel |
| `DB_USERNAME` | Database username | postgres |
| `DB_PASSWORD` | Database password | secret |
| `APP_USER` | User to run containers | root (dev), 1000:1000 (prod) |
| `FIX_PERMISSIONS` | Run permission fixes | false (dev), true (prod) |

See `.env.example` for a complete list.

## Troubleshooting

### Permission Issues

If you encounter permission issues with storage or cache directories:

```bash
# Enable permission fixing
FIX_PERMISSIONS=true make restart
```

### Database Connection Issues

If unable to connect to the database:

1. Check if the database container is running:
   ```bash
   docker-compose ps postgres
   ```

2. Verify connection details in `.env`

3. Try connecting with Adminer at http://localhost:8080

### Container Build Issues

If having issues with container builds:

```bash
# Build the image separately first
docker build -t solar-system-mining/app:dev -f Dockerfile --target development .

# Then start the services
docker-compose up -d
```

### PHP Extension or Library Problems

If you encounter issues with PHP extensions or libraries:

```bash
# Access the container
make bash

# Check PHP extensions
php -m

# Check PHP version and settings
php -i
```

---

## License

[License information here]

## Contributing

[Contribution guidelines here]
