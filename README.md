# Installation steps

- clone the repo : git clone https://github.com/adil62/laravel-subscriptions-app.git
- run the migrations : php artisan migrate
- run the seeds : php artisan db:seed
- start the dev server : php artisan serve
- run the queue woker : php artisan queue:work
- for creating a Post : 
  ``` 
      POST api/posts 
      body : {
          "title" : "post1",
          "description" : "description",
          "website_id" : 1
      }
  ```
- for subscribing to a website :
  ``` 
      POST /api/users/subscribe
      body {
        "user_id" : 1,
        "website_id" : 2
      }
  ```
- The artisan command for sending a random post to all users : php artisan send:random-post-email 