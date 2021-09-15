<?php

//Block-Pattern für den Blauen Info Block für Lehrkräfte definieren

define('PATTERN_POST_TYPE' , 'blockmeister_pattern');
define('PATTERN_DIDAKTIK_INFO_SLUG' , 'infos-fuer-den-unterrichtseinsatz');


include_once "inc/class_dashboard.php";
include_once "inc/class_material.php";
include_once "inc/class_license.php";

/*-------------------------------------*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if ( !function_exists( 'chld_thm_cfg_locale_css' ) ):
    function chld_thm_cfg_locale_css( $uri ){
        if ( empty( $uri ) && is_rtl() && file_exists( get_template_directory() . '/rtl.css' ) )
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter( 'locale_stylesheet_uri', 'chld_thm_cfg_locale_css' );

if ( !function_exists( 'child_theme_configurator_css' ) ):
    function child_theme_configurator_css() {
        wp_enqueue_style( 'chld_thm_cfg_child', trailingslashit( get_stylesheet_directory_uri() ) . 'style.css', array( 'ct-main-styles' ) );
    }
endif;
add_action( 'wp_enqueue_scripts', 'child_theme_configurator_css', 10 );

// END ENQUEUE PARENT ACTION

add_action( 'after_setup_theme', 'relilab_gutenberg_css' );

function misha_gutenberg_css(){

	add_theme_support( 'editor-styles' ); // if you don't add this line, your stylesheet won't be added
	add_editor_style( trailingslashit( get_stylesheet_directory_uri() ).'style-editor.css' ); // tries to include style-editor.css directly from your theme folder

}


add_filter( 'widget_text', function ($content){

    return do_shortcode($content);
});


/**
 * fixes blocksy bug: display the correct code form customizer settings
 */
add_filter("widget_display_callback",function ( $instance, $widget, $args){

	$instance["content"] = str_replace('<h2>',$args["before_title"],$instance["content"]);
	$instance["content"] = str_replace('</h2>',$args["after_title"],$instance["content"]);

	return ($instance);
}, 1,3);


/*********** GET THE EXCERPT FROM POST META VALUE **************************/
/**
 * forked blocky core function
 * @param int $length
 * @param string $class
 * @param null $post_id
 *
 * @return false|string
 */
function blocksy_entry_excerpt($length = 40, $class = 'entry-excerpt', $post_id = null) {

	global $post;

	$has_native_excerpt = $post->post_excerpt;

	if ($has_native_excerpt) {
		ob_start();
		blocksy_trim_excerpt(get_the_excerpt($post_id), $length);
		$excerpt = trim(ob_get_clean());
	}
	if (! $excerpt) {


		$excerpt = trim(get_metadata('post',$post->ID,'excerpt',true),$length);

	}
	ob_start();

	?>

	<div class="<?php echo esc_attr($class) ?>">
		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo wp_kses_post($excerpt);
		?>
	</div>

	<?php

	return ob_get_clean();
}

/*********** CUSTOM TAXONOMIES ***********Begin*/
/**
 * blocksy filter blocksy:post-meta:items
 *
 * addIcons to custom taxonomies autoren and lizenz to the cards
 *
 * @param $to_return
 * @param $post_meta_descriptor
 * @param $args
 *
 * @return array|string|string[]
 */
