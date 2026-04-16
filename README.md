# Docker Project — Apex Arena

## Presentation

Apex Arena is a game achievement tracking platform. Users can browse games, add them to their profile, and track which achievements they have unlocked. Administrators can manage games, achievements, and users through a dedicated admin panel.

The application has been containerized and split into three independent services communicating over isolated Docker networks.


## Prerequisites & Installation

### Requirements

- [Docker](https://www.docker.com/)
- [Docker Compose](https://docs.docker.com/compose/)

### Environment Setup

You will need to create a .env file that will be used by docker compose.

`.env` variables:

| Variable | Description | Example |
|----------|-------------|---------|
| `DB_ROOT_PASS` | MySQL root password | `rootpassword` |
| `DB_NAME` | Database name | `php_db` |
| `DB_USER` | Database user | `appuser` |
| `DB_PASS` | Database password | `apppassword` |
| `DB_PORT` | Host port for MySQL | `3306` |
| `WEB_PORT` | Host port for the frontend | `8080` |

### Running the Application

```bash
# Clone the repository
git clone https://github.com/Sky-Dash/projet-final-docker.git

# Build and start all containers
docker-compose up --build

# Or in detached mode
docker-compose up --build -d
```

The application will be available at **http://localhost:8080** (or whichever port you set for `WEB_PORT`).

### Stopping

```bash
docker-compose down

# To also remove volumes (wipes the database, except for default data)
docker-compose down -v
```

---

## Architecture

The project is split into three containers:



| Service | Role | Network |
|---------|------|---------|
| `front` | Serves HTML pages, calls the API via internal curl | `frontend` |
| `api` | Handles all business logic, talks to the DB, returns JSON | `backend` + `frontend` |
| `db` | MySQL database | `backend` |

- The api container is not exposed to the host — only front can reach it via the internal frontend network.
- Uploaded game images are stored in ./front/public/uploads as a bind mount, shared between api (writes) and front (serves).
- The database is persisted via a named Docker volume (db_data).

### Folder Structure

```
.
├── docker-compose.yml
├── .env
├── api/          # Backend container (JSON API)      
├── front/        # Frontend container (php Frontend)        
└── db/           # Contains initial db    
```

For details on each service, see the README inside each subfolder.