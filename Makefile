db:
	./console d:d:d --force
	./console d:d:c
	./console d:s:u --force

test-unit:
	vendor/bin/phpunit --testsuite unit --testdox

fix-cs:
	vendor/bin/php-cs-fixer fix src -vvv --config=.php_cs --cache-file=.php_cs.cache

test-cs:
	vendor/bin/php-cs-fixer fix src --no-interaction --dry-run --diff -vvv --config=.php_cs --cache-file=.php_cs.cache --using-cache=no