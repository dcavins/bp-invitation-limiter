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
class User_Has_Minimum_Membership_Time extends Invite_Limiter_Limit {

	/**
	 * Rule ID.
	 *
	 * @since 1.0.0
	 * @var string ID of rule.
	 */
	public $id = 'user_has_minimum_membership_time';

	/**
	 * Rule name.
	 *
	 * @since 1.0.0
	 * @var string Rule name.
	 */
	public $name = 'User has belonged to the site for some period of time.';

	/**
	 * Rule description.
	 *
	 * @since 1.0.0
	 * @var string Rule description.
	 */
	public $description = 'Allow only users that have belonged to the site for some period of time to send invites.';

	/**
	 * Order in which to display admin form rule and process permissions.
	 *
	 * @since 1.0.0
	 * @var int Rule order position.
	 */
	public $rule_order = 50;

	/**
	 * This is the logic to check whether the user can send invitations.
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id The user ID we're checking.
	 * @return bool True when allowed, false when denied.
	 */
	public function permissions_check( $user_id = 0 ) {
		if ( ! $user_id ) {
			return false;
		}
		$limit    = intval( $this->get_custom_settings() );
		$userdata = get_userdata( $user_id );

		if ( ! $userdata ) {
			return false;
		}

		$date_now        = new \DateTime();
		$date_registered = new \DateTime( $userdata->user_registered );

		// Add the required offset.
		$cushion = "+{$limit} hours";
		$date_registered->modify( $cushion );
		return $date_now > $date_registered;
	}

	/**
	 * Register the custom settings this limit may require.
	 *
	 * @since 1.0.0
	 */
	public function add_custom_settings() {
		register_setting( 'bp-members-invitations-limiter', $this->get_custom_setting_id(), 'absint' );
	}

	/**
	 * Render the custom custom settings inputs this limit may require.
	 *
	 * @since 1.0.0
	 *
	 * @return string HTML for the form inputs.
	 */
	public function render_custom_settings() {
		$limit      = $this->get_custom_settings();
		$input_name = $this->get_custom_setting_id();
		?>
		<label for="<?php echo $input_name; ?>"><?php esc_html_e( 'How long, in hours, must a member belong to your site before being allowed to send invitations?', 'bp-members-invitations-limiter' ); ?> <input id="<?php echo $input_name; ?>" name="<?php echo $input_name; ?>" value="<?php echo $limit; ?>" type="number" step="1"></label>
		<p class="description"><?php _e( 'For example, one day is 24 hours. One week is 168 hours. Four weeks is 672 hours.', 'bp-members-invitations-limiter' ); ?></p>
		<?php
	}

	/**
	 * Provide a custom error message that explains why the user cannot send invites.
	 *
	 * @since 1.0.0
	 *
	 * @return string The error message to show to the user.
	 */
	public function error_message() {
		return __( 'You must belong to the site for a while longer before you can send invitations.', 'bp-members-invitations-limiter' );
	}

}
