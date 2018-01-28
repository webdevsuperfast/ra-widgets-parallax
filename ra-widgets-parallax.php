<?php
/*
Plugin Name: RA Widgets Parallax
Plugin URI:  https://github.com/webdevsuperfast/ra-widgets-parallax
Description: Add parallax scrolling image effect on your widgets.
Version:     1.0.4
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

        // Enqueue SiteOrigin Panels Admin scripts
        add_action( 'siteorigin_panel_enqueue_admin_scripts', array( $this, 'rawp_siteorigin_panels_admin_scripts' ) );
    }

    public function rawp_in_widget_form( $t, $return, $instance ) {
        $instance = wp_parse_args( (array) $instance, array( 
            'parallax-image' => '', 
            'parallax-speed' => '0.2', 
            'parallax-position' => '',
            'parallax-container' => ''
        ) );

        if ( !isset( $instance['parallax-image'] ) ) $instance['parallax-image'] = null;
        if ( !isset( $instance['parallax-speed'] ) ) $instance['parallax-speed'] = null; 
        if ( !isset( $instance['parallax-position'] ) ) $instance['parallax-position'] = null;
        if ( !isset( $instance['parallax-container'] ) ) $instance['parallax-container'] = null;
        
        // Parallax Position
        $position = $this->rawp_position();
        ?>

        <div class="rawp-fields">
            <h3 class="rawp-toggle"><?php _e( 'Parallax Settings', 'ra-widgets-parallax' ); ?></h3>
            <div class="rawp-field" style="display: none;">
                <p>
                    <label for="<?php echo $t->get_field_name( 'parallax-image' ); ?>"><?php _e( 'Image', 'ra-widgets-parallax' ); ?></label>
                    <img src="<?php if ( !empty( $instance['parallax-image'] ) ) echo $instance['parallax-image']; ?>" alt="">
                    <input type="hidden" class="widefat rawp-input-image" id="<?php echo $t->get_field_id( 'parallax-image' ); ?>" name="<?php echo $t->get_field_name( 'parallax-image' ); ?>" value="<?php echo $instance['parallax-image']; ?>" />
                    <button class="rawp_upload_image_button button button-primary <?php if ( !empty( $instance['parallax-image'] ) ) echo 'hidden'; ?>"><?php _e( 'Upload image', 'ra-widgets-parallax' ); ?></button>
                    <button class="rawp_delete_image_button button button-secondary <?php if ( empty( $instance['parallax-image'] ) ) echo 'hidden'; ?>">Remove Image</button>
                    <span><em><?php _e( 'You must provide a path to the image to enable parallax effect.', 'ra-widgets-parallax' ); ?></em></span>
                </p>
                <p>
                    <label for="<?php echo $t->get_field_id('parallax-speed'); ?>"><?php _e( 'Speed', 'ra-widgets-parallax' ); ?></label>
                    <input type="text" class="widefat" id="<?php echo $t->get_field_id( 'parallax-speed' ); ?>" name="<?php echo $t->get_field_name( 'parallax-speed' ); ?>" value="<?php echo $instance['parallax-speed']; ?>" />
                    <span><em><?php _e( 'The speed at which the parallax effect runs. 0.0 means the image will appear fixed in place, and 1.0 the image will flow at the same speed as the page content.', 'ra-widgets-parallax' ); ?></em></span>
                </p>
                <p>
                    <label for="<?php echo $t->get_field_id( 'parallax-position' ); ?>"><?php _e( 'Position', 'ra-widgets-parallax' ); ?></label>
                    <select class="widefat" id="<?php echo $t->get_field_id('parallax-position'); ?>" name="<?php echo $t->get_field_name('parallax-position'); ?>">
                        <?php foreach( $position as $key => $value ) { ?>
                            <option <?php selected( $instance['parallax-position'], $key ); ?>value="<?php echo $key; ?>"><?php echo $value; ?></option>
                        <?php } ?>
                    </select>
                    <span><em><?php _e( 'This is analogous to the background-position css property. Specify coordinates as top, bottom, right, left, or center. The parallax image will be positioned as close to these values as possible while still covering the target element.', 'ra-widgets-parallax' ); ?></em></span>
                </p>
                <p>
                    <label for="<?php echo $t->get_field_id( 'parallax-container' ); ?>"><?php _e( 'Mirror Container', 'ra-widgets-parallax' ); ?></label>
                    <input type="text" class="widefat" id="<?php echo $t->get_field_id( 'parallax-container' ); ?>" name="<?php echo $t->get_field_name( 'parallax-container' ); ?>" value="<?php echo $instance['parallax-container']; ?>" />
                    <span><em><?php _e( 'The parallax mirror will be prepended into this container.', 'ra-widgets-parallax' ); ?></em></span>
                </p>
            </div>
        </div>
        <?php
        $return = null;

        return array( $t, $return, $instance );
    }

    public function rawp_in_widget_form_update( $instance, $new_instance, $old_instance ) {
        $instance['parallax-image'] = $new_instance['parallax-image'];
        $instance['parallax-speed'] = $new_instance['parallax-speed'];
        $instance['parallax-position'] = $new_instance['parallax-position'];
        $instance['parallax-container'] = $new_instance['parallax-container'];

        return $instance;
    }

    public function rawp_dynamic_sidebar_params( $params ) {
        global $wp_registered_widgets;
        
        $widget_id = $params[0]['widget_id'];
        $widget_obj = $wp_registered_widgets[$widget_id];
        $widget_opt = get_option( $widget_obj['callback'][0]->option_name );
        $widget_num = $widget_obj['params'][0]['number'];

        $attrs = array();

        // Parallax Speed
        if ( isset( $widget_opt[$widget_num]['parallax-speed'] ) && !empty( $widget_opt[$widget_num]['parallax-speed'] ) ) { 
            $attrs['data-speed'] = $widget_opt[$widget_num]['parallax-speed']; 
        } else {
            $attrs['data-speed'] = (float) '0.2';
        }

        // Parallax Position
        if ( isset( $widget_opt[$widget_num]['parallax-position'] ) && !empty( $widget_opt[$widget_num]['parallax-position'] ) ) $attrs['data-position'] = $widget_opt[$widget_num]['parallax-position'];

        // Parallax Mirror
        if ( isset( $widget_opt[$widget_num]['parallax-container'] ) && !empty( $widget_opt[$widget_num]['parallax-container'] ) ) $attrs['data-mirror-container'] = $widget_opt[$widget_num]['parallax-container'];

        // Parallax Image
        if ( isset( $widget_opt[$widget_num]['parallax-image'] ) && !empty( $widget_opt[$widget_num]['parallax-image'] ) ) {
            $attrs['class'] = 'parallax-window';
            $attrs['data-parallax'] = 'scroll';
            $attrs['data-image-src'] = $widget_opt[$widget_num]['parallax-image'];

            $attr = ' ';

            foreach( $attrs as $key => $value ) {
                $attr .= $key . '="' . $value .'" ';
            }

            $attr .= '>';

            $params[0]['before_widget'] = preg_replace( '/>$/D', $attr,  $params[0]['before_widget'], 1 );
        }

        return $params;
    }

    public function rawp_enqueue_scripts() {
        if ( !is_admin() ) {
            // Parallax JS
            wp_register_script( 'rawp-parallax-js', plugin_dir_url( __FILE__ ) . 'public/js/parallax.min.js', array( 'jquery' ), null, true );
            wp_enqueue_script( 'rawp-parallax-js' );

            // Main CSS
            wp_enqueue_style( 'rawp-parallax-css', plugin_dir_url( __FILE__ ) . 'public/css/app.css' );
        }
    }

    public function rawp_admin_enqueue_scripts() {
        //Get current page
        $current_page = get_current_screen();

        //Only load if we are not on the widget page
        if ( $current_page->id === 'widgets' ){
            // Enqueue WP Media
            wp_enqueue_script( 'media-upload' );
            wp_enqueue_media();
            
            // Admin Script
            wp_enqueue_script( 'rawp-admin-js', plugin_dir_url( __FILE__ ) . 'admin/js/admin.js' );

            // Admin Style
            wp_enqueue_style( 'rawp-admin-css', plugin_dir_url( __FILE__ ) . 'admin/css/admin.css' );
        }
    }

    public function rawp_siteorigin_panels_admin_scripts() {
        wp_register_script( 'rawp-siteorigin-panels-js', plugin_dir_url( __FILE__ ) . 'admin/js/siteorigin.js', array( 'jquery' ), null, true );
        wp_enqueue_script( 'rawp-siteorigin-panels-js' );
    }
    
    private function rawp_position() {
        $position = array(
            'center center' => __( 'Center Center' ),
            'center top' => __( 'Center Top' ),
            'center bottom' => __( 'Center Bottom' ),
            'right top' => __( 'Right Top' ),
            'right bottom' => __( 'Right Bottom' ),
            'left top' => __( 'Left Top' ),
            'left bottom' => __( 'Left Bottom' )
        );

        return $position;
    }
}

new RA_Widgets_Parallax();