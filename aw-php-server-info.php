<?php
/**
 * AWEOS PHP Server Info
 *
 * @wordpress-plugin
 * Plugin Name: AWEOS PHP Server Info
 * Plugin URI:  https://developer.wordpress.org/plugins/aweos-php-server-info/
 * Description: AWEOS PHP Server Info is used to get a quick overview on important server and php configurations. Everything is displayed in your admin dashboard.
 * Version:     1.3
 * Author:      AWEOS GmbH
 * Author URI:  https://aweos.de
 * Text Domain: aw_php-server-info
 * License:     GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 */

// protection
if ( !defined( "ABSPATH" ) ) {
	die("Hi there! I'm just a plugin, not much I can do when called directly");
	exit;
};

function awpi_register_activation_hook() {
	if ( version_compare(get_bloginfo( "version" ), "4.5", "<") ) {
		wp_die( "Please update WordPress to use this plugin" );
	}
}

// hooks

function awpi_admin_enqueue_scripts() {
	wp_register_style("awphp_aweos_style", plugin_dir_url( __FILE__ ) . "style.css");
	wp_enqueue_style("awphp_aweos_style");
}

function awpi_wp_dashboard_setup() {
	wp_add_dashboard_widget(
		'awpi-dashboard_overview', // Widget slug (css id). | 228ada
		'AWEOS PHP Server Info', // Title.
		'awpi_overview' // handler
	);
}

awpi_hook();

// helper

function awpi_ini_find($nice, $technical, $endless = false) {
	if ($endless && ini_get($technical) == "0") {
		return $nice . ": <b>" . "Unlimited" . "</b><br>";
	}

	if(ini_get($technical)) {
		return $nice . ": <b>" . ini_get($technical) . "</b><br>";
    } else {
        return $nice . ": Not found <br>";
    }
}

function awpi_hook() {
	register_activation_hook(__FILE__, "awpi_register_activation_hook");
	add_action("admin_enqueue_scripts", "awpi_admin_enqueue_scripts");
	add_action("wp_dashboard_setup", "awpi_wp_dashboard_setup");
}

// main

function awpi_overview() {
	?>

    <p>System: <b><?php echo php_uname('s'); ?></b></p><br>
	<p>PHP Version: <b><?php echo phpversion() ?></b></p><br>

	<p><?php echo awpi_ini_find('Memory Limit', 'memory_limit', true); ?></p>
	<p><?php echo awpi_ini_find('Max Execution Time', 'max_execution_time', true); ?></p>
	<p><?php echo awpi_ini_find('Upload Max Filesize', 'upload_max_filesize', true); ?></p>

	<p>Blog Post Counter: <b><?php echo count(get_posts([
		'posts_per_page' => -1,
		'depth'          => -1,
  		'post_status' => 'any',
    ])); ?></b><br></p>


	<p>All Pages Counter: <b><?php echo count(get_posts([
		'posts_per_page' => -1,
		'depth'          => -1,
		'post_status' => 'any',
  		'post_type' => 'page'
    ])); ?></b><br></p>

    <?php if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) { ?>

    <p>Product Counter: <b>

    	<?php echo count(get_posts([
			'posts_per_page' => -1,
			'depth'          => -1,
	  		'post_status' => 'any',
	  		'post_type' => 'product'
	    ]));

    	?>

    </b></p><br>

    <?php } ?>

    <p>Published Pages Counter: <b><?php echo count(get_pages([
		'depth'          => -1,
		'post_status' => 'publish'
	])); ?>

    </b></p><br><br><br>

    <div id="awpi-info">
    	<p>*The 'Blog Post Counter' does count public and private but not trashed blog posts.</p>
    </div>

	<?php
}
