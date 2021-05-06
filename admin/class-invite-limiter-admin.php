<?php
/**
 * This class should be used to work with the
 * administrative side of the WordPress site.
 *
 * @package BP_Members_Invitations_Limiter
 */
namespace BP_Members_Invitations_Limiter;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Invite_Limiter_Admin {

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 *
	 * Unique identifier for your plugin.
	 *
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'bp-members-invitations-limiter';

	/**
	 *
	 * The current version of the plugin.
	 *
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $version = '1.0.0';

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	public function __construct() {

		/*
		 * @TODO :
		 *
		 * - Uncomment following lines if the admin class should only be available for super admins
		 */
		/* if( ! is_super_admin() ) {
			return;
		} */


	}

	/**
	 * Hook WordPress filters and actions here.
	 *
	 * @since     1.0.0
	 */
	public function hook_actions() {
		// Load admin style sheet and JavaScript.
		// add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		// add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Add settings
		add_action( 'admin_init', array( $this, 'settings_init' ) );

		add_action( 'bp_admin_settings_after_members_invitations', array( $this, 'add_link_to_additional_invite_settings' ) );

		// Add an action link pointing to the options page.
		// $plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_slug . '.php' );
		// add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			// wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), $this->version );
		}

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
		}

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'Member Invites Limiter', 'bp-members-invitations-limiter' ),
			__( 'Member Invites Limiter', 'bp-members-invitations-limiter' ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {
		?>
		<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

		<form action="<?php echo admin_url( 'options.php' ) ?>" method='post'>

			<?php
			settings_fields( $this->plugin_slug );
			do_settings_sections( $this->plugin_slug );
			submit_button();
			?>

		</form>
		<?php
	}

	/**
	 * Add settings to BP options screen.
	 *
	 * @since    1.0.0
	 */
	public function add_link_to_additional_invite_settings() {
		echo sprintf(
			'<p class="description"><a href="%s" target="_blank">%s</a></div>',
			admin_url( 'options-general.php?page=' . $this->plugin_slug ),
			__( 'Configure additional invitation limits for users.' ),
		);
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', 'bp-members-invitations-limiter' ) . '</a>'
			),
			$links
		);

	}

	/**
	 * Register the settings and set up the sections and rules for the
	 * global settings screen.
	 *
	 * @since    1.0.0
	 */
	public function settings_init() {

		// Set report output format options.
		add_settings_section(
			'members_invitations_limiter_options',
			__( 'Set limits on which users can invite people to join the site.', 'bp-members-invitations-limiter' ),
			array( $this, 'section_null_callback' ),
			$this->plugin_slug
		);

		register_setting( $this->plugin_slug, 'members_invites_limiter_rules_enabled', array( $this, 'sanitize_enabled_rules' ) );
		add_settings_field(
			'members_invites_limiter_rules_enabled',
			__( 'Select which limits to enable.', 'bp-members-invitations-limiter' ),
			array( $this, 'render_enabled_limits' ),
			$this->plugin_slug,
			'members_invitations_limiter_options'
		);

		do_action( 'members_invitations_limiter_add_settings' );
	}

	/**
	 * Provide a section description for the global settings screen.
	 *
	 * @since    1.0.0
	 */
	public function section_null_callback() {}

	/**
	 * Add form inputs for the plugin option form.
	 *
	 * @since    1.0.0
	 */
	public function render_enabled_limits() {
		$limits = get_all_limit_class_objects();
		?>
		<ul>
			<?php
			foreach ( $limits as $limit_name => $limit_obj ) {
				?>
				<li style="margin-bottom:1em;">
					<?php
					echo $limit_obj->admin_form_field_enable();
					echo $limit_obj->render_custom_settings();
					?>
				</li>
				<?php
			}
			?>
		</ul>
		<?php
	}

	/**
	 * Add form inputs for the plugin option form.
	 *
	 * @since    1.0.0
	 */
	public function render_cares_maplayer_checker_include_time_limit() {
		$limit = get_error_email_include_time_limit();
		?>
		<label>In the report email, include "OK - 200" responses taking longer than this many seconds. <input name="cares_maplayer_checker_include_time_limit" value="<?php echo $limit; ?>" type="number" step="0.001"></label>
		<?php
	}

	/**
	 * Sanitize the input.
	 *
	 * @since    1.0.0
	 */
	public function sanitize_enabled_rules( $value ) {
		$limit_ids = array();
		$limits = get_limit_classes();
		foreach ( $limits as $limit_name => $limit_class ) {
			$limit_obj = new $limit_class();
			$limit_ids[] = $limit_obj->id;
		}

		if ( ! is_array( $value ) ) {
			$value = preg_split( '/[,\s]+/', $value );
		}

		$cleaned_values = array();
		// Ignore unknown/bad values.
		foreach ( $value as $limit_id => $enabled ) {
			if ( in_array( $limit_id, $limit_ids, true ) && ! empty( $enabled ) ) {
				$cleaned_values[ $limit_id ] = true;
			}
		}
		// Put em in order.
		ksort( $cleaned_values );
		return $cleaned_values;
	}

}