function blocksy_get_meta_icons($to_return,	$post_meta_descriptor,	$args){

    if(class_material::is_learnview()){
		return '';
	}
	$by = '<svg width="13" height="13" viewBox="0 0 15 15"><path d="M13.6,1.4c-1.9-1.9-4.9-1.9-6.8,0L2.2,6C2.1,6.1,2,6.3,2,6.5V12l-1.8,1.8c-0.3,0.3-0.3,0.7,0,1C0.3,14.9,0.5,15,0.7,15s0.3-0.1,0.5-0.2L3,13h5.5c0.2,0,0.4-0.1,0.5-0.2l2.7-2.7c0,0,0,0,0,0l1.9-1.9C15.5,6.3,15.5,3.3,13.6,1.4z M8.2,11.6H4.4l1.4-1.4h3.9L8.2,11.6z M12.6,7.2L11,8.9H7.1l3.6-3.6c0.3-0.3,0.3-0.7,0-1C10.4,4,10,4,9.7,4.3L5,9.1c0,0,0,0,0,0l-1.6,1.6V6.8l4.4-4.4c1.3-1.3,3.5-1.3,4.8,0C14,3.7,14,5.9,12.6,7.2C12.6,7.2,12.6,7.2,12.6,7.2z"/></svg>';
	$copyright = '<svg width="16" height="15" viewBox="0 0 16 15" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;"><g id="Layer_x0020_1"><g id="_1761831900240"><path d="M8.02,2.603c1.017,-1.102 2.206,-1.604 3.582,-1.409c1.421,0.2 2.63,1.216 3.06,2.584c0.331,1.054 0.194,2.233 -0.249,3.309c-0.689,1.673 -2.726,4.051 -6.413,5.714c-3.687,-1.663 -5.724,-4.041 -6.413,-5.714c-0.443,-1.076 -0.58,-2.255 -0.249,-3.309c0.43,-1.368 1.639,-2.384 3.06,-2.584c1.376,-0.195 2.565,0.307 3.582,1.409c0.002,0.002 0.02,0.021 0.02,0.021c0,0 0.018,-0.019 0.02,-0.021l-0,-0Zm-0.02,-1.563c-2.223,-1.745 -5.707,-1.29 -7.309,1.332c-0.532,0.871 -0.731,1.883 -0.684,2.888c0.081,1.743 0.833,3.267 1.939,4.582c1.488,1.77 3.639,3.161 6.007,4.2c0.003,0.001 0.031,0.013 0.047,0.02c0.014,-0.006 0.04,-0.017 0.047,-0.021c2.356,-1.038 4.519,-2.429 6.007,-4.199c1.106,-1.315 1.858,-2.839 1.939,-4.582c0.047,-1.005 -0.152,-2.017 -0.684,-2.888c-1.602,-2.622 -5.086,-3.077 -7.309,-1.332l-0,-0Z"/><path d="M7.943,7.475l-1.012,-0.501c-0.351,0.513 -0.607,0.86 -1.347,0.657c-0.58,-0.159 -0.752,-0.795 -0.774,-1.328c-0.03,-0.729 0.225,-1.57 1.095,-1.525c0.556,0.028 0.793,0.356 0.92,0.651l1.105,-0.566c-0.544,-1.039 -1.82,-1.392 -2.901,-1.102c-1.15,0.308 -1.733,1.366 -1.712,2.507c0.02,1.167 0.58,2.174 1.783,2.437c0.587,0.129 1.226,0.099 1.769,-0.173c0.42,-0.211 0.887,-0.618 1.074,-1.057l0,0Zm4.77,0l-1.012,-0.501c-0.351,0.513 -0.607,0.86 -1.346,0.657c-0.581,-0.159 -0.752,-0.795 -0.774,-1.328c-0.031,-0.729 0.224,-1.57 1.094,-1.525c0.557,0.028 0.793,0.356 0.92,0.651l1.105,-0.566c-0.544,-1.039 -1.82,-1.392 -2.901,-1.102c-1.15,0.308 -1.733,1.366 -1.712,2.507c0.021,1.167 0.581,2.174 1.783,2.437c0.587,0.129 1.226,0.099 1.769,-0.173c0.42,-0.211 0.887,-0.618 1.074,-1.057l0,0Z"/></g></g></svg>';
    $date = '<svg width="13" height="13" viewBox="0 0 15 15"><path d="M7.5,0C3.4,0,0,3.4,0,7.5S3.4,15,7.5,15S15,11.6,15,7.5S11.6,0,7.5,0z M7.5,13.6c-3.4,0-6.1-2.8-6.1-6.1c0-3.4,2.8-6.1,6.1-6.1c3.4,0,6.1,2.8,6.1,6.1C13.6,10.9,10.9,13.6,7.5,13.6z M10.8,9.2c-0.1,0.2-0.4,0.4-0.6,0.4c-0.1,0-0.2,0-0.3-0.1L7.2,8.1C7,8,6.8,7.8,6.8,7.5V4c0-0.4,0.3-0.7,0.7-0.7S8.2,3.6,8.2,4v3.1l2.4,1.2C10.9,8.4,11,8.8,10.8,9.2z"/></svg>';
    $cat = '<svg width="13" height="13" viewBox="0 0 15 15"><path d="M14.4,1.2H0.6C0.3,1.2,0,1.5,0,1.9V5c0,0.3,0.3,0.6,0.6,0.6h0.6v7.5c0,0.3,0.3,0.6,0.6,0.6h11.2c0.3,0,0.6-0.3,0.6-0.6V5.6h0.6C14.7,5.6,15,5.3,15,5V1.9C15,1.5,14.7,1.2,14.4,1.2z M12.5,12.5h-10V5.6h10V12.5z M13.8,4.4H1.2V2.5h12.5V4.4z M5.6,7.5c0-0.3,0.3-0.6,0.6-0.6h2.5c0.3,0,0.6,0.3,0.6,0.6S9.1,8.1,8.8,8.1H6.2C5.9,8.1,5.6,7.8,5.6,7.5z"/></svg>';
    $tag = '<svg width="13" height="13" viewBox="0 0 15 15"><path d="M5.7,14.4L0.6,9.3c0,0,0,0,0,0c-0.8-0.8-0.8-2.2,0-3l6.1-6.1C6.8,0.1,7,0,7.2,0l7.1,0C14.7,0,15,0.3,15,0.7v7.1c0,0.2-0.1,0.4-0.2,0.5l-6.1,6.1c-0.4,0.4-1,0.6-1.5,0.6C6.7,15,6.1,14.8,5.7,14.4zM13.6,1.4H7.5L1.6,7.3c-0.3,0.3-0.3,0.7,0,1l5.1,5.1c0.3,0.3,0.7,0.3,1,0l5.9-5.9V1.4zM1.1,8.8L1.1,8.8L1.1,8.8zM10.7,5c0.4,0,0.7-0.3,0.7-0.7c0-0.4-0.3-0.7-0.7-0.7h0c-0.4,0-0.7,0.3-0.7,0.7C10,4.6,10.4,5,10.7,5z"/></svg>';

	$to_return = str_replace('By',$by, $to_return);
	$to_return = str_replace('Copyright',$copyright, $to_return);
	$to_return = str_replace('At',$date, $to_return);
	$to_return = str_replace('In',$cat, $to_return);
	$to_return = str_replace('Tags',$tag, $to_return);

    return $to_return;
}
add_filter('blocksy:post-meta:items','blocksy_get_meta_icons',10,3);
/*********** CUSTOM TAXONOMIES **************END*/

