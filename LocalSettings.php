<?php
# LocalSettings.php for wiki.pastlives.space
#
# This file is the source of truth for the live wiki's configuration.
# It is deployed verbatim to /var/www/wiki.pastlives.space/public/LocalSettings.php
# on the Hetzner VPS by scripts/deploy.sh — do not edit the server copy directly.
#
# See includes/MainConfigSchema.php for all configurable settings
# and their default values, but don't forget to make changes in _this_
# file, not there.
#
# Further documentation for configuration settings may be found at:
# https://www.mediawiki.org/wiki/Manual:Configuration_settings

# Protect against web entry
if ( !defined( 'MEDIAWIKI' ) ) {
	exit;
}

# ---- Machine-local secrets ----
# DB password, secret keys, and R2 credentials live outside the webroot and
# outside git. Template: secrets.php.example in this repo.
require_once '/var/www/wiki.pastlives.space/secrets.php';

## Uncomment this to disable output compression
# $wgDisableOutputCompression = true;

$wgSitename = "Past Lives Wiki";
$wgMetaNamespace = "Past_Lives_Wiki";

## The URL base path to the directory containing the wiki;
## defaults for all runtime URL paths are based off of this.
## For more information on customizing the URLs
## (like /w/index.php/Page_title to /wiki/Page_title) please see:
## https://www.mediawiki.org/wiki/Manual:Short_URL
$wgScriptPath = "";

## The protocol and server name to use in fully-qualified URLs
$wgServer = "https://wiki.pastlives.space";

## The URL path to static resources (images, scripts, etc.)
$wgResourceBasePath = $wgScriptPath;

## The URL paths to the logo.  Make sure you change this from the default,
## or else you'll overwrite your logo when you upgrade!
$wgLogos = [
	'1x' => "$wgResourceBasePath/resources/assets/change-your-logo.svg",
	'icon' => "$wgResourceBasePath/resources/assets/change-your-logo-icon.svg",
];

## UPO means: this is also a user preference option

$wgEnableEmail = true;
$wgEnableUserEmail = true; # UPO

$wgEmergencyContact = "";
$wgPasswordSender = "";

$wgEnotifUserTalk = false; # UPO
$wgEnotifWatchlist = false; # UPO
$wgEmailAuthentication = true;

## Database settings ($wgDBpassword comes from secrets.php)
$wgDBtype = "mysql";
$wgDBserver = "localhost";
$wgDBname = "wiki_pastlives_space_db";
$wgDBuser = "wiki_pastlives_usr";

# MySQL specific settings
$wgDBprefix = "";
$wgDBssl = false;

# MySQL table options to use during installation or update
$wgDBTableOptions = "ENGINE=InnoDB, DEFAULT CHARSET=binary";

# Shared database table
# This has no effect unless $wgSharedDB is also set.
$wgSharedTables[] = "actor";

## Shared memory settings
$wgMainCacheType = CACHE_NONE;
$wgMemCachedServers = [];

## To enable image uploads, make sure the 'images' directory
## is writable, then set this to true:
$wgEnableUploads = false;
$wgUseImageMagick = true;
$wgImageMagickConvertCommand = "/usr/bin/convert";

# InstantCommons allows wiki to use images from https://commons.wikimedia.org
$wgUseInstantCommons = false;

# Periodically send a pingback to https://www.mediawiki.org/ with basic data
# about this MediaWiki instance. The Wikimedia Foundation shares this data
# with MediaWiki developers to help guide future development efforts.
$wgPingback = false;

# Site language code, should be one of the list in ./includes/languages/data/Names.php
$wgLanguageCode = "en";

# Time zone
$wgLocaltimezone = "UTC";

## Set $wgCacheDirectory to a writable directory on the web server
## to make your wiki go slightly faster. The directory should not
## be publicly accessible from the web.
#$wgCacheDirectory = "$IP/cache";

# $wgSecretKey and $wgUpgradeKey come from secrets.php

# Changing this will log out all existing sessions.
$wgAuthenticationTokenVersion = "1";

## For attaching licensing metadata to pages, and displaying an
## appropriate copyright notice / icon. GNU Free Documentation
## License and Creative Commons licenses are supported so far.
$wgRightsPage = ""; # Set to the title of a wiki page that describes your license/copyright
$wgRightsUrl = "";
$wgRightsText = "";
$wgRightsIcon = "";

# Path to the GNU diff3 utility. Used for conflict resolution.
$wgDiff3 = "/usr/bin/diff3";

## Default skin: you can change the default skin. Use the internal symbolic
## names, e.g. 'vector' or 'monobook':
$wgDefaultSkin = "vector-2022";

# Enabled skins.
# The following skins were automatically enabled:
wfLoadSkin( 'MinervaNeue' );
wfLoadSkin( 'MonoBook' );
wfLoadSkin( 'Timeless' );
wfLoadSkin( 'Vector' );


# End of automatically generated settings.
# Add more configuration options below.


# ---- APCu object caching ----
$wgMainCacheType = CACHE_ACCEL;

# ---- Enable uploads via R2 (S3-compatible) ----
$wgEnableUploads = true;

