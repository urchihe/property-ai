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

**Commands:**
# Build containers
docker compose up -d --build

# Start containers
docker compose up -d

# Stop containers
docker compose down

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
# Clone the repository
git clone <repo-url>
cd <repo-directory>

# Install PHP dependencies
composer install

# Install frontend dependencies
pnpm install && pnpm run dev

# Set up environment variables
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Start Docker containers
docker compose up -d

## Notes
- All Livewire components use @entangle for Alpine.js two-way binding.
- Input fields are cleared when regenerating descriptions.
- Toast notifications provide copy-to-clipboard feedback.
- The UI is mobile responsive and uses a background image.

## Reflection (max 200 words)
I approached this task by first designing a clear and responsive UI using Tailwind CSS and Alpine.js.
For interactivity, I leveraged Livewire for dynamic state handling and seamless AI description generation.
I used SOLID principles for services and separated concerns between Livewire components and the AI service.
Docker was configured for local development and production parity, with Supervisor managing background workers and Horizon handling queues.
Testing included unit tests for the AI service and Livewire components, while PHPStan and Pint were used to maintain code quality and style.
I also implemented user-friendly features like tone selection, loading indicators, and clipboard notifications to improve UX.

## Optional Enhancements
- Save description history to the database
- SEO/readability scoring for generated descriptions
- Additional AI prompt customization
