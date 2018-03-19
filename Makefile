# Help
TARGETS:=$(MAKEFILE_LIST)

.PHONY: help
help: ## This help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(TARGETS) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

.PHONY: start_services
services: start_booking start_train_data ## Start all dependency services

.PHONY: start_booking
start_booking: ## Start booking reference service
	python3 _services/booking_reference.py

.PHONY: start_train_data
start_train_data: ## Start train data service
	python3 _services/start_train_data.py

.PHONY: guiding_test
guiding_test: ## Launch the guiding test
	python3 -m unittest _services/guiding_test.py

# Tests

.PHONY: test
test: ## Run unit tests
	@test -f bin/phpunit || echo "cannot run unit tests (needs phpunit/phpunit)"
	php bin/phpunit --testdox --coverage-text --coverage-clover ./build/logs/clover.xml --whitelist=src/Domain

.PHONY: bdd
bdd: ## Run BDD tests
	@test -f bin/behat || echo "cannot run bdd tests (needs behat/behat)"
	php bin/behat

.PHONY: mutation_test
mutation_test: ## Run mutation tests
	@test -f bin/infection || echo "cannot run unit tests (needs infection/infection)"
	php bin/infection

.PHONY: check_security
check_security: ## Check for dependency vulnerabilities
	curl -H "Accept: text/plain" https://security.sensiolabs.org/check_lock -F lock=@composer.lock

.PHONY: qa
qa: ## PHP QA Analyzer
	@which phpqa > /dev/null || echo "cannot run unit tests (needs EdgedesignCZ/phpqa)"
	phpqa --report --analyzedDirs src --tools phpqa,phpmetrics,phploc,phpmd,pdepend,phpcpd,parallel-lint,phpstan --buildDir build/qa

# Coding Style

.PHONY: cs cs-fix cs-ci
cs: ## Check code style
	./bin/php-cs-fixer fix --dry-run --stop-on-violation --diff

cs-fix: ## Fix code style
	./bin/php-cs-fixer fix

cs-ci: ## Run Continuous Integration code style check
	./bin/php-cs-fixer fix --dry-run --using-cache=no --verbose

# Train Office

.PHONY: start
start: ## Starts the Ticket Office Service
	php -S 127.0.0.1:8083 web/index.php
