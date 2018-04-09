<?php
/**
 * @package Boss Child Theme
 * The parent theme functions are located at /boss/buddyboss-inc/theme-functions.php
 * Add your own functions in this file.
 */

/**
 * Sets up theme defaults
 *
 * @since Boss Child Theme 1.0.0
 */
function boss_child_theme_setup()
{
  /**
   * Makes child theme available for translation.
   * Translations can be added into the /languages/ directory.
   * Read more at: http://www.buddyboss.com/tutorials/language-translations/
   */

  // Translate text from the PARENT theme.
  load_theme_textdomain( 'boss', get_stylesheet_directory() . '/languages' );

  // Translate text from the CHILD theme only.
  // Change 'boss' instances in all child theme files to 'boss_child_theme'.
  // load_theme_textdomain( 'boss_child_theme', get_stylesheet_directory() . '/languages' );

}
add_action( 'after_setup_theme', 'boss_child_theme_setup' );

/**
 * Enqueues scripts and styles for child theme front-end.
 *
 * @since Boss Child Theme  1.0.0
 */
function boss_child_theme_scripts_styles()
{
  /**
   * Scripts and Styles loaded by the parent theme can be unloaded if needed
   * using wp_deregister_script or wp_deregister_style.
   *
   * See the WordPress Codex for more information about those functions:
   * http://codex.wordpress.org/Function_Reference/wp_deregister_script
   * http://codex.wordpress.org/Function_Reference/wp_deregister_style
   **/

  /*
   * Styles
   */
  wp_enqueue_style( 'boss-child-custom', get_stylesheet_directory_uri().'/css/custom.css' );
}
add_action( 'wp_enqueue_scripts', 'boss_child_theme_scripts_styles', 9999 );


/****************************** CUSTOM FUNCTIONS ******************************/

// Add your own custom functions here



// Function to change email address

function wpb_sender_email( $original_email_address ) {
    return 'team@ggcn.org';
}

// Function to change sender name
function wpb_sender_name( $original_email_from ) {
	return 'GGCN';
}

// Hooking up our functions to WordPress filters
add_filter( 'wp_mail_from', 'wpb_sender_email' );
add_filter( 'wp_mail_from_name', 'wpb_sender_name' );




if ( ! class_exists( 'DisableSearch' ) && !is_user_logged_in() ) :

class DisableSearch {

	public static function init() {
		add_filter( 'get_search_form', array( __CLASS__, 'get_search_form' ), 999 );
	}


	public static function get_search_form( $form ) {
		return '';
	}
}

DisableSearch::init();

endif;


// Redirects users to activity page after login.
if( !function_exists('custom_user_login_redirect') ) {
function custom_user_login_redirect() {
$redirect_to = 'https://connect.ggcn.org/activity';
return $redirect_to;
}
add_filter('login_redirect','custom_user_login_redirect',10,3);
}




// disable random password for password reset page.
add_filter( 'random_password', 'itsg_disable_random_password', 10, 2 );

function itsg_disable_random_password( $password ) {
    $action = isset( $_GET['action'] ) ? $_GET['action'] : '';
    if ( 'wp-login.php' === $GLOBALS['pagenow'] && ( 'rp' == $action  || 'resetpass' == $action ) ) {
        return '';
    }
    return $password;
}


function  user_online_update(){

	if ( is_user_logged_in()) {

		// get the user activity the list
		$logged_in_users = get_transient('online_status');

		// get current user ID
		$user = wp_get_current_user();

		// check if the current user needs to update his online status;
		// he does if he doesn't exist in the list
		$no_need_to_update = isset($logged_in_users[$user->ID])

		    // and if his "last activity" was less than let's say ...1 minutes ago
		    && $logged_in_users[$user->ID] >  (time() - (1 * 60));

		// update the list if needed
		if(!$no_need_to_update){
		  $logged_in_users[$user->ID] = time();
		  set_transient('online_status', $logged_in_users, (2*60)); // 2 mins
		}
	}
}
add_action( 'wp', 'user_online_update' );



?>
