<?php
/**
 * BP_Members_Invitations_Limiter Base Class.
 *
 * @package BP_Members_Invitations_Limiter
 * @since 1.0.0
 */
namespace BP_Members_Invitations_Limiter;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * BP Invitations class.
 *
 * Extend it to manage your class's invitations.
 * Your extension class, must, at a minimum, provide the
 * permissions_check() method.
 *
 * @since 5.0.0
 */
abstract class Invite_Limiter_Limit {

	/**
	 * Rule ID.
	 *
	 * @since 1.0.0
	 * @var string ID of rule.
	 */
	public $id;

	/**
	 * Rule name.
	 *
	 * @since 1.0.0
	 * @var string Rule name.
	 */
	public $name;

	/**
	 * Rule description.
	 *
	 * @since 1.0.0
	 * @var string Rule description.
	 */
	public $description;

	/**
	 * Order in which to display admin form rule and process permissions.
	 *
	 * @since 1.0.0
	 * @var int Rule order position.
	 */
	public $rule_order;

	/**
	 * Whether this limit is enabled.
	 *
	 * @since 1.0.0
	 * @var BP_XProfile_ProfileData Rule data for user ID.
	 */
	public $is_enabled;

	/**
	 * Initialize limit.
	 *
	 * @since 1.0.0
	 *
	 */
	public function __construct() {
		$enabled_rules = get_enabled_limit_ids();
		$this->is_enabled = ( in_array( $this->id, $enabled_rules, true ) );

		/**
		 * Fires when the Invite_Limiter_Limit object has been constructed.
		 *
		 * @since 1.0.0
		 *
		 * @param Invite_Limiter_Limit $this The limit object.
		 */
		do_action( 'members_invites_limiter_limit', $this );
	}

	/**
	 * Add any hooks that need to be added at plugin load.
	 *
	 * @since 1.0.0
	 */
	public function add_hooks() {
		add_action( 'members_invitations_limiter_add_settings', array( $this, 'add_custom_settings' ) );
	}

	/**
	 * Should this limit be enabled?
	 *
	 * @since 1.0.0
	 */
	public function admin_form_field_enable() {
		$input_id = esc_attr( 'members_invites_limiter_rules_enabled_' . $this->id );
		?>
		<label for="<?php echo $input_id; ?>" style="display: block;"><input type="checkbox" id="<?php echo $input_id; ?>" name="members_invites_limiter_rules_enabled[<?php echo $this->id; ?>]" <?php checked( $this->is_enabled ); ?> value=1> <?php echo wp_kses_post( $this->description ); ?> </label>
		<?php

	}

	/**
	 * Fetch the option ID for the stored custom setting.
	 *
	 * @since 1.0.0
	 */
	public function get_custom_setting_id() {
		return esc_attr( 'members_invites_limiter_rules_' . $this->id );
	}

	/**
	 * Register the custom settings this limit may require.
	 *
	 * @since 1.0.0
	 */
	public function add_custom_settings() {}

	/**
	 * Render the custom custom settings inputs this limit may require.
	 *
	 * @since 1.0.0
	 *
	 * @return string HTML for the form inputs.
	 */
	public function render_custom_settings() {}

	/**
	 * Get the "extra" custom settings this limit may require.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed Stored value.
	 */
	public function get_custom_settings() {
		$option_id  = 'members_invites_limiter_rules_' . $this->id;
		$saved_rule = get_option( $option_id );
		return $saved_rule;
	}

	/**
	 * This is the logic to check whether the user can send invitations.
	 * All extension classes must provide this logic.
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id The user ID we're checking.
	 * @return bool True when allowed, false when denied.
	 */
	abstract public function permissions_check( $user_id = 0 );

	/**
	 * Provide a custom error message that explains why the user cannot send invites.
	 *
	 * @since 1.0.0
	 *
	 * @return string The error message to show to the user.
	 */
	public function error_message() {
		return __( 'You are not allowed to send invitations.', 'bp-members-invitations-limiter' );
	}

}
