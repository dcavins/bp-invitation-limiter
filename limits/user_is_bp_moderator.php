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
class User_Is_BP_Moderator extends Invite_Limiter_Limit {

	/**
	 * Rule ID.
	 *
	 * @since 1.0.0
	 * @var string ID of rule.
	 */
	public $id = 'user_is_bp_moderator';

	/**
	 * Rule name.
	 *
	 * @since 1.0.0
	 * @var string Rule name.
	 */
	public $name = 'User can bp_moderate';

	/**
	 * Rule description.
	 *
	 * @since 1.0.0
	 * @var string Rule description.
	 */
	public $description = 'Allow only users with the <code>bp_moderate</code> capability to send invites.';

	/**
	 * Order in which to display admin form rule and process permissions.
	 *
	 * @since 1.0.0
	 * @var int Rule order position.
	 */
	public $rule_order = 20;

	/**
	 * This is the logic to check whether the user can send invitations.
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id The user ID we're checking.
	 * @return bool True when allowed, false when denied.
	 */
	public function permissions_check( $user_id = 0 ) {
		return bp_user_can( $user_id, 'bp_moderate' );
	}

	/**
	 * Provide a custom error message that explains why the user cannot send invites.
	 *
	 * @since 1.0.0
	 *
	 * @return string The error message to show to the user.
	 */
	public function error_message() {
		return __( 'You must be a BP Moderator to send invitations.', 'bp-members-invitations-limiter' );
	}

}
