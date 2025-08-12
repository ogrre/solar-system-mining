# Docker URLs - Solar System Mining

Ce document liste toutes les URLs et ports d'accès aux services Docker de l'application Solar System Mining.

## 🚀 Application Laravel

| Service | URL | Description | Authentification |
|---------|-----|-------------|------------------|
| **Application principale** | `http://solar-system-mining.localhost:8082` | Site web principal avec interface utilisateur | - |
| **Page de connexion** | `http://solar-system-mining.localhost:8082/login` | Formulaire de connexion utilisateur | - |
| **Tableau de bord** | `http://solar-system-mining.localhost:8082/dashboard` | Interface utilisateur connecté | Requis |
| **Inscription** | `http://solar-system-mining.localhost:8082/register` | Création de compte utilisateur | - |
| **Health Check** | `http://solar-system-mining.localhost:8082/up` | Vérification de l'état de l'application | - |

## 🗄️ Gestion des Données

### Base de Données PostgreSQL
| Service | URL/Port | Description | Connexion |
|---------|----------|-------------|-----------|
| **Adminer (Interface Web)** | `http://localhost:8080` | Gestionnaire de base de données graphique | Voir ci-dessous |
| **PostgreSQL Direct** | `localhost:5433` | Accès direct à la base de données | Voir ci-dessous |

**Paramètres de connexion PostgreSQL :**
- **Serveur** : `postgres` (depuis Docker) ou `localhost:5433` (depuis l'hôte)
- **Base de données** : `laravel`
- **Utilisateur** : `postgres`
- **Mot de passe** : `secret`
- **Port** : `5432` (interne) / `5433` (externe)

### Cache Redis
| Service | Port | Description | Connexion |
|---------|------|-------------|-----------|
| **Redis** | `localhost:6380` | Cache et sessions | Mot de passe : `null` |

## 📊 Message Broker (Kafka)

| Service | URL/Port | Description | Utilisation |
|---------|----------|-------------|-------------|
| **Kafka UI** | `http://localhost:8085` | Interface graphique pour Kafka | Gestion des topics, messages, consommateurs |
| **Kafka Broker** | `localhost:9093` | Broker de messages principal | Connexion directe pour applications |
| **Zookeeper** | `localhost:2181` | Coordination Kafka | Service interne Kafka |

**Configuration Kafka :**
- **Bootstrap Servers** : `localhost:9093`
- **Cluster Name** : `local`
- **Replication Factor** : `1`

## 📧 Services Optionnels

Ces services nécessitent d'être activés avec des profils Docker Compose :

| Service | URL | Activation | Description |
|---------|-----|------------|-------------|
| **MailHog** | `http://localhost:8025` | `docker-compose --profile mailhog up -d` | Capture et affichage des emails de test |
| **Queue Worker** | Service en arrière-plan | `docker-compose --profile queue up -d` | Traitement des tâches en file d'attente |

## 🔧 Commandes de Gestion

### Vérification des Services
```bash
# Voir l'état de tous les conteneurs
docker-compose ps

# Vérifier les logs d'un service
docker-compose logs [service-name]
docker-compose logs nginx
docker-compose logs kafka-ui
docker-compose logs adminer
```

### Gestion des Services
```bash
# Démarrer tous les services
docker-compose up -d

# Arrêter tous les services
docker-compose down

# Redémarrer un service spécifique
docker-compose restart [service-name]

# Activer les services optionnels
docker-compose --profile mailhog --profile queue up -d
```

### Accès aux Conteneurs
```bash
# Accès au conteneur Laravel
docker-compose exec app bash

# Commandes Artisan
docker-compose exec app php artisan [command]

# Accès à la base de données
docker-compose exec postgres psql -U postgres -d laravel
```

## 📋 Résumé des Ports

| Port | Service | Type | Accès |
|------|---------|------|-------|
| `8082` | Nginx (Laravel) | HTTP | Public |
| `8080` | Adminer | HTTP | Public |
| `8085` | Kafka UI | HTTP | Public |
| `8025` | MailHog | HTTP | Optionnel |
| `5433` | PostgreSQL | Database | Public |
| `6380` | Redis | Cache | Public |
| `9093` | Kafka | Message Broker | Public |
| `2181` | Zookeeper | Coordination | Public |

## 🔒 Sécurité

⚠️ **Important** : Ces URLs et ports sont configurés pour un environnement de développement local. En production :
- Modifier les mots de passe par défaut
- Restreindre l'accès aux services d'administration
- Utiliser HTTPS pour l'application principale
- Configurer des réseaux Docker isolés

## 🏗️ Architecture

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Nginx :8082   │────│  Laravel App    │────│ PostgreSQL:5433 │
└─────────────────┘    └─────────────────┘    └─────────────────┘
                                │
                       ┌─────────────────┐
                       │   Redis :6380   │
                       └─────────────────┘
                                │
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│ Kafka UI :8085  │────│  Kafka :9093    │────│ Zookeeper :2181 │
└─────────────────┘    └─────────────────┘    └─────────────────┘
                                │
                       ┌─────────────────┐
                       │ Adminer :8080   │
                       └─────────────────┘
```

---

**Dernière mise à jour** : 12 août 2025  
**Version** : Solar System Mining v1.0