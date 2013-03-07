#!/bin/bash

php vendor/php/php-src/run-tests.php -p ./php.sh --html ./full-test-results.html.md "vendor/php/php-src/tests"
perl -pi -e 's/<script>/&lt;script&gt;/g' ./full-test-results.html.md
