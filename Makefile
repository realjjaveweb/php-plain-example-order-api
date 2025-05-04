.DEFAULT_GOAL := help

DOCKER_COMPOSE_FILE := devops/docker-compose.yml

help: ## show this help (bash only)
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

up: ## starts services (detached)
	docker compose --file $(DOCKER_COMPOSE_FILE) up -d

down: ## stops and removes service containers
	docker compose --file $(DOCKER_COMPOSE_FILE) down

restart: down up ## stop, remove and recreate service containers (down up)

_api_up:
	docker compose --file $(DOCKER_COMPOSE_FILE) up --detach api
_api_down:
	docker compose --file $(DOCKER_COMPOSE_FILE) down api
_api_rebuild:
	docker compose --file $(DOCKER_COMPOSE_FILE) build api --no-cache

rebuild: _api_down _api_rebuild _api_up ## stop&remove api container => rebuild WITHOUT CACHE => start again

install: ## runs the initial install
	docker compose --file $(DOCKER_COMPOSE_FILE) exec api composer install

clear_cache: ## clears (symfony) cache
	docker compose --file $(DOCKER_COMPOSE_FILE) exec api composer clear-cache
	docker compose --file $(DOCKER_COMPOSE_FILE) exec api composer dump-autoload

csfix-dry: ## checks php formatting in a "dry" run, only showing what files WOULD be changed; has silent=1 to hide fixer output
	@docker compose --file $(DOCKER_COMPOSE_FILE) exec api \
	./devops/bin/javeLikesPadding.sh "CHECKING EVERYTHING IS OK..." --bg=blue --color=white --bold; \
	hideOutput=$$(if [ "$(silent)" = "1" ]; then echo "> /dev/null 2>&1"; else echo ""; fi); \
	eval "vendor/bin/php-cs-fixer fix --dry-run --config ./.php-cs $$hideOutput"; \
	EXIT_CODE=$$?; \
	if [ $$EXIT_CODE -eq 0 ]; then \
		./devops/bin/javeLikesPadding.sh "✨ ✨ ✨ WHOAH! YOUR CODE JUST SHINES! ✨ ✨ ✨" --bg=green --color=black; \
	else \
		./devops/bin/javeLikesPadding.sh "⚠️  ❕❕ ATTENTION ❕❕ ⚠️" --bg=red --color=white --bold; \
	fi

# fun fact - csfixer does not return proper exit codes unless --dry-run is used
_csfix:
	@docker compose --file $(DOCKER_COMPOSE_FILE) exec api \
	./devops/bin/javeLikesPadding.sh "FIXING..." --bg=blue --color=white --bold; \
	vendor/bin/php-cs-fixer fix --config ./.php-cs
csfix: _csfix
	@$(MAKE) --no-print-directory csfix-dry silent=1 ## actually runs php formatting and does FILE CHANGES

phpstan: ## runs static code analysis (phpstan)
	docker compose --file $(DOCKER_COMPOSE_FILE) exec api vendor/bin/phpstan analyse -c phpstan.neon
