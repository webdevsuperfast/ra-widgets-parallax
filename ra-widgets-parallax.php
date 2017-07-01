<?php
/*
Plugin Name: RA Widgets Parallax
Plugin URI:  https://github.com/webdevsuperfast/ra-widgets-parallax
Description: Add parallax scrolling image effect on your widgets.
Version:     1.0.0
Author:      Rotsen Mark Acob
Author URI:  https://rotsenacob.com/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: ra-widgets-parallax
Domain Path: /languages
*/

defined( 'ABSPATH' ) or die( esc_html_e( 'With great power comes great responsibility.', 'ra-widgets-parallax' ) );

class RA_Widgets_Parallax {
    public function __construct() {
        // Add input fields
        add_action( 'in_widget_form', array( $this, 'rawp_in_widget_form' ), 5, 3 );

        // Callback function for options update
        add_filter( 'widget_update_callback', array( $this, 'rawp_in_widget_form_update' ), 5, 3 );

        // Add data attributes
        add_filter( 'dynamic_sidebar_params', array( $this, 'rawp_dynamic_sidebar_params' ) );

        // Enqueue scripts
        add_action( 'wp_enqueue_scripts', array( $this, 'rawp_enqueue_scripts' ) );

        // Enqueue Admin scripts
        add_action( 'admin_enqueue_scripts', array( $this, 'rawp_admin_enqueue_scripts' ) );
    }

    public function rawp_in_widget_form( $t, $return, $instance ) {
        $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'parallax-image' => '' ) );

        if ( !isset( $instance['parallax-image'] ) ) $instance['parallax-image'] = null; ?>

        <div class="rawp-fields">
            <p><strong><?php echo __( 'Parallax Image', 'ra-widgets-parallax' ); ?></strong></p>
            <hr>
            <p>
                <label for="<?php echo $t->get_field_name( 'parallax-image' ); ?>">
                <input type="text" class="widefat" id="<?php echo $t->get_field_id( 'parallax-image' ); ?>" name="<?php echo $t->get_field_name( 'parallax-image' ); ?>" value="<?php echo $instance['parallax-image']; ?>" />
                <a href="#" class="rawp_upload_image_button button button-primary"><?php echo __( 'Upload image', 'ra-widgets-parallax' ); ?></a>
                </label>
            </p>
        </div>
        <?php
        $return = null;

        return array( $t, $return, $instance );
    }

    public function rawp_in_widget_form_update( $instance, $new_instance, $old_instance ) {
        $instance['parallax-image'] = $new_instance['parallax-image'];

        return $instance;
    }

    public function rawp_dynamic_sidebar_params( $params ) {
        global $wp_registered_widgets;
        
        $widget_id = $params[0]['widget_id'];
        $widget_obj = $wp_registered_widgets[$widget_id];
        $widget_opt = get_option( $widget_obj['callback'][0]->option_name );
        $widget_num = $widget_obj['params'][0]['number'];
        // $data = '';

        if ( isset( $widget_opt[$widget_num]['parallax-image'] ) && !empty( $widget_opt[$widget_num]['parallax-image'] ) ) {
            $data = 'style="position:relative;">';
            $data .= '<div class="parallax-window" data-parallax="scroll" data-image-src="' .$widget_opt[$widget_num]['parallax-image']. '"></div>';

            $params[0]['before_widget'] = preg_replace( '/>/', $data,  $params[0]['before_widget'], 1 );
        }

        return $params;
    }

    public function rawp_enqueue_scripts() {
        if ( !is_admin() ) {
            wp_register_script( 'rawp-parallax-js', plugin_dir_url( __FILE__ ) . 'public/js/parallax.min.js', array( 'jquery' ), null, true );
            wp_enqueue_script( 'rawp-parallax-js' );

            wp_enqueue_style( 'rawp-parallax-css', plugin_dir_url( __FILE__ ) . 'public/css/app.css' );
        }
    }

    public function rawp_admin_enqueue_scripts() {
        //Get current page
        $current_page = get_current_screen();

        //Only load if we are not on the widget page
        if ( $current_page->id === 'widgets' ){
            wp_enqueue_media();
            wp_enqueue_script( 'rawp-admin-js', plugin_dir_url( __FILE__ ) . 'admin/js/admin.js' );

            wp_enqueue_style( 'rawp-admin-css', plugin_dir_url( __FILE__ ) . 'admin/css/admin.css' );
        }
    }
}

new RA_Widgets_Parallax();