<?php
/*
 *	This is the admin portion of the MSTW Coaching Staffs Plugin
 *	It is loaded in mstw-team-rosters.php conditioned on is_admin() 
 */

/*  Copyright 2013  Mark O'Donnell  (email : mark@shoalsummitsolutions.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* ------------------------------------------------------------------------
 * CHANGE LOG:
 * 20130801-MAO: Started development of the initial version 0.1.
 *	
 * 
 *
 *  
 * ------------------------------------------------------------------------*/

// --------------------------------------------------------------------------------------
// Set-up Action and Filter Hooks for the Settings on the admin side
// --------------------------------------------------------------------------------------
//register_uninstall_hook(__FILE__, 'mstw_cs_delete_plugin_options');

// --------------------------------------------------------------------------------------
// Callback for: register_uninstall_hook(__FILE__, 'mstw_tr_delete_plugin_options')
// --------------------------------------------------------------------------------------
// It runs when the user deactivates AND DELETES the plugin. 
// It deletes the plugin options DB entry, which is an array storing all the plugin options
// --------------------------------------------------------------------------------------
//function mstw_cs_delete_plugin_options() {
//	delete_option('mstw_cs_options');
//
// --------------------------------------------------------------------------------------

	// ----------------------------------------------------------------
	// Add styles and scripts for the color picker.
	
	add_action( 'admin_enqueue_scripts', 'mstw_cs_enqueue_color_picker' );
	
	function mstw_cs_enqueue_color_picker( $hook_suffix ) {
		wp_enqueue_style( 'wp-color-picker' );
		//wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker-settings', plugins_url( 'coaching-staffs/js/cs-color-settings.js' ), array( 'wp-color-picker' ), false, true ); 
	}

	// ----------------------------------------------------------------
	// Load the Utility Functions if necessary
	if ( !function_exists( 'mstw_admin_utils_loaded' ) ) {
			require_once  plugin_dir_path( __FILE__ ) . 'mstw-admin-utils.php';
	}
	
	// ----------------------------------------------------------------
	// Add the custom MSTW icon to CPT pages
	
	function mstw_cs_custom_css() {
	echo '<style type="text/css">
		   #icon-mstw-cs-main-menu.icon32 {background: url( ' . plugins_url( '/coaching-staffs/images/mstw-logo-32x32.png', 'coaching-staffs' ) . ') transparent no-repeat;}
           #icon-coaching-staffs.icon32 {background: url( ' . plugins_url( '/coaching-staffs/images/mstw-logo-32x32.png', 'coaching-staffs' ) . ') transparent no-repeat;}
		   #icon-edit.icon32-posts-staff_position {background: url( ' . plugins_url( '/coaching-staffs/images/mstw-logo-32x32.png', 'coaching-staffs' ) . ') transparent no-repeat;}' .
         '</style>';
	}
	add_action('admin_head', 'mstw_cs_custom_css');
	
	/*
	function mstw_cs_load_admin_style( ) {
			wp_register_style( 'mstw_cs_admin_css', get_stylesheet_directory_uri( ) . '/css/mstw-cs-admin-styles.css' );
			wp_enqueue_style( 'mstw_cs_admin_css' );
	}
	add_action( 'admin_enqueue_scripts', 'mstw_cs_load_admin_style' );
	*/
	
	// ----------------------------------------------------------------
	// Remove Quick Edit Menu	
	add_filter( 'post_row_actions', 'mstw_cs_remove_quick_edit', 10, 2 );

	function mstw_cs_remove_quick_edit( $actions, $post ) {
		if( $post->post_type == 'coach' or $post->post_type == 'staff_position' ) {
			unset( $actions['inline hide-if-no-js'] );
		}
		return $actions;
	}

	// ----------------------------------------------------------------
	// Remove the Bulk Actions pull-downs
	add_filter( 'bulk_actions-' . 'edit-coach', '__return_empty_array' );
	add_filter( 'bulk_actions-' . 'edit-staff_position', '__return_empty_array' );	
		
	// ----------------------------------------------------------------
	// Add a filter the All Staff Positions screen based on the Staffs Taxonomy
	// This new code is from http://wordpress.stackexchange.com/questions/578/adding-a-taxonomy-filter-to-admin-list-for-a-custom-post-type
	add_action( 'restrict_manage_posts', 'mstw_cs_restrict_manage_posts' );
	add_filter( 'parse_query','mstw_cs_convert_restrict' );
	
	function mstw_cs_restrict_manage_posts( ) {
		global $typenow;
		if ( $typenow == 'staff_position' ) {
			$args = array( 'public' => true, '_builtin' => false ); 
			$post_types = get_post_types( $args );
			if ( in_array( $typenow, $post_types ) ) {
			$filters = get_object_taxonomies( $typenow );
				foreach ( $filters as $tax_slug ) {
					$tax_obj = get_taxonomy( $tax_slug );
					wp_dropdown_categories(array(
						'show_option_all' => __( 'Show All '.$tax_obj->label, 'mstw-loc-domain' ),
						'taxonomy' => $tax_slug,
						'name' => $tax_obj->name,
						'orderby' => 'term_order',
						'selected' => $_GET[$tax_obj->query_var],
						'hierarchical' => $tax_obj->hierarchical,
						'show_count' => true,
						'hide_empty' => true
					));
				}
			}
		}
	}
	
	function mstw_cs_convert_restrict( $query ) {
		global $pagenow;
		global $typenow;
		if ( $typenow == 'staff_position' ) {
			if ( $pagenow=='edit.php' ) {
				$filters = get_object_taxonomies( $typenow );
				foreach ( $filters as $tax_slug ) {
					$var = &$query->query_vars[$tax_slug];
					if ( isset($var) ) {
						$term = get_term_by( 'id', $var, $tax_slug );
						$var = $term->slug;
					}
				}
			}
			return $query;
		}
	}
	
	
	// ----------------------------------------------------------------
	// Create the meta box for the Team Roster custom post type
	add_action( 'add_meta_boxes', 'mstw_cs_add_meta_boxes' );

	function mstw_cs_add_meta_boxes () {	
		add_meta_box(	'mstw-cs-coach-meta', 
						__('Coach', 'mstw-loc-domain'), 
						'mstw_cs_coach_ui', 
						'coach', 
						'normal', 
						'high' );
						
		add_meta_box(	'mstw-cs-staff-position-meta', 
						__('Staff Position', 'mstw-loc-domain'), 
						'mstw_cs_staff_position_ui', 
						'staff_position', 
						'normal', 
						'high' );
	}

	// ----------------------------------------------------------------------
	// Create the UI form for entering a Coach
	// Callback for: add_meta_box('mstw-cs-coach-meta', ... )
	
	function mstw_cs_coach_ui( $post ) {
		// Retrieve the metadata values if they exist
		$alma_mater = get_post_meta( $post->ID, 'mstw_cs_alma_mater', true );
		$experience = get_post_meta( $post->ID, 'mstw_cs_experience', true );
		   
		?>	
		
	   <table class="form-table">
		<tr valign="top">
			<th scope="row"><label for="mstw_cs_alma_mater" ><?php echo( __( 'Alma Mater', 'mstw-loc-domain' ) . ':' ); ?> </label></th>
			<td><input maxlength="64" size="20" name="mstw_cs_alma_mater"
				value="<?php echo esc_attr( $alma_mater ); ?>"/></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="mstw_cs_experience" ><?php echo( __( 'Experience', 'mstw-loc-domain' ) . ':' ); ?> </label></th>
			<td><input maxlength="64" size="20" name="mstw_cs_experience"
				value="<?php echo esc_attr( $experience ); ?>"/></td>
		</tr>
		
    </table> 
    
<?php       	
}

	// ----------------------------------------------------------------------
	// Create the UI form for entering a Staff Position
	// Callback for: add_meta_box('mstw-cs-staff-position-meta', ... )
	
	function mstw_cs_staff_position_ui( $post ) {
		// Retrieve the metadata values if they exist
		$position_coach = get_post_meta( $post->ID, 'mstw_cs_position_coach', true );
		$display_order = get_post_meta( $post->ID, 'mstw_cs_display_order', true );   
		?>	
		
		<table class="form-table">
			
			<tr>
			<th>Coach: </th>
			<td>
			<select name="mstw_cs_position_coach" > <!--onchange='document.location.href=this.options[this.selectedIndex].value;'> -->
				 <option value="">
				<?php echo esc_attr( __( 'Select coach' ) ); ?></option> 
				 <?php 
				 $args = array(
								'posts_per_page'   => -1,
								'offset'           => 0,
								'category'         => '',
								'orderby'          => 'title',
								'order'            => 'ASC',
								'include'          => '',
								'exclude'          => '',
								'meta_key'         => '',
								'meta_value'       => '',
								'post_type'        => 'coach',
								'post_mime_type'   => '',
								'post_parent'      => '',
								'post_status'      => 'publish',
								'suppress_filters' => true ); 
				  $coaches = get_posts( $args ); 
				  		  
				  foreach ( $coaches as $coach ) {
					$selected = ( $coach->ID == $position_coach ) ? 'selected="selected"' : '';
					$option = '<option value="' . $coach->ID . '" ' . $selected . '>';
					$option .= $coach->post_title;
					$option .= '</option>';
					echo $option;
				  }
				?>
			</select>
			</td>
			</tr>
						
			<tr valign="top">
				<th scope="row"><label for="mstw_cs_display_order" ><?php echo( __( 'Display Order:', 'mstw-loc-domain' ) ); ?> </label></th>
				<td><input maxlength="5" size="5" name="mstw_cs_display_order"
					value="<?php echo esc_attr( $display_order ); ?>"/></td>
			</tr>
			
		</table>
		
	<?php
	}


