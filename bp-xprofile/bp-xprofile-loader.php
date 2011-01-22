<?php

/**
 * BuddyPress XProfile Loader
 *
 * An extended profile component for users. This allows site admins to create
 * groups of fields for users to enter information about themselves.
 *
 * @package BuddyPress
 * @subpackage XProfile Core
 */

class BP_XProfile_Component extends BP_Component {

	/**
	 * Start the xprofile component creation process
	 *
	 * @since BuddyPress {unknown}
	 */
	function BP_XProfile_Component() {
		parent::start( 'profile', __( 'Extended Profiles', 'buddypress' ) );
	}

	/**
	 * Setup globals
	 *
	 * The BP_XPROFILE_SLUG constant is deprecated, and only used here for
	 * backwards compatibility.
	 *
	 * @since BuddyPress {unknown}
	 * @global obj $bp
	 */
	function _setup_globals() {
		global $bp;

		// Define a slug, if necessary
		if ( !defined( 'BP_XPROFILE_SLUG' ) )
			define( 'BP_XPROFILE_SLUG', 'profile' );

		// Assign the base group and fullname field names to constants to use
		// in SQL statements
		define ( 'BP_XPROFILE_BASE_GROUP_NAME',     stripslashes( $bp->site_options['bp-xprofile-base-group-name']     ) );
		define ( 'BP_XPROFILE_FULLNAME_FIELD_NAME', stripslashes( $bp->site_options['bp-xprofile-fullname-field-name'] ) );

		// Do some slug checks
		$this->slug      = BP_XPROFILE_SLUG;
		$this->root_slug = isset( $bp->pages->xprofile->slug ) ? $bp->pages->xprofile->slug : $this->slug;

		// Tables
		$this->table_name_data   = $bp->table_prefix . 'bp_xprofile_data';
		$this->table_name_groups = $bp->table_prefix . 'bp_xprofile_groups';
		$this->table_name_fields = $bp->table_prefix . 'bp_xprofile_fields';
		$this->table_name_meta	 = $bp->table_prefix . 'bp_xprofile_meta';

		// Notifications
		$this->notification_callback = 'xprofile_format_notifications';

		// Register this in the active components array
		$bp->active_components[$this->id] = $this->id;

		// Set the support field type ids
		$this->field_types = apply_filters( 'xprofile_field_types', array(
			'textbox',
			'textarea',
			'radio',
			'checkbox',
			'selectbox',
			'multiselectbox',
			'datebox'
		) );
	}

	/**
	 * Include files
	 */
	function _includes() {
		require_once( BP_PLUGIN_DIR . '/bp-xprofile/bp-xprofile-cssjs.php'     );
		require_once( BP_PLUGIN_DIR . '/bp-xprofile/bp-xprofile-admin.php'     );
		require_once( BP_PLUGIN_DIR . '/bp-xprofile/bp-xprofile-actions.php'   );
		require_once( BP_PLUGIN_DIR . '/bp-xprofile/bp-xprofile-screens.php'   );
		require_once( BP_PLUGIN_DIR . '/bp-xprofile/bp-xprofile-classes.php'   );
		require_once( BP_PLUGIN_DIR . '/bp-xprofile/bp-xprofile-filters.php'   );
		require_once( BP_PLUGIN_DIR . '/bp-xprofile/bp-xprofile-template.php'  );
		require_once( BP_PLUGIN_DIR . '/bp-xprofile/bp-xprofile-functions.php' );
	}

	/**
	 * Setup BuddyBar navigation
	 *
	 * @global obj $bp
	 */
	function _setup_nav() {
		global $bp;

		// Add 'Profile' to the main navigation
		bp_core_new_nav_item( array(
			'name'                => __( 'Profile', 'buddypress' ),
			'slug'                => $this->slug,
			'position'            => 20,
			'screen_function'     => 'xprofile_screen_display_profile',
			'default_subnav_slug' => 'public',
			'item_css_id'         => $this->id
		) );

		$profile_link = trailingslashit( $bp->loggedin_user->domain . $this->slug );

		// Add the subnav items to the profile
		bp_core_new_subnav_item( array(
			'name'            => __( 'Public', 'buddypress' ),
			'slug'            => 'public',
			'parent_url'      => $profile_link,
			'parent_slug'     => $this->slug,
			'screen_function' => 'xprofile_screen_display_profile',
			'position'        => 10
		) );

		// Edit Profile
		bp_core_new_subnav_item( array(
			'name'            => __( 'Edit Profile', 'buddypress' ),
			'slug'            => 'edit',
			'parent_url'      => $profile_link,
			'parent_slug'     => $this->slug,
			'screen_function' => 'xprofile_screen_edit_profile',
			'position'        => 20
		) );

		// Change Avatar
		bp_core_new_subnav_item( array(
			'name'            => __( 'Change Avatar', 'buddypress' ),
			'slug'            => 'change-avatar',
			'parent_url'      => $profile_link,
			'parent_slug'     => $this->slug,
			'screen_function' => 'xprofile_screen_change_avatar',
			'position'        => 30
		) );

		if ( $bp->current_component == $this->id ) {
			if ( bp_is_my_profile() ) {
				$bp->bp_options_title = __( 'My Profile', 'buddypress' );
			} else {
				$bp->bp_options_avatar = bp_core_fetch_avatar( array(
					'item_id' => $bp->displayed_user->id,
					'type'    => 'thumb'
				) );
				$bp->bp_options_title = $bp->displayed_user->fullname;
			}
		}
	}
}
// Create the xprofile component
if ( !isset( $bp->profile->id ) )
	$bp->profile = new BP_XProfile_Component();

?>