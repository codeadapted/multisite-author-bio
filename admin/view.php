<?php
// Admin View Options Page

if( !current_user_can( 'manage_options' ) ) {
	wp_die(__('You do not have sufficient permissions to access this page.'));
}

$main_site_id = get_main_site_id();
switch_to_blog( $main_site_id );

if( get_option( 'mab_clear_data' ) ) {
	$clear_data = true;
} else {
	$clear_data = false;
}

restore_current_blog();

?>
<div id="admin-view">
	<form id="mabForm" class="admin-view-form">
		<h1>Multisite Author Bio</h1>
		<div class="sections">
			<div class="section clear-data">
				<div class="checkbox">
					<div class="check">
						<input type="checkbox" name="cleardata" id="cleardata" value="1" <?php echo $clear_data ? 'checked' : ''; ?>>
					</div>
					<div class="label">
						<label for="cleardata">Clear translation data on uninstall</label>
					</div>
					<div class="desc">
						Enabling this option will delete all the user meta data added by the plugin. It is highly advised to leave this unchecked if you plan to continue using this plugin.
					</div>
				</div>
			</div>

			<input id="submitForm" class="button button-primary" name="submitForm" type="submit" value="Save Changes">
		</div>
	</form>
</div>
