#!/bin/bash

php vendor/php/php-src/run-tests.php -p ./php.sh --html ./test-results.html.md vendor/php/php-src/tests/lang 
perl -pi -e 's/<script>/&lt;script&gt;/g' ./test-results.html.md