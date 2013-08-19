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
				'show_title'			=> 1,
				'show_photos'			=> 1,
				'show_position'			=> 1,
				'show_alma_mater'		=> 1,
				'show_experience'		=> 1,
				'table_photo_width'		=> 64,
				'table_photo_height'	=> 64,
				'profile_bio_heading_text' => 'Profile',
				'profile_bio_border_width' => 2,		
				);
				
		return $defaults;
	}
	?>
