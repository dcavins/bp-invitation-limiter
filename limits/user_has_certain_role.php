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
 * Invite_Limiter_Limit class.
 *
 * @since 1.0.0
 */
class User_Has_Approved_Role extends Invite_Limiter_Limit {

	/**
	 * Rule ID.
	 *
	 * @since 1.0.0
	 * @var string ID of rule.
	 */
	public $id = 'user_has_approved_role';

	/**
	 * Rule name.
	 *
	 * @since 1.0.0
	 * @var string Rule name.
	 */
	public $name = 'User has one of the approved roles.';

	/**
	 * Rule description.
	 *
	 * @since 1.0.0
	 * @var string Rule description.
	 */
	public $description = 'Allow only users that have certain roles to send invites.';

	/**
	 * Order in which to display admin form rule and process permissions.
	 *
	 * @since 1.0.0
	 * @var int Rule order position.
	 */
	public $rule_order = 10;

	/**
	 * This is the logic to check whether the user can send invitations.
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id The user ID we're checking.
	 * @return bool True when allowed, false when denied.
	 */
	public function permissions_check( $user_id = 0 ) {
		$retval = false;
		if ( ! $user_id ) {
			return $retval;
		}

		$userdata  = get_userdata( $user_id );
		if ( ! isset( $userdata->roles ) || ! is_array( $userdata->roles ) ) {
			return $retval;
		}

		$approved_roles = $this->get_custom_settings();
		foreach ( $userdata->roles as $key => $role_id ) {
			if ( in_array( $role_id,  $approved_roles ) ) {
				$retval = true;
				break;
			}
		}
		return $retval;
	}

	/**
	 * Register the custom settings this limit may require.
	 *
	 * @since 1.0.0
	 */
	public function add_custom_settings() {
		register_setting( 'bp-members-invitations-limiter', $this->get_custom_setting_id(), array( $this, 'filter_saved_roles' ) );
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
		$roles      = get_editable_roles();
		?>
		<fieldset style="margin-left: 1em; margin-top: .5em;">
			<legend><?php esc_html_e( 'Which user roles are allowed to send invitations?', 'bp-members-invitations-limiter' ); ?></legend>
			<?php foreach ( $roles as $id => $role_obj ) {
				?>
				<label for="<?php echo $input_name; ?>-<?php echo $id; ?>"><input id="<?php echo $input_name; ?>-<?php echo $id; ?>" name="<?php echo $input_name; ?>[<?php echo $id; ?>]" value="1" type="checkbox" <?php checked( in_array( $id, $limit, true ) ); ?>> <?php echo $role_obj['name'];?></label>
				<?php
			}
			?>
		</fieldset>
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
		if ( ! function_exists( 'get_editable_roles' ) ) {
			require_once ABSPATH . 'wp-admin/includes/user.php';
		}

		$approved_roles = $this->get_custom_settings();
		$roles          = get_editable_roles();
		$list           = array();
		foreach ( $approved_roles as $role_id ) {
			if ( isset( $roles[ $role_id ][ 'name' ] ) ) {
				$list[] = $roles[ $role_id ][ 'name' ];
			}
		}
		return sprintf(
			__( 'You must have one of the following roles: %s', 'bp-members-invitations-limiter' ),
			implode( ', ', $list )
		);
	}

	/**
	 * Sanitize the saved approved roles and reshape for flat storage.
	 *
	 * @since 1.0.0
	 *
	 * @return array The value to save.
	 */
	public function filter_saved_roles( $value ) {
		if ( ! is_array( $value ) ) {
			$value = preg_split( '/[,\s]+/', $value );
		}
		$filtered = array();
		$roles    = get_editable_roles();
		foreach ( $value as $id => $enabled ) {
			// Must be set to "enabled" and be a valid role id.
			if ( $enabled && array_key_exists( $id, $roles ) ) {
				$filtered[] = $id;
			}
		}

		return $filtered;
	}

}
