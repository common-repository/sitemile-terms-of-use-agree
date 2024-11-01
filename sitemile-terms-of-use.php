<?php
/*
Plugin Name:  SiteMile Terms Of Use Agree
Plugin URI:   https://sitemile.com/
Description:  Show the Terms of Use and/or Privacy Policy of the website on the registration page and/or Login Page.
Version:      1.4
Author:       sitemie.com
Author URI:   https://sitemile.com
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  sitemiletos
Domain Path:  /languages

SiteMile Terms Of Use Agree is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
SiteMile Terms Of Use Agree is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with SiteMile Terms Of Use Agree. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/



/* When the plugin is activated run the activation hook */

register_activation_hook(__FILE__, 	'sitemile_terms_of_use_get_version');
add_action('admin_menu', 			'sitemile_terms_of_use_admin_actions');  
add_action('admin_notices', 		'sitemiletos_admin_notice');
add_action('register_form',			'sitemile_tos_show_terms_of_use');
add_action('register_post',			'sitemiletos_check_fields',10,3);
register_deactivation_hook( __FILE__, 'sitemiletos_pluginprefix_deactivation' );

function sitemiletos_pluginprefix_deactivation() {
 
    // lets flush this
    flush_rewrite_rules();
}



/* We need to get the version of WordPress the user is using */
function sitemile_terms_of_use_get_version() {
	global $wp_version;
	if (version_compare($wp_version, '4.8', '<')) {
		exit("<div style='font-size: 13px; font-family: 'HelveticaNeue-Light','Helvetica Neue Light','Helvetica Neue',sans-serif;'><strong>ERROR:</strong> This plugin will not work with your version of WordPress. Update to the latest version.</div>");
	}
}


/* Register the Terms of Use options page */
function sitemile_terms_of_use_admin_actions() {  
    add_options_page("SiteMile TOS", "SiteMile Terms of Use", 1, "sitemile-tos-terms-of-use", "sitemile_tos_terms_admin");  
} 



//***************************************************
//*
//*	admin options
//*
//**************************************************


function sitemile_tos_terms_admin() { 
remove_action( 'admin_notices', 'sitemiletos_admin_notice');
 	echo '<div class="wrap">';
    	echo '<h2>' . __( 'SiteMile Themes & Plugins - Terms of Use', 'sitemiletos_tos' ) . '</h2>';
    	/* Check to see if the post has been submitted */
		
		
		$tmz 	= sanitize_text_field($_POST['termsofuse']);
		$tmz2 	= sanitize_text_field($_POST['page-dropdown']);
		
		
		if($tmz and current_user_can( 'administrator' )) {
			/* If the page is empty return an error */
			if(empty($tmz2)) {
				echo '<div class="error"><p><strong>ERROR</strong>: You must select a page.</p></div>';
			} else {	
		
		
		
			$sitemiletos_page 	= sanitize_text_field($_POST['page-dropdown']);
			$sitemiletos_msg 	= '1';
			update_option('sitemiletos_page_id', $sitemiletos_page);
			update_option('sitemiletos_msg', $sitemiletos_msg);
			echo '<div class="updated"><p><strong>Options Saved.</strong></p></div>';
			}    	
		}
		
		echo '<p>Please select your Terms of Use page.</p>';    	
    	echo '<form method="POST" action="'.admin_url("admin.php?page=sitemile-tos-terms-of-use"). '" id="termsofuse">';

		/* Grab a list of pages and sub pages of the website */
		$sitemiletos_page_id = get_option('sitemiletos_page_id'); 
 			$args = array(
    			'depth'            => 0,
    			'child_of'         => 0,
    			'selected'         => $sitemiletos_page_id,
    			'echo'             => 1,
    			'name'             => 'page-dropdown',
    			'show_option_none'  => '-- Please Select -- ');
    			
		wp_dropdown_pages($args);	    	
		echo '<br /><br /><input name="termsofuse" type="submit" value="Update" class="button-primary">';    	
    	echo '</form>';
    echo '</div>';
    echo '<div id="wpbody">';
    	echo '<div id="wpbody-content">';
    		echo '<div class="wrap">';
    			echo '<h2>Support</h2>';
    			echo '<p>Need help with one of our plugins? Head on over to our <a href="https://www.sitemile.com/contact-us" target="_blank">contact form</a> today.</p>';
    		echo '</div>';
    		echo '<div class="wrap">';
    			echo '<h2>Donate</h2>';
    			echo '<p>Like our work? Why not support us by making a small donation. All donations go towards future development of our scripts and server costs.';
    			echo '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="759XBPJNJV3VS">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>

';
    		echo '</div>';
    	echo '</div>';
    echo '</div>';
} 

function sitemiletos_admin_notice(){
$page_name = $_GET['page'];
	if(get_option('sitemiletos_msg') == '0') {
		if($page_name == 'sitemile-tos-terms-of-use') { 
		} else {
    echo '<div class="updated">
       <p><strong>Error:</strong> Please visit the Sitemile Terms of Use <a href="'.get_admin_url().'options-general.php?page=sitemile-tos-terms-of-use">Settings</a> page to setup the plugin.</p>
    </div>';
    	}
    }
}




function sitemiletos_check_fields($login, $email, $errors) { 
   
   $ag = sanitize_text_field($_POST['agree']);
   
   if($ag == '') {
   	global $agree;
   	$errors->add('empty_agree', __("<strong>ERROR</strong>: You must Agree to our Terms of Use.",'sitemiletos'));
   } else {
   	$agree = sanitize_text_field($_POST['agree']);
	$agree = sanitize_key($agree);
   }
}

function sitemile_tos_show_terms_of_use() {
	$sitemiletos_page_id = get_option('sitemiletos_page_id'); 
	if(!empty($sitemiletos_page_id)) {
 	$permalink = get_permalink( $sitemiletos_page_id );
	echo '<p><input type="checkbox" name="agree" value="1"> '. sprintf(__('Agree to <a href="%s" target="_blank" title="Click to view our Terms of Use">Terms of Use</a>','sitemiletos'), $permalink) . '.<br /><br /></p>';
	}
}


?>
