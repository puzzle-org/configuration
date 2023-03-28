#------------------------------------------------------------------------------
# Github actions targets
# 	Note : to easaly run tests with multiple php version
#	       github action does not use phpunit in a docker container but
#          use the php executable from the configured host
#------------------------------------------------------------------------------

.PHONY:ga-run-tests
ga-run-tests: -phpunit-local

#------------------------------------------------------------------------------
