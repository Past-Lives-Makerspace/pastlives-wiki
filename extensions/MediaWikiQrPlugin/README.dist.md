# MediaWiki QR Plugin

A MediaWiki extension that adds a QR code to every wiki page. The QR code
encodes the canonical URL of the page it appears on, so you can print a page
(or just the QR code) for offline use and still find your way back to the
source document by scanning the code with any phone or tablet.

Clicking on the QR brings up a page with just the QR and URL. This can be printed and attached to a shop machine so that you can find more information such as runbooks, maintenance manuals, and so on.

## Features

- Renders a QR code after the content of every page, in every namespace
  (articles, Talk, Special, etc.).
- Encodes the page's full canonical URL (scheme, host, port, and path), so the
  code resolves to exactly the page it was printed from.
- Generates the QR code entirely server-side and embeds it as an inline SVG
  image. No third-party QR service or URL shortener is contacted, and no data
  about your pages leaves your server.
- Ships with its 3rd-party QR library bundled, so it installs without running
  Composer.

## Requirements

- MediaWiki 1.43.0 or newer
- PHP 8.2 or newer

## Installation

1. Extract this archive into your MediaWiki `extensions/` directory. It unpacks
   to a folder named `MediaWikiQrPlugin/`:

   ```
   extensions/MediaWikiQrPlugin/
   ```

2. Load the extension by adding the following line to your `LocalSettings.php`:

   ```php
   wfLoadExtension( 'MediaWikiQrPlugin' );
   ```

3. Reload any wiki page. A QR code linking to that page now appears below the
   page content.

## Bundled Third Party Libraries

The extension bundles its dependency (`chillerlan/php-qrcode`) under `vendor/`,
so no additional Composer install step is required.

## Usage

There is nothing to configure. Once the extension is loaded, every page renders
a QR code beneath its content along with the page's URL in plain text. Print the
page, or just the QR code, and scan it later to return to the live page.

## License

Released under the MIT License. See `extension.json` for author and license
metadata. Bundled third-party libraries under `vendor/` retain their own
licenses (see the `LICENSE`/`NOTICE` files within each library).