// ----------------------------------------------------------------------
// Save the Custom Post Type Meta Data
	add_action( 'save_post', 'mstw_cs_save_meta_data' );

	function mstw_cs_save_meta_data( $post_id ) {
		global $typenow;
		
		if ( $typenow == 'coach' ) {
			//First verify the required metadata is set and valid. If not, set default or return error
			//if ( get_the_title( $post_id ) == '' ) {
			//
			//}
			update_post_meta( $post_id, 'mstw_cs_alma_mater', 
					strip_tags( $_POST['mstw_cs_alma_mater'] ) );
					
			update_post_meta( $post_id, 'mstw_cs_experience', 
					strip_tags( $_POST['mstw_cs_experience'] ) );
		}
		else if ( $typenow == 'staff_position' ) {
			update_post_meta( $post_id, 'mstw_cs_position_coach', 
					strip_tags( $_POST['mstw_cs_position_coach'] ) );
					
			update_post_meta( $post_id, 'mstw_cs_display_order', 
					strip_tags( $_POST['mstw_cs_display_order'] ) );
		}
	}
	

	// ----------------------------------------------------------------
	// Set up the Coaches 'view all' columns
	add_filter( 'manage_edit-coach_columns', 'mstw_cs_edit_coach_columns' ) ;

	function mstw_cs_edit_coach_columns( $columns ) {

		$columns = array(
			//'cb' 			=> '<input type="checkbox" />',
			'title' 		=> __( 'Name', 'mstw-loc-domain' ),
			'photo' 		=> __( 'Photo', 'mstw-loc-domain' ),
			'alma_mater' 	=> __( 'Alma Mater', 'mstw-loc-domain' ),
			'experience' 	=> __( 'Experience', 'mstw-loc-domain' )
		);

		return $columns;
	}


	// ----------------------------------------------------------------
	// Display the Coaches 'view all' columns
	add_action( 'manage_coach_posts_custom_column', 'mstw_cs_manage_coach_columns', 10, 2 );

	function mstw_cs_manage_coach_columns( $column, $post_id ) {
		global $post;

		switch( $column ) {
			//case 'team' :
			//	$taxonomy = 'teams';
				
			//	$teams = get_the_terms( $post_id, $taxonomy );
			//	if ( is_array( $teams) ) {
			//		foreach( $teams as $key => $team ) {
			//			$teams[$key] =  $team->name;
			//		}
			//			echo implode( ' | ', $teams );
			//	}
			//	break;
			case 'photo':
				if ( has_post_thumbnail( $post->ID ) ) {
					echo get_the_post_thumbnail( $post->ID, array(64, 64) ); 
				}
				else {
					$photo_file_url = plugins_url() . '/coaching-staffs/images/default-photo.jpg';
					echo '<img width="64" height="64" src="' . $photo_file_url . '" class="attachment-64x64 wp-post-image" alt="No photo available">';
					
				}
				break;
			
			case 'alma_mater' :
				printf( '%s', get_post_meta( $post_id, 'mstw_cs_alma_mater', true ) );
				break;
				
			case 'experience' :
				printf( '%s', get_post_meta( $post_id, 'mstw_cs_experience', true ) );
				break;
				
			// Just break out of the switch statement for everything else. 
			default :
				break;
		}
	}
	
