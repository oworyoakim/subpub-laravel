# Sub/Pub System in Laravel 7.x
## Requirements
- Laravel 7.x and all the requirements needed to set up and run a laravel Application as described [here](https://laravel.com/docs/7.x#server-requirements)
- Predis. Installed using `composer require predis/predis`
- Redis
    - Installed using `brew install redis`
    - Start redis server using `brew services start redis` 
## Installation
- Clone this repository found at [https://github.com/oworyoakim/subpub-laravel](https://github.com/oworyoakim/subpub-laravel)
- Ensure that Redis server is set up and running.
- CD to the project directory run the following commands:
    - `composer install`
    - `cp .env.example .env'
    - `php artisan key:generate`
- Modify the .env file to match your redis host configurations
- Either modify config/database.php file by changing the redis client from `phpredis` to `predis` OR add `REDIS_CLIENT=predis` in the .env file

## Running the Application
### Running the Publishing Server
- Run `php artisan serve --port 8000`
### Running the Listeners
- Spin up 3 or more servers using the same command but with different ports as follows:
    - Run `php artisan serve --port 8001`
    - Run `php artisan serve --port 8002`
    - Run `php artisan serve --port 8003`
### Subscribing for events
- Using Curl, Postman or any other HTTP client of your choice, send  3 POST requests to the Publishing server as follows:
    - Endpoint: `http://127.0.0.1:8000/api/subscribe/topic1`
         - Body: {"url": "http://127.0.0.1:8001/api/event"}
    - Endpoint: `http://127.0.0.1:8000/api/subscribe/topic1`
         - Body:  {"url": "http://127.0.0.1:8002/api/event"}
    - Endpoint: `http://127.0.0.1:8000/api/subscribe/topic1`
         - Body:  {"url": "http://127.0.0.1:8003/api/event"}
### Publishing events
- To publish an event about topic1, send the following POST request to the Publishing server
    - Endpoint: `http://127.0.0.1:8000/api/publish/topic1`
    - Body: {"message": "Hello from http://127.0.0.1:8000"}
### Output
- Open storage/logs/laravel.log and you will see three lines of information logged by the listeners signifying that the all the 3 listeners received a notification
    <pre>
     [2020-08-10 14:52:39] local.INFO: topic1: Hello from http://127.0.0.1:8000 
     [2020-08-10 14:52:39] local.INFO: topic1: Hello from http://127.0.0.1:8000 
     [2020-08-10 14:52:39] local.INFO: topic1: Hello from http://127.0.0.1:8000
    </pre>

## Route Definitions
- The application routes were defined in the routes/api.php as follows:
<pre>
    Route::post('subscribe/{topic}', 'SubPubController@subscribe');
    Route::post('publish/{topic}', 'SubPubController@publish');
    Route::post('event', 'SubPubController@processEvent');
</pre>
- In Laravel, all routes declared in the routes/api.php namespace are prefixed with `api` by default, hence we have three endpoints:
    - `/api/subscribe/{topic}` is a POST endpoint that takes a variable route parameter `topic` and a `url` in its body, where the subscriber will be listening from
    - `/api/publish/{topic}` is a POST endpoint that takes a variable route parameter `topic` and a `message` in its body, which will be broadcast to all the subscribers listening on the `topic` channel
    - `/api/event` is a POST endpoint that receives broadcast messages from the publishing server about the `topic` subscribed for
        - Just logs the message and topic in a Laravel log file. In node.js, this would be a console.log() call
    - The first two routes are defined in the Publishing server, whereas the last one is defined in the clinets (listeners)  
 
## Testing
- Ensure you are in the project directory
- Start one test client/listener on port `8001` using the command below:
    - `php artisan serve --port 8001`
- Run `./vendor/bin/phpunit` to run the tests



# Happy Coding
