# A7gzle API (Real Estate Rental App)

A Laravel-based RESTful API developed as a training project for a real estate rental platform. It allows property owners to list their properties (villas, apartments, etc.) and tenants to browse and book them. 

## 🚀 Tech Stack
* **Framework:** Laravel (PHP)
* **Database:** MySQL

## ✨ Features
* User Authentication (Landlords & Tenants)
* Property listing and management
* Property search and booking system
* RESTful API architecture

*Note: The application fully supports the Arabic language.*

## 📱 Mobile App (Frontend)
The Flutter frontend repository for this project can be found here:
[A7gzle Mobile App](https://github.com/iamutaz/a7gzle)

## 🛠️ Quick Start
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
