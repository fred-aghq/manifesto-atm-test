## Introduction
Did I overengineer the heck out of this? I sure did! 

The test spec felt somewhat like Laravel would make things a bit too easy, and invite itself to lazy coding, so I've made an effort 
to show off both my knowledge of Laravel and  approach to cleaner coding. I had the time to have a mini-hackathon and had
some fun with it.

This application is dockerised, including a mysql container (with an ephemeral volume).

The docker stack is semi-DIY, having found this awesome quick guide on medium that's actually pretty in line with
how I might approach starting a stack destined for production.

ref: https://medium.com/geekculture/the-easiest-way-to-dockerize-your-laravel-application-94977fe2ed6d

There are a couple of helper commands included to update/persist new customer data, and change the ATM total cash.

## Requirements
- Docker/Docker Compose
- WSL2 (Windows only) or a *nix-based system

## Installation
1. `cp .env.example .env` - .env is gitignored to protect sensitive values
2. `docker-compose up -d --build` - The stack will build itself, the app entrypoint will generate Laravel keys
3. `docker-compose exec app php artisan migrate --seed` - This will seed customer accounts that suit the spec
> You can optionally choose not to include `--seed`. When first running the ATM command, it'll request an amount of cash with which
> it should be initialised.
> 
> However, you'll have to manually create customer accounts with the command `docker-compose exec app php artisan manifest:atm:customer`

> Note: any commands beginning `docker-compose exec app` can be executed inside the container shell if preferred,
> e.g. `docker-compose exec app sh`, then `php artisan` from there.
> 

## Running The Main App
1. `docker-compose exec app php artisan manifesto:atm`

## Helper/Admin methods
- `docker-compose exec app php artisan manifesto:atm:cash`
  - Updates the machine's cash amount
    
- `docker-compose exec app php artisan manifesto:atm:customer`
    - Update/Create new customer entity.

## Testing
1. `docker-compose exec app composer test`

## Nice to haves I thought about
- multiple accounts per customer, e.g. Customer HasMany Account
- multiple machines
- feature testing the actual CLI entrypoint with provided laravel test helpers - I tried but weird PHPUnit errors ate 
  into a load of time.
- more user-friendly helper commands (though this was way out of scope, so basic validation and errors will have to do)
- Better automated codesniffer fixing. I added the laravel-rules to have at least *some* sort of code sniffing/linting.
