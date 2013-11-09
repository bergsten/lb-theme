<?php
/**
 * Baskonfiguration för WordPress.
 *
 * Denna fil innehåller följande konfigurationer: Inställningar för MySQL,
 * Tabellprefix, Säkerhetsnycklar, WordPress-språk, och ABSPATH.
 * Mer information på {@link http://codex.wordpress.org/Editing_wp-config.php 
 * Editing wp-config.php}. MySQL-uppgifter får du från ditt webbhotell.
 *
 * Denna fil används av wp-config.php-genereringsskript under installationen.
 * Du behöver inte använda webbplatsen, du kan kopiera denna fil direkt till
 * "wp-config.php" och fylla i värdena.
 *
 * @package WordPress
 */

// ** MySQL-inställningar - MySQL-uppgifter får du från ditt webbhotell ** //
/** Namnet på databasen du vill använda för WordPress */
define('DB_NAME', 'lyxbling_wp');

/** MySQL-databasens användarnamn */
define('DB_USER', 'lyxbling_mats');

/** MySQL-databasens lösenord */
define('DB_PASSWORD', 'synover70');

/** MySQL-server */
define('DB_HOST', 'localhost');

/** Teckenkodning för tabellerna i databasen. */
define('DB_CHARSET', 'utf8');

/** Kollationeringstyp för databasen. Ändra inte om du är osäker. */
define('DB_COLLATE', '');

/**#@+
 * Unika autentiseringsnycklar och salter.
 *
 * Ändra dessa till unika fraser!
 * Du kan generera nycklar med {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * Du kan när som helst ändra dessa nycklar för att göra aktiva cookies obrukbara, vilket tvingar alla användare att logga in på nytt.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'j1VqWmxOI~lQd#h=>XLTrS8jQU7w|7N~lco1Sp1f^7^ /~e`)F18$^/7V #$.Fb4');
define('SECURE_AUTH_KEY',  'P9[hM<W0J3=+O:gr+mx5lCF@)J:c+UK3ByHAr{6/5_1+|)&*C?-|Nf&`O59e5sTo');
define('LOGGED_IN_KEY',    'HuiCC{6/3D:bJ<1Gy` 9IIq92>=IePY3E?TbW U(LX%(->yW73vQ(_3u?4L(-B-$');
define('NONCE_KEY',        '0ox.%Y{j,(r?9$oKICP![C4WOa(v1<180+q+5^NZB6Msk)}Kambk,z=Nd6SwJh}p');
define('AUTH_SALT',        'XQ}T25>o ${Uvy/DD!._7;w/|8u4a@G%xn%SiAv,nxWt.Pb@V#8^i7l$IJNfM#+7');
define('SECURE_AUTH_SALT', 'Zh._8]EHZs 4A1r;|40kpt%!{Xdx/W7z3+FIs1e!}M0L0&{n;u[;Z_4VRpGvbg%K');
define('LOGGED_IN_SALT',   '^%dllZy21//dB15tf GTz.1W0HLGLD[=oYVb9 S&eRCTkVm06:$Ts9L:nnhbKukP');
define('NONCE_SALT',       '8H0b=r/Qgus22`_TYtwwv~Wt>F1gR-|H?o~HbVC3e~dj`eTrbGW+chP]5R8p_1{t');

/**#@-*/

/**
 * Tabellprefix för WordPress Databasen.
 *
 * Du kan ha flera installationer i samma databas om du ger varje installation ett unikt
 * prefix. Endast siffror, bokstäver och understreck!
 */
$table_prefix  = 'wp_';

/**
 * WordPress-språk, förinställt för svenska.
 *
 * Du kan ändra detta för att ändra språk för WordPress.  En motsvarande .mo-fil
 * för det valda språket måste finnas i wp-content/languages. Exempel, lägg till
 * sv_SE.mo i wp-content/languages och ange WPLANG till 'sv_SE' för att få sidan
 * på svenska.
 */
define('WPLANG', 'sv_SE');

/** 
 * För utvecklare: WordPress felsökningsläge. 
 * 
 * Ändra detta till true för att aktivera meddelanden under utveckling. 
 * Det är rekommderat att man som tilläggsskapare och temaskapare använder WP_DEBUG 
 * i sin utvecklingsmiljö. 
 */ 
define('WP_DEBUG', false);

/* Det var allt, sluta redigera här! Blogga på. */

/** Absoluta sökväg till WordPress-katalogen. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Anger WordPress-värden och inkluderade filer. */
require_once(ABSPATH . 'wp-settings.php');