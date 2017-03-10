#------------------------------------------------------------------------------
# PHPUnit
#------------------------------------------------------------------------------
CONTAINER_NAME=puzzle/configuration/phpunit
CONTAINER_SOURCE_PATH=/usr/src/puzzle-configuration

phpunit = docker run -it --rm --name phpunit \
	                 -v ${HOST_SOURCE_PATH}:${CONTAINER_SOURCE_PATH} \
	                 -w ${CONTAINER_SOURCE_PATH} \
	                 -u ${USER_ID}:${GROUP_ID} \
	                 ${CONTAINER_NAME} \
	                 vendor/bin/phpunit $1 $(CLI_ARGS)

phpunit: vendor/bin/phpunit create-phpunit-image
	$(call phpunit, )

phpunit-coverage: vendor/bin/phpunit create-phpunit-image
	$(call phpunit, --coverage-html=coverage/)

vendor/bin/phpunit: composer-install

create-phpunit-image:
	docker build -q -t ${CONTAINER_NAME} docker/images/phpunit/

clean-phpunit-image:
	docker rmi ${CONTAINER_NAME}

.PHONY: phpunit create-phpunit-image clean-phpunit-image
