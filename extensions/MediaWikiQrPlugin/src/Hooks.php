<?php

namespace MediaWikiQrPlugin;

// Load the Composer autoloader for vendored dependencies (chillerlan/php-qrcode).
// __DIR__ is /var/www/html/extensions/MediaWikiQrPlugin/src, so we step up one
// level to reach the extension root where vendor/ lives.
require_once __DIR__ . '/../vendor/autoload.php';

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Skin;

/**
 * Hook handlers for the MediaWikiQrPlugin extension.
 */
class Hooks {

	/**
	 * SkinAfterContent hook — appends a QR code image linking to the current
	 * page after the bodyContent of every wiki page.
	 *
	 * The URL encoded in the QR code is the canonical full URL of the current
	 * page as MediaWiki knows it (protocol + hostname + port + path), derived
	 * from $wgServer and the page title. This works for all namespaces including
	 * Talk, Special, etc.
	 *
	 * The QR code is generated entirely server-side using chillerlan/php-qrcode
	 * (vendored in this extension) and delivered as an inline SVG data URI —
	 * no third-party service or URL shortener is involved.
	 *
	 * @param string &$data   HTML to append after bodyContent (modified in place)
	 * @param Skin   $skin    The current skin instance
	 */
	public static function onSkinAfterContent( string &$data, Skin $skin ): void {
		// Build the full canonical URL for the current page.
		// Title::getFullURL() uses $wgServer (which the entrypoint renders from
		// config.env) and handles every namespace correctly.
		$title = $skin->getTitle();
		$pageUrl = $title->getFullURL();

		// Load the client-side module that opens the QR image in a new tab.
		// Browsers block top-level navigation to data: URIs, so the JS converts
		// the data URI to a blob: URL (which is allowed) on click.
		$skin->getOutput()->addModules( 'ext.mediaWikiQrPlugin' );

		// Generate the QR code as a base64-encoded SVG data URI.
		// QRMarkupSVG is the default output interface; outputBase64=true makes
		// render() return a data URI we can drop straight into <img src="...">.
		// svgAddXmlHeader is disabled so the embedded SVG is clean HTML.
		$options = new QROptions( [
			'outputBase64'    => true,
			'svgAddXmlHeader' => false,
			'addQuietzone'    => true,
			'quietzoneSize'   => 4,
			'eccLevel'        => 'M',  // 15% error correction — good balance for URLs
		] );

		$dataUri = ( new QRCode( $options ) )->render( $pageUrl );

		// Escape the URL for use in a plain-text alt attribute.
		$escapedUrl = htmlspecialchars( $pageUrl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );

		$data .= <<<HTML
			<div style="text-align: center; margin-top: 1em;">
				<p></p>
				<p>
					Link to this page:
				</p>
				<a href="{$dataUri}" class="mw-qr-plugin-link" data-qr-url="{$escapedUrl}" target="_blank" rel="noopener noreferrer">
					<img src="{$dataUri}"
					     alt="QR code linking to {$escapedUrl}"
					     title="{$escapedUrl}"
					     width="150" height="150" />
				</a>
				<p>
					{$escapedUrl}
				</p>
			</div>
			HTML;
	}
}
