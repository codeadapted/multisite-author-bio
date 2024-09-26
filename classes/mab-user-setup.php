<?php

class MAB_UserSetup {

	/**
	* Constructor to initialize hooks.
	*
	* @param   void
	* @return  void
	**/
	public function __construct() {

		// Ensure setup only for WP Admin
		if ( is_admin() ) {

			// Add AJAX action to retrieve bio variation
			add_action( 'wp_ajax_mab_get_bio_variation', array( $this, 'mab_get_bio_variation' ) );

			// Add custom profile fields to the user profile and edit pages
			add_action( 'show_user_profile', array( $this, 'mab_custom_user_profile_fields' ) );
			add_action( 'edit_user_profile', array( $this, 'mab_custom_user_profile_fields' ) );

			// Save custom profile fields when registering or updating a user
			add_action( 'user_register', array( $this, 'mab_save_custom_user_profile_fields' ) );
			add_action( 'profile_update', array( $this, 'mab_save_custom_user_profile_fields' ) );

			// Enqueue styles and scripts for user profile
			add_action( 'admin_enqueue_scripts', array( $this, 'mab_user_screen_enqueue' ) );

		}

	}

	/**
	 * Register and enqueue admin stylesheet & scripts.
	 * Only enqueues on the user profile/edit pages.
	 *
	 * @param   string $page The current admin page being viewed.
	 * @return  void
	 */
	public function mab_user_screen_enqueue( $page ) {

		// Only enqueue scripts and styles on the settings page
        if ( strpos( mab()->plugin()->mab_get_current_admin_url(), mab()->plugin()->mab_get_admin_url() ) !== false ) {

			// Enqueue custom stylesheet for user setup
			wp_enqueue_style( 'mab_user_stylesheet', MAB_PLUGIN_DIR . 'admin/css/user-setup.css', array(), '1.0.0' );

			// Enqueue the main admin script (dependent on jQuery)
			wp_enqueue_script( 'mab_user_script', MAB_PLUGIN_DIR . 'admin/js/user-setup.js', array( 'jquery' ), '1.0.0', true );

			// Localize the script to pass AJAX URL and nonce to the JavaScript file
			wp_localize_script( 'mab_user_script', 'mab_user_obj',
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' ), // The admin AJAX URL
					'mab_nonce' => $nonce // The nonce for AJAX security
				)
			);

		}

	}

	/**
	 * Retrieve user bio variation for the selected site via AJAX.
	 * Returns the bio variation for the specified site.
	 *
	 * @param   void
	 * @return  mixed The user bio variation text
	 */
	public function mab_get_bio_variation() {

		// Validate the nonce
        if ( ! mab()->plugin()->mab_validate_nonce() ) {
            return false;
        }

		// Sanitize inputs
		$site_name = sanitize_text_field( wp_slash( $_GET['site_name'] ) );
		$user_id = absint( $_GET['user_id'] );

		// Retrieve user meta for the selected site
		$bio_variation = get_user_meta( $user_id, 'mab_profile_bio_' . $site_name, true );

		// Send the bio variation via JSON if it exists, otherwise return false
		if ( ! empty( $bio_variation ) ) {
			wp_send_json_success( $bio_variation );
		} else {
			wp_send_json_error( array( 'message' => __( 'No bio variation found.', 'multisite-author-bio' ) ) );
		}

	}

	/**
	 * Retrieve a list of sites in the multisite network.
	 * Generates a dropdown of sites for the profile bio variation.
	 *
	 * @param   void
	 * @return  string|false The HTML options for the sites or false if none found.
	 */
	public function mab_get_sites() {

		// Get sites data
		$main_site_id = get_main_site_id();
		$current_site_id = get_current_blog_id();
		$options = '';
		$sites = get_sites();

		// Loop through each site and generate dropdown options
		foreach ( $sites as $site ) {
			if ( $site->blog_id != $main_site_id ) {
				$site_slug = parse_url( $site->siteurl, PHP_URL_HOST ); // Get the site slug
				if ( $site_slug ) {
					$options .= '<option value="' . esc_html( $site_slug ) . '"' . selected( $current_site_id, $site->blog_id, false ) . '>' . strtoupper( esc_html( $site_slug ) ) . '</option>';
				}
			}
		}

		// Return
		return ! empty( $options ) ? $options : false;

	}

	/**
	 * Add custom bio variation fields to the user profile edit page.
	 *
	 * @param   WP_User $user The user object.
	 * @return  void
	 */
	public function mab_custom_user_profile_fields( $user ) {

		// Load text domain for translations
		mab()->plugin()->mab_load_plugin_textdomain();

		// Get user id and sites
		$user_id = absint( $user->ID );
		$variations = $this->mab_get_sites();

		if( function_exists('is_multisite') && is_multisite() ) {
		?>

			<h3><?php esc_html_e( 'Multisite Author Bio', 'multisite-author-bio' ); ?></h3>
			<p><em><?php esc_html_e( 'Select the network site you wish to update/view the user bio for.', 'multisite-author-bio' ); ?></em></p>
			<div class="mab-form-container" data-user="<?php echo esc_attr( $user_id ); ?>">
				<div class="mab-form-wrapper">
					<select class="mab-select-bio-variation" name="mabSelectBioVariation">
						<option value=""><?php esc_html_e( 'Select Site', 'multisite-author-bio' ); ?></option>
						<?php echo wp_kses( $variations, array( 'option' => array( 'value' => array(), 'selected' => array() ) ) ); ?>
					</select>
					<p class="mab-bio-variation-label hidden"><em><?php esc_html_e( 'Below is the user bio variation for the site selected above.', 'multisite-author-bio' ); ?></em></p>
					<textarea rows="4" cols="60" placeholder="<?php esc_html_e( 'Insert profile bio variation', 'multisite-author-bio' ); ?>" class="mab-bio-variation-text hidden" name="mabBioVariation" id="mab-bio-variation-text"></textarea>
				</div>
			</div>

		<?php
		} else { ?>

			<h3><?php esc_html_e( 'Multisite Author Bio', 'multisite-author-bio' ); ?></h3>
			<div class="mab-form-container" data-user="<?php echo esc_attr( $user_id ); ?>">
				<div class="mab-form-wrapper">
					<?php esc_html_e( 'Multisite is not enabled.', 'multisite-author-bio' ); ?>
				</div>
			</div>

		<?php
		}

	}

	/**
	 * Save custom bio variation fields to user meta.
	 *
	 * @param   int $user_id The ID of the user being saved.
	 * @return  void|bool
	 */
	public function mab_save_custom_user_profile_fields( $user_id ) {
		
		// Ensure only administrators can update this field
		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		// Save the bio variation for the selected site
		$bio_variation = sanitize_text_field( wp_slash( $_POST['mabSelectBioVariation'] ) );
		$bio_text = sanitize_textarea_field( wp_slash( $_POST['mabBioVariation'] ) );

		// Update user meta with the bio variation for the selected site
		update_user_meta( $user_id, 'mab_profile_bio_' . $bio_variation, $bio_text );

	}

}
