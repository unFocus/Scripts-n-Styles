<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wordpress');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'wp');

/** MySQL hostname */
define('DB_HOST', 'mysql');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'aN.1K7nSW/q6 1ChlzB0Gi,)n&Bcc7aLrSlH=~`*P`oGw$+h>qoJf1e($$R},S{w');
define('SECURE_AUTH_KEY',  '|: IPS7-+YK*?BU]W Sq4ie;l9(ptz)6%9slo-x_CEPWm925^5|ar[v88;IHi_JX');
define('LOGGED_IN_KEY',    'eNyGfEW*|86XpWLTDe*ukyH=YFpCh 1|Ea74)<v.h|q{Hb(2vP s=/vx-C[zK w7');
define('NONCE_KEY',        '8Q3pedi]siG}(|4!3)zx/9:Gz{IydXXz2O`7`f2c<M+s/q._JW!Cpm]z4P5av6bA');
define('AUTH_SALT',        '^VBc|_<uxOsNz}4U(Sf^e`6YGv7`EMoW@w~Csr+49mM[1:P+/noviqUd~_iYh{q8');
define('SECURE_AUTH_SALT', 'H0$^-xHq>J]~ceqppQ+ZW8NI;lj-+Q-# TM~j^o7+e@L8KG|x_3=hE/?M8JMOG}3');
define('LOGGED_IN_SALT',   '`qraxcc<|vYc;>Fkas2zTp+%rk!:ByL*q,xaEPO(qQtU1w|d8vp)f~(Y{MrL#kl~');
define('NONCE_SALT',       'p]SK3)~UU&qT:?|~B4@d.86-gfU(5 0 B+o,5,?7,Khg]./0Y$n_!T;($;<TQ+fb');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', true);
if (WP_DEBUG) {
	define( 'SAVEQUERIES', true );
	define( 'WP_DEBUG_LOG', true );
}

if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
	$_SERVER['HTTPS'] = 'on';
}
/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
