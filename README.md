## Blogging Platform

## [Server requirements](https://laravel.com/docs/9.x/deployment#server-requirements)

## Steps to set up the project
- ``` docker-compose up ```
- Inside web container execute the following commands:
- ```cp .env.example .env``` and update with your data
- ```composer install```
- ```php artiasn key:generate```
- ```npm install```
- ```npm run build```
- ```bash /opt/bin/entrypoint.sh ``` and keep it running or use [Scheduler](https://laravel.com/docs/9.x/scheduling#running-the-scheduler)