// ----------------------------------------------------------------
	// Set up the Staff Positions 'view all' columns
	add_filter( 'manage_edit-staff_position_columns', 'mstw_cs_edit_staff_columns' ) ;

	function mstw_cs_edit_staff_columns( $columns ) {

		$columns = array(
			//'cb' 			=> '<input type="checkbox" />',
			'title' 		=> __( 'Position', 'mstw-loc-domain' ),
			'staff' 		=> __( 'Staff', 'mstw-loc-domain' ),
			//'alma_mater' 	=> __( 'Alma Mater', 'mstw-loc-domain' ),
			'coach' 	=> __( 'Coach', 'mstw-loc-domain' )
		);

		return $columns;
	}
	
// ----------------------------------------------------------------
	// Display the Staff Positions 'view all' columns
	add_action( 'manage_staff_position_posts_custom_column', 'mstw_cs_manage_staff_columns', 10, 2 );

	function mstw_cs_manage_staff_columns( $column, $post_id ) {
		global $post;

		switch( $column ) {
			case 'staff' :
				$taxonomy = 'staffs';
				$staffs = get_the_terms( $post_id, $taxonomy );
				if ( is_array( $staffs) ) {
					foreach( $staffs as $key => $staff ) {
						$staffs[$key] =  $staff->name;
					}
					echo implode( ' | ', $staffs );
				}
				break;
			
			case 'coach' :
				$coach_id = get_post_meta( $post_id, 'mstw_cs_position_coach', true );
				printf( '%s', get_the_title( $coach_id ) );
				break;
				
			case 'experience' :
				printf( '%s', get_post_meta( $post_id, 'mstw_cs_experience', true ) );
				break;
				
			// Just break out of the switch statement for everything else. 
			default :
				break;
		}
	}
		
	// --------------------------------------------------------------------------------------
	// Add a menu item for the Admin pages
	add_action('admin_menu', 'mstw_cs_register_menu_pages');

	function mstw_cs_register_menu_pages( ) {
		add_menu_page( 	__( 'MSTW Coaching Staffs', 'mstw-loc-domain' ), // Page title
						__( 'Coaching Staffs', 'mstw-loc-domain' ),	// Menu entry (+ icon)
						'manage_options', 		// Capability required to access
						'mstw-cs-main-menu', 	// Unique menu slug
						'mstw_cs_menu_page', 	// Callback
						plugins_url( 'coaching-staffs/images/mstw-admin-menu-icon.png' ), // Menu Icon
						58 						//Menu position - right above Appearance
					 ); 
		
		add_submenu_page( 	'mstw-cs-main-menu', 							//parent slug
							__( 'Coaching Staffs', 'mstw-loc-domain' ), 	//page title
							__( 'Staffs', 'mstw-loc-domain' ),				//menu title
							'manage_options', 								//user capability required to access
							'edit-tags.php?taxonomy=staffs&post_type=staff_position', 						//unique menu slug
							'' //callback to display page
						);					
							
		add_submenu_page( 'mstw-cs-main-menu', 				//parent slug
							__( 'Coaching Staffs Display Settings', 'mstw-loc-domain' ), 	//page title
							__( 'Display Settings', 'mstw-loc-domain' ),		//menu title
							'manage_options', 				//user capability required to access
							'mstw-cs-display-settings', 		//unique menu slug
							'mstw_cs_settings_page' 			//callback to display page
						);					
							
	}

	function mstw_cs_settings_page( ) {
		?>
		<div class="wrap">
			<?php screen_icon( 'coaching-staffs' ); ?>
			<h2>Coaching Staffs Display Settings</h2>
			<?php //settings_errors(); ?>
			<form action="options.php" method="post">
				<?php settings_fields( 'mstw_cs_settings_fields' ); ?>
				<?php do_settings_sections( 'mstw-cs-display-settings' ); ?>
				<p>
				<input name="submit" type="submit" class="button-primary" value=<?php _e( "Save Changes", "mstw-loc-domain" ); ?> />
				
				<input type="submit" name="mstw_cs_options[reset]" value=<?php _e( "Reset Default Values", "mstw-loc-domain" ) ?> />
					<strong><?php _e( "WARNING! Reset Default Values will do so without further warning!", "mstw-loc-domain" ); ?></strong>
				</p>
			</form>
		</div>
		<?php
	}

	// ----------------------------------------------------------------
	// Register and define the settings
	// ----------------------------------------------------------------
	add_action('admin_init', 'mstw_cs_admin_init');

	function mstw_cs_admin_init( ){
		$options = get_option( 'mstw_cs_options' );
		$options = wp_parse_args( $options, mstw_cs_get_defaults( ) );
		//print_r ($options);
		
		// Settings for the fields and columns display and label controls.
		register_setting(
			'mstw_cs_settings_fields',
			'mstw_cs_options',
			'mstw_cs_validate_settings'
		);
		
		//Coaching Staff Table [shortcode] Settings
		mstw_cs_table_settings_setup( );
		
		//Single Coach Profile Settings
		mstw_cs_profile_setup( );
		
		//Coaching Staff Gallery Settings
		mstw_cs_gallery_setup( );
	}

	function mstw_cs_table_settings_setup( ) {
		$display_page = 'mstw-cs-display-settings'; 	//menu page slug on which to display
		$page_section = 'mstw_cs_table_settings'; 	//page section slug on which to display
		
		$options = get_option( 'mstw_cs_options' );
		
		add_settings_section(
			$page_section,  	//id attribute of tags
			__( 'Roster Table/[shortcode] Settings', 'mstw-loc-domain' ),	//title of the section
			'mstw_cs_table_settings_text',	//callback to fill section with desired output - should echo
			$display_page 	//page slug on which to display
		);
		
		// Show/Hide Staff Table Title
		$args = array(	'id' => 'show_title',
						'name' => 'mstw_cs_options[show_title]',
						'value' => $options['show_title'],
						'label' => __( 'Will show the table title (as "Staff Name" + "Coaching Staff")', 'mstw-loc-domain')
						);
						
		add_settings_field(
			'show_title',
			__( 'Show Roster Table Titles:', 'mstw-loc-domain' ),
			'mstw_utl_show_hide_ctrl',     	//Callback to display field
			$display_page,					//Page to display field
			$page_section, 					//Page section to display field
			$args							//Callback arguments
			);
			
		// Roster Table[shortcode] Title Color
		$args = array( 	'id' => 'table_title_color',
						'name' => 'mstw_cs_options[table_title_color]',
						'value' => $options['table_title_color'],
						'label' => ''
					 );
					 
		add_settings_field(
			'table_title_color',
			__( 'Table Title Color:', 'mstw-loc-domain' ),
			'mstw_utl_color_ctrl',
			$display_page,					//Page to display field
			$page_section, 					//Page section to display field
			$args
		);	
			
		// Show/Hide Coaches' Photos
		$args = array(	'id' => 'show_photos',
						'name' => 'mstw_cs_options[show_photos]',
						'value' => $options['show_photos'],
						'label' => __( 'Will show coaches photos in the staff tables', 'mstw-loc-domain')
						);
						
		add_settings_field(
			'show_photos',
			__( "Show Coaches' Photos:", 'mstw-loc-domain' ),
			'mstw_utl_show_hide_ctrl',		//Callback to display field
			$display_page,					//Page to display field
			$page_section, 					//Page section to display field
			$args							//Callback arguments
			);
		

		// Coaches' Photos Width
		$args = array( 	'id' => 'table_photo_width',
						'name'	=> 'mstw_cs_options[table_photo_width]',
						'value'	=> $options['table_photo_width'],
						'label'	=> __( 'Set width in pixels for table photos, if shown. (Default: 80px)', 'mstw-loc-domain' )
						);
						
		add_settings_field(
			'table_photo_width',
			__( 'Table Photo Width:', 'mstw-loc-domain' ),
			'mstw_utl_text_ctrl',
			$display_page,					//Page to display field
			$page_section, 					//Page section to display field
			$args
		);
		
		// Coaches' Photos Height
		$args = array( 	'id' => 'table_photo_height',
						'name'	=> 'mstw_cs_options[table_photo_height]',
						'value'	=> $options['table_photo_height'],
						'label'	=> __( 'Set height in pixels for table photos, if shown. (Default: 80px)', 'mstw-loc-domain' )
						);
						
		add_settings_field(
			'table_photo_height',
			__( 'Table Photo Height:', 'mstw-loc-domain' ),
			'mstw_utl_text_ctrl',
			$display_page,					//Page to display field
			$page_section, 					//Page section to display field
			$args
		);
		
		// Table[shortcode] Border Color
		$args = array( 	'id' => 'table_border_color',
						'name' => 'mstw_cs_options[table_border_color]',
						'value' => $options['table_border_color'],
						'label' => ''
					 );
					 
		add_settings_field(
			'table_border_color',
			__( 'Table Border Color:',  'mstw-loc-domain' ),
			'mstw_utl_color_ctrl',
			$display_page,					//Page to display field
			$page_section, 					//Page section to display field
			$args
		);
		
		// Table[shortcode] Border width
		$args = array( 	'id' => 'table_border_width',
						'name' => 'mstw_cs_options[table_border_width]',
						'value' => $options['table_border_width'],
						'label' => __( 'in pixels', 'mstw-loc-domain' ),
					 );
					 
		add_settings_field(
			'table_border_width',
			__( 'Table Border Width:', 'mstw-loc-domain' ),
			'mstw_utl_text_ctrl',
			$display_page,					//Page to display field
			$page_section, 					//Page section to display field
			$args
		);
		
		// Table[shortcode] Header Background Color
		$args = array( 	'id' => 'table_header_bkgd_color',
						'name' => 'mstw_cs_options[table_header_bkgd_color]',
						'value' => $options['table_header_bkgd_color'],
						'label' => ''
					 );
					 
		add_settings_field(
			'table_header_bkgd_color',
			__( 'Table Header Background Color:', 'mstw-loc-domain' ),
			'mstw_utl_color_ctrl',
			$display_page,					//Page to display field
			$page_section, 					//Page section to display field
			$args
		);
		
		// Table[shortcode] Header Text Color 
		$args = array( 	'id' => 'table_header_text_color',
						'name' => 'mstw_cs_options[table_header_text_color]',
						'value' => $options['table_header_text_color'],
						'label' => ''
					 );
					 
		add_settings_field(
			'table_header_text_color',
			__( 'Table Header Text Color:', 'mstw-loc-domain' ),
			'mstw_utl_color_ctrl',
			$display_page,					//Page to display field
			$page_section, 					//Page section to display field
			$args
		);
		
		// Roster Table[shortcode] Even Row Text Color
		$args = array( 	'id' => 'table_even_text_color',
						'name' => 'mstw_cs_options[table_even_text_color]',
						'value' => $options['table_even_text_color'],
						'label' => ''
					 );
					 
		add_settings_field(
			'table_even_text_color',
			__( 'Table Even Row Text Color:', 'mstw-loc-domain' ),
			'mstw_utl_color_ctrl',
			$display_page,					//Page to display field
			$page_section, 					//Page section to display field
			$args
		);	
		
		// Roster Table[shortcode] Even Row Background Color
		$args = array( 	'id' => 'table_even_bkgd_color',
						'name' => 'mstw_cs_options[table_even_bkgd_color]',
						'value' => $options['table_even_bkgd_color'],
						'label' => ''
					 );
					 
		add_settings_field(
			'table_even_bkgd_color',
			__( 'Table Even Row Background Color:', 'mstw-loc-domain' ),
			'mstw_utl_color_ctrl',
			$display_page,					//Page to display field
			$page_section, 					//Page section to display field
			$args
		);
		
		// Roster Table[shortcode] Even Row Link Color
		$args = array( 	'id' => 'table_even_link_color',
						'name' => 'mstw_cs_options[table_even_link_color]',
						'value' => $options['table_even_link_color'],
						'label' => ''
					 );
					 
		add_settings_field(
			'table_even_link_color',
			__( 'Table Even Row Link Color:', 'mstw-loc-domain' ),
			'mstw_utl_color_ctrl',
			$display_page,					//Page to display field
			$page_section, 					//Page section to display field
			$args
		);

// Roster Table[shortcode] Odd Row Text Color
		$args = array( 	'id' => 'table_odd_text_color',
						'name' => 'mstw_cs_options[table_odd_text_color]',
						'value' => $options['table_odd_text_color'],
						'label' => ''
					 );
					 
		add_settings_field(
			'table_odd_text_color',
			__( 'Table Odd Row Text Color:', 'mstw-loc-domain' ),
			'mstw_utl_color_ctrl',
			$display_page,					//Page to display field
			$page_section, 					//Page section to display field
			$args
		);	
		
		// Roster Table[shortcode] Odd Row Background Color
		$args = array( 	'id' => 'table_odd_bkgd_color',
						'name' => 'mstw_cs_options[table_odd_bkgd_color]',
						'value' => $options['table_odd_bkgd_color'],
						'label' => ''
					 );
					 
		add_settings_field(
			'table_odd_bkgd_color',
			__( 'Table Odd Row Background Color:',  'mstw-loc-domain' ),
			'mstw_utl_color_ctrl',
			$display_page,					//Page to display field
			$page_section, 					//Page section to display field
			$args
		);	

		// Roster Table[shortcode] Odd Row Link Color
		$args = array( 	'id' => 'table_odd_link_color',
						'name' => 'mstw_cs_options[table_odd_link_color]',
						'value' => $options['table_odd_link_color'],
						'label' => ''
					 );
					 
		add_settings_field(
			'table_odd_link_color',
			__( 'Table Odd Row Link Color:', 'mstw-loc-domain' ),
			'mstw_utl_color_ctrl',
			$display_page,					//Page to display field
			$page_section, 					//Page section to display field
			$args
		);		
		
	}
	
	function mstw_cs_profile_setup( ) {
		$options = get_option( 'mstw_cs_options' );
		
		$display_page = 'mstw-cs-display-settings'; 	//menu page slug on which to display
		$page_section = 'mstw_cs_profile_settings'; 	//page section slug on which to display
		
		add_settings_section(
			$page_section,  	//id attribute of tags
			__( 'Coach Profile Settings', 'mstw-loc-domain' ),	//title of the section
			'mstw_cs_profile_settings_text',	//callback to fill section with desired output - should echo
			$display_page 	//page slug on which to display
		);
		
		// Profile Bio Heading Text
		$args = array( 	'id' => 'profile_bio_heading_text',
						'name'	=> 'mstw_cs_options[profile_bio_heading_text]',
						'value'	=> $options['profile_bio_heading_text'],
						'label'	=> __( 'The text for the coach\'s profile bio heading.', 'mstw-loc-domain' )
						);
						
		add_settings_field(
			'profile_bio_heading_text',
			__( 'Coach\'s Bio Heading:', 'mstw-loc-domain' ),
			'mstw_utl_text_ctrl',
			$display_page,					//Page to display field
			$page_section, 					//Page section to display field
			$args
		);
		
		// Profile Header Background Color
		$args = array( 	'id' => 'profile_header_bkgd_color',
						'name' => 'mstw_cs_options[profile_header_bkgd_color]',
						'value' => $options['profile_header_bkgd_color'],
						'label' => ''
					 );
					 
		add_settings_field(
			'profile_header_bkgd_color',
			__( 'Header Background Color:', 'mstw-loc-domain' ),
			'mstw_utl_color_ctrl',
			$display_page,					//Page to display field
			$page_section, 					//Page section to display field
			$args
		);
		
		$args = array( 	'id' => 'profile_header_text_color',
						'name' => 'mstw_cs_options[profile_header_text_color]',
						'value' => $options['profile_header_text_color'],
						'label' => ''
					 );
					 
		add_settings_field(
			'profile_header_text_color',
			__( 'Header Text Color:', 'mstw-loc-domain' ),
			'mstw_utl_color_ctrl',
			$display_page,					//Page to display field
			$page_section, 					//Page section to display field
			$args
		);
		
		// Profile Header Coach's Name Color
		$args = array( 	'id' => 'profile_header_name_color',
						'name' => 'mstw_cs_options[profile_header_name_color]',
						'value' => $options['profile_header_name_color'],
						'label' => ''
					 );
					 
		add_settings_field(
			'profile_header_name_color',
			__( 'Coach\'s Name Color:', 'mstw-loc-domain' ),
			'mstw_utl_color_ctrl',
			$display_page,					//Page to display field
			$page_section, 					//Page section to display field
			$args
		);
		
		// Profile Header Coach's Position Color
		$args = array( 	'id' => 'profile_header_position_color',
						'name' => 'mstw_cs_options[profile_header_position_color]',
						'value' => $options['profile_header_position_color'],
						'label' => ''
					 );
					 
		add_settings_field(
			'profile_header_position_color',
			__( 'Coach\'s Position Color:', 'mstw-loc-domain' ),
			'mstw_utl_color_ctrl',
			$display_page,					//Page to display field
			$page_section, 					//Page section to display field
			$args
		);
		
		// Coach's Bio Heading Color
		$args = array( 	'id' => 'profile_bio_heading_color',
						'name' => 'mstw_cs_options[profile_bio_heading_color]',
						'value' => $options['profile_bio_heading_color'],
						'label' => ''
					 );
					 
		add_settings_field(
			'profile_bio_heading_color',
			__( 'Coach\'s Bio Heading Color:', 'mstw-loc-domain' ),
			'mstw_utl_color_ctrl',
			$display_page,					//Page to display field
			$page_section, 					//Page section to display field
			$args
		);
		
		// Coach's Bio Text Color
		$args = array( 	'id' => 'profile_bio_text_color',
						'name' => 'mstw_cs_options[profile_bio_text_color]',
						'value' => $options['profile_bio_text_color'],
						'label' => ''
					 );
					 
		add_settings_field(
			'profile_bio_text_color',
			__( 'Coach\'s Bio Text Color:', 'mstw-loc-domain' ),
			'mstw_utl_color_ctrl',
			$display_page,					//Page to display field
			$page_section, 					//Page section to display field
			$args
		);
		
		// Coach's Bio Background Color
		$args = array( 	'id' => 'profile_bio_bkgd_color',
						'name' => 'mstw_cs_options[profile_bio_bkgd_color]',
						'value' => $options['profile_bio_bkgd_color'],
						'label' => ''
					 );
					 
		add_settings_field(
			'profile_bio_bkgd_color',
			__( 'Coach\'s Bio Background Color:', 'mstw-loc-domain' ),
			'mstw_utl_color_ctrl',
			$display_page,					//Page to display field
			$page_section, 					//Page section to display field
			$args
		);
		
		// Coach's Bio Border Color
		$args = array( 	'id' => 'profile_bio_border_color',
						'name' => 'mstw_cs_options[profile_bio_border_color]',
						'value' => $options['profile_bio_border_color'],
						'label' => ''
					 );
					 
		add_settings_field(
			'profile_bio_border_color',
			__( 'Coach\'s Bio Border Color:', 'mstw-loc-domain' ),
			'mstw_utl_color_ctrl',
			$display_page,					//Page to display field
			$page_section, 					//Page section to display field
			$args
		);
		
		// Profile Bio Border Width
		$args = array( 	'id' => 'profile_bio_border_width',
						'name'	=> 'mstw_cs_options[profile_bio_border_width]',
						'value'	=> $options['profile_bio_border_width'],
						'label'	=> 'in pixels'
						);
						
		add_settings_field(
			'profile_bio_border_width',
			__( 'Coach\'s Bio Border Width:', 'mstw-loc-domain' ),
			'mstw_utl_text_ctrl',
			$display_page,					//Page to display field
			$page_section, 					//Page section to display field
			$args
		);	
	}
	
	function mstw_cs_gallery_setup( ) {
		$options = get_option( 'mstw_cs_options' );
		
		$display_page = 'mstw-cs-display-settings'; 	//menu page slug on which to display
		$page_section = 'mstw_cs_gallery_settings'; 	//page section slug on which to display
		
		add_settings_section(
			$page_section,  	//id attribute of tags
			__( 'Coaches Gallery Settings', 'mstw-loc-domain' ),	//title of the section
			'mstw_cs_gallery_settings_text',	//callback to fill section with desired output - should echo
			$display_page 	//page slug on which to display
		);	
	}

