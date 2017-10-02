<?php
/*
Plugin Name: RA Widgets Parallax
Plugin URI:  https://github.com/webdevsuperfast/ra-widgets-parallax
Description: Add parallax scrolling image effect on your widgets.
Version:     1.0.2
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
        $instance = wp_parse_args( (array) $instance, array( 
            'parallax-image' => '', 
            'parallax-speed' => '0.3', 
            'parallax-type' => 'background',
            'parallax-direction' => 'vertical'
        ) );

        if ( !isset( $instance['parallax-image'] ) ) $instance['parallax-image'] = null;
        if ( !isset( $instance['parallax-speed'] ) ) $instance['parallax-speed'] = null; 
        if ( !isset( $instance['parallax-type'] ) ) $instance['parallax-type'] = null;
        if ( !isset( $instance['parallax-direction'] ) ) $instance['parallax-direction'] = null;
        
        // Parallax Type
        $type = $this->rawp_type();

        // Parallax Direction
        $direction = $this->rawp_direction();
        ?>

        <div class="rawp-fields">
            <h3 class="toggle"><?php _e( 'Parallax Settings', 'ra-widgets-parallax' ); ?></h3>
            <div class="rawp-field" style="display: none;">
                <p>
                    <label for="<?php echo $t->get_field_name( 'parallax-image' ); ?>"><?php _e( 'Image', 'ra-widgets-parallax' ); ?></label>
                    <input type="text" class="widefat rawp-input-image" id="<?php echo $t->get_field_id( 'parallax-image' ); ?>" name="<?php echo $t->get_field_name( 'parallax-image' ); ?>" value="<?php echo $instance['parallax-image']; ?>" />
                    <a href="#" class="rawp_upload_image_button button button-primary"><?php _e( 'Upload image', 'ra-widgets-parallax' ); ?></a>
                    <span><em><?php _e( 'You must provide a path to the image to enable parallax effect.', 'ra-widgets-parallax' ); ?></em></span>
                </p>
                <p>
                    <label for="<?php echo $t->get_field_id('parallax-speed'); ?>"><?php _e( 'Speed', 'ra-widgets-parallax' ); ?></label>
                    <input type="text" class="widefat" id="<?php echo $t->get_field_id( 'parallax-speed' ); ?>" name="<?php echo $t->get_field_name( 'parallax-speed' ); ?>" value="<?php echo $instance['parallax-speed']; ?>" />
                    <span><em><?php _e( 'This attribute is necessary to enable parallax scrolling effect. It sets elements offset and speed. It can be positive (0.3) or negative (-0.3). Less means slower.', 'ra-widgets-parallax' ); ?></em></span>
                </p>
                <p>
                    <label for="<?php echo $t->get_field_id( 'parallax-type' ); ?>"><?php _e( 'Type', 'ra-widgets-parallax' ); ?></label>
                    <select class="widefat" id="<?php echo $t->get_field_id('parallax-type'); ?>" name="<?php echo $t->get_field_name('parallax-type'); ?>">
                        <?php foreach( $type as $key => $value ) { ?>
                            <option <?php selected( $instance['parallax-type'], $key ); ?>value="<?php echo $key; ?>"><?php echo $value; ?></option>
                        <?php } ?>
                    </select>
                    <span><em><?php _e( 'This attribute is optional. It has two values background and foreground.', 'ra-widgets-parallax' ); ?></em></span>
                </p>
                <p>
                    <label for="<?php echo $t->get_field_id( 'parallax-direction' ); ?>"><?php _e( 'Direction', 'ra-widgets-parallax' ); ?></label>
                    <select class="widefat" id="<?php echo $t->get_field_id('parallax-direction'); ?>" name="<?php echo $t->get_field_name('parallax-direction'); ?>">
                        <?php foreach( $direction as $key => $value ) { ?>
                            <option <?php selected( $instance['parallax-direction'], $key ); ?>value="<?php echo $key; ?>"><?php echo $value; ?></option>
                        <?php } ?>
                    </select>
                    <span><em><?php _e( 'This attribute is optional. It has two values vertical and horizontal.', 'ra-widgets-parallax' ); ?></em></span>
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
        $instance['parallax-type'] = $new_instance['parallax-type'];
        $instance['parallax-direction'] = $new_instance['parallax-direction'];

        return $instance;
    }

    public function rawp_dynamic_sidebar_params( $params ) {
        global $wp_registered_widgets;
        
        $widget_id = $params[0]['widget_id'];
        $widget_obj = $wp_registered_widgets[$widget_id];
        $widget_opt = get_option( $widget_obj['callback'][0]->option_name );
        $widget_num = $widget_obj['params'][0]['number'];

        $attrs = array();
        
        if ( isset( $widget_opt[$widget_num]['parallax-image'] ) && !empty( $widget_opt[$widget_num]['parallax-image'] ) ) {
            $attrs['class'] = 'parallax-window';

            $styles = array();

            $style = '';
            $styles['background'] = 'url( '. $widget_opt[$widget_num]['parallax-image'] .' ) no-repeat center';

            foreach( $styles as  $key => $value ) {
                $style .= $key . ':' . $value . '; ';
            }

            $attrs['style'] = $style;
        }

        // Parallax Speed
        if ( isset( $widget_opt[$widget_num]['parallax-speed'] ) && !empty( $widget_opt[$widget_num]['parallax-speed'] ) ) { 
            $attrs['data-paroller-factor'] = $widget_opt[$widget_num]['parallax-speed']; 
        } else {
            $attrs['data-paroller-factor'] = (int) '0.3';
        }

        // Parallax Type
        if ( isset( $widget_opt[$widget_num]['parallax-type'] ) && !empty( $widget_opt[$widget_num]['parallax-type'] ) ) {
            $attrs['data-paroller-type'] = $widget_opt[$widget_num]['parallax-type'];
        }
        
        if ( isset( $widget_opt[$widget_num]['parallax-direction'] ) && !empty( $widget_opt[$widget_num]['parallax-direction'] ) ) {
            $attrs['data-paroller-direction'] = $widget_opt[$widget_num]['parallax-direction'];
        } 

        
        
        if ( isset( $widget_opt[$widget_num]['parallax-image'] ) && !empty( $widget_opt[$widget_num]['parallax-image'] ) ) {
            $attr = 'style="position: relative;"><div ';

            foreach( $attrs as $key => $value ) {
                $attr .= $key . '="' . $value .'" ';
            }

            $attr .= '></div>';

            $params[0]['before_widget'] = preg_replace( '/>/D', $attr,  $params[0]['before_widget'], 1 );
        }

        return $params;
    }

    public function rawp_enqueue_scripts() {
        if ( !is_admin() ) {
            // Paroller JS
            wp_register_script( 'rawp-parallax-js', plugin_dir_url( __FILE__ ) . 'public/js/jquery.paroller.min.js', array( 'jquery' ), null, true );
            wp_enqueue_script( 'rawp-parallax-js' );

            wp_add_inline_script( 'rawp-parallax-js', 'jQuery(window).paroller();' );

            // Main CSS
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

    private function rawp_direction() {
        $direction = array(
            'horizontal' => __( 'Horizontal' ),
            'vertical' => __( 'Vertical' )
        );

        return $direction;
    }

    private function rawp_type() {
        $type = array(
            'background' => __( 'Background' ),
            'foreground' => __( 'Foreground' )
        );

        return $type;
    }
}

new RA_Widgets_Parallax();