# ---- AWS/R2 file storage ----
# ($wgAWSCredentials and the R2 endpoint come from secrets.php)
wfLoadExtension( 'AWS' );

$wgAWSRegion = 'auto';
$wgAWSBucketName = "plfog";
$wgAWSBucketTopSubdirectory = "/mediawiki/";
$wgAWSBucketDomain = "https://pub-10991d66419645cd89916024ee6bb62c.r2.dev";

$wgFileBackends['s3']['use_path_style_endpoint'] = true;

# ---- Short URLs ----
$wgArticlePath = "/$1";
$wgUsePathInfo = true;

# ---- Cache directory ----
$wgCacheDirectory = "/var/www/wiki.pastlives.space/public/cache";

# ---- Performance ----
$wgJobRunRate = 0.01;

# ---- Custom Settings ----
#
# ============================================================
# Custom Extensions
# (non-bundled extensions are installed/updated by scripts/deploy.sh)
# ============================================================
 wfLoadExtension( 'Lockdown' );
 wfLoadExtension( 'MediaWikiQrPlugin' );

# ============================================================
# Custom Namespace IDs
# ============================================================
 define('NS_CERAMICS',              100);
 define('NS_EVENTS',                102);
 define('NS_GARDENERS',             104);
 define('NS_JEWELERS',              106);
 define('NS_METALWORKERS',          108);
 define('NS_GLASS',                 110);
 define('NS_TECH',                  112);
 define('NS_TEXTILES',              114);
 define('NS_WOODWORKERS',           116);
 define('NS_WRITERS',               118);
 define('NS_ART_FRAMING',           120);
 define('NS_PRISON_OUTREACH',       122);
 define('NS_FOOD_INDEPENDENCE',     124);
 define('NS_VISUAL_ARTS',           126);
 define('NS_DARKROOM_AND_PHOTOGRAPHY', 128);

# ============================================================
# Register Namespaces
# ============================================================
 $wgExtraNamespaces[NS_CERAMICS]                 = 'Ceramics';
 $wgExtraNamespaces[NS_EVENTS]                   = 'Events';
 $wgExtraNamespaces[NS_GARDENERS]                = 'Gardeners';
 $wgExtraNamespaces[NS_JEWELERS]                 = 'Jewelers';
 $wgExtraNamespaces[NS_METALWORKERS]             = 'Metalworkers';
 $wgExtraNamespaces[NS_GLASS]                    = 'Glass';
 $wgExtraNamespaces[NS_TECH]                     = 'Tech';
 $wgExtraNamespaces[NS_TEXTILES]                 = 'Textiles';
 $wgExtraNamespaces[NS_WOODWORKERS]              = 'Woodworkers';
 $wgExtraNamespaces[NS_WRITERS]                  = 'Writers';
 $wgExtraNamespaces[NS_ART_FRAMING]              = 'Art_framing';
 $wgExtraNamespaces[NS_PRISON_OUTREACH]          = 'Prison_outreach';
 $wgExtraNamespaces[NS_FOOD_INDEPENDENCE]        = 'Food_independence';
 $wgExtraNamespaces[NS_VISUAL_ARTS]              = 'Visual_arts';
 $wgExtraNamespaces[NS_DARKROOM_AND_PHOTOGRAPHY] = 'Darkroom_and_photography';

# ============================================================
# Namespace Permissions
# Everyone can read. Only specific editor groups can edit.
# ============================================================
# $wgNamespacePermissionLockdown = [];

 $customNamespaces = [
     NS_CERAMICS                 => 'ceramics-editors',
     NS_EVENTS                   => 'events-editors',
     NS_GARDENERS                => 'gardeners-editors',
     NS_JEWELERS                 => 'jewelers-editors',
     NS_METALWORKERS             => 'metalworkers-editors',
     NS_GLASS                    => 'glass-editors',
     NS_TECH                     => 'tech-editors',
     NS_TEXTILES                 => 'textiles-editors',
     NS_WOODWORKERS              => 'woodworkers-editors',
     NS_WRITERS                  => 'writers-editors',
     NS_ART_FRAMING              => 'art-framing-editors',
     NS_PRISON_OUTREACH          => 'prison-outreach-editors',
     NS_FOOD_INDEPENDENCE        => 'food-independence-editors',
     NS_VISUAL_ARTS              => 'visual-arts-editors',
     NS_DARKROOM_AND_PHOTOGRAPHY => 'darkroom-and-photography-editors',
     ];

 foreach ( $customNamespaces as $ns => $group ) {
     $wgNamespacePermissionLockdown[$ns]['edit']   = [ $group, 'sysop' ];
     $wgNamespacePermissionLockdown[$ns]['create'] = [ $group, 'sysop' ];
     $wgNamespacePermissionLockdown[$ns]['move']   = [ $group, 'sysop' ];
     }

# Ensure edit is not in the default permissions for non-logged-in users
     $wgGroupPermissions['*']['edit'] = false;
     $wgGroupPermissions['user']['edit'] = false; # editing requires group membership
     $wgGroupPermissions['sysop']['edit'] = true;
