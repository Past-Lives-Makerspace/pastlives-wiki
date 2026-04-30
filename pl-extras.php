<?php
/**
 * pl-extras.php — Past Lives Makerspace wiki overrides.
 *
 * Required from LocalSettings.php (one line at the end):
 *
 *     require_once __DIR__ . '/pl-extras.php';
 *
 * All secrets come from $_ENV (set on the Render service), so this file
 * is safe to commit to the public repo. Anything wired up here OVERRIDES
 * whatever the install wizard wrote into LocalSettings.php — that is the
 * point: keep the wizard's hardcoded literals out of git.
 */

// ---- Database (override wizard literals) -----------------------------------
$wgDBtype     = $_ENV['MW_DB_TYPE'];      // 'postgres'
$wgDBserver   = $_ENV['MW_DB_SERVER'];
$wgDBname     = $_ENV['MW_DB_NAME'];
$wgDBuser     = $_ENV['MW_DB_USER'];
$wgDBpassword = $_ENV['MW_DB_PASS'];

// ---- File uploads via Cloudflare R2 (Extension:AWS) ------------------------
wfLoadExtension( 'AWS' );

$wgEnableUploads = true;

$wgAWSCredentials = [
    'key'    => $_ENV['MW_R2_ACCESS_KEY_ID'],
    'secret' => $_ENV['MW_R2_SECRET_ACCESS_KEY'],
    'token'  => false,
];

// R2 ignores region but the extension requires *something*; 'auto' is R2's
// own documented value and works with the AWS SDK.
$wgAWSRegion = 'auto';

// Bucket is shared with another app — every upload lives under
// MW_R2_PATH_PREFIX (e.g. "/mediawiki") so the namespaces don't collide.
$wgAWSBucketName            = $_ENV['MW_R2_BUCKET'];
$wgAWSBucketTopSubdirectory = $_ENV['MW_R2_PATH_PREFIX'];

// Hashed subdirs match MediaWiki's local layout — recommended.
$wgAWSRepoHashLevels        = '2';
$wgAWSRepoDeletedHashLevels = '3';

// R2 S3 API endpoint (PutObject, GetObject, etc.). R2 only supports
// path-style addressing on this endpoint, so use_path_style_endpoint
// is mandatory — without it every PutObject 404s.
$wgFileBackends['s3']['endpoint']                = 'https://' . $_ENV['MW_R2_ACCOUNT_ID'] . '.r2.cloudflarestorage.com';
$wgFileBackends['s3']['use_path_style_endpoint'] = true;

// Public host where browsers fetch the uploaded files from. Host only
// (no scheme, no path); the extension prepends https:// and appends
// the object key. Use the r2.dev dev URL or a custom domain pointed at
// the bucket.
$wgAWSBucketDomain = $_ENV['MW_R2_PUBLIC_HOST'];

// AWS SDK ≥ 3.337 sends x-amz-sdk-checksum-algorithm headers that R2
// rejects. The two putenv() lines are the documented R2 workaround.
putenv( 'AWS_REQUEST_CHECKSUM_CALCULATION=WHEN_REQUIRED' );
putenv( 'AWS_RESPONSE_CHECKSUM_VALIDATION=WHEN_REQUIRED' );
