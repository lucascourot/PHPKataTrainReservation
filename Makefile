# Help
TARGETS:=$(MAKEFILE_LIST)

.PHONY: help
help: ## This help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(TARGETS) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

.PHONY: start_services
services: start_booking start_train_data ## Start all dependency services

.PHONY: start_booking
start_booking: ## Start booking reference service
	python3 _services/booking_reference.py

.PHONY: start_train_data
start_train_data: ## Start train data service
	python3 _services/start_train_data.py

.PHONY: guiding_test
guiding_test: ## Launch the guiding test
	python3 -m unittest _services/guiding_test.py
