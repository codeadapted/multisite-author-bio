<?php

class MAB_Plugin {

	/**
	 * install
	 *
	 * Run installation functions.
	 *
	 * @param   void
	 * @return  void
	 */
	public static function install() {

		update_option( 'mab_activated', true );

	}

	/**
	 * deactivate
	 *
	 * Run deactivation functions.
	 *
	 * @param   void
	 * @return  void
	 */
	public static function deactivate() {

		delete_option( 'mab_activated' );

	}

	/**
	 * uninstall
	 *
	 * Run uninstall functions.
	 *
	 * @param   void
	 * @return  void
	 */
	public static function uninstall() {

		if( sanitize_text_field( get_option( 'mab_clear_data' ) ) ) {
			self::mab_clear_data();
			delete_option( 'mab_clear_data' );
		}

	}

	/**
	 * __construct
	 *
	 * @param   void
	 * @return  void
	 */
	public function __construct() {

		register_uninstall_hook( MAB_FILE, array( __CLASS__, 'uninstall' ) );
		register_deactivation_hook( MAB_FILE, array( __CLASS__, 'deactivate' ) );
		register_activation_hook( MAB_FILE, array( __CLASS__, 'install' ) );

		if ( is_admin() ) {
			add_filter( 'plugin_action_links_' . MAB_BASENAME . '/multisite-author-bio.php', array( $this, 'add_settings_link' ) );
			add_action( 'admin_init', array( $this, 'admin_init' ) );
			add_action( 'admin_menu', array( $this, 'admin_page' ) );
			add_action( 'wp_ajax_mab_save_admin_page', array( $this, 'mab_save_admin_page' ) );
		}

	}

	/**
	 * mab_clear_data
	 *
	 * Clear translated user bio data.
	 *
	 * @param   void
	 * @return  void
	 */
	public function mab_clear_data() {

		$main_site_id = get_main_site_id();

		if( function_exists('is_multisite') && is_multisite() ) {
			switch_to_blog( $main_site_id );
		}

		global $wpdb;

		$deleted_rows = $wpdb->query( "DELETE FROM {$wpdb->usermeta} WHERE `meta_key` LIKE '%mab_profile_bio%'" );

		restore_current_blog();

	}

	/**
	 * mab_save_admin_page
	 *
	 * Save admin page data
	 *
	 * @param   void
	 * @return  void
	 */
	 function mab_save_admin_page() {

		mab()->plugin()->mab_load_plugin_textdomain();

		$clear_data = sanitize_text_field( $_POST['clear_data'] );
		$main_site_id = get_main_site_id();

		if( function_exists('is_multisite') && is_multisite() ) {
			switch_to_blog( $main_site_id );
		}

		if( $clear_data ) {
			update_option( 'mab_clear_data', true );
		} else {
			delete_option( 'mab_clear_data' );
		}

		restore_current_blog();

		wp_send_json_success( __( 'User bio variations set to be cleared on uninstall', 'multisite-author-bio' ) );
	 }

	/**
	 * add_settings_link
	 *
	 * Add settings link on plugin page
	 *
	 * @param   array $links The links array.
	 * @return  array The links array.
	 */
	public function add_settings_link( $links ) {
		$links[] = '<a href="' . $this->get_admin_url() . '">' . __( 'Settings' ) . '</a>';
		return $links;
	}

	/**
	 * admin_init
	 *
	 * Register and enqueue admin stylesheet & scripts
	 *
	 * @param   void
	 * @return  void
	 */
	public function admin_init() {
		// only enqueue these things on the settings page
		if ( $this->get_current_admin_url() == $this->get_admin_url() ) {
			wp_register_style( 'mab_stylesheet', MAB_PLUGIN_DIR . 'admin/css/admin.css' );
			wp_enqueue_style( 'mab_stylesheet' );
			wp_register_script( 'mab_script', MAB_PLUGIN_DIR . 'admin/js/admin.js', array( 'jquery' ) );
			wp_localize_script( 'mab_script', 'mab_obj',
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' )
				)
			);
			wp_enqueue_script( 'mab_script' );
		}
	}

	/**
	 * admin_page
	 *
	 * Register admin page and menu.
	 *
	 * @param   void
	 * @return  void
	 */
	public function admin_page() {
		add_submenu_page(
			'options-general.php',
			__( 'Multisite Author Bio', 'multisite-author-bio' ),
			__( 'Multisite Author Bio', 'multisite-author-bio' ),
			'administrator',
			MAB_DIRNAME,
			array( $this, 'admin_page_settings' ),
			100
		);
	}

	/**
	 * admin_page_settings
	 *
	 * Render admin view
	 *
	 * @param   void
	 * @return  void
	 */
	public function admin_page_settings() {
		require_once MAB_DIRNAME . '/admin/view.php';
	}

	/**
	 * get_current_admin_url
	 *
	 * Get the current admin url.
	 *
	 * @param   void
	 * @return  void
	 */
	function get_current_admin_url() {
		$uri = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		$uri = preg_replace( '|^.*/wp-admin/|i', '', $uri );
		if ( ! $uri ) {
			return '';
		}
		return remove_query_arg( array( '_wpnonce' ), admin_url( $uri ) );
	}

	/**
	 * get_admin_url
	 *
	 * Add settings link on plugin page
	 *
	 * @param   void
	 * @return  string the admin url
	 */
	public function get_admin_url() {
		return admin_url( 'options-general.php?page=' . MAB_BASENAME );
	}

	/**
	 * mab_load_plugin_textdomain
	 *
	 * Add settings link on plugin page
	 *
	 * @param   void
	 * @return  string the translation .mo file path
	 */
	public function mab_load_plugin_textdomain() {
		load_plugin_textdomain( 'multisite-author-bio', false, MAB_BASENAME . '/languages/' );
	}

}
