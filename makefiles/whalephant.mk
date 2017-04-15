#------------------------------------------------------------------------------
# Whalephant
#------------------------------------------------------------------------------
docker/images/phpunit/Dockerfile: whalephant 
	docker run -it --rm --name whalephant \
               -v ${HOST_SOURCE_PATH}:${CONTAINER_SOURCE_PATH} \
               -w ${CONTAINER_SOURCE_PATH} \
               -u ${USER_ID}:${GROUP_ID} \
               php:7.1-cli \
               ./whalephant generate docker/images/phpunit

clean-whalephant: 
	rm -f whalephant

whalephant:
	$(eval LATEST_VERSION := $(shell curl -L -s -H 'Accept: application/json' https://github.com/niktux/whalephant/releases/latest | sed -e 's/.*"tag_name":"\([^"]*\)".*/\1/'))
	@echo "Latest version of Whalephant is ${LATEST_VERSION}"
	wget -O whalephant -q https://github.com/Niktux/whalephant/releases/download/${LATEST_VERSION}/whalephant.phar
	chmod 0755 whalephant

clean-phpunit-dockerfile: ## Force phpunit dockerfile regeneration
	rm -f docker/images/phpunit/Dockerfile

.PHONY: clean-whalephant
