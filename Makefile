HOST_SOURCE_PATH=$(shell dirname $(realpath $(firstword $(MAKEFILE_LIST))))

USER_ID=$(shell id -u)
GROUP_ID=$(shell id -g)

export USER_ID
export GROUP_ID

ifneq (,$(filter $(firstword $(MAKECMDGOALS)),composer phpunit))
    CLI_ARGS := $(wordlist 2,$(words $(MAKECMDGOALS)),$(MAKECMDGOALS))
    $(eval $(CLI_ARGS):;@:)
endif

#------------------------------------------------------------------------------
# Includes
#------------------------------------------------------------------------------

include makefiles/executables.mk
include makefiles/composer.mk
include makefiles/github.mk
include makefiles/whalephant.mk
include makefiles/phpunit.mk

#------------------------------------------------------------------------------
# Help
#------------------------------------------------------------------------------
.DEFAULT_GOAL := help

.PHONY: help
help:
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "\033[36m%-25s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

#------------------------------------------------------------------------------

.PHONY: clean-all
clean-all: clean-composer clean-whalephant clean-phpunit-dockerfile clean-phpunit-image ## Clean all generated artefacts
