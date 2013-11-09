<?php
/**
 * Baskonfiguration f�r WordPress.
 *
 * Denna fil inneh�ller f�ljande konfigurationer: Inst�llningar f�r MySQL,
 * Tabellprefix, S�kerhetsnycklar, WordPress-spr�k, och ABSPATH.
 * Mer information p� {@link http://codex.wordpress.org/Editing_wp-config.php 
 * Editing wp-config.php}. MySQL-uppgifter f�r du fr�n ditt webbhotell.
 *
 * Denna fil anv�nds av wp-config.php-genereringsskript under installationen.
 * Du beh�ver inte anv�nda webbplatsen, du kan kopiera denna fil direkt till
 * "wp-config.php" och fylla i v�rdena.
 *
 * @package WordPress
 */

// ** MySQL-inst�llningar - MySQL-uppgifter f�r du fr�n ditt webbhotell ** //
/** Namnet p� databasen du vill anv�nda f�r WordPress */
define('DB_NAME', 'lyxbling_wp');

/** MySQL-databasens anv�ndarnamn */
define('DB_USER', 'lyxbling_mats');

/** MySQL-databasens l�senord */
define('DB_PASSWORD', 'synover70');

/** MySQL-server */
define('DB_HOST', 'localhost');

/** Teckenkodning f�r tabellerna i databasen. */
define('DB_CHARSET', 'utf8');

/** Kollationeringstyp f�r databasen. �ndra inte om du �r os�ker. */
define('DB_COLLATE', '');

/**#@+
 * Unika autentiseringsnycklar och salter.
 *
 * �ndra dessa till unika fraser!
 * Du kan generera nycklar med {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * Du kan n�r som helst �ndra dessa nycklar f�r att g�ra aktiva cookies obrukbara, vilket tvingar alla anv�ndare att logga in p� nytt.
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
 * Tabellprefix f�r WordPress Databasen.
 *
 * Du kan ha flera installationer i samma databas om du ger varje installation ett unikt
 * prefix. Endast siffror, bokst�ver och understreck!
 */
$table_prefix  = 'wp_';

/**
 * WordPress-spr�k, f�rinst�llt f�r svenska.
 *
 * Du kan �ndra detta f�r att �ndra spr�k f�r WordPress.  En motsvarande .mo-fil
 * f�r det valda spr�ket m�ste finnas i wp-content/languages. Exempel, l�gg till
 * sv_SE.mo i wp-content/languages och ange WPLANG till 'sv_SE' f�r att f� sidan
 * p� svenska.
 */
define('WPLANG', 'sv_SE');

/** 
 * F�r utvecklare: WordPress fels�kningsl�ge. 
 * 
 * �ndra detta till true f�r att aktivera meddelanden under utveckling. 
 * Det �r rekommderat att man som till�ggsskapare och temaskapare anv�nder WP_DEBUG 
 * i sin utvecklingsmilj�. 
 */ 
define('WP_DEBUG', false);

/* Det var allt, sluta redigera h�r! Blogga p�. */

/** Absoluta s�kv�g till WordPress-katalogen. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Anger WordPress-v�rden och inkluderade filer. */
require_once(ABSPATH . 'wp-settings.php');