/*********** HELPER FUUNCTIONS ************Begin*/
/**
 * full render content editor blocks
 *
 * @param $block
 *
 * @return mixed|void
 */
function render_content_block($block){
	return apply_filters( 'the_content', render_block( $block ) );
}

function d_log($string){

    if (!is_string($string)){
        $string = json_encode($string);
    }
    file_put_contents(dirname(__FILE__).'/debug.log', $string."\n", FILE_APPEND);
}
/*********** HELPER FUNCTIONS *************End*/

/*********** LEARN VIEW **************** Begin*/

function learnview_endpoint_init(){
	add_rewrite_endpoint( 'learnview', EP_PERMALINK  );
}
add_action( 'init', 'learnview_endpoint_init' );

function add_query_vars_filter($vars){
	$vars[] = "learnview";
	return $vars;
}
//add_filter('query_vars','add_query_vars_filter');


/**
 * Change template based on learnview_endpoint
 *
 * @param String $template
 *
 * @return String $template
 */
function learnview_template_mods( $template ) {

    global $wp_query;

    if(isset($wp_query->query_vars['learnview']) ){
	    // Attempt to locate our template
	    $new_template = locate_template( array( 'learnview.php' ) );

	    // If the template was found, switch it
	    if( ! empty( $new_template ) ) {
		    $template = $new_template;
	    }

    }

	return $template;

}
add_filter( 'template_include', 'learnview_template_mods' );
/***** LEARN VIEW ***** End*/

//display welcome message on sortcode [display_user_login_logout]additional Message[/display_user_login_logout]
function display_user_welcome_shortcode($atts = array(),$content=""){
	global $current_user;

	$user = wp_get_current_user();

	if ( is_user_logged_in() ) {
		$loginout = '<p>Am Ende kannst du dich hier ' . strtolower( wp_loginout( '', false ) ) . '.</p>';

		$list_url  = home_url() . '/autoren/' . $user->user_login;
		$admin_url = admin_url() . 'edit.php?post_type=material&author_name=' . $user->user_login;

		$return = '<p><strong>Willkommen, ' . $current_user->display_name . '!</strong></p>';
		$return .= '<ul><li><a href="' . $list_url . '">Hier findest du <strong>deine OER-Materialien</strong></a>.</li>';
		$return .= '<li>Um sie zu <a href="' . $admin_url . '">bearbeiten</a>, wechselst du am besten in die ';
		$return .= '<a href="' . $admin_url . '"><strong>Autorenansicht</strong></a></li></ul>';
		$return .= $loginout;

		if($content)
			$return =  '<p>'.$content.'</p>';


	} else {

	    $loginout = str_replace('<a' ,'<a class="button"', wp_loginout(home_url(),false));

	    if($content)
	        $return =  '<p>'.$content.'</p>'.$loginout;
	    else
		    $return =  $loginout;

	}
	return $return;
}
add_shortcode('display_user_login_logout', 'display_user_welcome_shortcode');

apply_filters('coauthors_meta_box_context', function (){return 'side';});


/** Startseite **/
//Inhalt aus https://my.relilab.org/wp-admin/post.php?post=580&action=edit
add_action( 'blocksy:hero:after', 'homepage_welcome' );
function homepage_welcome(){
	if ( is_home() && !is_search() ){

	    $home = get_post(580);
	    $blocks =parse_blocks($home->post_content);
	    foreach ($blocks as $block){
		    echo (render_block($block));
        }


    }
}



