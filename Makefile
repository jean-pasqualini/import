db:
	php tests/App/console.php d:d:d --force
	php tests/App/console.php d:d:c
	php tests/App/console.php d:s:u --force

test-unit:
	vendor/bin/phpunit --testsuite unit --testdox

test-unit-ci:
	vendor/bin/phpunit --coverage-clover=coverage.clover --testsuite unit --testdox

fix-cs:
	vendor/bin/php-cs-fixer fix src -vvv --config=.php_cs --cache-file=.php_cs.cache

test-cs:
	vendor/bin/php-cs-fixer fix src --no-interaction --dry-run --diff -vvv --config=.php_cs --cache-file=.php_cs.cache --using-cache=no

test-integration: db
	php tests/App/console.php process:run -vv -- create_boutique
	php tests/App/console.php process:run -vv -- import_product
	php tests/App/console.php process:run -vv -- demo_unique_filter
	php tests/App/console.php process:run -vv -- demo_filter_step
	php tests/App/console.php process:run -vv -- demo_validate_object