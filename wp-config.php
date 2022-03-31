<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

/*
 * Don't show deprecations
 */
error_reporting( E_ALL ^ E_DEPRECATED );

/**
 * Set root path
 */
$root_path = realpath( __DIR__ );

define( 'ROOTPATH', $root_path );

/**
 * Include the Composer autoload
 */
require_once( $root_path . '/vendor/autoload.php' );

$site_url = 'https://' . $_SERVER['HTTP_HOST'] . '/';

define( 'WP_HOME', $site_url );
define( 'WP_SITEURL', $site_url . 'wp/' );

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress' );

/** Database username */
define( 'DB_USER', 'wordpress' );

/** Database password */
define( 'DB_PASSWORD', 'wordpress' );

/** Database hostname */
define( 'DB_HOST', 'database' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',          'T)TGP21}-zc0Idt72n5gLql5X+nW;V*NgFL:/AoG/xRIARLldA?i}z%8L!R*J#s7' );
define( 'SECURE_AUTH_KEY',   'lV~Vuh>#>=nRu5xvG#rkzrg%adu?II5y~,[SL$p9]^h}at4].VdD+=A%aWdVL2[(' );
define( 'LOGGED_IN_KEY',     'I7MDvtzk/VV_Zp.-p7Xd7MIaSP[kwBE19n.!OzxotrIg&,+%<;?Dm>A-Z^#83#U9' );
define( 'NONCE_KEY',         ':v]/,y+i?U^F6$s!e|PKELju.6YrAMg$zSv)QAT5sVf<-MNUV*-;c^}LeTR=nC:<' );
define( 'AUTH_SALT',         'lBUL`xu>|k)=}Au=N<J+$D:g0t_ow=nDU?:M-nRl=A5M#,nb-$0dp 4/E(5SWYT*' );
define( 'SECURE_AUTH_SALT',  '{pU4^q3rT@`K=}8woD)KhBd1_w_<iawrnD#Fr~}9WbNov3#{f5=CaC_He%f:LjLk' );
define( 'LOGGED_IN_SALT',    '9:yFHO6q_Ur^47-v]SlsXxH/.^BA10Pj$dSam!.;4KM/#24rf<sJY)1)BrN}W}yt' );
define( 'NONCE_SALT',        '9<yN^;E0ES0hJ#;0%WNo!ytB[%M(a#rFOBTB ptm^Q,Ieoctj]Q3PB0Y`)G>_cMP' );
define( 'WP_CACHE_KEY_SALT', '*Lo`I3+}&|F~KM_Bns$r;pLWieq_LckC|{A/u5FA8YKO|GHQh0Pa8 Id~.</lpGa' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );


/* Add any custom values between this line and the "stop editing" line. */

define( 'WP_DISABLE_FATAL_ERROR_HANDLER', true );

/**
 * Disallow on server file edits
 */
define( 'DISALLOW_FILE_EDIT', true );
define( 'DISALLOW_FILE_MODS', true );

/**
 * Force SSL
 */
define( 'FORCE_SSL_ADMIN', true );

/**
 * Limit post revisions
 */
define( 'WP_POST_REVISIONS', 3 );

/*
* Define wp-content directory outside of WordPress core directory
*/
define( 'WP_CONTENT_DIR', dirname( __FILE__ ) . '/wp-content' );
define( 'WP_CONTENT_URL', WP_HOME . '/wp-content' );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

define( 'WEBPATH', dirname( __FILE__ ) );

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
