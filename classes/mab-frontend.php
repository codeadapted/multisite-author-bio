<?php

class MAB_Frontend {

	/**
	 * __construct
	 *
	 * @param   void
	 * @return  void
	 */
	public function __construct() {

		add_filter( 'get_the_author_user_description', array( $this, 'mab_author_description_filter' ) );

	}

	/**
	* mab_author_description_filter
	*
	* Override standard user bio if translation exists
	*
	* @param   string $value Standard user bio
	* @return  string Either the standard user bio or the translated one
	**/
	public function mab_author_description_filter( $value ) {

		$value = sanitize_text_field( $value );

		$site_slug = explode( '.', home_url() )[0];
		$site_slug = str_replace( array( 'http://', 'https://' ), '', sanitize_text_field( $site_slug ) );
		$post_id = get_the_ID();
		$user_id = get_post_field( 'post_author', $post_id );
		$override_description = get_user_meta( $user_id, 'mab_profile_bio_' . sanitize_text_field( $site_slug ), true );
		if( isset( $override_description ) && !empty( $override_description ) ) {
			return esc_textarea( $override_description );
		} else {
			return esc_textarea( $value );
		}

	}

}
