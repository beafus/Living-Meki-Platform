<?php
/*
Plugin Name: WP Email Template LITE
Plugin URI: http://a3rev.com/shop/wp-email-template/
Description: This plugin automatically adds a professional, responsive, customizable, email browser optimized HTML template for all WordPress and WordPress plugin generated emails that are sent from your site to customers and admins. Works with any WordPress plugin including the e-commerce plugins WooCommerce and WP e-Commerce.
Version: 1.4.1
Author: A3 Revolution
Author URI: http://www.a3rev.com/
License: This software is under commercial license and copyright to A3 Revolution Software Development team

	WP Email Template plugin
	Copyright© 2011 A3 Revolution Software Development team

	A3 Revolution Software Development team
	admin@a3rev.com
	PO Box 1170
	Gympie 4570
	QLD Australia
*/
?>
<?php
define('WP_EMAIL_TEMPLATE_FILE_PATH', dirname(__FILE__));
define('WP_EMAIL_TEMPLATE_DIR_NAME', basename(WP_EMAIL_TEMPLATE_FILE_PATH));
define('WP_EMAIL_TEMPLATE_FOLDER', dirname(plugin_basename(__FILE__)));
define('WP_EMAIL_TEMPLATE_URL', untrailingslashit(plugins_url('/', __FILE__)));
define('WP_EMAIL_TEMPLATE_DIR', WP_PLUGIN_DIR . '/' . WP_EMAIL_TEMPLATE_FOLDER);
define('WP_EMAIL_TEMPLATE_NAME', plugin_basename(__FILE__));
define('WP_EMAIL_TEMPLATE_IMAGES_URL', WP_EMAIL_TEMPLATE_URL . '/assets/images');
define('WP_EMAIL_TEMPLATE_JS_URL', WP_EMAIL_TEMPLATE_URL . '/assets/js');
define('WP_EMAIL_TEMPLATE_CSS_URL', WP_EMAIL_TEMPLATE_URL . '/assets/css');
if (!defined("WP_EMAIL_TEMPLATE_AUTHOR_URI")) define("WP_EMAIL_TEMPLATE_AUTHOR_URI", "http://a3rev.com/shop/wp-email-template/");
if (!defined("WP_EMAIL_TEMPLATE_DOCS_URI")) define("WP_EMAIL_TEMPLATE_DOCS_URI", "http://docs.a3rev.com/user-guides/plugins-extensions/wordpress/wp-email-template/");

include ('admin/admin-ui.php');
include ('admin/admin-interface.php');

include ('admin/admin-pages/admin-email-template-page.php');
include ('admin/admin-pages/send-wp-emails-page.php');

include ('admin/admin-init.php');

include ('classes/class-send-wp-email-functions.php');
include ('classes/class-email-functions.php');
include ('classes/class-email-hook.php');
include ('classes/class-email-exclude-subject-data.php');
include ('admin/email-init.php');

/**
 * Call when the plugin is activated and deactivated
 */
register_activation_hook(__FILE__, 'wp_email_template_install');

?>