.DEFAULT_GOAL := help

DOCKER_COMPOSE_FILE := devops/docker-compose.yml

help: ## show this help (bash only)
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

#
# Docker commands
#
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


#
# App related commands
#
install: ## runs the initial install
	docker compose --file $(DOCKER_COMPOSE_FILE) exec api composer install

clear_cache: ## clears (symfony) cache
	docker compose --file $(DOCKER_COMPOSE_FILE) exec api composer clear-cache
	docker compose --file $(DOCKER_COMPOSE_FILE) exec api composer dump-autoload


#
# Analysis/Tool commands
#
_line: # -----------------------------------
	@docker compose --file $(DOCKER_COMPOSE_FILE) exec api php -r "echo \"\033[1;35m\" . str_repeat('-', 80) . \"\033[0m\" . PHP_EOL;"

csfix-dry: ## checks php formatting in a "dry" run, only showing what files WOULD be changed; has silent=1 to hide fixer output
	@docker compose --file $(DOCKER_COMPOSE_FILE) exec api \
	./devops/bin/javeLikesPadding.sh "CSFIX CHECK IF EVERYTHING IS OK..." --bg=blue --color=white --bold; \
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
	./devops/bin/javeLikesPadding.sh "CSFIXING..." --bg=blue --color=white --bold; \
	vendor/bin/php-cs-fixer fix --config ./.php-cs
csfix: _csfix
	@$(MAKE) --no-print-directory csfix-dry silent=1 ## actually runs php formatting and does FILE CHANGES

phpstan: ## runs static code analysis (phpstan)
	docker compose --file $(DOCKER_COMPOSE_FILE) exec api vendor/bin/phpstan analyse -c phpstan.neon

phpunit: ## TODO: runs unit tests (phpunit)
	@docker compose --file $(DOCKER_COMPOSE_FILE) exec api \
	./devops/bin/javeLikesPadding.sh "TODO: phpunit" --bg=yellow --color=black --bold


test: ## runs fixer, static analysis, unit tests, etc...
	@$(MAKE) --no-print-directory _line
	@docker compose --file $(DOCKER_COMPOSE_FILE) exec api \
	echo -e "\033[1;35mRUNNING COMPLETE ANALYSIS\033[0m\nLines \"\033[1;35m-----\033[0m\" separate each step"
	@$(MAKE) --no-print-directory _line
	@$(MAKE) --no-print-directory csfix
	@$(MAKE) --no-print-directory _line
	@$(MAKE) --no-print-directory phpstan
	@$(MAKE) --no-print-directory _line
	@$(MAKE) --no-print-directory phpunit
