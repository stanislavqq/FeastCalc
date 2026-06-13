test:
	docker compose exec app bash -c "cd /application && vendor/bin/phpunit"