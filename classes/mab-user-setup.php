<?php

class MAB_UserSetup {

	/**
	* __construct
	*
	* @param   void
	* @return  void
	**/
	public function __construct() {
		if ( is_admin() ) {
			add_action( 'wp_ajax_mab_get_bio_variation', array( $this, 'mab_get_bio_variation' ) );
			add_action( 'show_user_profile', array( $this, 'mab_custom_user_profile_fields' ) );
			add_action( 'edit_user_profile', array( $this, 'mab_custom_user_profile_fields' ) );
			add_action( 'user_register', array( $this, 'mab_save_custom_user_profile_fields' ) );
			add_action( 'profile_update', array( $this, 'mab_save_custom_user_profile_fields' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'mab_user_screen_enqueue' ) );
		}
	}

	/**
	 * mab_user_screen_enqueue
	 *
	 * Register and enqueue admin stylesheet & scripts
	 *
	 * @param   void
	 * @return  void
	 */
	public function mab_user_screen_enqueue( $page ) {

		if ( !in_array( $page, array( 'user-edit.php', 'profile.php' ) ) ) {
			return;
		}

		wp_register_style( 'mab_user_stylesheet', MAB_PLUGIN_DIR . 'admin/css/user-setup.css' );
		wp_enqueue_style( 'mab_user_stylesheet' );
		wp_register_script( 'mab_user_script', MAB_PLUGIN_DIR . 'admin/js/user-setup.js', array( 'jquery' ) );
		wp_localize_script( 'mab_user_script', 'mab_user_obj',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' )
			)
		);
		wp_enqueue_script( 'mab_user_script' );
	}

	/**
	* mab_get_bio_variation
	*
	* Pull user bio variation for selected site
	*
	* @param   void
	* @return  mixed The user bio variation text
	**/
	public function mab_get_bio_variation() {
		$site_name = sanitize_text_field( $_GET['site_name'] );
		$user_id = sanitize_text_field( $_GET['user_id'] );
		$bio_variation = get_user_meta( $user_id, 'mab_profile_bio_' . $site_name, true );

		if( isset( $bio_variation ) && !empty( $bio_variation ) ) {
			wp_send_json_success( $bio_variation );
		} else {
			return false;
		}
	}

	/**
	* mab_get_sites
	*
	* Override standard user bio if variation exists
	*
	* @param   string $value Standard user bio
	* @return  mixed The network sites
	**/
	public function mab_get_sites() {
		$main_site_id = get_main_site_id();
		$current_site_id = get_current_blog_id();
		$options = '';
		$sites = get_sites();
		foreach ( $sites as $site ) {
			if( $site->blog_id != $main_site_id ) {
				$site_slug = explode( '.', $site->siteurl )[0];
				$site_slug = str_replace( array( 'http://', 'https://' ), '', $site_slug );
				if( $site_slug ) {
					if( $current_site_id == $site->blog_id ) {
						$options .= '<option value="' . esc_html( $site_slug ) . '" selected="selected">' . strtoupper( esc_html( $site_slug ) ) . '</option>';
					} else {
						$options .= '<option value="' . esc_html( $site_slug ) . '">' . strtoupper( esc_html( $site_slug ) ) . '</option>';
					}
				}
			}
		}

		if( isset( $options ) && !empty( $options ) ) {
			return $options;
		} else {
			return false;
		}
	}

	/**
	* mab_custom_user_profile_fields
	*
	* Add Translate bio form to user edit page
	*
	* @param   object $user User object
	* @return  void
	**/
	public function mab_custom_user_profile_fields( $user ) {

		$user_id = sanitize_user_field( 'ID', $user->ID, $user->ID, 'raw' );
		$variations = $this->mab_get_sites();
		if( function_exists('is_multisite') && is_multisite() ) {
		?>

			<h3><?php esc_html_e( 'Multisite Author Bio', 'mab' ); ?></h3>
			<p><em><?php esc_html_e( 'Select the network site you wish to update/view the user bio for.', 'mab' ); ?></em></p>
			<div class="mab-form-container" data-user="<?php esc_html_e( $user_id ); ?>">
				<div class="mab-form-wrapper">
					<select class="mab-select-bio-variation" name="mabSelectBioVariation">
						<option value=""><?php esc_html_e( 'Select Site', 'mab' ); ?></option>
						<?php echo $variations; ?>
					</select>
					<p class="mab-bio-variation-label hidden"><em><?php esc_html_e( 'Below is the user bio variation for the site selected above.', 'mab' ); ?></em></p>
					<textarea rows="4" cols="60" placeholder="<?php esc_html_e( 'Insert profile bio variation', 'mab' ); ?>" class="mab-bio-variation-text hidden" name="mabBioVariation" value="" id="mab-bio-variation-text"></textarea>
				</div>
			</div>

		<?php
		} else { ?>

			<h3><?php esc_html_e( 'Translate Bio', 'mab' ); ?></h3>
			<div class="mab-form-container" data-user="<?php esc_html_e( $user_id ); ?>">
				<div class="mab-form-wrapper">
					<?php esc_html_e( 'Multisite is not enabled.', 'mab' ); ?>
				</div>
			</div>

		<?php
		}

	}

	/**
	* mab_save_custom_user_profile_fields
	*
	* Process saved fields to add to user meta
	*
	* @param   int $user_id User ID
	* @return  void
	**/
	public function mab_save_custom_user_profile_fields( $user_id ){

		// Only if admin
		if( !current_user_can( 'manage_options' ) ) {
			return false;
		}

		// Save author bio variation in user meta
		$bio_variation = sanitize_text_field( $_POST['mabSelectBioVariation'] );

		update_usermeta( $user_id, 'mab_profile_bio_' . $bio_variation, sanitize_textarea_field( $_POST['mabBioVariation'] ) );

	}

}
