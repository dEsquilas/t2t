# T2T - Text to Text Transformation Tool

A Laravel application with Vue.js, Inertia.js, and Laravel Sail for Docker development.

## Prerequisites

- Docker Desktop
- pnpm (Package manager)
- Git

## Tech Stack

- **Backend**: Laravel 12
- **Frontend**: Vue.js 3 with Inertia.js
- **Styling**: Tailwind CSS
- **Authentication**: Laravel Breeze
- **Development Environment**: Laravel Sail (Docker)
- **Database**: MySQL 8.0
- **Package Manager**: pnpm

## Installation

1. Clone the repository:
```bash
git clone [repository-url]
cd t2t
```

2. Copy environment file:
```bash
cp .env.example .env
```

3. Start Docker containers with Sail:
```bash
sail up -d
```

4. Install PHP dependencies:
```bash
sail composer install
```

5. Generate application key:
```bash
sail artisan key:generate
```

6. Run database migrations:
```bash
sail artisan migrate
```

7. Install frontend dependencies:
```bash
sail pnpm install
```

8. Build frontend assets:
```bash
sail pnpm run build
```

## Development

### Starting the development environment:
```bash
# Start Docker containers
sail up -d

# Start Vite dev server
sail pnpm run dev
```

### Common Sail commands:
```bash
# Run Artisan commands
sail artisan [command]

# Run Composer commands
sail composer [command]

# Run pnpm commands
sail pnpm [command]

# Access MySQL CLI
sail mysql

# Run tests
sail test

# Stop all containers
sail down
```

### Building for production:
```bash
sail pnpm run build
```

## Project Structure

- `/app` - Laravel application code
- `/resources/js` - Vue.js components and pages
- `/resources/css` - Stylesheets
- `/routes` - Application routes
- `/database` - Migrations and seeders
- `/docker` - Docker configuration files

## Features

- User authentication (registration, login, password reset)
- User profile management
- Responsive design with Tailwind CSS
- SPA experience with Inertia.js
- Docker-based development environment

## Testing

Run the test suite:
```bash
sail test
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).