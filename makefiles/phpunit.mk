#------------------------------------------------------------------------------
# PHPUnit
#------------------------------------------------------------------------------
IMAGE_NAME=puzzle/configuration/phpunit:latest
CONTAINER_SOURCE_PATH=/usr/src/puzzle-configuration

phpunit = docker run -it --rm --name phpunit \
	                 -v ${HOST_SOURCE_PATH}:${CONTAINER_SOURCE_PATH} \
	                 -w ${CONTAINER_SOURCE_PATH} \
	                 -u ${USER_ID}:${GROUP_ID} \
	                 ${IMAGE_NAME} \
	                 vendor/bin/phpunit $1 $(CLI_ARGS)

#------------------------------------------------------------------------------

phpunit: vendor/bin/phpunit create-phpunit-image ## Run unit tests
	$(call phpunit, )

phpunit-dox: vendor/bin/phpunit create-phpunit-image
	$(call phpunit, --testdox)

phpunit-coverage: vendor/bin/phpunit create-phpunit-image ## Run unit tests with coverage report
	$(call phpunit, --coverage-html=coverage/)

#------------------------------------------------------------------------------

# Runs phpunit from the host for CI
.PHONY: -phpunit-local
-phpunit-local: vendor/bin/phpunit
	vendor/bin/phpunit -c phpunit.xml --coverage-clover=coverage.xml

#------------------------------------------------------------------------------

vendor/bin/phpunit: composer-install

create-phpunit-image: docker/images/phpunit/Dockerfile
	docker build -q -t ${IMAGE_NAME} docker/images/phpunit/

#------------------------------------------------------------------------------

.PHONY: clean-phpunit
clean-phpunit: clean-phpunit-image clean-phpunit-dockerfile

.PHONY: clean-phpunit-image
clean-phpunit-image:
	@if [ -n "$$(docker images -q ${IMAGE_NAME} 2> /dev/null)" ]; then \
  		docker rmi ${IMAGE_NAME} ;\
  		echo "Image ${IMAGE_NAME} removed"; \
	else \
		echo "Image ${IMAGE_NAME} not found, no removal needed."; \
	fi

#------------------------------------------------------------------------------

.PHONY: phpunit phpunit-dox phpunit-coverage create-phpunit-image clean-phpunit-image
