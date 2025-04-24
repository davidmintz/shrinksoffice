.PHONY: refresh-fixtures

refresh-fixtures:
	@echo "☢️ Nuking and reloading test database..."
	php bin/console doctrine:database:drop --force --if-exists --env=test
	php bin/console doctrine:database:create --env=test
	php bin/console doctrine:migrations:migrate --no-interaction --env=test
	php bin/console doctrine:fixtures:load --no-interaction --env=test
	@echo "✅ Fixtures loaded fresh!"
