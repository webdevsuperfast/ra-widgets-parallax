<?php
/*
Plugin Name: RA Widgets Parallax
Plugin URI:  https://github.com/webdevsuperfast/ra-widgets-parallax
Description: Add parallax scrolling image effect on your widgets.
Version:     1.0.1
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
        $instance = wp_parse_args( (array) $instance, array( 'parallax-image' => '', 'parallax-position' => '', 'parallax-speed' => '0.2' ) );

        if ( !isset( $instance['parallax-image'] ) ) $instance['parallax-image'] = null;
        if ( !isset( $instance['parallax-position'] ) ) $instance['parallax-position'] = null;
        if ( !isset( $instance['parallax-speed'] ) ) $instance['parallax-speed'] = null; 
        ?>

        <div class="rawp-fields">
            <h3 class="toggle"><?php echo __( 'Parallax Settings', 'ra-widgets-parallax' ); ?></h3>
            <div class="rawp-field" style="display: none;">
                <p>
                    <label for="<?php echo $t->get_field_name( 'parallax-image' ); ?>"><?php echo __( 'Image', 'ra-widgets-parallax' ); ?></label>
                    <input type="text" class="widefat rawp-input-image" id="<?php echo $t->get_field_id( 'parallax-image' ); ?>" name="<?php echo $t->get_field_name( 'parallax-image' ); ?>" value="<?php echo $instance['parallax-image']; ?>" />
                    <a href="#" class="rawp_upload_image_button button button-primary"><?php echo __( 'Upload image', 'ra-widgets-parallax' ); ?></a>
                    <span><em>You must provide a path to the image to enable parallax effect.</em></span>
                </p>
                <p>
                    <label for="<?php echo $t->get_field_id( 'parallax-position' ); ?>"><?php echo __( 'Position', 'ra-widgets-parallax' ); ?></label>
                    <?php
                    $positions = array(
                        '' => __( 'Default' ),
                        'center center' => __( 'Center Center' ),
                        'center top' => __( 'Center Top' ),
                        'center bottom' => __( 'Center Bottom' ),
                        'right top' => __( 'Right Top' ),
                        'right bottom' => __( 'Right Bottom' ),
                        'left top' => __( 'Left Top' ),
                        'left bottom' => __( 'Left Bottom' )
                    ); ?>
                    <select class="widefat" id="<?php echo $t->get_field_id('parallax-position'); ?>" name="<?php echo $t->get_field_name('parallax-position'); ?>">
                        <?php foreach( $positions as $key => $value ) { ?>
                            <option <?php selected( $instance['parallax-position'], $key ); ?>value="<?php echo $key; ?>"><?php echo $value; ?></option>
                        <?php } ?>
                    </select>
                    <span><em>This is analogous to the background-position css property.</em></span>
                </p>
                <p>
                    <label for="<?php echo $t->get_field_id('parallax-speed'); ?>"><?php echo __( 'Speed', 'ra-widgets-parallax' ); ?></label>
                    <select class="widefat" id="<?php echo $t->get_field_id('parallax-speed'); ?>" name="<?php echo $t->get_field_name('parallax-speed'); ?>">
                        <?php foreach( range(0, 1, 0.1) as $number ) { ?>
                            <option <?php selected( $instance['parallax-speed'], $number ); ?>value="<?php echo $number; ?>"><?php echo $number; ?></option>
                        <?php } ?>
                    </select>
                    <span><em>The speed at which the parallax effect runs. 0.0 means the image will appear fixed in place, and 1.0 the image will flow at the same speed as the page content.</em></span>
                </p>
            </div>
        </div>
        <?php
        $return = null;

        return array( $t, $return, $instance );
    }

    public function rawp_in_widget_form_update( $instance, $new_instance, $old_instance ) {
        $instance['parallax-image'] = $new_instance['parallax-image'];
        $instance['parallax-position'] = $new_instance['parallax-position'];
        $instance['parallax-speed'] = $new_instance['parallax-speed'];

        return $instance;
    }

    public function rawp_dynamic_sidebar_params( $params ) {
        global $wp_registered_widgets;
        
        $widget_id = $params[0]['widget_id'];
        $widget_obj = $wp_registered_widgets[$widget_id];
        $widget_opt = get_option( $widget_obj['callback'][0]->option_name );
        $widget_num = $widget_obj['params'][0]['number'];
        // $data = '';

        $attrs = array();

        if ( isset( $widget_opt[$widget_num]['parallax-image'] ) && !empty( $widget_opt[$widget_num]['parallax-image'] ) ) {
            $attrs['class'] = 'parallax-window';
            $attrs['data-image-src'] = $widget_opt[$widget_num]['parallax-image'];
            $attrs['data-parallax'] = 'scroll';
        }

        if ( isset( $widget_opt[$widget_num]['parallax-position'] ) && !empty( $widget_opt[$widget_num]['parallax-position'] ) ) $attrs['data-position'] = $widget_opt[$widget_num]['parallax-position'];
        if ( isset( $widget_opt[$widget_num]['parallax-speed'] ) && !empty( $widget_opt[$widget_num]['parallax-speed'] ) ) $attrs['data-speed'] = $widget_opt[$widget_num]['parallax-speed'];
        $attr = '';
        if ( isset( $widget_opt[$widget_num]['parallax-image'] ) && !empty( $widget_opt[$widget_num]['parallax-image'] ) ) {
            $attr = 'style="position: relative;"><div ';
            foreach( $attrs as $key => $value ) {
                $attr .= $key . '="' . $value .'" ';
            }
            $attr .= '></div>';

            $params[0]['before_widget'] = preg_replace( '/>/', $attr,  $params[0]['before_widget'], 1 );
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