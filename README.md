# PHP plain - example Order API

## Disclaimer
This project is not a fully DDD(domain driven design) or a fully hexagonal architecture,
it's a simple showcase how a project could be set up and how to abstract the data source, the same approach used for the Repository could be applied to the controlling/view side.
The purpose of the chosen solution was to show abstraction, yet not to overengineer - so please, take it with a grain of salt - it's just an example, not a full-on production app.

## Recommendations
- tested on Ubuntu 24

## Requirements
- `git` available (`sudo apt install git`)
- `make` command available (`sudo apt install make`)
- Latest Docker Engine (CLI) installed and **set up correctly** (emphasis on the post-install user & group, logout & login).
  - Installation guide (Ubuntu): https://docs.docker.com/engine/install/ubuntu/#install-using-the-repository
    - Other platforms/distributions: https://docs.docker.com/engine/install/
  - **Post-install config:** https://docs.docker.com/engine/install/linux-postinstall/

## Set up
1. clone the repository
2. `cd` into the repository root
3. Create a `.env` file from `.env.example` and set up the environment variables
    - **Note:** create the `.env` file **NOW** - it's used by the docker compose too - so it must be present before step "4."
4. Run `make up`, sit, wait, have a ☕
5. Run `make install` to do the initial installation of packages
6. Run `make db_migrate` to create & fill the test database
7. Done: http://localhost:9000 (/api/...)

Run `make help` to list more handy commands, run `make test` before you send any code to the world 😉

## DB Admin
Adminer - http://localhost:8080/
Use credentials from .env file.

___
___
# And that's all - Have a great day! 🙂
