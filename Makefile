include .env

start:
	docker-compose up -d
	@echo Wait for boot up.
	while [ $(shell curl  -sw '%{http_code}' localhost:8000) -gt 302 ]; do true; done;
	@docker exec -it owncloud_server /bin/bash -c "occ user:modify admin email not@valid.tld"
	@docker exec -it owncloud_server /bin/bash -c "occ app:enable oauth2 && occ app:enable describo"
	@docker exec -it owncloud_server /bin/bash -c "occ oauth2:add-client describo AfRGQ5ywVhNQDlfGVbntjDOn2rLPTjg0SYEVBlvuYV4UrtDmmgIvKWktIMDP5Dqq WnxAqddPtPzX3lyCYijHi3pVs1HGpoumzTYSUWqrVfL0vT7E92JSzNTQABBzCaIm ${OWNCLOUD_DOMAIN}/apps/describo/authorize"
	@echo Start on http://localhost:8000

stop:
	docker-compose down
	sudo chown -R $(shell id -un):$(shell id -gn) php