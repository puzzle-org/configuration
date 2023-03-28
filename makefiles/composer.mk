#------------------------------------------------------------------------------
# Composer
#------------------------------------------------------------------------------

COMPOSER_VERSION?=latest

#------------------------------------------------------------------------------

composer = $(DOCKER_RUN) --rm \
                -v $(shell pwd):/var/www/app \
                -v ~/.cache/composer:/tmp/composer \
                -e COMPOSER_CACHE_DIR=/tmp/composer \
                -w /var/www/app \
                -u ${USER_ID}:${GROUP_ID} \
                composer:${COMPOSER_VERSION} ${COMPOSER_OPTIONS} $(COMPOSER_INTERACTIVE) $1 $2

#------------------------------------------------------------------------------

# Spread cli arguments
ifneq (,$(filter $(firstword $(MAKECMDGOALS)),composer))
    CLI_ARGS := $(wordlist 2,$(words $(MAKECMDGOALS)),$(MAKECMDGOALS))
    $(eval $(CLI_ARGS):;@:)
endif

# Add ignore platform reqs for composer install & update
COMPOSER_ARGS=
ifeq (composer, $(firstword $(MAKECMDGOALS)))
    ifneq (,$(filter install update require,$(CLI_ARGS)))
        COMPOSER_ARGS=--ignore-platform-reqs
    endif
endif

#------------------------------------------------------------------------------


.PHONY: composer
composer: -composer-init ## Run composer
	$(call composer, $(CLI_ARGS), $(COMPOSER_ARGS))

.PHONY: composer-install
composer-install: -composer-init vendor/ ## Install dependencies

.PHONY: composer-update
composer-update: -composer-init
	$(call composer, update, --ignore-platform-reqs)

.PHONY: composer-dumpautoload
composer-dumpautoload: -composer-init
	$(call composer, dumpautoload)

.PHONY: composer-version
composer-version: -composer-init ## Show composer version
	$(call composer, --version)

#------------------------------------------------------------------------------
# Non PHONY targets
#------------------------------------------------------------------------------

vendor/: composer.lock composer.json
	@$(call composer, install, --ignore-platform-reqs)

~/.cache/composer:
	mkdir -p ~/.cache/composer

#
# Target composer.lock is empty :
# 	The lock file is not versionned, it does not exist at first time
# 	Must exist to avoid a target does not exist
#
composer.lock:

#------------------------------------------------------------------------------
# Private targets
#------------------------------------------------------------------------------

.PHONY: -composer-init
-composer-init: ~/.cache/composer

#------------------------------------------------------------------------------

.PHONY: clean-composer
clean-composer:
	rm -f composer.lock
	rm -rf vendor
