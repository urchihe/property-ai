# AI PROPERTY DESCRIPTION ENHANCER

## Project Overview
This project is a Laravel 11 + Livewire 3 application that generates SEO-optimized property descriptions using AI.
Users can input property details and generate or regenerate descriptions with optional tone selection.

## Features
- Input property details: Title, Property Type, Location, Price, Key Features
- Generate AI-powered property descriptions
- Regenerate descriptions
- Copy descriptions to clipboard
- Tone selection: Formal or Casual
- Loading indicators and validation feedback
- Mobile responsive UI with Tailwind CSS

## Tech Stack
**Backend:** Laravel 11, Livewire 3, PHP 8.3  
**Frontend:** Tailwind CSS, Alpine.js  
**Queue & Background Jobs:** Laravel Horizon, Supervisor  
**Database:** MySQL (Dockerized)  
**Cache:** Redis  
**Docker & Docker Compose:** Multi-service environment

## Docker Setup
**Environment Variables:**
PROJECT=property-ai
COMPOSE_PROJECT_NAME=${PROJECT}
COMPOSE_PATH_SEPARATOR=;
COMPOSE_FILE=docker/app.yml;docker/database.yml;docker/database_administration.yml;docker/redis.yml
OPENAI_API_KEY=your-key

## Production Setup
- Supervisor manages queue workers and Laravel Horizon.
- Horizon handles async job processing for AI generation and other background tasks.
- Entry Point Script ensures PHP-FPM and queue workers start correctly.

## Quality Tools
- PHPStan: Static analysis to enforce type safety.
- Laravel Pint: Code formatting and style checks.
- Run analysis:
  composer pint
  composer lint

## Testing
- Unit Tests cover services and Livewire components.
- Run tests with:
  php artisan test

## AI Integration
- Uses OpenAI or a seeded/mock response for testing.
- Prompt engineering produces SEO-friendly, natural-sounding descriptions.

## Project Structure
app/
Http/
Livewire/
PropertyDescription.php
Services/
PropertyDescriptionService.php
docker/
app.yml
database.yml
database_administration.yml
redis.yml
resources/
views/
livewire/
property-description.blade.php
public/
images/
bg-pattern.jpg
tests/
Feature/
Unit/

## Setup Instructions

### Option 1 — Using Docker (recommended)


### Clone the repository
```bash
git clone git@github.com:urchihe/property-ai.git
```
```bash
cd property-ai
````

### Set up environment variables
```bash
cp .env.example .env
```
update the .env with your open api key

### Build and start all containers
```bash
docker compose up -d --build
````

### Run database migrations
```bash
docker compose exec app php artisan migrate
````

### Generate application key (if not already set)
```bash
docker compose exec app php artisan key:generate
```
Once the containers are up:

App runs on http://localhost:8080

Vite dev server (frontend) runs on http://localhost:5173

### Option 2 — Without Docker

### Clone the repository
```bash
git clone git@github.com:urchihe/property-ai.git
```
```bash
cd property-ai
```

### Install PHP dependencies
```bash
composer install
```

### Install frontend dependencies
```bash
pnpm install && pnpm run dev
```
### Set up environment variables
```bash
cp .env.example .env
```
### Generate application key
```bash
php artisan key:generate
```

# Run database migrations
```bash
php artisan migrate
```

## Notes
- All Livewire components use @entangle for Alpine.js two-way binding.
- Input fields are cleared when regenerating descriptions.
- Toast notifications provide copy-to-clipboard feedback.
- The UI is mobile responsive and uses a background image.

## Reflection (max 200 words)
I approached this project by first designing a clean and responsive UI using Tailwind CSS and Alpine.js.
Livewire managed dynamic state and enabled seamless AI-powered property description generation.
I followed SOLID principles to keep logic modular, separating business rules into dedicated service classes for maintainability and testability.

During testing, I encountered OpenAI API rate limits, which helped me plan for future scalability and reliability improvements.
To address this, I introduced containerized configurations using app.yml, database.yml, and redis.yml, enabling easier scaling across environments.
Docker ensures consistency between local and production setups, while Supervisor and Horizon efficiently manage background workers and queues.

Continuous Integration and Deployment were implemented through GitHub Actions, automatically running PHPStan, Pint, and PHPUnit tests on every push or pull request to maintain high code quality and enforce standards.

I also prioritized user experience with tone selection, loading indicators, and real-time clipboard notifications for a smooth workflow.
This combination of scalable architecture, automated quality checks, and thoughtful UI design results in a robust, maintainable, and user-friendly application.

## Optional Enhancements
- Save description history to the database
- SEO/readability scoring for generated descriptions
- Additional AI prompt customization
