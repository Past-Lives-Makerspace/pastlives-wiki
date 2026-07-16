# pastlives-wiki

The [Past Lives Makerspace](https://pastlives.space) wiki — **[wiki.pastlives.space](https://wiki.pastlives.space)**.

## What this repo is

**The live webroot, verbatim.** This repo is `/var/www/wiki.pastlives.space/public` on the
Past Lives Hetzner VPS: stock MediaWiki 1.43 (LTS) core, the extensions, and our
`LocalSettings.php`. Deploying is a `git pull` on the server. What you see on `main` is what
runs in production.

The only things on the server that are *not* in this repo:

| Thing | Where it lives |
|---|---|
| Secrets (DB password, keys, R2 credentials) | `/var/www/wiki.pastlives.space/secrets.php` — outside the webroot, never in git. Template: [`secrets.php.example`](secrets.php.example) |
| Runtime cache | `cache/` (gitignored) |
| Database | Local MariaDB — `wiki_pastlives_space_db` |
| nginx / PHP-FPM / TLS | System config; reference copy of the vhost in [`nginx/wiki.pastlives.space.conf`](nginx/wiki.pastlives.space.conf) |

File uploads go to Cloudflare R2 (`plfog` bucket, `/mediawiki` prefix) via
[Extension:AWS](https://github.com/edwardspec/mediawiki-aws-s3), so `images/` stays empty.

## Making a change

Most config changes are edits to [`LocalSettings.php`](LocalSettings.php) — permissions,
namespaces, enabling an extension.

1. Open a PR against this repo.
2. After merge, someone with server access runs, on the VPS:
   ```
   /var/www/wiki.pastlives.space/public/deploy.sh
   ```
   (a `git pull` + `update.php`)
3. Verify at [Special:Version](https://wiki.pastlives.space/Special:Version).

New extensions are vendored: unpack/clone into `extensions/NameHere` **without** a nested
`.git` directory, run its `composer install --no-dev` if it has a `composer.json`, add the
`wfLoadExtension( 'NameHere' );` line to `LocalSettings.php`, and commit it all in one PR.

Custom extensions of ours:
[MediaWikiQrPlugin](https://github.com/Past-Lives-Makerspace/MediaWikiQrPlugin) — a QR code
on every page linking back to its canonical URL (developed in its own repo, vendored here).

## MediaWiki upgrades

Core is upgraded by unpacking the new 1.43.x tarball over the checkout, reviewing the diff,
committing, and running `deploy.sh` on the server after merge (its `update.php` step applies
schema migrations).

## History

This repo previously packaged a Docker image for a Render.com deployment. That deployment
was retired on 2026-07-16 when the wiki moved to the Hetzner VPS, and the repo was rebuilt
as the live webroot. The old Render database credentials that appeared in the earlier
history belong to a database that no longer exists.
