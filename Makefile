cs-fix:
	vendor/bin/php-cs-fixer fix

phpstan:
	vendor/bin/phpstan analyse -c phpstan.neon

psalm:
	vendor/bin/psalm

rector:
	vendor/bin/rector process

phpunit:
	vendor/bin/phpunit

checks: rector cs-fix phpstan psalm