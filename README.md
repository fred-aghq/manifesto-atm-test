## Introduction
Did I over-engineer the heck out of this? I sure did! :)

The test spec felt somewhat like Laravel would make things a bit too easy, and invite "lazy" coding, so I've made an effort 
to show off both my knowledge of Laravel and approach to clean code, while trying to keep a "sprint" iteration vibe. I had the time to have a mini-hackathon and had
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
> Note: any commands beginning `docker-compose exec app` can be executed inside the container shell if preferred,
> e.g. `docker-compose exec app sh`, then `php artisan` from there.
 

1. `cp .env.example .env` - .env is gitignored to protect sensitive values
2. `docker-compose up -d --build` - Stack builds itself, app entrypoint generates Laravel keys
3. `docker-compose exec app php artisan migrate --seed` - Seeds customer accounts that suit the spec.

> You can optionally choose not to include `--seed`. When first running the ATM command, it'll request an amount of cash with which
> it should be initialised.
> 
> However, you'll have to manually create customer accounts with the command `docker-compose exec app php artisan manifest:atm:customer`
> 
> When running the seeder, the two test accounts `12345678` and `87654321` will be reset to their initial states as per the 
> test spec.

## Running The Main App
1. `docker-compose exec app php artisan manifesto:atm`

If the ATM has not been initialised with cash, you will prompted to enter a value.

The application will request for login details. The following test accounts are created when seeded (See installation 
section):

> `12345678` / `1234`
> `87654321` / `4321`
 
Upon successful login, the application accepts the following characters as an option:
```
W - Withdraw
B - Show Balance
D - Login as a different account
E - exit
```

## Helper/Admin methods
- `docker-compose exec app php artisan manifesto:atm:cash`
  - Updates the machine's cash amount
    
- `docker-compose exec app php artisan manifesto:atm:customer`
    - Update/Create new customer entity.

## Testing
1. `docker-compose exec app composer test`

## Code Sniffing/Linting
- `docker-compose exec app composer test:style`
  
- `docker-compose exec app composer fix:style`

## Nice to haves I thought about
- Building composer dependencies into the image rather than installing at runtime - this got a bit fiddly and I didn't 
  want to spend far too much time on it.
  
- multiple accounts per customer, e.g. Customer HasMany Account
  
- multiple machines
  
- feature testing the actual CLI entrypoint with provided laravel test helpers - I tried but weird PHPUnit errors ate 
  into a load of time.
  
- more user-friendly helper commands (though this was way out of scope, so basic validation and errors will have to do)
  
- Better automated codesniffer fixing. I added the laravel-rules to have at least *some* sort of code sniffing/linting.
