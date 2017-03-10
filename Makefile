HOST_SOURCE_PATH=$(shell dirname $(realpath $(lastword $(MAKEFILE_LIST))))

USER_ID=$(shell id -u)
GROUP_ID=$(shell id -g)

export USER_ID
export GROUP_ID

ifneq (,$(filter $(firstword $(MAKECMDGOALS)),composer phpunit))
    CLI_ARGS := $(wordlist 2,$(words $(MAKECMDGOALS)),$(MAKECMDGOALS))
    $(eval $(CLI_ARGS):;@:)
endif

COMPOSER_ARGS=
ifeq (composer, $(firstword $(MAKECMDGOALS)))
    ifneq (,$(filter install update,$(CLI_ARGS)))
        COMPOSER_ARGS=--ignore-platform-reqs
    endif
endif

composer: composer.phar
	php composer.phar $(CLI_ARGS) $(COMPOSER_ARGS)

composer-install: composer.phar
	php composer.phar install --ignore-platform-reqs

composer.phar:
	curl -sS https://getcomposer.org/installer | php

clean: remove-deps
	rm -f composer.lock
	rm -f composer.phar

remove-deps:
	rm -rf vendor

-include phpunit.mk

.PHONY: composer composer-install clean remove-deps
