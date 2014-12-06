<?php
/*
 *	This is the admin portion of the MSTW Coaching Staffs Plugin
 *	It is loaded in mstw-coaching-staffs.php conditioned on is_admin() 
 */

/*-----------------------------------------------------------------------------------
Copyright 2012-13  Mark O'Donnell  (email : mark@shoalsummitsolutions.com)

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.

Code from the CSV Importer plugin was modified under that plugin's 
GPLv2 (or later) license from Smackcoders. 

Code from the File_CSV_DataSource class was re-used unchanged under
that class's MIT license & copyright (2008) from Kazuyoshi Tlacaelel. 
-----------------------------------------------------------------------------------*/

//-----------------------------------------------------------------------------------
//
// See http://wordpress.org/plugins/coaching-staffs/developers/ for CHANGE LOG
//
//-----------------------------------------------------------------------------------

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

	add_filter('months_dropdown_results', '__return_empty_array');

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
	add_action('admin_head', 'mstw_cs_custom_css');
	
	function mstw_cs_custom_css() {
	echo '<style type="text/css">
		   #icon-mstw-cs-main-menu.icon32 {background: url( ' . plugins_url( '/coaching-staffs/images/mstw-logo-32x32.png', 'coaching-staffs' ) . ') transparent no-repeat;}
           #icon-coaching-staffs.icon32 {background: url( ' . plugins_url( '/coaching-staffs/images/mstw-logo-32x32.png', 'coaching-staffs' ) . ') transparent no-repeat;}
		   #icon-edit.icon32-posts-staff_position {background: url( ' . plugins_url( '/coaching-staffs/images/mstw-logo-32x32.png', 'coaching-staffs' ) . ') transparent no-repeat;}' .
         '</style>';
	}

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
	add_filter( 'bulk_actions-edit-coach', 'mstw_cs_bulk_actions' );
	add_filter( 'bulk_actions-edit-staff_position', 'mstw_cs_bulk_actions' );

	function mstw_cs_bulk_actions( $actions ){
        unset( $actions['edit'] );
        return $actions;
    }	
		
	// ----------------------------------------------------------------
	// Add a filter the All Staff Positions screen based on the Staffs Taxonomy
	// This new code is from http://wordpress.stackexchange.com/questions/578/adding-a-taxonomy-filter-to-admin-list-for-a-custom-post-type
	/*
	add_action( 'restrict_manage_posts', 'mstw_cs_restrict_manage_posts' );
	add_filter( 'parse_query','mstw_cs_convert_restrict' );
	*/
	add_action( 'restrict_manage_posts', 'mstw_cs_restrict_positions_by_staff' );
	
	function mstw_cs_restrict_positions_by_staff( ) {
	global $typenow;

	if( $typenow == 'staff_position' ) {
		
		$taxonomy_slugs = array( 'staffs' );
		
		foreach ( $taxonomy_slugs as $tax_slug ) {
			//retrieve the taxonomy object for the tax_slug
			$tax_obj = get_taxonomy( $tax_slug );
			$tax_name = $tax_obj->labels->name;
			
			$terms = get_terms( $tax_slug );
		
			//output the html for the drop down menu
			echo "<select name='$tax_slug' id='$tax_slug' class='postform'>";
            echo "<option value=''>". __( 'Show All Staffs', 'mstw-schedules-scoreboards') . "</option>";
			
			//output each select option line
            foreach ($terms as $term) {
                //check against the last $_GET to show the current selection
				if ( array_key_exists( $tax_slug, $_GET ) ) {
					$selected = ( $_GET[$tax_slug] == $term->slug )? ' selected="selected"' : '';
				}
				else {
					$selected = '';
				}
                echo '<option value=' . $term->slug . $selected . '>' . $term->name . ' (' . $term->count . ')</option>';
            }
            echo "</select>"; 
		}	
	}
} //End: mstw_ss_restrict_games_by_scoreboard( )
	
	
	
	function mstw_cs_restrict_manage_posts( ) {
		global $typenow;
		//$args=array( 'public' => true, '_builtin' => false ); 
		//$post_types = get_post_types($args);
		//if ( in_array($typenow, $post_types) ) {
		if ( $typenow == 'staff_position' ) {
			//$args = array( 'public' => true, '_builtin' => false ); 
			//$post_types = get_post_types( $args );
			//if ( in_array( $typenow, $post_types ) ) {
			$filters = get_object_taxonomies( $typenow );
			$tax_slug = $filters[0];
				//foreach ( $filters as $tax_slug ) {
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
				//}
			//}
		}
	}
	
	function mstw_cs_convert_restrict( $query ) {
		global $pagenow;
		global $typenow;
		//if ( $typenow == 'staff_position' ) {
			if ( $pagenow=='edit.php' ) {
				$filters = get_object_taxonomies( $typenow );
				//$tax_slug = $filters[0];
				foreach ( $filters as $tax_slug ) {
					$var = &$query->query_vars[$tax_slug];
					if ( isset($var) ) {
						$term = get_term_by( 'id', $var, $tax_slug );
						$var = $term->slug;
					}
				}
			}
		//}
		return $query;
	}

	// ----------------------------------------------------------------
	// Create the meta box for the Coaching Staff custom post type
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
		$experience = get_post_meta( $post->ID, 'mstw_cs_experience', true );
		$alma_mater = get_post_meta( $post->ID, 'mstw_cs_alma_mater', true );
		$degree = get_post_meta( $post->ID, 'mstw_cs_degree', true );
		$birth_date = get_post_meta( $post->ID, 'mstw_cs_birth_date', true );
		$home_town = get_post_meta( $post->ID, 'mstw_cs_home_town', true );
		$high_school = get_post_meta( $post->ID, 'mstw_cs_high_school', true );
		$family = get_post_meta( $post->ID, 'mstw_cs_family', true );   
		?>	
		
	   <table class="form-table">
		
		<tr valign="top">
			<th scope="row"><label for="mstw_cs_experience" ><?php echo( __( 'Experience', 'mstw-loc-domain' ) . ':' ); ?> </label></th>
			<td><input size="20" name="mstw_cs_experience"
				value="<?php echo esc_attr( $experience ); ?>"/></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="mstw_cs_alma_mater" ><?php echo( __( 'Alma Mater', 'mstw-loc-domain' ) . ':' ); ?> </label></th>
			<td><input size="20" name="mstw_cs_alma_mater"
				value="<?php echo esc_attr( $alma_mater ); ?>"/></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="mstw_cs_degree" ><?php echo( __( 'Degree', 'mstw-loc-domain' ) . ':' ); ?> </label></th>
			<td><input size="20" name="mstw_cs_degree"
				value="<?php echo esc_attr( $degree ); ?>"/></td>
		</tr>	
		<tr valign="top">
			<th scope="row"><label for="mstw_cs_birth_date" ><?php echo( __( 'Birth Date', 'mstw-loc-domain' ) . ':' ); ?> </label></th>
			<td><input size="20" name="mstw_cs_birth_date"
				value="<?php echo esc_attr( $birth_date ); ?>"/></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="mstw_cs_home_town" ><?php echo( __( 'Home Town', 'mstw-loc-domain' ) . ':' ); ?> </label></th>
			<td><input size="20" name="mstw_cs_home_town"
				value="<?php echo esc_attr( $home_town ); ?>"/></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="mstw_cs_high_school" ><?php echo( __( 'High School', 'mstw-loc-domain' ) . ':' ); ?> </label></th>
			<td><input size="20" name="mstw_cs_high_school"
				value="<?php echo esc_attr( $high_school ); ?>"/></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="mstw_cs_family" ><?php echo( __( 'Family', 'mstw-loc-domain' ) . ':' ); ?> </label></th>
			<td><textarea rows="5" cols="40" name="mstw_cs_family">
				<?php echo esc_attr( $family ); ?> </textarea></td>
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
			//Strip tags for safety before storing strings		
			update_post_meta( $post_id, 'mstw_cs_experience', 
					strip_tags( $_POST['mstw_cs_experience'] ) );
					
			update_post_meta( $post_id, 'mstw_cs_alma_mater', 
					strip_tags( $_POST['mstw_cs_alma_mater'] ) );
					
			update_post_meta( $post_id, 'mstw_cs_degree', 
					strip_tags( $_POST['mstw_cs_degree'] ) );
					
			update_post_meta( $post_id, 'mstw_cs_birth_date', 
					strip_tags( $_POST['mstw_cs_birth_date'] ) );
					
			update_post_meta( $post_id, 'mstw_cs_home_town', 
					strip_tags( $_POST['mstw_cs_home_town'] ) );
					
			update_post_meta( $post_id, 'mstw_cs_high_school', 
					strip_tags( $_POST['mstw_cs_high_school'] ) );

			update_post_meta( $post_id, 'mstw_cs_family', 
					strip_tags( $_POST['mstw_cs_family'] ) );		
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
			'cb' 			=> '<input type="checkbox" />',
			'title' 		=> __( 'Name', 'mstw-loc-domain' ),
			'photo' 		=> __( 'Photo', 'mstw-loc-domain' ),
			'alma_mater' 	=> __( 'Alma Mater', 'mstw-loc-domain' ),
			'experience' 	=> __( 'Experience', 'mstw-loc-domain' ),
			'degree' 	=> __( 'Degree', 'mstw-loc-domain' ),
			'birth_date' 	=> __( 'Birth Date', 'mstw-loc-domain' ),
			'home_town' 	=> __( 'Home Town', 'mstw-loc-domain' ),
			'high_school' 	=> __( 'High School', 'mstw-loc-domain' ),
			'family' 	=> __( 'Family', 'mstw-loc-domain' ),
		);

		return $columns;
	}


	// ----------------------------------------------------------------
	// Display the Coaches 'view all' columns
	add_action( 'manage_coach_posts_custom_column', 'mstw_cs_manage_coach_columns', 10, 2 );

	function mstw_cs_manage_coach_columns( $column, $post_id ) {
		global $post;

		switch( $column ) {
			case 'photo':
				if ( has_post_thumbnail( $post->ID ) ) {
					echo get_the_post_thumbnail( $post->ID, array(64, 64) ); 
				}
				else {
					$photo_file_url = plugins_url() . '/coaching-staffs/images/default-photo.jpg';
					echo '<img width="64" height="64" src="' . $photo_file_url . '" class="attachment-64x64 wp-post-image" alt="No photo available">';
					
				}
				break;
				
			case 'experience' :
				printf( '%s', get_post_meta( $post_id, 'mstw_cs_experience', true ) );
				break;
				
			case 'alma_mater' :
				printf( '%s', get_post_meta( $post_id, 'mstw_cs_alma_mater', true ) );
				break;
		
				
			case 'degree' :
				printf( '%s', get_post_meta( $post_id, 'mstw_cs_degree', true ) );
				break;
				
			case 'birth_date' :
				printf( '%s', get_post_meta( $post_id, 'mstw_cs_birth_date', true ) );
				break;

			case 'home_town' :
				printf( '%s', get_post_meta( $post_id, 'mstw_cs_home_town', true ) );
				break;
				
			case 'high_school' :
				printf( '%s', get_post_meta( $post_id, 'mstw_cs_high_school', true ) );
				break;
				
			case 'family' :
				printf( '%s', nl2br( get_post_meta( $post_id, 'mstw_cs_family', true ) ) );
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
			'cb' 			=> '<input type="checkbox" />',
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
						'edit_posts', //'manage_options', 		// Capability required to access
						'mstw-cs-main-menu', 	// Unique menu slug
						'mstw_cs_menu_page', 	// Callback
						plugins_url( 'coaching-staffs/images/mstw-admin-menu-icon.png' ), // Menu Icon
						58 						//Menu position - right above Appearance
					 ); 
		
		add_submenu_page( 	'mstw-cs-main-menu', 							//parent slug
							__( 'Coaching Staffs', 'mstw-loc-domain' ), 	//page title
							__( 'Staffs', 'mstw-loc-domain' ),				//menu title
							'edit_posts', //'manage_options', 								//user capability required to access
							'edit-tags.php?taxonomy=staffs&post_type=staff_position', 						//unique menu slug
							'' //callback to display page
						);					
							
		add_submenu_page( 'mstw-cs-main-menu', 				//parent slug
							__( 'Coaching Staffs Display Settings', 'mstw-loc-domain' ), 	//page title
							__( 'Display Settings', 'mstw-loc-domain' ),		//menu title
							'edit_posts', //'manage_options', 				//user capability required to access
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
		
		//Data fields
		mstw_cs_data_fields_setup( );
		
		//Coaching Staff Table [shortcode] Settings
		mstw_cs_table_settings_setup( );
		
		//Single Coach Profile Settings
		mstw_cs_profile_setup( );
		
		//Coaching Staff Gallery Settings
		mstw_cs_gallery_setup( );
	}
	
	// --------------------------------------------------------------------------------------
	// Data field visibility and labels	
	function mstw_cs_data_fields_setup( ) {
		$display_page = 'mstw-cs-display-settings'; 	//menu page slug on which to display
		$page_section = 'mstw_cs_data_field_settings'; 	//page section slug on which to display
		
		$options = get_option( 'mstw_cs_options' );
		
		add_settings_section(
			$page_section,  	//id attribute of tags
			__( 'Data Field Settings', 'mstw-loc-domain' ),	//title of the section
			'mstw_cs_data_fields_settings_text',	//callback to fill section with desired output - should echo
			$display_page 	//page slug on which to display
		);
		
		//Coach's name - label only
		$args = array(	'id' => 'name_label',
						'name' => 'mstw_cs_options[name_label]',
						'value' => $options['name_label'],
						'label' => ''
						);
						
		add_settings_field(
			'name_label',
			__( 'Name Label:', 'mstw-loc-domain' ),
			'mstw_utl_text_ctrl',     		//Callback to display field
			$display_page,					//Page to display field
			$page_section, 					//Page section to display field
			$args							//Callback arguments
			);
		
		
		//Show/Hide Position
		$args = array(	'id' => 'show_position',
						'name' => 'mstw_cs_options[show_position]',
						'value' => $options['show_position'],
						'label' => ''
						);
						
		add_settings_field(
			'show_position',
			__( 'Show Position:', 'mstw-loc-domain' ),
			'mstw_utl_show_hide_ctrl',     	//Callback to display field
			$display_page,					//Page to display field
			$page_section, 					//Page section to display field
			$args							//Callback arguments
			);
			
			
			
		//Position Label
		$args = array(	'id' => 'position_label',
						'name' => 'mstw_cs_options[position_label]',
						'value' => $options['position_label'],
						'label' => ''
						);
						
		add_settings_field(
			'position_label',
			__( 'Position Label:', 'mstw-loc-domain' ),
			'mstw_utl_text_ctrl',     	//Callback to display field
			$display_page,					//Page to display field
			$page_section, 					//Page section to display field
			$args							//Callback arguments
			);
			
		//Show/Hide Experience
		$args = array(	'id' => 'show_experience',
						'name' => 'mstw_cs_options[show_experience]',
						'value' => $options['show_experience'],
						'label' => ''
						);
						
		add_settings_field(
			'show_experience',
			__( 'Show Experience:', 'mstw-loc-domain' ),
			'mstw_utl_show_hide_ctrl',     	//Callback to display field
			$display_page,					//Page to display field
			$page_section, 					//Page section to display field
			$args							//Callback arguments
			);	
			
		//Experience Label
		$args = array(	'id' => 'experience_label',
						'name' => 'mstw_cs_options[experience_label]',
						'value' => $options['experience_label'],
						'label' => ''
						);
						
		add_settings_field(
			'experience_label',
			__( 'Experience Label:', 'mstw-loc-domain' ),
			'mstw_utl_text_ctrl',     	//Callback to display field
			$display_page,					//Page to display field
			$page_section, 					//Page section to display field
			$args							//Callback arguments
			);
		
		//Show/Hide Alma-mater
		$args = array(	'id' => 'show_alma_mater',
						'name' => 'mstw_cs_options[show_alma_mater]',
						'value' => $options['show_alma_mater'],
						'label' => ''
						);
						
		add_settings_field(
			'show_alma_mater',
			__( 'Show Alma Mater:', 'mstw-loc-domain' ),
			'mstw_utl_show_hide_ctrl',     	//Callback to display field
			$display_page,					//Page to display field
			$page_section, 					//Page section to display field
			$args							//Callback arguments
			);	
			
		//Alma Mater Label
		$args = array(	'id' => 'alma_mater_label',
						'name' => 'mstw_cs_options[alma_mater_label]',
						'value' => $options['alma_mater_label'],
						'label' => ''
						);
						
		add_settings_field(
			'alma_mater_label',
			__( 'Alma Mater Label:', 'mstw-loc-domain' ),
			'mstw_utl_text_ctrl',     		//Callback to display field
			$display_page,					//Page to display field
			$page_section, 					//Page section to display field
			$args							//Callback arguments
			);
			
		//Show/Hide Degree
		$args = array(	'id' => 'show_degree',
						'name' => 'mstw_cs_options[show_degree]',
						'value' => $options['show_degree'],
						'label' => ''
						);
						
		add_settings_field(
			'show_degree',
			__( 'Show Degree:', 'mstw-loc-domain' ),
			'mstw_utl_show_hide_ctrl',     	//Callback to display field
			$display_page,					//Page to display field
			$page_section, 					//Page section to display field
			$args							//Callback arguments
			);	
			
		//Degree Label
		$args = array(	'id' => 'degree_label',
						'name' => 'mstw_cs_options[degree_label]',
						'value' => $options['degree_label'],
						'label' => ''
						);
						
		add_settings_field(
			'degree_label',
			__( 'Degree Label:', 'mstw-loc-domain' ),
			'mstw_utl_text_ctrl',     		//Callback to display field
			$display_page,					//Page to display field
			$page_section, 					//Page section to display field
			$args							//Callback arguments
			);
			
		//Show/Hide Birth Date
		$args = array(	'id' => 'show_birth_date',
						'name' => 'mstw_cs_options[show_birth_date]',
						'value' => $options['show_birth_date'],
						'label' => ''
						);
						
		add_settings_field(
			'show_birth_date',
			__( 'Show Birth Date:', 'mstw-loc-domain' ),
			'mstw_utl_show_hide_ctrl',     	//Callback to display field
			$display_page,					//Page to display field
			$page_section, 					//Page section to display field
			$args							//Callback arguments
			);	
			
		//Birth Date Label
		$args = array(	'id' => 'birth_date_label',
						'name' => 'mstw_cs_options[birth_date_label]',
						'value' => $options['birth_date_label'],
						'label' => ''
						);
						
		add_settings_field(
			'birth_date_label',
			__( 'Birth Date Label:', 'mstw-loc-domain' ),
			'mstw_utl_text_ctrl',     		//Callback to display field
			$display_page,					//Page to display field
			$page_section, 					//Page section to display field
			$args							//Callback arguments
			);
		
		//Show/Hide Hometown
		$args = array(	'id' => 'show_home_town',
						'name' => 'mstw_cs_options[show_home_town]',
						'value' => $options['show_home_town'],
						'label' => ''
						);
						
		add_settings_field(
			'show_home_town',
			__( 'Show Hometown:', 'mstw-loc-domain' ),
			'mstw_utl_show_hide_ctrl',     	//Callback to display field
			$display_page,					//Page to display field
			$page_section, 					//Page section to display field
			$args							//Callback arguments
			);	
			
		//Hometown Label
		$args = array(	'id' => 'home_town_label',
						'name' => 'mstw_cs_options[home_town_label]',
						'value' => $options['home_town_label'],
						'label' => ''
						);
						
		add_settings_field(
			'home_town_label',
			__( 'Hometown Label:', 'mstw-loc-domain' ),
			'mstw_utl_text_ctrl',     		//Callback to display field
			$display_page,					//Page to display field
			$page_section, 					//Page section to display field
			$args							//Callback arguments
			);
		
		//Show/Hide High School
		$args = array(	'id' => 'show_high_school',
						'name' => 'mstw_cs_options[show_high_school]',
						'value' => $options['show_high_school'],
						'label' => ''
						);
						
		add_settings_field(
			'show_high_school',
			__( 'Show High School:', 'mstw-loc-domain' ),
			'mstw_utl_show_hide_ctrl',     	//Callback to display field
			$display_page,					//Page to display field
			$page_section, 					//Page section to display field
			$args							//Callback arguments
			);	
			
		//Hometown Label
		$args = array(	'id' => 'high_school_label',
						'name' => 'mstw_cs_options[high_school_label]',
						'value' => $options['high_school_label'],
						'label' => ''
						);
						
		add_settings_field(
			'high_school_label',
			__( 'High School Label:', 'mstw-loc-domain' ),
			'mstw_utl_text_ctrl',     		//Callback to display field
			$display_page,					//Page to display field
			$page_section, 					//Page section to display field
			$args							//Callback arguments
			);

		//Show/Hide Family
		$args = array(	'id' => 'show_family',
						'name' => 'mstw_cs_options[show_family]',
						'value' => $options['show_family'],
						'label' => ''
						);
						
		add_settings_field(
			'show_family',
			__( 'Show Family:', 'mstw-loc-domain' ),
			'mstw_utl_show_hide_ctrl',     	//Callback to display field
			$display_page,					//Page to display field
			$page_section, 					//Page section to display field
			$args							//Callback arguments
			);	
			
		//Hometown Label
		$args = array(	'id' => 'family_label',
						'name' => 'mstw_cs_options[family_label]',
						'value' => $options['family_label'],
						'label' => ''
						);
						
		add_settings_field(
			'family_label',
			__( 'Family Label:', 'mstw-loc-domain' ),
			'mstw_utl_text_ctrl',     		//Callback to display field
			$display_page,					//Page to display field
			$page_section, 					//Page section to display field
			$args							//Callback arguments
			);
			
		
		
	}
	
	// --------------------------------------------------------------------------------------
	// Staff table settings	
	function mstw_cs_table_settings_setup( ) {
		$display_page = 'mstw-cs-display-settings'; 	//menu page slug on which to display
		$page_section = 'mstw_cs_table_settings'; 	//page section slug on which to display
		
		$options = get_option( 'mstw_cs_options' );
		
		add_settings_section(
			$page_section,  	//id attribute of tags
			__( 'Staff Table/[shortcode] Settings', 'mstw-loc-domain' ),	//title of the section
			'mstw_cs_table_settings_text',	//callback to fill section with desired output - should echo
			$display_page 	//page slug on which to display
		);
		
		// Show/Hide Staff Table Title
		$foo = '';
		if ( isset( $options['show_title'] ) )
			$foo = $options['show_title'];
			
		//$foo = isset( $options['show_title'] ) ? $options['show_title'] : '';
		$foo = mstw_cs_safe_ref( $options, 'show_title' );
		
		$args = array(	'id' => 'show_title',
						'name' => 'mstw_cs_options[show_title]',
						'value' => $foo, //$options['show_title'], //mstw_cs_admin_safe_ref( $options, 'show_title' ),
						'label' => __( 'Will use "Staff Name" from Staff taxonomy as default. Hide to use another page element for the table title.', 'mstw-loc-domain')
						);
						
		add_settings_field(
			'show_title',
			__( 'Show Staff Table Titles:', 'mstw-loc-domain' ),
			'mstw_utl_show_hide_ctrl',     	//Callback to display field
			$display_page,					//Page to display field
			$page_section, 					//Page section to display field
			$args							//Callback arguments
			);
		
		//$foo = isset( $options['table_title_color'] ) ? $options['table_title_color'] : '';
		$foo = mstw_cs_safe_ref( $options, 'table_title_color' );
		
		// Staff Table[shortcode] Title Color
		$args = array( 	'id' => 'table_title_color',
						'name' => 'mstw_cs_options[table_title_color]',
						'value' => mstw_cs_safe_ref( $options, 'table_title_color' ), //$options['table_title_color'], //mstw_cs_admin_safe_ref( $options, 'table_title_color' ),
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
						'value' => $options['show_photos'], //mstw_cs_admin_safe_ref( $options, 'show_photos' ),
						'label' => __( 'Will show coaches photos in the staff tables', 'mstw-loc-domain')
						);
						
		add_settings_field(
			'show_photos',
			__( "Show Photos:", 'mstw-loc-domain' ),
			'mstw_utl_show_hide_ctrl',		//Callback to display field
			$display_page,					//Page to display field
			$page_section, 					//Page section to display field
			$args							//Callback arguments
			);
		

		// Coaches' Photos Width
		$args = array( 	'id' => 'table_photo_width',
						'name'	=> 'mstw_cs_options[table_photo_width]',
						'value'	=> mstw_cs_safe_ref( $options, 'table_photo_width' ), //$options['table_photo_width'], //mstw_cs_admin_safe_ref( $options, 'table_photo_width' ),
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
						'value'	=> mstw_cs_safe_ref( $options, 'table_photo_height' ), //$options['table_photo_height'], //mstw_cs_admin_safe_ref( $options, 'table_photo_height' ),
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
						'label' => __( 'Set table border width in pixels (default:2px)', 'mstw-loc-domain' ),
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
		
		// Staff Table[shortcode] Even Row Text Color
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
		
		// Staff Table[shortcode] Even Row Background Color
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
		
		// Staff Table[shortcode] Even Row Link Color
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

	// Staff Table[shortcode] Odd Row Text Color
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
		
		// Staff Table[shortcode] Odd Row Background Color
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

		// Staff Table[shortcode] Odd Row Link Color
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
	
	// --------------------------------------------------------------------------------------
	// Single coach profile settings
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
			__( 'Profile Heading:', 'mstw-loc-domain' ),
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
		
		// Profile Header Text Color
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
			__( 'Name Color:', 'mstw-loc-domain' ),
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
			__( 'Position Color:', 'mstw-loc-domain' ),
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
			__( 'Bio Section Heading Color:', 'mstw-loc-domain' ),
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
			__( 'Bio Section Text Color:', 'mstw-loc-domain' ),
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
			__( 'Bio Section Background Color:', 'mstw-loc-domain' ),
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
			__( 'Bio Section Border Color:', 'mstw-loc-domain' ),
			'mstw_utl_color_ctrl',
			$display_page,					//Page to display field
			$page_section, 					//Page section to display field
			$args
		);
		
		// Profile Bio Border Width
		$args = array( 	'id' => 'profile_bio_border_width',
						'name'	=> 'mstw_cs_options[profile_bio_border_width]',
						'value'	=> $options['profile_bio_border_width'],
						'label'	=> __( 'Set bio border width in pixels (default:2px)', 'mstw-loc-domain' )
						);
						
		add_settings_field(
			'profile_bio_border_width',
			__( 'Bio Section Border Width:', 'mstw-loc-domain' ),
			'mstw_utl_text_ctrl',
			$display_page,					//Page to display field
			$page_section, 					//Page section to display field
			$args
		);	
	}
	
	// --------------------------------------------------------------------------------------
	// Coaches Gallery settings	
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
		
		// Show/Hide Gallery Title
		$args = array(	'id' => 'show_gallery_title',
						'name' => 'mstw_cs_options[show_gallery_title]',
						'value' => $options['show_gallery_title'],
						'label' => __( 'Will use "Staff Name" from Staff taxonomy as default. Hide to use another page element for the table title.', 'mstw-loc-domain')
						);
						
		add_settings_field(
			'show_gallery_title',
			__( "Show Gallery Title:", 'mstw-loc-domain' ),
			'mstw_utl_show_hide_ctrl',		//Callback to display field
			$display_page,					//Page to display field
			$page_section, 					//Page section to display field
			$args							//Callback arguments
			);
		
		// Gallery Title Color
		$args = array( 	'id' => 'gallery_title_color',
						'name' => 'mstw_cs_options[gallery_title_color]',
						'value' => $options['gallery_title_color'],
						'label' => ''
					 );
					 
		add_settings_field(
			'gallery_title_color',
			__( 'Gallery Title Color:', 'mstw-loc-domain' ),
			'mstw_utl_color_ctrl',
			$display_page,					//Page to display field
			$page_section, 					//Page section to display field
			$args
		);	
		
		//Tile Corner Style
		$args = array(	'id' => 'gallery_tile_radius',
						'name' => 'mstw_cs_options[gallery_tile_radius]',
						'value'	=> $options['gallery_tile_radius'],
						'label' => __( 'Default is rounded.', 'mstw-loc-domain' ),
						'options' => array( __( 'Rounded', 'mstw-loc-doman' ) => 15,
											__( 'Square', 'mstw-loc-doman' ) => 0,
											),
						);
						
		add_settings_field(
			'gallery_tile_radius',
			__( 'Gallery Tile Corner Style:', 'mstw-loc-domain' ),
			'mstw_utl_select_option_ctrl',
			$display_page,					//Page to display field
			$page_section, 					//Page section to display field
			$args
		);

		// Coaches' Gallery Photo Width
		$args = array( 	'id' => 'gallery_photo_width',
						'name'	=> 'mstw_cs_options[gallery_photo_width]',
						'value'	=> $options['gallery_photo_width'],
						'label'	=> __( 'Set width in pixels for gallery photos, if shown. (Default: 150px)', 'mstw-loc-domain' )
						);
						
		add_settings_field(
			'gallery_photo_width',
			__( 'Gallery Photo Width:', 'mstw-loc-domain' ),
			'mstw_utl_text_ctrl',
			$display_page,					//Page to display field
			$page_section, 					//Page section to display field
			$args
		);
		
		// Coaches' Gallery Photo Height
		$args = array( 	'id' => 'gallery_photo_height',
						'name'	=> 'mstw_cs_options[gallery_photo_height]',
						'value'	=> $options['gallery_photo_height'],
						'label'	=> __( 'Set height in pixels for gallery photos, if shown. (Default: 150px)', 'mstw-loc-domain' )
						);
						
		add_settings_field(
			'gallery_photo_height',
			__( 'Gallery Photo Height:', 'mstw-loc-domain' ),
			'mstw_utl_text_ctrl',
			$display_page,					//Page to display field
			$page_section, 					//Page section to display field
			$args
		);
		
		// Gallery Tile Border Color
		$args = array( 	'id' => 'gallery_tile_border_color',
						'name' => 'mstw_cs_options[gallery_tile_border_color]',
						'value' => $options['gallery_tile_border_color'],
						'label' => ''
					 );
					 
		add_settings_field(
			'gallery_tile_border_color',
			__( 'Gallery Tile Border Color:',  'mstw-loc-domain' ),
			'mstw_utl_color_ctrl',
			$display_page,					//Page to display field
			$page_section, 					//Page section to display field
			$args
		);
		
		// Gallery Tile Border width
		$args = array( 	'id' => 'gallery_tile_border_width',
						'name' => 'mstw_cs_options[gallery_tile_border_width]',
						'value' => $options['gallery_tile_border_width'],
						'label' => __( 'Set border width in pixels. (Default: 2px)', 'mstw-loc-domain' ),
					 );
					 
		add_settings_field(
			'gallery_tile_border_width',
			__( 'Gallery Tile Border Width:', 'mstw-loc-domain' ),
			'mstw_utl_text_ctrl',
			$display_page,					//Page to display field
			$page_section, 					//Page section to display field
			$args
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
	
	function mstw_cs_data_fields_settings_text( ) {
		echo '<p>' . __( 'Enter the visibility and labels for the data fiels. The name field must always be displayed, but it can be re-labeled. Note that these settings can be overridden by [shortcode] arguments.', 'mstw-loc-domain' ) .  '</p>';
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
					case 'gallery_tile_border_color':
					case 'gallery_title_color':
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
					case 'gallery_photo_width':
					case 'gallery_photo_height':
					case 'gallery_tile_border_width':
					case 'gallery_tile_radius':
						$output[$key] = $input[$key] == '' ? '' : intval( $input[$key] );
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
	
	function mstw_cs_safe_ref( $array, $ref ) {
		$foo = isset( $array[$ref] ) ? $array[$ref] : '';
		return $foo;
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