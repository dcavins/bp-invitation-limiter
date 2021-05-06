<?php
/**
 * @package BP_Members_Invitations_Limiter
 * @wordpress-plugin
 * Plugin Name:       BP Members Invitations Limiter
 * Version:           1.0.0
 * Description:       Adds filters for restricting if a user can send BuddyPress site membership invitations.
 * Author:            dcavins
 * Text Domain:       bp-members-invitations-limiter
 * Domain Path:       /languages
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * GitHub Plugin URI: https://github.com/dcavins/bp-members-invitations-limiter
 * @copyright 2021 dcavins
 */

namespace BP_Members_Invitations_Limiter;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/
function init() {
	$basepath = plugin_dir_path( __FILE__ );

	require_once $basepath . 'functions.php';
	require_once $basepath . 'filters.php';

	// Include limit classes
	foreach ( glob( $basepath . "limits/*.php" ) as $filename ) {
		include $filename;
	}
	$limits = get_all_limit_class_objects();
	foreach ( $limits as $limit_name => $limit_obj ) {
		$limit_obj->add_hooks();
	}

	// Admin and dashboard functionality
	if ( is_admin() && ! wp_doing_ajax() ) {
		require_once( $basepath . 'admin/class-invite-limiter-admin.php' );
		$admin_class = new Invite_Limiter_Admin();
		$admin_class->hook_actions();
	}
}
add_action( 'bp_init', __NAMESPACE__ . '\init' );

/**
 * Activation tasks.
 */
function activation() {
}
register_activation_hook( __FILE__, __NAMESPACE__ . '\activation' );

/**
 * Deactivation tasks.
 */
function deactivation() {
}
register_deactivation_hook( __FILE__, __NAMESPACE__ . '\deactivation' );

