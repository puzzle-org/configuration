#------------------------------------------------------------------------------
# Composer
#------------------------------------------------------------------------------
COMPOSER_ARGS=
ifeq (composer, $(firstword $(MAKECMDGOALS)))
    ifneq (,$(filter install update,$(CLI_ARGS)))
        COMPOSER_ARGS=--ignore-platform-reqs
    endif
endif

composer: composer.phar
	php composer.phar $(CLI_ARGS) $(COMPOSER_ARGS)

composer-install: composer.phar ## Install dependencies
	php composer.phar install --ignore-platform-reqs

composer.phar:
	curl -sS https://getcomposer.org/installer | php

clean:
	rm -f composer.lock
	rm -f composer.phar
	rm -rf vendor

.PHONY: composer composer-install clean
