<?php
/*----------------------------------------------------------
 *	MSTW-CS-UTILITY-FUNCTIONS.PHP
 *	mstr_cs_get_defaults( ) - returns the default option settings
 *	
 *---------------------------------------------------------*/

/*---------------------------------------------------------------------------------
 *	mstw_cs_get_defaults: returns the array of option defaults
 *-------------------------------------------------------------------------------*/	
	function mstw_cs_get_defaults( ) {
		//Base defaults
		$defaults = array(	
				'staff'					=> "",
				'show_title'			=> 1,	// table title only
				'show_photos'			=> 1,   //in table only
				'photo_label'			=> __( 'Photo', 'mstw-loc-domain' ),
				'name_label'			=> __( 'Name', 'mstw-loc-domain' ),
				'position_label'		=> __( 'Position', 'mstw-loc-domain' ),
				'show_position'			=> 1,
				'experience_label'		=> __( 'Experience', 'mstw-loc-domain' ),
				'show_experience'		=> 1,
				'alma_mater_label'		=> __( 'Alma Mater', 'mstw-loc-domain' ),
				'show_alma_mater'		=> 1,
				'degree_label'			=> __( 'Degree', 'mstw-loc-domain' ),
				'show_degree'			=> 0,
				'birth_date_label'		=> __( 'Born', 'mstw-loc-domain' ),
				'show_birth_date'		=> 0,
				'home_town_label'		=> __( 'Hometown', 'mstw-loc-domain' ),
				'show_home_town'		=> 0,
				'high_school_label'		=> __( 'High School', 'mstw-loc-domain' ),
				'show_high_school'		=> 0,
				'family_label'			=> __( 'Family', 'mstw-loc-domain' ),
				'show_family'			=> 0,
				
				//'table_photo_width'	=> 80,
				//'table_photo_height'	=> 80,
				
				'profile_bio_heading_text' => 'Profile',
				//'profile_bio_border_width' => 2,
				
				'show_gallery_title'	=> 1, 
				//'gallery_photo_width'	=> 150,
				//'gallery_photo_height'	=> 150,
				);
				
		return $defaults;
	}
	?>
