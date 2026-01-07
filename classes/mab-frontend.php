<?php

class MAB_Frontend {

	/**
	 * Constructor to initialize hooks.
	 *
	 * @param   void
	 * @return  void
	 */
	public function __construct() {

		// Hook into the author bio filter with priority 10 and 2 accepted arguments
		add_filter( 'get_the_author_user_description', array( $this, 'mab_author_description_filter' ), 10, 2 );

	}

	/**
	 * Override standard user bio if translation exists for the current site.
	 * 
	 * @param   string $bio     The standard user bio.
	 * @param   int    $user_id The user ID (passed by the filter).
	 * @return  string Either the standard user bio or the translated one for the multisite.
	 */
	public function mab_author_description_filter( $bio, $user_id = 0 ) {

		// Get current site's host
		$site_slug = $this->mab_get_current_site_slug();

		// Get user ID from filter parameter, fallback to post author if not provided
		if ( empty( $user_id ) ) {
			$user_id = get_post_field( 'post_author', get_the_ID() );
		}

		// If we still don't have a user ID, return original bio
		if ( empty( $user_id ) ) {
			return $bio;
		}

		// Get the user's bio variation for the current site
		$bio_variation = get_user_meta( $user_id, 'mab_profile_bio_' . $site_slug, true );

		// Return the bio variation if it exists, otherwise return the original bio
		if ( ! empty( $bio_variation ) ) {
			return esc_textarea( $bio_variation );
		} 

		// Return the original bio if no variation exists
		return esc_textarea( $bio );

	}

	/**
	 * Retrieve the current site's slug (hostname).
	 * 
	 * @return string The sanitized site slug (hostname).
	 */
	private function mab_get_current_site_slug() {

		// Get the site URL and parse it using wp_parse_url for consistency with WordPress
		$site_url = get_site_url();
		$parsed_url = wp_parse_url( $site_url );
		
		$domain = isset( $parsed_url['host'] ) ? $parsed_url['host'] : '';
		$path = isset( $parsed_url['path'] ) ? trim( $parsed_url['path'], '/' ) : '';
	
		if ( !empty( $path ) ) {

			// For path-based multisites (e.g., testsite.com/es)
			$slug = sanitize_title( $path );

		} else {

			// For domain-based multisites (e.g., michaelbox.net or es.testsite.com)
			$parts = explode( '.', $domain );
			$slug = (count( $parts ) > 2) ? sanitize_title( $parts[0] ) : sanitize_title( $parts[0] );

		}
		
		// Return the slug
		return $slug;

	}
	

}
