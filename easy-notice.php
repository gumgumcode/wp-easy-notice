<?php
/**
 * Plugin Name: Easy Notice
 * Description: Creates a notice on the site. Can be used for announcements, updates, etc.
 * Version: 1.0.0
 * Author: Omkar Bhagat
 * Author URI: https://omkarbhagat.com
 */

 /*
Easy Notice is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
any later version.
 
Easy Notice is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with Easy Notice. If not, see https://www.gnu.org/licenses/gpl.html.
*/

/* Fun fact: Functions are prefixed by sn_ because initially 
I named the plugin Simple Notice but later switched it to Easy Notice 
to avoid any resemblance with a plugin called SimpleNote. 
*/

 // exit if we try to access it directly
if ( !defined( 'ABSPATH' ) )
    exit;

// creating an admin menu
add_action( 'admin_menu', 'sn_create_menu' );

function sn_create_menu() {
    // create a top lvl menu
    add_menu_page( 'Easy Notice', 'Easy Notice', 'administrator', 'sn_menu', 'sn_menu_page', 'dashicons-format-aside' );

    // register settings on admin_init
    add_action( 'admin_init', 'sn_register_settings' );
}

// actual options to register
function sn_register_settings() {
    register_setting( 'sn_settings_group', 'sn_opt_msg' ); // what to display
    register_setting( 'sn_settings_group', 'sn_opt_checkbox_posts' ); // where to display opt 1 - posts
    register_setting( 'sn_settings_group', 'sn_opt_checkbox_pages' ); // where to display opt 2 - pages
    register_setting( 'sn_settings_group', 'sn_opt_checkbox_home' ); // where to display opt 2 - home 
    register_setting( 'sn_settings_group', 'sn_opt_ryg' ); // red/yellow/green
}

// actual code to create the admin menu page for this plugin
function sn_menu_page() {
    ?>

    <div class="wrap">
        <h1>Easy Notice</h1>

        <?php settings_errors(); // displays admin errors upon save, eg: settings saved. ?> 

        <form method="post" action="options.php">

            <?php settings_fields( 'sn_settings_group' ); ?> 
            <?php do_settings_sections( 'sn_settings_group' ); ?>

            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Your notice </th>
                    <td>
                        <textarea rows="3" class="large-text" name="sn_opt_msg"><?php echo esc_attr(get_option( 'sn_opt_msg'))  ; ?></textarea>
                        <p class="description"> The message or notice you want to display on your site </p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Where to display this Easy Notice?</th>
                    <td>
                        <fieldset>
                            <label for="sn_location_posts">
                                <input name="sn_opt_checkbox_posts" type="checkbox" value="posts" <?php checked('posts', get_option('sn_opt_checkbox_posts')); ?> >
                                Single Posts
                            </label>
                            <br>
                            <label for="sn_location_pages">
                                <input name="sn_opt_checkbox_pages" type="checkbox" value="pages" <?php checked('pages', get_option('sn_opt_checkbox_pages')); ?> >
                                Single Pages
                            </label>
                            <br>
                            <label for="sn_location_home">
                                <input name="sn_opt_checkbox_home" type="checkbox" value="home" <?php checked('home', get_option('sn_opt_checkbox_home')); ?> >
                                On top of Homepage
                            </label>
                        </fieldset>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Red/Yellow/Green</th>
                    <td>
                        <label for="sn_radio_red"><input name="sn_opt_ryg" type="radio" value="1" <?php checked( '1', get_option( 'sn_opt_ryg' ) ); ?> />Red</label><br>
                        <label for="sn_radio_yellow"><input name="sn_opt_ryg" type="radio" value="2" <?php checked( '2', get_option( 'sn_opt_ryg' ) ); ?> />Yellow</label><br>
                        <label for="sn_radio_green"><input name="sn_opt_ryg" type="radio" value="3" <?php checked( '3', get_option( 'sn_opt_ryg' ) ); ?> />Green</label><br>
                    </td>
                </tr>
            </table>

            <?php submit_button(); ?>
        </form>

    </div>
    <?php
}

// enqueue scripts and styles for this plugin
function sn_enqueue_scripts() {
    wp_register_style( 'easy-notice-main', plugins_url('easy-notice/easy-notice-main.css') ) ;
    wp_enqueue_style( 'easy-notice-main' );
}
add_action('wp_enqueue_scripts', 'sn_enqueue_scripts');

// return red/yellow/green class for notice
function sn_get_ryg_class() {
    if ( get_option( 'sn_opt_ryg' ) ) {
        // store red yellow green status code
        $ryg_status = get_option( 'sn_opt_ryg' );
        switch( $ryg_status ) {
            case 1:
                return 'sn_red';
            case 2:
                return 'sn_yellow';
            case 3:
                return 'sn_green';
            default:
                return NULL;
        }
    }
}

// gets the actual notice 
function sn_get_the_actual_notice() {
    return '<div class="sn_notice ' . sn_get_ryg_class() . '"> ' . get_option( 'sn_opt_msg' ) . '</div>';
}

// adds the notice to the top of posts/pages if those options are selected
function sn_prepend_post_pages ( $content ) {

    $new_content = sn_get_the_actual_notice() . $content;
    
    if ( (is_single() && get_option('sn_opt_checkbox_posts')) || (is_page() && get_option('sn_opt_checkbox_pages')) )
        return $new_content;

    return $content;
}
add_filter( 'the_content', 'sn_prepend_post_pages' );

// adds the notice to the top of home page if that option is selected
function sn_prepend_home(){
    if (is_home() && get_option('sn_opt_checkbox_home'))
        echo '<div class="sn_notice_home ' . sn_get_ryg_class() . '">' . sn_get_the_actual_notice() . ' </div>';
}
add_action( "template_redirect", "sn_prepend_home" );

?>
