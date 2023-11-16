<?php

require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tmy-g11n-translator.php';

class G11n_Language_Widget extends WP_Widget {

    protected $translator;

    public function __construct()
                {                      // id_base        ,  visible name
                    parent::__construct( 'g11n_lang_wg', 'TMY Lanaguage Switcher Widget' );
         	    $this->translator = new TMY_G11n_Translator();
                }

    public function widget( $args, $instance ) {
                    $switcher =  $this->translator->get_language_switcher('widget');
                    echo tmy_g11n_html_kses_esc($switcher);
                    //echo $args['before_widget'], wpautop( $instance['text'] ), $args['after_widget'];
                }

    public function form( $instance ) {
                    $switcher =  $this->translator->get_language_switcher('widget');
                    $text = $switcher;

                    //printf('<textarea class="widefat" rows="7" cols="20" id="%1$s" name="%2$s">%3$s</textarea>',
                    //    $this->get_field_id( 'text' ),
                    //    $this->get_field_name( 'text' ),
                    //    $text
                    //);
                }
}

function g11n_language_chooser_widget() {

        // Register our own widget.
        register_widget( 'G11n_Language_Widget' );

        // Register two sidebars.
        //$sidebars = array ( 'a' => 'top-widget', 'b' => 'bottom-widget' );
        //foreach ( $sidebars as $sidebar )
        //{
        //        register_sidebar(
        //            array (
        //                'name'          => $sidebar,
        //                'id'            => $sidebar,
        //                'before_widget' => '',
        //                'after_widget'  => ''
        //            )
        //        );
        //}
}

add_action( 'widgets_init', 'g11n_language_chooser_widget' );

