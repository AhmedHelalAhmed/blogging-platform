## Blogging Platform
## [Server requirements](https://laravel.com/docs/9.x/deployment#server-requirements)
## [Scheduler](https://laravel.com/docs/9.x/scheduling#running-the-scheduler) required for cron jobs

## Steps to set up the project:

### If you have  docker installed
- ``` docker-compose up ```
- Inside web container execute the following commands:
- ```cp .env.example .env``` and update with your data
- ```composer install```
- ```php artiasn key:generate```
- ```php artiasn migrate```
- ```php artiasn db:seed```
- ```npm install```
- ```npm run build```
### The same for local development but you need to run
- ``` php artisan schedule:work ``` For cron Job to work

### Technologies and tools:
- PHP
- Laravel9
- Vue.js3
- Inertia.js
- TailwindCSS
