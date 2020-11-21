DOCKER_PHP_SERVICE = php-fs

.PHONY: new up kill test analyse install update validate dump-autoload index

new: kill
	#cp .env.dist .env
	docker-compose up -d --build --remove-orphans
	make install
up:
	docker-compose up -d
kill:
	docker-compose kill
	docker-compose down --volumes --remove-orphans
test:
	docker-compose exec $(DOCKER_PHP_SERVICE) composer test
analyse:
	docker-compose exec $(DOCKER_PHP_SERVICE) composer analyse
install:
	docker-compose exec $(DOCKER_PHP_SERVICE) composer install --no-interaction --prefer-dist
update:
	docker-compose exec $(DOCKER_PHP_SERVICE) composer up --with-all-dependencies
update-lock:
	docker-compose exec $(DOCKER_PHP_SERVICE) composer up --lock
dump-autoload:
	docker-compose exec $(DOCKER_PHP_SERVICE) composer dump-autoload
validate:
	docker-compose exec $(DOCKER_PHP_SERVICE) composer validate
index:
	docker-compose exec $(DOCKER_PHP_SERVICE) php index.php
