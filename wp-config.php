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
define('DB_NAME', 'jollyjump');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

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
define('AUTH_KEY',         'gS]q(UVO+&3|ATY:rZw4$z`Z9^3diM/govc)>5*<R=S[U1Ou5zdL w6[tsc/@aG5');
define('SECURE_AUTH_KEY',  '+QJ9?#baLXtS37:_jC7lmgD>4oCp{v2qpm*^MU&FTO6NRUa#1vJ1Z>ufkkxF>[V=');
define('LOGGED_IN_KEY',    'l4H<3?R/lRjwH^wTkq{Ki9qmZ$Mg;jpAayTaqbTu7]PGn-/=h/g!?w|}]40,4-0N');
define('NONCE_KEY',        'aA@ZNgZKg=Nmg^FgXe/@=Wn-N]=]]AgqMQ.+JjVl*+B<mQ]8S<:9~@KV _~rT>ii');
define('AUTH_SALT',        'Yj$,}6DYts5LZq^g{,c%Va;x}(`$S%Z-@ug=-eC.9^2vBEESil*h)rHX}l&^)<!(');
define('SECURE_AUTH_SALT', 'c(J9f1ySfCm2KZup8ZgZBn3Sk>Kf650m}/!]v/15`nCM# ji8w1QV%uPoZwi+@/q');
define('LOGGED_IN_SALT',   '/741wdUFz=}2qgARH}tZ!*Oq:B-K]`Zdx+D(qesRmaj_%O6`&Z1V&/eDZ64@*3:|');
define('NONCE_SALT',       'P:LQsu)vbbwJ!z#nHJK|&gpaMZEL325FhPdCP_|6,CfVbt[89Qlw?fjZz$(0x|IH');

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
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
