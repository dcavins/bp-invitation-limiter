<?php
/**
 * Functions in the namespace scope
 *
 * @package BP_Members_Invitations_Limiter
 */
namespace BP_Members_Invitations_Limiter;

/**
 * Get details of all limits.
 *
 * @since 1.0.0
 *
 * @return array Key/value pairs (limit_id => class name).
 */
function get_limit_classes() {
	$limits = array(
		'user_is_bp_moderator'             => 'User_Is_BP_Moderator',
		'user_has_minimum_membership_time' => 'User_Has_Minimum_Membership_Time',
		'user_has_approved_role'           => 'User_Has_Approved_Role',
	);

	// Prepend the Namespace to the limit classnames.
	foreach ( $limits as $id => $classname ) {
		$limits[ $id ] = __NAMESPACE__ . '\\' . $classname;
	}

	/**
	 * Filters the list of all limits.
	 *
	 * If you've added a custom limit in a plugin, register it with this filter.
	 *
	 * @since 1.0.0
	 *
	 * @param array $limits Array of limit ID/class name pairings.
	 */
	 apply_filters( 'bp_invite_limiter_classes', $limits );

	 return $limits;
}

function get_enabled_limit_ids() {
	$saved_rules   = get_option( 'members_invites_limiter_rules_enabled', array() );
	$enabled_rules = array();
	foreach ( $saved_rules as $key => $value ) {
		if ( $value ) {
			$enabled_rules[] = $key;
		}
	}
	return $enabled_rules;
}

function get_all_limit_class_objects() {
	$all_rules = get_limit_classes();
	$classes   = array();
  	foreach ( $all_rules as $limit_id => $limit_class ) {
		$classes[ $limit_id ] = new $limit_class();
	}
	// Sort them by rule_order
	uasort( $classes, function( $a, $b ) { return $a->rule_order <=> $b->rule_order; } );
	return $classes;
}

function get_enabled_limit_class_objects() {
	$all_rules       = get_limit_classes();
	$enabled_rules   = get_enabled_limit_ids();
	$enabled_classes = array();
  	foreach ( $all_rules as $limit_id => $limit_class ) {
		if ( in_array( $limit_id, $enabled_rules, true ) ) {
			$enabled_classes[ $limit_id ] = new $limit_class();
		}
	}
	// Sort them by rule_order
	uasort( $enabled_classes, function( $a, $b ) { return $a->rule_order <=> $b->rule_order; } );
	return $enabled_classes;
}
