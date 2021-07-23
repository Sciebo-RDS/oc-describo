include .env

start:
	docker-compose up -d
	@echo Wait for boot up.
	@while [ $(shell curl  -sw '%{http_code}' localhost:8000) -gt 302 ]; do true; done;
	@docker exec -it owncloud_server /bin/bash -c "occ user:modify admin email not@valid.tld" | true
	@docker exec -it owncloud_server /bin/bash -c "occ app:enable oauth2 && occ app:enable describo" | true
	@docker exec -it owncloud_server /bin/bash -c "occ oauth2:add-client describo_oc_app AfRGQ5ywVhNQDlfGVbntjDOn2rLPTjg0SYEVBlvuYV4UrtDmmgIvKWktIMDP5Dqq WnxAqddPtPzX3lyCYijHi3pVs1HGpoumzTYSUWqrVfL0vT7E92JSzNTQABBzCaIm ${OWNCLOUD_DOMAIN}/apps/describo/authorize" | true
	@docker exec -it owncloud_server /bin/bash -c "occ oauth2:add-client describo_standalone bHj2JMPy50blVwdMXergYKMmCI8Np0UfA4wJEvDgcTgElK7ewf5e9d0u7Db3GUTZ 5Bf16MtNVU13o7Jxp8MWVCaxb79dFTNlg1cWSq6R3zla82yYKn2WxJYrJE95u3Zm ${DESCRIBO_REDIRECT_URL}" | true
	@echo Start on http://localhost:8000

stop:
	docker-compose down
	sudo chown -R $(shell id -un):$(shell id -gn) php