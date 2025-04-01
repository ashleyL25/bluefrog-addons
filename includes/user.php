<?php
/**
 * Add more data for user
 */

/**
 * Add more contact method for user
 *
 * @param array $methods
 *
 * @return array
 */
function bluefrog_addons_user_contact_methods( $methods ) {
	$methods['facebook']  = esc_html__( 'Facebook' );
	$methods['twitter']   = esc_html__( 'Twitter' );
	$methods['pinterest'] = esc_html__( 'Pinterest' );
	$methods['instagram'] = esc_html__( 'Instagram' );

	return $methods;
}

add_filter( 'user_contactmethods', 'bluefrog_addons_user_contact_methods' );