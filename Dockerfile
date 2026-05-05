FROM mediawiki:1.43

# ---------------------------------------------------------------------------
# 1. PostgreSQL PHP driver
#    Upstream mediawiki only ships mysqli + sqlite3; pgsql is required to
#    use Render's managed Postgres. Standard php-image pattern: install
#    libpq5 (runtime) as manual, libpq-dev (build) as build dep, compile,
#    then auto-remove build deps while keeping libpq5 so pgsql.so can
#    resolve -lpq at runtime.
# ---------------------------------------------------------------------------
RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends libpq5 git unzip; \
    savedAptMark="$(apt-mark showmanual)"; \
    apt-get install -y --no-install-recommends libpq-dev; \
    docker-php-ext-install pdo_pgsql pgsql; \
    apt-mark auto '.*' > /dev/null; \
    [ -z "$savedAptMark" ] || apt-mark manual $savedAptMark > /dev/null; \
    apt-mark manual libpq5 git unzip > /dev/null; \
    apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false; \
    rm -rf /var/lib/apt/lists/*

# ---------------------------------------------------------------------------
# 2. Composer (binary copied from the official composer image — the
#    mediawiki runtime image does not ship one).
# ---------------------------------------------------------------------------
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# ---------------------------------------------------------------------------
# 3. Extension:AWS for R2/S3 file uploads
#    Clones the extension, registers it with composer-merge-plugin, then
#    runs composer to pull aws-sdk-php into core's vendor/.
# ---------------------------------------------------------------------------
RUN set -eux; \
    git clone --depth 1 https://github.com/edwardspec/mediawiki-aws-s3.git \
        /var/www/html/extensions/AWS; \
    printf '%s\n' '{"extra":{"merge-plugin":{"include":["extensions/AWS/composer.json"]}}}' \
        > /var/www/html/composer.local.json; \
    cd /var/www/html && composer update --no-dev --no-interaction --prefer-dist

# ---------------------------------------------------------------------------
# 4. pl-extras.php — pulled in by LocalSettings.php (Steven adds one
#    require_once line after the install wizard). Owns DB + R2 wiring so
#    secrets stay in Render env vars, not in the public repo.
# ---------------------------------------------------------------------------
COPY pl-extras.php /var/www/html/pl-extras.php
COPY LocalSettings.php /var/www/html/LocalSettings.php
# ---------------------------------------------------------------------------
# 5. NamespaceLockdown extension for namespace permissions
# ---------------------------------------------------------------------------
RUN git clone --depth 1 https://github.com/wikimedia/mediawiki-extensions-NamespaceLockdown.git \
     /var/www/html/extensions/NamespaceLockdown
