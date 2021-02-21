# See https://tech.davis-hansson.com/p/make/
MAKEFLAGS += --warn-undefined-variables
MAKEFLAGS += --no-builtin-rules

.DEFAULT_GOAL := help
.PHONY: help
help:
	@printf "\033[33mUsage:\033[0m\n  make TARGET\n\033[33m\nAvailable Commands:\n\033[0m"
	@grep -E '^[a-zA-Z-]+:.*?## .*$$' Makefile | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  [32m%-27s[0m %s\n", $$1, $$2}'

#
# Variables
#---------------------------------------------------------------------------

PHP_CS_FIXER=vendor/bin/php-cs-fixer
PHP_CS_FIXER_ARGS=fix --diff --diff-format=udiff --verbose

#
# Commands (phony targets)
#---------------------------------------------------------------------------

analyze: cs-check test

cs-check: prerequisites ## Runs code style checks in dry-run mode
	$(PHP_CS_FIXER) $(PHP_CS_FIXER_ARGS) --dry-run

cs-fix: prerequisites ## Runs code style checks and fix founded issues
	$(PHP_CS_FIXER) $(PHP_CS_FIXER_ARGS)

test: prerequisites ## Runs Unit tests
	vendor/phpunit/phpunit/phpunit

# We need both vendor/autoload.php and composer.lock being up to date
.PHONY: prerequisites
prerequisites: vendor/autoload.php composer.lock
