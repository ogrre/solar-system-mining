# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Architecture

This is a Laravel 11 application using PHP 8.2+ with a modern Docker-based development environment. The project is called "Solar System Mining" and uses:

- **Backend**: Laravel 11 with Livewire 3.6 for reactive components
- **Database**: PostgreSQL with Redis for caching/queues
- **Frontend**: Tailwind CSS, Alpine.js, Vite for asset compilation
- **Message Broker**: Kafka with Zookeeper
- **Authentication**: Laravel Breeze
- **Monitoring**: Sentry for error tracking
- **Feature Flags**: ylsideas/feature-flags package

## Development Environment

The project runs entirely in Docker containers. All commands should be executed through Docker Compose or the Makefile shortcuts.

### Essential Commands

**Start/Stop Development Environment:**
```bash
make up                    # Start all containers
make down                  # Stop all containers
make bootstrap             # Full project setup (first time)
```

**Laravel Development:**
```bash
make artisan@migrate       # Run migrations
make artisan@tinker        # Open Laravel Tinker
make artisan@route         # List all routes
make artisan@make t=controller n=UserController  # Generate files
```

**Testing:**
```bash
make code@test             # Run all tests
make code@test args="--filter=TestName"  # Run specific test
make code@coverage         # Run tests with coverage
```

**Code Quality:**
```bash
make code@check            # Run all quality checks
make code@pint             # Check code style
make code@pint-fix         # Fix code style issues
make code@stan             # Run PHPStan static analysis
```

**Dependencies:**
```bash
make composer@install     # Install PHP dependencies
make composer@require p=vendor/package  # Add PHP package
make npm@install          # Install JS dependencies
make npm@dev              # Build assets for development
```

## Key Architecture Patterns

**Authentication**: Uses Laravel Breeze with standard auth controllers in `app/Http/Controllers/Auth/`

**Models**: Standard Eloquent models in `app/Models/`

**Views**: Blade templates in `resources/views/` with Livewire components

**Configuration**: Standard Laravel config in `config/` directory

**Database**: Migrations in `database/migrations/`, seeders in `database/seeders/`

## Code Quality Tools

The project has strict code quality standards enforced by:
- **Laravel Pint**: Code style (PSR-12 based)
- **PHPStan**: Static analysis (level 8)
- **Rector**: Automated refactoring suggestions
- **PHPUnit**: Testing framework

Always run `make code@check` before committing changes.

## Docker Services

- **app**: Main PHP application container
- **nginx**: Web server (port 80)
- **postgres**: Database (port 5433 externally)
- **redis**: Cache/sessions/queues (port 6380 externally)
- **kafka**: Message broker (port 9093 externally)
- **adminer**: Database admin UI (port 8080)
- **kafka-ui**: Kafka management UI (port 8081)

## Environment Configuration

Copy `.env.example` to `.env` for local development. Key variables:
- Database connection uses PostgreSQL by default
- Redis for caching and queues
- Kafka broker configuration
- Sentry DSN for error tracking

**Multi-Platform Support:**
- `DOCKER_PLATFORM`: Auto-detects architecture (leave empty) or force with `linux/amd64`/`linux/arm64`
- `VOLUME_OPTIONS`: Set to `cached` on macOS for performance, leave empty on Windows/Linux
- `UID`/`GID`: Use 1000:1000 on macOS/Windows, or `$(id -u):$(id -g)` on Linux

## Testing Strategy

- **Unit Tests**: In `tests/Unit/` for isolated component testing
- **Feature Tests**: In `tests/Feature/` for full application flow testing
- Tests use the testing database configuration from `phpunit.xml`
- Run specific test suites: `make code@test args="tests/Feature/Auth"`

## File Access

Use `make bash` to access the application container shell for debugging or manual commands.