function mstw_cs_gallery_settings_text( ) {
	echo '<p>' . __( 'Enter the default settings for Coaches Gallery pages.', 'mstw-loc-domain' ) .  '</p>';
}

function mstw_cs_profile_settings_text( ) {
	echo '<p>' . __( 'Enter the default settings for Single Coach Profile pages.', 'mstw-loc-domain' ) .  '</p>';
}

function mstw_cs_table_settings_text( ) {
	echo '<p>' . __( 'Enter the default settings for Staff Table/[shortcode]. These can be overridden by [shortcode] arguments.', 'mstw-loc-domain' ) .  '</p>';
}
 
function mstw_cs_validate_settings( $input ) {
	// Create our array for storing the validated options
	$output = array();
	// Pull the previous (good) options
	$options = get_option( 'mstw_tr_options' );
	
	if ( array_key_exists( 'reset', $input ) ) {
		if ( $input['reset'] ) {
				$output = mstw_cs_get_defaults( );
				return $output;
		}
	}
	
	// Loop through each of the incoming options
	foreach( $input as $key => $value ) {
		// Check to see if the current option has a value. If so, process it.
		if( isset( $input[$key] ) ) {
			switch ( $key ) {
				// add the hex colors
				case 'table_header_bkgd_color':
				case 'table_header_text_color':
				case 'table_border_color':
				case 'table_title_color':
				case 'table_even_text_color':
				case 'table_even_bkgd_color':
				case 'table_even_link_color':
				case 'table_odd_text_color':
				case 'table_odd_bkgd_color':
				case 'table_odd_link_color':
				case 'profile_header_bkgd_color':
				case 'profile_header_text_color':
				case 'profile_header_name_color':
				case 'profile_header_position_color':
				case 'profile_bio_heading_color':
				case 'profile_bio_text_color':
				case 'profile_bio_bkgd_color':
				case 'profile_bio_border_color':
					// validate the color for proper hex format
					$sanitized_color = mstw_utl_sanitize_hex_color( $input[$key] );
					
					// decide what to do - save new setting 
					// or display error & revert to last setting
					if ( isset( $sanitized_color ) ) {
						// blank input is valid
						$output[$key] = $sanitized_color;
					}
					else  {
						// there's an error. Reset to the last stored value
						$output[$key] = $options[$key];
						// add error message
						add_settings_error( 'mstw_tr_' . $key,
											'mstw_tr_hex_color_error',
											'Invalid hex color entered!',
											'error');
					}
					break;
				
				// Integers
				case 'table_photo_width':
				case 'table_photo_height':
				case 'table_border_width':
				case 'profile_bio_border_width':
					$output[$key] = intval( $input[$key] );
					break;
					
				// 0-1 stuff
				case 'show_title':
				case 'show_photos':
					if ( $input[$key] == 1 ) {
						$output[$key] = 1;
					}
					else {
						$input[$key] = 0;
					}
					break;
				// Sanitize all other settings as text
				default:
					$output[$key] = sanitize_text_field( $input[$key] );
					// There should not be user/accidental errors in these fields
					break;
				
			} // end switch
		} // end if
	} // end foreach
	
	return $output;
}

/*

	//------------------------------------------------------------------
	// Add admin_notices action - need to look at this more someday
	
	add_action( 'admin_notices', 'mstw_tr_admin_notices' );
	
	function mstw_tr_admin_notices() {
		settings_errors( );
	}
*/
?>