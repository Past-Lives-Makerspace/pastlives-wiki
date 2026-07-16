DIST_NAME       ?= MediaWikiQrPlugin
RELEASE_VERSION ?= 1.0
COMPOSER        ?= composer
DIST_README_SRC ?= README.dist.md
DIST_CONTENTS   := extension.json composer.json LICENSE src vendor

STAGING := $(DIST_NAME)
ARCHIVE := $(DIST_NAME)-$(RELEASE_VERSION).tar.gz

.PHONY: dist

## dist: Restore vendor/ via composer install, then package the distributable
##       extension into a versioned archive matching the top-level dist target.
dist:
	@echo "make: restoring vendor/ via composer install"
	@$(COMPOSER) install --no-interaction --prefer-dist || { \
		status=$$?; \
		echo "make: error: 'composer install' failed (exit $$status); not packaging" >&2; \
		exit $$status; \
	}
	@echo "make: packaging $(DIST_NAME) v$(RELEASE_VERSION)"
	@rm -rf "$(STAGING)"
	@mkdir -p "$(STAGING)"
	@for item in $(DIST_CONTENTS); do \
		if [ ! -e "$$item" ]; then \
			echo "make: error: required distribution item '$$item' not found; aborting" >&2; \
			exit 1; \
		fi; \
		echo "make: staging $$item"; \
		cp -R "$$item" "$(STAGING)/"; \
	done
	@if [ ! -e "$(DIST_README_SRC)" ]; then \
		echo "make: error: distribution README '$(DIST_README_SRC)' not found; aborting" >&2; \
		exit 1; \
	fi
	@echo "make: staging $(DIST_README_SRC) as README.md"
	@cp "$(DIST_README_SRC)" "$(STAGING)/README.md"
	@echo "make: creating archive $(ARCHIVE)"
	@tar -czf "$(ARCHIVE)" "$(STAGING)"
	@rm -rf "$(STAGING)"
	@echo "make: bundled archive ready: $(ARCHIVE)"
