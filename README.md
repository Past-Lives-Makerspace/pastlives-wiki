# pastlives-wiki

MediaWiki for [Past Lives Makerspace](https://pastlives.space), deployed on Render at [wiki.pastlives.space](https://wiki.pastlives.space).

## What this repo is

A custom Docker image that extends `mediawiki:1.43` with two things the upstream image lacks:

- **`php-pgsql`** — needed to use Render's managed Postgres.
- **Extension:AWS** + the AWS PHP SDK — wired up for Cloudflare R2 file uploads.

The Render service `pastlives-wiki` (`srv-d7mgjubbc2fs7387t8f0`) builds and deploys from this repo on every push to `main`.

## Architecture

| Piece | Where |
|---|---|
| Web service | Render — `pastlives-wiki` |
| Database | Render Postgres (`mediawiki` DB, `mediawiki_user` role) |
| File uploads | Cloudflare R2 (`plfog` bucket, `/mediawiki` prefix) via Extension:AWS |
| Domain | `wiki.pastlives.space` |
| All DB / R2 secrets | Render env vars (`MW_DB_*`, `MW_R2_*`) — never commit them |

## Files

- **`Dockerfile`** — extends `mediawiki:1.43`, installs `pdo_pgsql`/`pgsql` and the `mediawiki-aws-s3` extension.
- **`pl-extras.php`** — the override file that wires DB + R2 from `$_ENV`. Copied into `/var/www/html/` by the Dockerfile. Loaded from `LocalSettings.php`.
- **`LocalSettings.php`** — gitignored until the installer runs once (see below).

## Required env vars on the Render service

Database (already set):

| Var | Value |
|---|---|
| `MW_DB_TYPE` | `postgres` |
| `MW_DB_SERVER` | `dpg-…oregon-postgres.render.com` |
| `MW_DB_NAME` | `mediawiki` |
| `MW_DB_USER` | `mediawiki_user` |
| `MW_DB_PASS` | (Render Postgres password) |

R2 (already set):

| Var | Format / example |
|---|---|
| `MW_R2_ACCOUNT_ID` | Cloudflare account id (32-char hex) |
| `MW_R2_BUCKET` | `plfog` |
| `MW_R2_PATH_PREFIX` | `/mediawiki` (leading slash, no trailing) |
| `MW_R2_PUBLIC_HOST` | host only — `pub-XYZ.r2.dev`, no scheme |
| `MW_R2_ACCESS_KEY_ID` | R2 API token access key |
| `MW_R2_SECRET_ACCESS_KEY` | R2 API token secret |

## First-time setup (install wizard)

1. Visit `https://wiki.pastlives.space/mw-config/` (or `https://pastlives-wiki.onrender.com/mw-config/` if DNS isn't pointed yet).
2. Walk through the wizard. When it asks for DB info, paste from the [handoff message](#handoff). Choose **PostgreSQL**.
3. At the end the wizard offers `LocalSettings.php` for download.
4. Open the downloaded file and add **one line at the very bottom**:

   ```php
   require_once __DIR__ . '/pl-extras.php';
   ```

   That single line replaces the wizard's literal DB password with `$_ENV` reads and turns on R2 uploads. You can leave the wizard's literal DB block alone — `pl-extras.php` overrides it.

5. Commit the file:

   ```sh
   cd ~/Code/pastlives-wiki
   # Remove LocalSettings.php from .gitignore (it now uses $_ENV reads via pl-extras.php)
   sed -i '/^LocalSettings\.php$/d' .gitignore
   cp ~/Downloads/LocalSettings.php ./LocalSettings.php
   git add .gitignore LocalSettings.php
   git commit -m "Initial LocalSettings.php from install wizard"
   git push
   ```

   Render auto-rebuilds and the wiki boots with persistent config. From then on every config change goes through a PR — no SSH-on-the-server editing required.

## Local development

```sh
docker build -t pastlives-wiki .
docker run --rm -p 8080:80 \
  -e MW_DB_TYPE=postgres \
  -e MW_DB_SERVER=... \
  ... (other env vars)
  pastlives-wiki
```

Open `http://localhost:8080`.

## Deploying

Push to `main`. Render auto-deploys.
