# PHP plain - example Order API

## Recommendations
- tested on Ubuntu 24

## Requirements
- `git` available (`sudo apt install git`)
- `make` command available (`sudo apt install make`)
- latest Docker Engine (cli) installed and **set up correctly** (emphasis on the post-install user&group, logout&login)
  - installation guide (Ubuntu): https://docs.docker.com/engine/install/ubuntu/#install-using-the-repository
    - other platforms/distributions: https://docs.docker.com/engine/install/
  - **post-install config:** https://docs.docker.com/engine/install/linux-postinstall/
  
## Set up
1. clone the repository
2. `cd` into the repository root
3. run `make up`, sit, wait, have a ☕
4. done: http://localhost:9000 (/api/...)

Run `make help` to list more handy commands, run `make test` before you send any code to the world 😉

## DB Admin
Adminer - http://localhost:8080/
Use credentials from .env file.

___
___
# And that's all - Have a great day! 🙂