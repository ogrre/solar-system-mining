# Solar System Mining

A modern Laravel application for managing mining operations in the solar system, using a microservices architecture with Docker.

## 🚀 Technology Stack

- **Backend**: Laravel 11 with PHP 8.2+
- **Frontend**: Blade Templates + Livewire 3.6 + Alpine.js + Tailwind CSS
- **Database**: PostgreSQL 15
- **Cache & Sessions**: Redis
- **Message Broker**: Apache Kafka with Zookeeper
- **Assets**: Vite for compilation
- **Authentication**: Laravel Breeze
- **Containerization**: Docker & Docker Compose

## 📋 Prerequisites

- **Docker**: Version 20.10+ ([Installation](https://docs.docker.com/get-docker/))
- **Docker Compose**: Version 2.0+ (included with Docker Desktop)
- **Git**: To clone the repository
- **Make**: Optional, to use Makefile shortcuts

### Multi-Platform Support
✅ **Linux** (AMD64/ARM64)  
✅ **macOS** (Intel/Apple Silicon)  
✅ **Windows** (WSL2 recommended)

## 🛠️ Installation

### 1. Clone the Repository
```bash
git clone <repository-url>
cd solar-system-mining
```

### 2. Environment Configuration

#### Automatic Variable Configuration (Recommended)
```bash
# Copy the example file
cp .env.example .env

# Configure automatically based on your platform
# Linux/WSL2
export UID=$(id -u) GID=$(id -g) DOCKER_PLATFORM="" VOLUME_OPTIONS=""

# macOS
export UID=1000 GID=1000 DOCKER_PLATFORM="" VOLUME_OPTIONS="cached"

# Windows (PowerShell)
$env:UID="1000"; $env:GID="1000"; $env:DOCKER_PLATFORM=""; $env:VOLUME_OPTIONS=""
```

#### Manual .env Configuration
Edit the `.env` file according to your platform:

```bash
# Multi-Platform Support
# Leave empty for auto-detection or force: linux/amd64, linux/arm64
DOCKER_PLATFORM=

# Volume performance (macOS only)
# cached for macOS, empty for Linux/Windows
VOLUME_OPTIONS=

# User IDs (1000:1000 by default, $(id -u):$(id -g) on Linux)
UID=1000
GID=1000

# External ports (modifiable if conflicts)
NGINX_PORT=8082
DB_PORT_EXTERNAL=5433
REDIS_PORT_EXTERNAL=6380
KAFKA_PORT_EXTERNAL=9093
PMA_PORT=8080
KAFKA_UI_PORT=8085
```

### 3. Bootstrap Setup (First Installation)
```bash
# Option 1: With Make (recommended)
make bootstrap

# Option 2: Manual
docker-compose build
docker-compose up -d
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate
docker-compose exec app npm install
docker-compose exec app npm run build
```

### 4. Local Domain Configuration (Optional)
To use `solar-system-mining.localhost` instead of `localhost:8082`:

```bash
# Add to hosts file
# Linux/macOS
echo "127.0.0.1 solar-system-mining.localhost" | sudo tee -a /etc/hosts

# Windows (as Administrator)
echo "127.0.0.1 solar-system-mining.localhost" >> C:\Windows\System32\drivers\etc\hosts
```

## 🌐 Access URLs

### Main Application
| Service | URL | Description |
|---------|-----|-------------|
| **Website** | `http://solar-system-mining.localhost:8082`<br/>or `http://localhost:8082` | Main application |
| **Login** | `/login` | User authentication |
| **Dashboard** | `/dashboard` | Authenticated user interface |
| **Health Check** | `/up` | Application status check |

### Development Services
| Service | URL | Credentials | Description |
|---------|-----|-------------|-------------|
| **Adminer** | `http://localhost:8080` | See Database section | PostgreSQL management interface |
| **Kafka UI** | `http://localhost:8085` | - | Kafka management interface |
| **MailHog** | `http://localhost:8025` | - | Email capture (optional) |

### Direct Connections
| Service | Host:Port | Credentials |
|---------|-----------|-------------|
| **PostgreSQL** | `localhost:5433` | `postgres` / `secret` / `laravel` |
| **Redis** | `localhost:6380` | No password |
| **Kafka** | `localhost:9093` | - |

## 🗄️ Database

### Connection via Adminer
1. Access `http://localhost:8080`
2. **System**: PostgreSQL
3. **Server**: `postgres`
4. **Username**: `postgres`
5. **Password**: `secret`
6. **Database**: `laravel`

### Direct Connection
```bash
# From host
psql -h localhost -p 5433 -U postgres -d laravel

# From Docker
docker-compose exec postgres psql -U postgres -d laravel
```

## ⚡ Development Commands

### Makefile (Shortcuts)
```bash
# Container management
make up                    # Start environment
make down                  # Stop environment
make restart               # Restart all services
make bootstrap             # Complete installation (first time)

# Laravel
make artisan@migrate       # Run migrations
make artisan@tinker        # Laravel console
make artisan@make t=controller n=UserController  # Generate files

# Testing & Quality
make code@test             # Run tests
make code@coverage         # Tests with coverage
make code@check            # Quality checks (Pint + PHPStan)
make code@pint             # Check code style
make code@pint-fix         # Fix code style automatically

# Dependencies
make composer@install      # Install PHP dependencies
make composer@require p=vendor/package  # Add PHP package
make npm@install           # Install JS dependencies
make npm@dev              # Compile assets (development)
make npm@build            # Compile assets (production)

# Utilities
make bash                  # Access app container shell
make logs                  # View all logs
make fresh                 # Complete reset (DB + cache)
```

### Docker Compose Direct
```bash
# Services
docker-compose up -d              # Start in background
docker-compose down               # Stop and remove
docker-compose restart [service] # Restart specific service
docker-compose logs -f [service] # Follow logs

# Laravel Artisan
docker-compose exec app php artisan migrate
docker-compose exec app php artisan tinker
docker-compose exec app php artisan cache:clear

# Composer
docker-compose exec app composer install
docker-compose exec app composer require vendor/package

# NPM
docker-compose exec app npm install
docker-compose exec app npm run dev
docker-compose exec app npm run build

# Optional Services
docker-compose --profile mailhog up -d    # Enable MailHog
docker-compose --profile queue up -d      # Enable Queue Worker
```

## 🔧 Development

### Project Structure
```
solar-system-mining/
├── app/                    # Laravel code
├── resources/              # Views, CSS, JS
├── database/              # Migrations, Seeders
├── docker/                # Docker configuration
├── public/                # Public assets
├── routes/                # Web/API routes
├── tests/                 # Automated tests
├── docker-compose.yml     # Services configuration
├── Dockerfile            # Application image
├── Makefile              # Command shortcuts
└── .env                  # Environment configuration
```

### Development Workflow
1. **Start environment**: `make up`
2. **Modify code** in your preferred editor
3. **Run tests**: `make code@test`
4. **Check quality**: `make code@check`
5. **Compile assets**: `make npm@dev` (or `npm@build` for production)

### Hot Reload & Watch
```bash
# Assets in watch mode
make npm@dev

# Tests in watch mode
make code@test args="--filter=MyTest"
```

## 🧪 Testing

```bash
# All tests
make code@test

# Specific tests
make code@test args="--filter=UserTest"
make code@test args="tests/Feature/Auth"

# With coverage
make code@coverage
```

## 📊 Message Broker (Kafka)

### Kafka UI Interface
Access `http://localhost:8085` to:
- Visualize topics
- Monitor messages
- Manage consumers
- Analyze performance

### Kafka Configuration
```bash
# Bootstrap servers for your applications
KAFKA_BROKERS=localhost:9093

# Default topics (auto-created)
# - user.created
# - mining.operations
# - system.alerts
```

## 🚀 Deployment

### Production Environment
1. **Configure environment variables**
2. **Modify `docker-compose.prod.yml`** (if available)
3. **Use HTTPS** with a reverse proxy (Nginx, Traefik)
4. **Secure access** to administration services

### Production Variables
```bash
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Use strong passwords
DB_PASSWORD=your-secure-password
REDIS_PASSWORD=your-redis-password

# Secure Kafka configuration
KAFKA_SECURITY_PROTOCOL=SASL_SSL
```

## 🔒 Security

⚠️ **Important**: This configuration is intended for local development.

**For production**:
- Change all default passwords
- Use HTTPS
- Restrict access to administration services
- Configure isolated Docker networks
- Enable Kafka authentication

## 🐛 Troubleshooting

### Common Issues

#### Port already in use
```bash
# Check occupied ports
netstat -tulpn | grep :8082

# Modify ports in .env
NGINX_PORT=8083
```

#### Permissions (Linux/macOS)
```bash
# Fix permissions
sudo chown -R $USER:$USER .
```

#### Laravel encryption key
```bash
# Regenerate APP_KEY
docker-compose exec app php artisan key:generate
```

#### Corrupted cache
```bash
# Clean all caches
make fresh
# or
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan view:clear
```

### Debug Logs
```bash
# All services logs
docker-compose logs

# Specific service logs
docker-compose logs app
docker-compose logs nginx
docker-compose logs postgres

# Laravel logs
docker-compose exec app tail -f storage/logs/laravel.log
```

## 🤝 Contributing

1. **Fork** the project
2. **Create** a branch for your feature
3. **Develop** following project standards
4. **Test** with `make code@test` and `make code@check`
5. **Submit** a Pull Request

### Code Standards
- **PSR-12** for PHP
- **ESLint** for JavaScript
- **Unit tests** required for new features
- **API documentation** required

## 📞 Support

- **Issues**: Use GitHub issue system
- **Documentation**: See `/docs` folder (if available)
- **Wiki**: Check project wiki

---

**Built with ❤️ for space exploration**

*Last updated: July 2, 2025*