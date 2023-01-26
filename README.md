## Blogging Platform
## [Server requirements](https://laravel.com/docs/9.x/deployment#server-requirements)
## [Scheduler](https://laravel.com/docs/9.x/scheduling#running-the-scheduler) required for cron jobs

## Steps to set up the project:

### If you have  docker installed
- ```cp .env.example .env``` and update with your data like database and redis
- ``` docker-compose up ```
- Inside the web container execute the following commands:
#### To go inside the container run ``` docker exec -it blogging-platform_web bash ``` (this line only for docker) 
### Change the value of UID in .env User id number in ubuntu in the terminal just write id and get uid value in case of using docker
- ```composer install```
- ```php artiasn key:generate```
- ```php artiasn migrate```
- ```php artiasn db:seed```
- ```npm install```
- ```npm run build```
- ``` php artisan inertia:start-ssr ``` For Server-side Rendering and keep it running
### The same for local development but you need to run
- ``` php artisan schedule:work ``` For cron Job to work and keep it running
- ``` php artisan serve ``` For php webserver to start
### To run tests
- ```php artisan test ```
### To run code Fixer
- ``` ./vendor/bin/pint ```
### Technologies and tools
- PHP
- [Laravel9](https://laravel.com/docs/9.x)
- [Laravel Breeze](https://laravel.com/docs/9.x/starter-kits#laravel-breeze)
- [Vue.js3](https://vuejs.org)
- [Nodejs](https://nodejs.org/en)
- [Inertia.js](https://inertiajs.com)
- [TailwindCSS](https://tailwindcss.com)
- [Server-side Rendering (SSR)](https://inertiajs.com/server-side-rendering)
- [Docker](https://docs.docker.com) & [docker-compose](https://docs.docker.com/compose)
- [Mysql](https://www.mysql.com)
- [Redis](https://redis.io)

## Project requirements
- The homepage will show all the blog posts to everyone visiting the web
- Any user will be able to register in the platform login to a private area to see the posts he created and, if they want, create new ones. They won't be able to edit or delete them.
- The blog posts will only contain a title, description and publication date. The users should be able to sort them by publication_date.
- Also, the customer is using another blogging platform and she wants us to auto import the posts created there and add them to our new blogging platform, for that reason, she provided us the following REST api endpoint that returns the new posts
- The posts from this feed should be saved under a designated, system-created user, 'admin'.
- Our customer is a very popular blogger, who generates between 2 and 3 posts an hour. The site which powers the feed linked above is a very popular one (several million visitors a month), and we are expecting a similar level of traffic on our site. One of our goals is to minimise the strain put on our system during traffic peaks, while also minimising the strain we put on the feed server.

## Tech notes:
- As the system is a blog then we should do server-side rendering allowing your visitors to see your website prior to the JavaScript fully loading. It also makes it easier for search engines to index your site.
- Docker-compose setup has:
  - Apache webserver
  - php:8.2
  - node.js
  - npm
  - database (mysql server)
  - phpmyadmin
  - Redis
  - cron scheduler
- I used redis to cache the posts that were visited for the first time based on sorting and page number to reduce the hits of the database for the following requests with an expiry time of 1 hour and the cache of the first page only will be reset once there is post saved or post imported to the system

## About each container
- web: (blogging-platform_web): it has the web server(apache) and php, node.js and npm
- database_server: (blogging-platform_database): it has mysql database server
- phpmyadmin: (blogging-platform_phpmyadmin): ui for database (database client)
- redis: (blogging-platform_redis): in-memory database used in cache
- cron: (blogging-platform_cron): container to run cron jobs and scheduler

## Links of local dev for docker
- [blogging-platform](http://localhost)
- [phpmyadmin](http://localhost:8080)

## Links of local dev for built-in server
- [blogging-platform](http://localhost:8000)
