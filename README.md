### Setup ###

* git clone
* setup .env file
* chmod storage and bootrap folders
* composer install
* php artisan migrate
* php artisan db:seed (composer dump-autoload -o  might be needed before that to register seed classes in autoload)

### User logins ###
* user1@gmail.com:password
* user2@gmail.com:password
* user3@gmail.com:password

### Features ###

* email sending through mailtrap.io
* geocoding through google api, simple point caching implemented (doesn't send request to google if the point with same lat/lng is already added in DB)
* ajax refresh on device map/device select
* etc