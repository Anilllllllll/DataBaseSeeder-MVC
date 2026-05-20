DataBaseSeeder-MVC

A simple MVC-based database seeder application built with Laravel that automates inserting and managing sample data for development and testing environments using a clean Model-View-Controller architecture.

🚀 Features
MVC architecture using Laravel
Database seeding for sample/test data
Blade-based frontend templates
Clean project structure
Environment configuration support
Vite integration for frontend assets
Easy setup for development environments
🛠️ Tech Stack
Backend
PHP
Laravel
Frontend
Blade Templates
Vite
CSS
Database
MySQL
📂 Project Structure
app/
bootstrap/
config/
database/
public/
resources/
routes/
storage/
tests/
⚙️ Installation
Clone the repository
git clone https://github.com/your-username/DataBaseSeeder-MVC.git
Navigate to project folder
cd DataBaseSeeder-MVC
Install dependencies
composer install
npm install
Configure environment
cp .env.example .env

Update database credentials inside .env

DB_DATABASE=your_database
DB_USERNAME=root
DB_PASSWORD=your_password
Generate application key
php artisan key:generate
Run migrations and seeders
php artisan migrate --seed
Start development server
php artisan serve
Run Vite
npm run dev
📌 Usage

The application helps developers quickly populate databases with sample data for testing and development purposes while maintaining a clean MVC structure.
