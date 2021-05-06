<?php
/**
 * Functions in the namespace scope
 *
 * @package BP_Members_Invitations_Limiter
 */
namespace BP_Members_Invitations_Limiter;

/**
 * Filter the bp_user_can value to determine what the user can do in the members component.
 *
 * @since 1.0.0
 *
 * @param bool   $retval     Whether or not the current user has the capability.
 * @param int    $user_id
 * @param string $capability The capability being checked for.
 * @param int    $site_id    Site ID. Defaults to the BP root blog.
 * @param array  $args       Array of extra arguments passed.
 *
 * @return bool
 */
function user_can_filter( $retval, $user_id, $capability, $site_id, $args = array() ) {

	switch ( $capability ) {
		case 'bp_members_send_invitation':
			// Only act if BuddyPress Core thinks the user can send invites.
			if ( true === $retval ) {
				$limits = get_enabled_limit_class_objects();
				foreach ( $limits as $limit_id => $limit_obj ) {
					$allowed   = $limit_obj->permissions_check( $user_id );
					if ( false === $allowed ) {
						$retval = false;
						break;
					}
				}
			}
			break;

		case 'bp_members_receive_invitation':
			break;

		case 'bp_members_invitations_view_screens':
			$retval = bp_get_members_invitations_allowed();
			break;

		case 'bp_members_invitations_view_send_screen':
			$retval = bp_get_members_invitations_allowed() && bp_user_can( $user_id, 'bp_members_invitations_view_screens' );
			break;
	}

	return $retval;
}
add_filter( 'bp_user_can', __NAMESPACE__ . '\user_can_filter', 50, 5 );

// Filter messages
function members_invitations_form_access_restricted_filter( $message ) {

	if ( ! bp_current_user_can( 'bp_members_send_invitation' ) ) {
		$limits = get_enabled_limit_class_objects();
		foreach ( $limits as $limit_id => $limit_obj ) {
			if ( false === $limit_obj->permissions_check( bp_current_user_id() ) ) {
				$message .= $limit_obj->error_message();
				break;
			}
		}
	}

	return $message;
}
add_filter( 'members_invitations_form_access_restricted', __NAMESPACE__ . '\members_invitations_form_access_restricted_filter', 50);
