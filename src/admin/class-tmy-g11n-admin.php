<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    TMY_G11n
 * @subpackage TMY_G11n/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    TMY_G11n
 * @subpackage TMY_G11n/admin
 * @author     Yu Shao <yu.shao.gm@gmail.com>
 */

class TMY_G11n_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	private $translator;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $translator ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->translator = $translator;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in TMY_G11n_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The TMY_G11n_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/tmy-g11n-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in TMY_G11n_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The TMY_G11n_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/tmy-g11n-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function tmy_plugin_register_settings() {

    		register_setting( 'tmy-g11n-settings-group', 'g11n_default_lang' );
    		register_setting( 'tmy-g11n-settings-group', 'g11n_additional_lang' );

    		register_setting( 'tmy-g11n-settings-group', 'g11n_server_url' );
    		register_setting( 'tmy-g11n-settings-group', 'g11n_server_user' );
    		register_setting( 'tmy-g11n-settings-group', 'g11n_server_token' );
    		register_setting( 'tmy-g11n-settings-group', 'g11n_server_project' );
    		register_setting( 'tmy-g11n-settings-group', 'g11n_server_version' );
    		register_setting( 'tmy-g11n-settings-group', 'g11n_server_trunksize' );

    		register_setting( 'tmy-g11n-settings-group', 'g11n_l10n_props_blogname' );
    		register_setting( 'tmy-g11n-settings-group', 'g11n_l10n_props_blogdescription' );

                $all_post_types = tmy_g11n_available_post_types();

                foreach ( $all_post_types  as $post_type ) {
    		    register_setting( 'tmy-g11n-settings-group', 'g11n_l10n_props_'.$post_type );
                }
    		register_setting( 'tmy-g11n-settings-group', 'g11n_l10n_props_tax' );

    		//register_setting( 'tmy-g11n-settings-group', 'g11n_l10n_props_posts' );
    		//register_setting( 'tmy-g11n-settings-group', 'g11n_l10n_props_pages' );
	
    		register_setting( 'tmy-g11n-settings-group', 'g11n_site_lang_cookie' );
    		register_setting( 'tmy-g11n-settings-group', 'g11n_site_lang_session' );
    		register_setting( 'tmy-g11n-settings-group', 'g11n_site_lang_query_string' );
    		register_setting( 'tmy-g11n-settings-group', 'g11n_site_lang_browser' );

    		register_setting( 'tmy-g11n-settings-group', 'g11n_switcher_tagline' );
    		register_setting( 'tmy-g11n-settings-group', 'g11n_switcher_post' );
    		register_setting( 'tmy-g11n-settings-group', 'g11n_switcher_title' );
    		register_setting( 'tmy-g11n-settings-group', 'g11n_switcher_sidebar' );
    		register_setting( 'tmy-g11n-settings-group', 'g11n_switcher_floating' );
    		register_setting( 'tmy-g11n-settings-group', 'g11n_switcher_type' );
    		register_setting( 'tmy-g11n-settings-group', 'g11n_using_google_tookit' );

    		register_setting( 'tmy-g11n-settings-group', 'g11n_auto_pullpush_translation' );
    		register_setting( 'tmy-g11n-settings-group', 'g11n_resource_file_location' );
    		register_setting( 'tmy-g11n-settings-group', 'g11n_editor_choice' );

    		register_setting( 'tmy-g11n-settings-group', 'g11n_seo_url_enable' );
    		register_setting( 'tmy-g11n-settings-group', 'g11n_seo_url_label' );
	

                $all_post_types = tmy_g11n_available_post_types();
                foreach ( $all_post_types  as $post_type ) {
                    add_filter( 'bulk_actions-edit-' . $post_type, array($this, 'tmy_plugin_g11n_register_bulk_actions'));
                    add_filter( 'handle_bulk_actions-edit-' . $post_type, array($this, 'tmy_plugin_g11n_bulk_action_handler'), 10, 3 );
                }

	}

	public function tmy_plugin_register_admin_menu() {


                $tmy_logo_svg = file_get_contents( plugin_dir_path( __FILE__ ) . 'include/tmy.svg', false);
                $tmy_menu_icon = 'data:image/svg+xml;base64,' . base64_encode( $tmy_logo_svg );

                add_menu_page( __( 'TMY Globalization', 'tmy-globalization'),
                               __( 'TMY Globalization', 'tmy-globalization'),
                              'manage_options',
                              'tmy-g11n-main-menu',
                              false,
                              $tmy_menu_icon);

		add_submenu_page( 'tmy-g11n-main-menu',
                                  __( 'TMY Setup', 'tmy-globalization'),
                          	  __( 'TMY Setup', 'tmy-globalization'),
                            	  'manage_options', 
                          	  'tmy-g11n-setup-menu', 
                          	  array( $this,
                                         'tmy_admin_options_page'),
                                  1 );

        	add_submenu_page( 'tmy-g11n-main-menu',
                                  __( 'TMY Dashboard', 'tmy-globalization'),
                          	  __( 'TMY Dashboard', 'tmy-globalization'),
                          	  'manage_options',
                          	  'tmy-g11n-dashboard-menu',
                          	  array( $this,
                                         'tmy_l10n_manager_page') );

        	add_submenu_page( 'tmy-g11n-main-menu',
                                  __( 'TMY Taxonomies', 'tmy-globalization'),
                          	  __( 'TMY Taxonomies', 'tmy-globalization'),
                          	  'manage_options',
                          	  'tmy-g11n-taxonomy-menu',
                          	  array( $this,
                                         'tmy_l10n_taxonomy_page') );
	
        	add_submenu_page( 'tmy-g11n-main-menu',
                                  __( 'TMY Text', 'tmy-globalization'),
                          	  __( 'TMY Text', 'tmy-globalization'),
                          	  'manage_options',
                          	  'tmy-g11n-text-menu',
                          	  array( $this,
                                         'tmy_l10n_text_page') );

        	add_submenu_page( 'tmy-g11n-main-menu',
                                  __( 'TMY Diagnosis', 'tmy-globalization'),
                          	  __( 'TMY Diagnosis', 'tmy-globalization'),
                          	  'manage_options',
                          	  'tmy-support-manager',
                          	  array( $this,
                                         'tmy_support_manager_page') );

                remove_submenu_page( 'tmy-g11n-main-menu', 'tmy-g11n-main-menu' );

                $all_post_types = tmy_g11n_available_post_types();
                $all_post_types['g11n_translation'] = 'g11n_translation';
	
               	add_meta_box( 'tmy_g11n_trans_status_box', 
		                  __( 'Translation Status', 'tmy-globalization'),
		                  array( $this, 'tmy_translation_metabox_callback'), 
		                  array_values($all_post_types),
		                  //format: array('post','page','g11n_translation','product'),
		                  'normal', // (normal, side, advanced)
		                  'default' // (default, low, high, core) 
                            );

	}

        public function _update_g11n_translation_status( $id, $html_flag = false ) {

                $post_id = $id;
                $post_type = get_post_type($post_id);
                $post_status = get_post_status($post_id);
            
                if ( WP_TMY_G11N_DEBUG ) {
                    error_log("In _update_g11n_translation_status: ".esc_attr($post_id));
                }
               
	    	if (strcmp($post_type,"g11n_translation")!==0) {
                    return '';
                }

	    	if (strcmp($post_type,"g11n_translation")===0) {
                    $original_id = get_post_meta($post_id, 'orig_post_id', true);
                    $original_type = get_post_meta($post_id, 'g11n_tmy_orig_type', true);
                    $original_title = get_the_title($original_id);

                    if ( tmy_g11n_post_type_enabled($original_id, $original_title, $original_type) ) {
                        if (strcmp($post_status,"publish")===0) {
                            $translation_entry_status = 'LIVE'; 
                            update_post_meta( $post_id, 'g11n_tmy_lang_status', 'LIVE');
                            if ( $html_flag ) {
                                $translation_entry_status = '<button type="button" style="background-color:#4CAF50;color:white; height:25px;" >' . 
                                __( 'LIVE', 'tmy-globalization') . '</button>';
                            }
                        } else {
                            $translation_entry_status = 'PROGRESS'; 
                            update_post_meta( $post_id, 'g11n_tmy_lang_status', 'PROGRESS');
                            if ( $html_flag ) {
                                $translation_entry_status = '<button type="button" style="background-color:#EE9A4D;color:white; height:25px;" >' .
                                __( 'IN PROGRESS', 'tmy-globalization') . '</button>';
                            }
                        }
                    } else {
                        if (strcmp($post_status,"publish")===0) {
                            $translation_entry_status = 'DISABLED-LIVE'; 
                            update_post_meta( $post_id, 'g11n_tmy_lang_status', 'DISABLED-LIVE');
                            if ( $html_flag ) {
                                $translation_entry_status = '<button type="button" style="background-color:#C0C0C0;color:white; height:25px;" >' .
                                __( 'DISABLED', 'tmy-globalization') . '</button>';
                            }
                        } else {
                            $translation_entry_status = 'DISABLED-PROGRESS'; 
                            update_post_meta( $post_id, 'g11n_tmy_lang_status', 'DISABLED-PROGRESS');
                            if ( $html_flag ) {
                                $translation_entry_status = '<button type="button" style="background-color:#C0C0C0;color:white; height:25px;" >' .
                                __( 'DISABLED', 'tmy-globalization') . '</button>';
                            }
                        }
                    }

                    return $translation_entry_status;
                }
        }


        public function _get_tmy_g11n_metabox($id, $post_type) {

                $post_id = $id;
                //$post_type = get_post_type($post_id);
                $post_status = get_post_status($post_id);
                $return_msg = '';
                $qualified_taxonomies = get_taxonomies(array("public" => true, "show_ui"=> true), "names", "or");
                $all_post_types = tmy_g11n_available_post_types();
	    	if (strcmp($post_type,"g11n_translation")===0) {
                    $return_msg .= '<div style="border:1px solid #A8A7A7;padding: 10px;">';
                    $trans_info = $this->translator->get_translation_info($post_id);
		    $origin_type = get_post_meta($post_id,'g11n_tmy_orig_type',true);
		    $trans_lang = get_post_meta($post_id,'g11n_tmy_lang',true);

                    /* translators: %1$s is languge code */
                    $return_msg_format .= __('This is the %1$s translation page of <a href="%2$s">%3$s (ID:%4$s)</a>', 'tmy-globalization');

                    if (array_key_exists($origin_type, $qualified_taxonomies)) {
	    	    //if (strcmp($origin_type,"taxonomy")===0) {
                        $original_id = get_post_meta($post_id,'orig_post_id',true);
                        //$return_msg .= '<b>This is the ' . esc_attr($trans_lang) . ' translation page of <a href="' . 
                        //     esc_url( get_edit_term_link($original_id) ) . '">' . "Taxonomy" . 
                        //   ' (ID:' . esc_attr($original_id) . ')</a>';
                        $return_msg .= '<b>' . sprintf($return_msg_format, esc_attr($trans_lang), 
                                                                           esc_url(get_edit_term_link($original_id)),
                                                                           "Taxonomy",
                                                                           esc_attr($original_id));

                    } else {
                        if (isset($trans_info[0])) {
                            $original_id = $trans_info[0]->ID;
                            $original_title = $trans_info[0]->post_title;
                        }

                        //$return_msg .= '<b>This is the ' . esc_attr($trans_lang) . ' translation page of <a href="' . 
                        //     esc_url( get_edit_post_link($original_id) ) . '">' . esc_attr($original_title) . 
                        //   ' (ID:' . esc_attr($original_id) . ')</a>';
                        $return_msg .= '<b>' . sprintf($return_msg_format, esc_attr($trans_lang), 
                                                                           esc_url(get_edit_post_link($original_id)),
                                                                           esc_attr($original_title),
                                                                           esc_attr($original_id));
                    }

		    $return_msg .= ' ' . __('Status', 'tmy-globalization') . ': ' . $this->_update_g11n_translation_status($post_id, true) . '</br>';
                    //$return_msg .= "</div>";

                } elseif ((array_key_exists($post_type, $all_post_types)) ||
                        //(strcmp($post_type,"taxonomy")===0)) {
                          (array_key_exists($post_type, $qualified_taxonomies))) {

                //} elseif ((strcmp($post_type,"post")===0) || (strcmp($post_type,"page")===0)) {
                    $return_msg .= '<div style="border:1px solid #A8A7A7;padding: 10px;">';
                    $return_msg .= '<table class="wp-list-table striped table-view-list">';
    		    $return_msg .= '<tr><td><b>' . __('Language', 'tmy-globalization') . '</b></td> <td><b>' . 
                                                   __('Code', 'tmy-globalization') . '</b></td> <td><b>Id</b></th> <td><b>' . 
                                                   __('Status', 'tmy-globalization') . '</b></td></tr>';

                    $all_langs = get_option('g11n_additional_lang', array());
                    $default_lang = get_option('g11n_default_lang');
                    unset($all_langs[$default_lang]);

                    if (is_array($all_langs)) {
                        foreach( $all_langs as $value => $code) {
                            $translation_id = $this->translator->get_translation_id($post_id,$code,$post_type);
			    if (isset($translation_id)) {
                                $translation_status = get_post_status($translation_id);
                                //$return_msg .= esc_attr($value) . '-' . esc_attr($code) . ' Translation page is at <a href="' . esc_url( get_edit_post_link($translation_id) ) . 
                                //     '">ID ' . esc_attr($translation_id) . '</a>, status: ' . esc_attr($translation_status) . '</br>';
    		                $return_msg .= '<tr><td>' . esc_attr($value) . '</td>
                                                    <td>' . esc_attr($code) . '</td>
                                                    <td>' . '<a href="' . esc_url(get_edit_post_link($translation_id)) .'">'.esc_attr($translation_id). '</a></td>
                                                    <td>' . esc_attr($translation_status) . '</td></tr>';
                            } else {
                                //$return_msg .= esc_attr($value) . '-' . esc_attr($code) . ' Not Started Yet </br>';
    		                $return_msg .= '<tr><td>' . esc_attr($value) . '</td>
                                                    <td>' . esc_attr($code) . '</td>
                                                    <td> </td>
                                                    <td> ' . __('Not Started Yet', 'tmy-globalization') . '</td></tr>';
                            }

                         }
                    }
                    $return_msg .= "</table>";

                    $return_msg .= '<button type="button" aria-disabled="false" class="components-button editor-post-publish-button editor-post-publish-button__button is-primary" onclick="create_sync_translation(' . esc_attr($post_id) . ', \'' . 
                                           esc_attr($post_type) . '\')">' . __('Start or Sync Translation', 'tmy-globalization') . '</button> ' .
                                                                            __('Press to Start Translation Or Send To Translation Server', 'tmy-globalization');
                }
                $return_msg .= '<br>' . __('Visit', 'tmy-globalization'). ' <a href="' . get_home_url() . '/wp-admin/edit.php?post_type=g11n_translation' . 
                               '">' . __('TMY Translations', 'tmy-globalization') . '</a> ' . __('for all translations', 'tmy-globalization');
                $return_msg .= '<br>' . __('Or, visit', 'tmy-globalization') .' <a href="' . get_home_url() . '/wp-admin/admin.php?page=tmy-g11n-dashboard-menu' . 
                               '">' . __('TMY Dashboard', 'tmy-globalization') . '</a> ' . __('for translation summary', 'tmy-globalization') . '<br>';

                if ((strcmp('', get_option('g11n_server_user','')) !== 0) && (strcmp('', get_option('g11n_server_token','')) !== 0)) {
    		    $return_msg .= '<br>' . __('Latest status with Translation Server:', 'tmy-globalization') . '<div id="g11n_push_status_text_id"><h5>'. 
		    get_post_meta(get_the_ID(),'translation_push_status',true) . '</h5></div>';
                }
                $return_msg .= "</div>";
                return $return_msg;
       }

        public function tmy_translation_metabox_taxonomy_edit( $wp_term, $taxonomy ) {

            echo ("<table><tr><td><div id=\"tmy_translation_status_box_div\">");
            echo ("<br><b>" . __('Translation Status:', 'tmy-globalization') . " </b>" );
            //echo tmy_g11n_html_kses_esc($this->_get_tmy_g11n_metabox($wp_term->term_id, "taxonomy"));
            echo tmy_g11n_html_kses_esc($this->_get_tmy_g11n_metabox($wp_term->term_id, $taxonomy));
            echo ("</div></td></tr></table>");
            
            ?>
                 <script>            
                 function create_sync_translation(id, post_type) {

                        var r = confirm("This will create sync translation");
                        if (r == true) {
                            jQuery(document).ready(function($) {
                                    var data = {
                                            'action': 'tmy_create_sync_translation',
                                            'id': id,
                                            'post_type': post_type
                                    };
                                    $.ajax({
                                        type:    "POST",
                                        url:     ajaxurl,
                                        data:    data,
                                        success: function(response) {
                                            if (response === undefined) {
                                                alert(__('No Server Reply, talk to system admininstrator.', 'tmy-globalization'));
                                            } else {
                                                var response_json = $.parseJSON(response);
                                                if (response_json.message === undefined) {
                                                    alert(__('No Server Reply, talk to system admininstrator.', 'tmy-globalization'));
                                                } else {
                                                    if (response_json.div_status !== undefined) {
                                                        var div = document.getElementById('tmy_translation_status_box_div');
                                                        div.innerHTML = response_json.div_status.slice(0, -1) ;
                                                    }
                                                    alert('Server Reply: ' + response_json.message);
                                                }
                                                //window.location.reload();
                                            }
                                        },
                                        error:   function(jqXHR, textStatus, errorThrown ) {
                                            alert("Error, status = " + jqXHR.status + ", " + "textStatus: " + textStatus + "ErrorThrown: " + errorThrown);
                                        }
                                    });
                                    return;
                            });
                        }
                    }
                </script>
             <?php


        }

        public function tmy_translation_metabox_callback( $post ) {

            ?>
                <script>

                   (function ($, window, document) {
                      'use strict';
                      // execute when the DOM is ready
                      $(document).ready(function () {

                          wp.data.subscribe(function () {
                            var isSavingPost = wp.data.select('core/editor').isSavingPost();
                            var isAutosavingPost = wp.data.select('core/editor').isAutosavingPost();

                            if (isSavingPost && !isAutosavingPost) {
                              // Here goes your AJAX code ......
                                    console.log("all events");
                                    var data = {
                                            'action': 'tmy_get_post_translation_status',
                                            'id': <?php echo intval($post->ID); ?>,
                                            'post_type': '<?php echo $post->post_type; ?>'
                                    };
                                    $.ajax({
                                        type:    "POST",
                                        url:     ajaxurl,
                                        data:    data,
                                        success: function(response) {

                                            var div = document.getElementById('tmy_translation_status_box_div');
                                            div.innerHTML = response.slice(0, -1) ;

                                            //alert('Server Reply: ' + response);
                                        },
                                        error:   function(jqXHR, textStatus, errorThrown ) {
                                            alert("Error, status = " + jqXHR.status + ", " + "textStatus: " + textStatus + "ErrorThrown: " + errorThrown);
                                        }
                                    });
                                    return;

                          
                            }
                          })

                    });
                    }(jQuery, window, document));

                    function create_sync_translation(id, post_type) {

                        var r = confirm("This will create sync translation");
                        if (r == true) {
                            jQuery(document).ready(function($) {
                                    var data = {
                                            'action': 'tmy_create_sync_translation',
                                            'id': id,
                                            'post_type': post_type
                                    };
                                    $.ajax({
                                        type:    "POST",
                                        url:     ajaxurl,
                                        data:    data,
                                        success: function(response) {
                                            if (response === undefined) {
                                                alert('No Server Reply, talk to system admininstrator.');
                                            } else {
                                                var response_json = $.parseJSON(response);
                                                if (response_json.message === undefined) {
                                                    alert('No Server Reply, talk to system admininstrator.');
                                                } else {
                                                    if (response_json.div_status !== undefined) {
                                                        var div = document.getElementById('tmy_translation_status_box_div');
                                                        div.innerHTML = response_json.div_status.slice(0, -1) ;
                                                    }
                                                    alert('Server Reply: ' + response_json.message);
                                                }
                                                //window.location.reload();
                                            }
                                        },
                                        error:   function(jqXHR, textStatus, errorThrown ) {
                                            alert("Error, status = " + jqXHR.status + ", " + "textStatus: " + textStatus + "ErrorThrown: " + errorThrown);
                                        }
                                    });
                                    return;
                            });
                        }
                    }
                </script>
                <?php

                //$post_id = $post->ID;
                //$post_type = get_post_type($post_id);
                //$post_status = get_post_status($post_id);

                echo '<div id="tmy_translation_status_box_div">';
                echo tmy_g11n_html_kses_esc($this->_get_tmy_g11n_metabox($post->ID, $post->post_type));
                echo '</div>';
	
        }

        public function tmy_admin_options_page() {

		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		defined( 'ABSPATH' ) or die();
    		$tmy_g11n_dir = dirname( __FILE__ );

    		require_once "{$tmy_g11n_dir}/include/g11n-lang-list.php";

		?>		

		<div class="wrap"><h1> <img src="<?php echo plugin_dir_url( __FILE__ ) . 'include/tmy-full.png'; ?>" width="64" alt="TMY"> <?php _e('Globalization Options', 'tmy-globalization') ?></h1>
		<form method="post" action="options.php">
		
		<?php

    		settings_fields( 'tmy-g11n-settings-group' );
    		do_settings_sections( 'tmy-g11n-settings-group' );

		?>
                    <table class="form-table">
        		<tr valign="top">
        		<th scope="row"><?php _e('Default Language', 'tmy-globalization') ?></th>

        		<td><select name="g11n_default_lang">
		<?php
        
          	if (!get_option('g11n_default_lang')) {
              		$sys_default_lang = "English";
          	} else {
              		$sys_default_lang = get_option('g11n_default_lang');
          	}

          	foreach ($complete_lang_list as $lang => $code) :
               		echo '<option value="' . esc_attr($lang) . '" ' . 
                             selected(esc_attr($sys_default_lang), esc_attr($lang)) . ' >' . 
                             esc_attr($lang) . '</option>';
          	endforeach;
		
		?>   
		    </select>
        		</td>
        		</tr>
        		<tr valign="top">
        		<th scope="row"><?php _e('All Enabled Languages', 'tmy-globalization') ?> </th><td>
		<?php



        	if (!get_option('g11n_additional_lang')) {
            		$all_configed_langs = array('English' => $complete_lang_list['English']);
        	} else {
            		$all_configed_langs = get_option('g11n_additional_lang');
        	}
		
		//$all_configed_langs = get_option('g11n_additional_lang');
		//if (strcmp(esc_attr($all_configed_langs),"")==0){
		//    $dlang = esc_attr(get_option('g11n_default_lang'));
		    //$all_configed_langs = array('$dlang' => $complete_lang_list['$dlang']);
		//    $all_configed_langs = array('English' => "en");
		//}

		if (is_array($all_configed_langs)) {
		    foreach( $all_configed_langs as $value => $code) {
		        echo esc_attr($value). 
                        '('.esc_attr($code).
                        ') <input type="checkbox" name="g11n_additional_lang['.esc_attr($value).']" value="'.esc_attr($code).'" checked/><br>';
		    }
		}
		
		?>

		------
        	<div id="g11n_new_languages"></div>
       		<select id="g11n_add_language">

		<?php
        
          	foreach ($complete_lang_list as $lang => $code) :
               		echo '<option value="'.esc_attr($code).'">'.esc_attr($lang).'</option>';
          	endforeach;
       

		?>
		</select>
		<button type="button" onclick="G11nmyFunction()">Add Language</button> Click Save To Keep The Changes

		<script>
		function G11nmyFunction() {

		    	var e = document.getElementById("g11n_add_language");
		    	var code = e.options[e.selectedIndex].value;
		    	var lang = e.options[e.selectedIndex].text;
		    	var div = document.getElementById('g11n_new_languages');
		    	//div.innerHTML = div.innerHTML + lang + "(" + code +") ";
		    	var text = document.createTextNode(lang + "(" + code +") ");
		    	var cb = document.createElement('input');
		    	cb.type = 'checkbox';
		    	cb.checked = true;
		    	cb.name = "g11n_additional_lang["+lang+"]";
		    	cb.value = code;
		    	div.appendChild(text);
		    	div.appendChild(cb);

		}
		function g11n_using_gtookit_change() {
                    
                    var element = document.getElementById("g11n_using_google_tookit"); 
                    if (element.value=='Yes'){
                        console.log("yes");
                        document.getElementById("g11n_switcher_title").disabled=true;
                        document.getElementById("g11n_switcher_tagline").disabled=true;
                        document.getElementById("g11n_switcher_post").disabled=true;
                        document.getElementById("g11n_switcher_sidebar").disabled=true;
                    }
                    if(element.value=='No'){
                        console.log("no");
                        document.getElementById("g11n_switcher_title").disabled=false;
                        document.getElementById("g11n_switcher_tagline").disabled=false;
                        document.getElementById("g11n_switcher_post").disabled=false;
                        document.getElementById("g11n_switcher_sidebar").disabled=false;
                    }
                

                }
		</script>


	       	</td>
	        </tr>

        	<tr valign="top">
        	<th scope="row"><?php _e('Live Translation powered by Google Translate', 'tmy-globalization') ?></th>
        	<td><select id="g11n_using_google_tookit" name="g11n_using_google_tookit" onChange="javascript:g11n_using_gtookit_change();"> 
        	<!-- <td><select id="g11n_using_google_tookit" name="g11n_using_google_tookit" > -->
               		<option value='Yes'  <?php selected( esc_attr(get_option('g11n_using_google_tookit','No')), 'Yes' ); ?>>Yes</option>
               		<option value='No'  <?php selected( esc_attr(get_option('g11n_using_google_tookit','No')), 'No' ); ?>>No</option>
            	</select>
        	</td>
        	</tr>

                <?php 
               	    if (strcmp(get_option('g11n_using_google_tookit','No'),'Yes' )===0) {
                        $config_selected_disable = "disabled";
                        //$config_selected_disable = "";
                    } else {
                        $config_selected_disable = "";
                    }
                    $config_disable = "";
                ?>

        	<tr valign="top">
        	<th scope="row"><?php _e('Language Switcher Location', 'tmy-globalization') ?></th>
        	<td>
            	<input type="checkbox" id="g11n_switcher_title" name="g11n_switcher_title" value="Yes" <?php checked( esc_attr(get_option('g11n_switcher_title')), "Yes" ); ?> <?php echo esc_attr($config_selected_disable); ?>/> In Title <br>
            	<input type="checkbox" id="g11n_switcher_tagline" name="g11n_switcher_tagline" value="Yes" <?php checked( esc_attr(get_option('g11n_switcher_tagline')), "Yes" ); ?> <?php echo esc_attr($config_selected_disable); ?>/> In Tagline <br>
            	<input type="checkbox" id="g11n_switcher_post" name="g11n_switcher_post" value="Yes" <?php checked( esc_attr(get_option('g11n_switcher_post')), "Yes" ); ?> <?php echo esc_attr($config_selected_disable); ?>/> In Each Post <br>
            	<input type="checkbox" id="g11n_switcher_sidebar" name="g11n_switcher_sidebar" value="Yes" <?php checked( esc_attr(get_option('g11n_switcher_sidebar')), "Yes" ); ?><?php echo esc_attr($config_selected_disable); ?> /> Top of Sidebar <br>
            	<input type="checkbox" id="g11n_switcher_floating" name="g11n_switcher_floating" value="Yes" <?php checked( esc_attr(get_option('g11n_switcher_floating')), "Yes" ); ?> /> Draggable Floating Menu <br> <br>
                <?php _e('Language Switchers could be added to different locations via widget "TMY Language Switcher Widget" from "Appearance-> Widgets",<br>
                                                         or Gutenberg block(block) titled "TMY Language Switcher Block". ', 'tmy-globalization') ?>
 	    	</td>
                </tr>
 

	        <tr valign="top">
	        <th scope="row"><?php _e('Get Visitor Language Preference From', 'tmy-globalization') ?></th>
	        <td>Cookie <input type="checkbox" id="g11n_site_lang_cookie" name="g11n_site_lang_cookie" value="Yes" <?php checked(esc_attr( get_option('g11n_site_lang_cookie')), "Yes"); echo esc_attr($config_disable); ?> /><br>
            		Browser Language Preference <input type="checkbox" id="g11n_site_lang_browser" name="g11n_site_lang_browser" 
                                                           value="Yes" <?php checked(esc_attr( get_option('g11n_site_lang_browser')), "Yes"); echo esc_attr($config_disable); ?> />
        	</td>
        	</tr>

        	<tr valign="top">
        	<th scope="row"><?php _e('Translation Server(Optional)', 'tmy-globalization') ?></th>
        	<td>URL <input type="text" id="g11n_server_url" name="g11n_server_url" value="<?php echo esc_attr( get_option('g11n_server_url') ); ?>" <?php echo esc_attr($config_disable); ?> /><br>
            	User <input type="text" id="g11n_server_user" name="g11n_server_user" value="<?php echo esc_attr( get_option('g11n_server_user') ); ?>" <?php echo esc_attr($config_disable); ?> />
            	Token <input type="text" id="g11n_server_token" name="g11n_server_token" value="<?php echo esc_attr( get_option('g11n_server_token') ); ?>" <?php echo esc_attr($config_disable); ?> /> <br>
            	Project Name <input type="text" id="g11n_server_project" name="g11n_server_project" value="<?php echo esc_attr( get_option('g11n_server_project') ); ?>" <?php echo esc_attr($config_disable); ?>  />
            	<!-- <button onclick="g11ncreateproject('project')">Create Project on Translation Server</button> -->
                <!-- <input type="button" value="Create Project on Translation Server" onclick="g11ncreateproject('project')"> -->
            	Version <input type="text" id="g11n_server_version" name="g11n_server_version" value="<?php echo esc_attr( get_option('g11n_server_version') ); ?>" <?php echo esc_attr($config_disable); ?>  />
                <input type="button" id="g11n_create_project_button" value="Create Project on Translation Server" onclick="g11ncreateproject('project')" <?php echo esc_attr($config_disable); ?> > <br>(Remember To Save Changes At the Bottom of The Page)<br><br>
            	Trunk Size <input type="text" id="g11n_server_trunksize" name="g11n_server_trunksize" value="<?php echo esc_attr( get_option('g11n_server_trunksize',900) ); ?>" <?php echo esc_attr($config_disable); ?>  />
        	</td>

		<script>
		function g11ncreateproject(type) {

		    //var proj_name = document.getElementById("g11n_server_project").value;
		    //console.log(document.getElementById("g11n_server_project").value);
		    //
		    var r = confirm("This will create: " + document.getElementById("g11n_server_project").value + " Version: " + 
                                                           document.getElementById("g11n_server_version").value + " in translation server");
		    if (r == true) {
			jQuery(document).ready(function($) {
				var data = {
					'action': 'tmy_create_server_project',
					'proj_name': document.getElementById("g11n_server_project").value,
					'proj_ver': document.getElementById("g11n_server_version").value,
					'action_type': type
				};
	//		        jQuery.post(ajaxurl, data, function(response) {
	//			    alert('Server Reply: ' + response);
	//	                    console.log("aaa");
	//			    //var div = document.getElementById("g11n_server_project_status");
	//			    //var text = document.createTextNode(response);
	//			    //div.appendChild(text);
	//			});
                        
                                $.ajax({
                                    type:    "POST",
                                    url:     ajaxurl,
                                    data:    data,
                                    success: function(response) {
	         			alert('Server Reply: ' + response);
                                    },
                                    error:   function(jqXHR, textStatus, errorThrown ) {
                                        alert("Error, status = " + jqXHR.status + ", " + "textStatus: " + textStatus + "ErrorThrown: " + errorThrown);
                                    }
                                });
				return;
			});
		    }
		}
		</script>


		<tr valign="top">
        	<th scope="row"><?php esc_html_e('Enable Translation On', 'tmy-globalization') ?></th>
        	<td>
            	<br>
            	<input type="checkbox" id="g11n_l10n_props_blogname" name="g11n_l10n_props_blogname" value="Yes" <?php checked( esc_attr(get_option('g11n_l10n_props_blogname')), "Yes" ); echo esc_attr($config_disable); ?> /> 
                                                                                                            Site Title/Blog Name <br>
            	<input type="checkbox" id="g11n_l10n_props_blogdescription" name="g11n_l10n_props_blogdescription" value="Yes" <?php checked( esc_attr(get_option('g11n_l10n_props_blogdescription')), "Yes" ); echo esc_attr($config_disable); ?> /> Tagline<br><br>

                <?php
                $all_post_types = tmy_g11n_available_post_types();
                foreach ( $all_post_types  as $post_type ) {
                    $option_name = 'g11n_l10n_props_' . $post_type;
                    ?>
            	    <input type="checkbox" id="<?php echo esc_attr($option_name); ?>" name="<?php echo esc_attr($option_name); ?>" value="Yes" <?php checked( esc_attr(get_option("$option_name")), "Yes" ); echo esc_attr($config_disable); ?> /> <?php echo esc_attr($post_type); ?><br>
                    <?php
                }

                ?>
                <!--
            	<input type="checkbox" id="g11n_l10n_props_posts" name="g11n_l10n_props_posts" value="Yes" <?php checked( esc_attr(get_option('g11n_l10n_props_posts')), "Yes" ); echo esc_attr($config_disable); ?> /> Posts<br>
            	<input type="checkbox" id="g11n_l10n_props_pages" name="g11n_l10n_props_pages" value="Yes" <?php checked( esc_attr(get_option('g11n_l10n_props_pages')), "Yes" ); echo esc_attr($config_disable); ?> /> Pages 
                -->

        	</td>
        	</tr>             
 
		<tr valign="top">
        	<th scope="row"><?php esc_html_e('Taxonomy Translation Option', 'tmy-globalization') ?></th>
        	<td>
            	<br>
                <?php

                $all_configed_taxs = get_option('g11n_l10n_props_tax', array());
       
                //if (! isset($all_configed_taxs)) {
                if (! is_array($all_configed_taxs)) {
                    $all_configed_taxs = array();
                }
                $all_taxs = get_taxonomies(array("public" => true, "show_ui"=> true), "names", "or");
                foreach ( $all_taxs as $key ) {
                    if (! empty($all_configed_taxs)) {
                        if (array_key_exists($key, $all_configed_taxs)) {
                            $item_checked = "checked";
                        } else {
                            $item_checked = "";
                        }
                    } else {
                        $item_checked = "";
                    }
                    ?>
            	    <input type="checkbox" id="<?php echo esc_attr($key); ?>" name="g11n_l10n_props_tax[<?php echo esc_attr($key); ?>]" value="<?php echo esc_attr($key); ?>" <?php echo $item_checked; echo esc_attr($config_disable); ?> /> <?php echo esc_attr($key); ?><br>
                    <?php
                }

                ?>

        	</td>
        	</tr>             
            	<?php

         
		echo '<tr valign="top"><th scope="row">' . __('Language Switcher Type', 'tmy-globalization') . '</th><td>';

		  
		$g11n_switch_type = array(
		          'Text' => __('Text', 'tmy-globalization'),
		          'Flag' => __('Flag', 'tmy-globalization')
		);
		
		foreach ($g11n_switch_type as $key => $desc) :
		          $selected = (get_option('g11n_switcher_type','Flag') == $key) ? 'checked="checked"' : '';
		          echo "\n\t<label><input type='radio' id='g11n_switcher_type_".esc_attr($key).
                               "' name='g11n_switcher_type' value='" . esc_attr($key) . 
                               "' " . esc_attr($selected) . esc_attr($config_disable) . " /> " . esc_attr($desc) . "</label><br />";
	  	endforeach;
		 
          	?>
        	</td>
        	</tr>

        	<tr valign="top">
        	<th scope="row"><?php esc_html_e('Use Classic Editor', 'tmy-globalization') ?></th>
        	<td><select name="g11n_editor_choice">
               		<option value='Yes'  <?php selected( esc_attr(get_option('g11n_editor_choice','No')), 'Yes' ); ?>>Yes</option>
               		<option value='No'  <?php selected( esc_attr(get_option('g11n_editor_choice','No')), 'No' ); ?>>No</option>
            	</select>
        	</td>
        	</tr>
        	<tr valign="top">
        	<th scope="row"><?php esc_html_e('Auto Push/Pull Translation', 'tmy-globalization') ?></th>
        	<td><select name="g11n_auto_pullpush_translation">
               		<option value='No'  <?php selected( esc_attr(get_option('g11n_auto_pullpush_translation')), 'No' ); ?>>No</option>
            	</select>
        	</td>
        	</tr>
        	<tr valign="top">
        	<th scope="row"><?php esc_html_e('Translation Resource File Directory', 'tmy-globalization') ?></th>
        	<td><input type="text" name="g11n_resource_file_location" value="<?php echo esc_attr( get_option('g11n_resource_file_location') ); ?>" /></td>
        	</tr>

        	</tr>
        	<tr valign="top">
        	<th scope="row"><?php esc_html_e('Theme Translation Files', 'tmy-globalization') ?></th>
        	<td> <?php 
                         $theme_name = get_template();
                         $all_langs = get_option('g11n_additional_lang', array());
                         $default_lang = get_option('g11n_default_lang');
                         unset($all_langs[$default_lang]);
                         foreach( $all_langs as $value => $code) {
                             echo WP_LANG_DIR . '/' . $code . '.mo <br>';
                             echo WP_LANG_DIR . '/themes/' . $theme_name."-".$code . '.mo <br>';
                         } 
                     ?>
                </td>
        	</tr>
                <tr valign="top">
                <th scope="row"><?php esc_html_e('Search Engine Optimization(SEO) URL', 'tmy-globalization') ?></th>
                <td> 
                    <?php
		
                         $blog_url = get_bloginfo('url');

                         $seo_disabled = "";

               	         //if ((strcmp(get_option('g11n_using_google_tookit','No'),'Yes' )===0) ||
                         //    (strcmp(trim(get_option('permalink_structure')),'')===0)) {
                         //    $seo_disabled = "disabled";
                         //} else {
                         //    $seo_disabled = "";
                         //}

		         $current_seo_option = esc_attr(get_option('g11n_seo_url_enable','No'));
                         if (strcmp($current_seo_option, "")===0) {
                             $current_seo_option = "No";
                         }

                         //echo "Change the Permalinks Setting to non-Plain to start: Settings->Permalinks<br><br>";

		         $selected_no = ($current_seo_option === 'No') ? 'checked' : '';
		         echo "\n\t<input type='radio' onclick=\"tmy_seo_url_option_changed('No');\" id='g11n_seo_url_enable_no".
                                    "' name='g11n_seo_url_enable' value='No' " . 
                                    esc_attr($selected_no) . " " . esc_attr($seo_disabled) . " > <label> " . 'No' . "</label><br><br>";

		         $selected_yes = ($current_seo_option === 'Yes') ? 'checked' : '';
		         echo "\n\t<input type='radio' onclick=\"tmy_seo_url_option_changed('Yes');\" id='g11n_seo_url_enable_yes".
                                    "' name='g11n_seo_url_enable' value='Yes' " .
                                    esc_attr($selected_yes) . " " . esc_attr($seo_disabled) . " > <label> " . 
                                    'Yes - URL format: ' . esc_url($blog_url) . 
                                    '/<i>language_code</i>/' .
                                    "</label><br>";

		         if  ($current_seo_option == 'Yes') {
                             echo "<div id=\"tmy_seo_example_urls\" style=\"display: block\"><br>";
                         } else {
                             echo "<div id=\"tmy_seo_example_urls\" style=\"display: none\"><br>";
                         }
                         $all_langs = get_option('g11n_additional_lang',array());
                         $rewrite_rules = strtolower(implode("|", $all_langs));
                         $rewrite_rules = str_replace('_', '-', $rewrite_rules);

                         foreach( $all_langs as $value => $code) {
                             echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . esc_url($blog_url) . "/" . esc_attr(strtolower(str_replace('_', '-', $code))) . "/ (" . esc_attr($value) . " URL)<br>";
                         } 
                         echo "</div><br>";



                         $home_root = parse_url( home_url() );
                         if ( isset( $home_root['path'] ) ) {
                            $home_root = trailingslashit( $home_root['path'] );
                         } else {
                            $home_root = '/';
                         }

                         $htaccess_file = get_home_path() . '.htaccess';

                         if (is_writable($htaccess_file)) {
                            $htaccess_permission = $htaccess_file . " is writable, .htaccess will be updated automatically.";
                         } else {
                            $htaccess_permission = $htaccess_file . " is NOT writable, please make sure your .htaccess is updated.";
                         }

                     ?>
            	</select>

		<script>
		    function tmy_seo_url_option_changed(type) {

                        var example_urls_div = document.getElementById('tmy_seo_example_urls');
                        var rules_box_div = document.getElementById('tmy_seo_rules_box');

                        if (type === "Yes") {
                            example_urls_div.style.display = "block";
                            rules_box_div.style.display = "block";
                        }
                        if (type === "No") {
                            example_urls_div.style.display = "none";
                            rules_box_div.style.display = "none";
                        }
                    }
		</script>
              <?php
		 if  (($current_seo_option == 'Yes') && (strcmp(trim(get_option('permalink_structure')),'')!==0)) {
                     echo "<div id=\"tmy_seo_rules_box\" style=\"display: block\"><br>";
                 } else {
                     echo "<div id=\"tmy_seo_rules_box\" style=\"display: none\"><br>";
                 }
              ?> 
                        Depends on your specific configuration, here is the example .htaccess file if you are using Apache, lines between "# BEGIN TMY G11N RULES" and "# END TMY G11N RULES" are newly added. Your .htaccess might look different, in most cases, adding these new lines will work.<br><br>
                        Let's know if you need help, SEO friendly URLs could get tricky sometimes.<br><br>

                        Current .htaccess file: <?php echo esc_attr($htaccess_permission);?> 
<div style="width: 1000px; padding: 10px; border: 2px solid black; margin: 0;"> 
<b># BEGIN TMY_G11N_RULES <br>
&lt;IfModule mod_rewrite.c&gt; <br>
RewriteCond %{REQUEST_FILENAME} -d  <br> 
RewriteCond %{REQUEST_URI} /+[^\.]+$  <br>
RewriteRule ^(.+[^/])$ %{REQUEST_URI}/ [R=301,L] <br> 
RewriteCond %{HTTPS} off <br>
RewriteRule ^(<?php echo $rewrite_rules; ?>)/(.*) http://%{HTTP_HOST}<?php echo esc_attr($home_root); ?>$2?g11n_tmy_lang_code=$1 [QSA,P,NC] <br>
RewriteCond %{HTTPS} on <br>
RewriteRule ^(<?php echo $rewrite_rules; ?>)/(.*) https://%{HTTP_HOST}<?php echo esc_attr($home_root); ?>$2?g11n_tmy_lang_code=$1 [QSA,P,NC] <br>
&lt;IfModule&gt; <br>
# END TMY_G11N_RULES <br> </b>
&lt;IfModule mod_rewrite.c&gt; <br>
RewriteEngine On <br>
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}] <br>
RewriteBase <?php echo esc_attr($home_root); ?> <br>
RewriteRule ^index\.php$ - [L] <br>
RewriteCond %{REQUEST_FILENAME} !-f <br>
RewriteCond %{REQUEST_FILENAME} !-d <br>
RewriteRule . <?php echo esc_attr($home_root); ?>index.php [L]<br>
&lt;IfModule&gt; <br>
</div></div>
                </td>
                </tr>

    		</table>
    			<!-- <?php submit_button(); ?> -->
                        <input type="submit" name="submit" id="submit" class="button button-primary" onclick="G11nmyOptionSaveChanges()" value="Save Changes"  /> &nbsp; <div id="tmy_save_changes_status" style="display:inline-block; vertical-align: middle;"></div><br>
		</form>

                <script>
                    function G11nmyOptionSaveChanges() {
                        var div = document.getElementById('tmy_save_changes_status');
                        div.innerHTML = "<div class=\"tmy_loader\"></div>   Saving Changes ....";
                    }
                </script>

		</div>
		<?php 

        }

        public function tmy_l10n_text_page() {
	    if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
 	    }
            ?>
            <div class="wrap"><h1> <img src="<?php echo plugin_dir_url( __FILE__ ) . 'include/tmy-full.png'; ?>" width="64" alt="TMY"> <?php esc_html_e('Globalization Text Translation Manager', 'tmy-globalization') ?></h1>
            <?php

            $tmy_g11n_dir = dirname( __FILE__ );
            require_once "{$tmy_g11n_dir}/include/class-tmy-g11n-table.php";

            echo '<form method="post">';

            echo "<br>";
            echo "<br>";

            $table = new TMY_G11N_Text_Table();
            $table->process_bulk_action = $this->tmy_translation_text_table_action();
            $table->prepare_items();

            ?> <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']) ?>" /><?php
            $table->display();
            echo '</form>';




        }
        public function tmy_l10n_taxonomy_page() {
	    if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
 	    }
            ?>
            <div class="wrap"><h1> <img src="<?php echo plugin_dir_url( __FILE__ ) . 'include/tmy-full.png'; ?>" width="64" alt="TMY"> <?php esc_html_e('Globalization Taxonomy Translation Manager', 'tmy-globalization') ?></h1>
            <?php

            //echo "<br>Abc<br>";
            //echo "<br>". json_encode(get_current_screen()) . "<br>";
            $tmy_g11n_dir = dirname( __FILE__ );
            require_once "{$tmy_g11n_dir}/include/class-tmy-g11n-table.php";

            echo '<form method="post">';
          
            echo "<br>";
            echo "<br>";

            $table = new TMY_G11N_Table();
            $table->process_bulk_action = $this->tmy_translation_taxonomy_table_action();
            //$table->process_bulk_action();
            $table->prepare_items();

            ?> <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']) ?>" /><?php
            $table->display();
            echo '</form>';


        }

        public function tmy_translation_text_table_action() {

            if ( isset( $_POST['_wpnonce'] ) && ! empty( $_POST['_wpnonce'] ) ) {
                $nonce  = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING );
                $action = 'bulk-' . $this->_args['plural'];

                if ( ! wp_verify_nonce( $nonce, $action ) )
                    wp_die( 'Nope! Security check failed!' );
            }
            global $wpdb;
            if ( isset( $_POST['action'] )) {

                switch ( esc_attr($_POST['action']) ) {

                    case 'start_translation_from_text_form':

                        $text_str = esc_sql($_POST['text_str']);

                        foreach ($text_str as $text_string) {
                            $sql = "select ID from {$wpdb->prefix}posts where post_title=\"" . esc_sql($text_string) . "\" and post_status=\"private\"";
                            $result = $wpdb->get_results($sql);
                            if (! isset($result[0]->ID)) {
                                 $holder_id = wp_insert_post(
                                      array(
                                           'post_title'    => esc_attr($text_string),
                                           'post_content'  => esc_attr($text_string),
                                           'post_status' => 'private',
                                           'post_type'  => "post"
                                     ));
                            } else {
                                $holder_id = $result[0]->ID;
                            }
                            $response = json_decode($this->_tmy_create_sync_translation($holder_id, "post"));
                            echo '<div class="notice notice-success is-dismissible"><p>' .  esc_html($response->message) . '</p></div>';
                        }

                        return;
                        break;

                    case 'remove_translation_from_text_form':

                        $text_str = esc_sql($_POST['text_str']);

                        foreach ($text_str as $text_string) {
                            $term_notify = "";
                            $all_langs = get_option('g11n_additional_lang', array());
                            $default_lang = get_option('g11n_default_lang');
                            unset($all_langs[$default_lang]);

                            $sql = "select ID from {$wpdb->prefix}posts where post_title=\"" . esc_sql($text_string) . "\" and post_status=\"private\"";
                            $result = $wpdb->get_results($sql);
                            if (isset($result[0]->ID)) {
                                wp_delete_post( $result[0]->ID );
                                $term_notify .= "ID : {$result[0]->ID} ";
                                if (is_array($all_langs)) {
                                    foreach( $all_langs as $value => $code) {
                                        $translation_id = $this->translator->get_translation_id($result[0]->ID, $code, "post");
                                        if (isset($translation_id)) {
                                            wp_delete_post( $translation_id );
                                            delete_post_meta( $translation_id, 'orig_post_id' );
                                            delete_post_meta( $translation_id, 'g11n_tmy_lang' );
                                            delete_post_meta( $translation_id, 'g11n_tmy_orig_type' );
                                            $term_notify .= "{$code}:{$translation_id} ";
                                        }
                                    }
                                }
                            }
                            if (strcmp($term_notify,"")===0) {
                                echo '<div class="notice notice-success is-dismissible"><p> Text: ' . esc_attr($text_string) . ", no translation found" . '</p></div>';
                            } else {
                                echo '<div class="notice notice-success is-dismissible"><p> Text: ' . esc_attr($text_string) . ", removed translation for: " . esc_attr($term_notify) . '</p></div>';
                            }
                        }

                        return;
                        break;


                    default:
                        // do nothing or something else
                        return;
                        break;
                }
            }

            return;

        }
        public function tmy_translation_taxonomy_table_action() {

            //         echo '<div class="notice notice-success is-dismissible"><p> bulk action 456</p></div>';
            // security check!
            if ( isset( $_POST['_wpnonce'] ) && ! empty( $_POST['_wpnonce'] ) ) {
                $nonce  = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING );
                $action = 'bulk-' . $this->_args['plural'];

                if ( ! wp_verify_nonce( $nonce, $action ) )
                    wp_die( 'Nope! Security check failed!' );
            }

            $action = current_action();

            if ( isset( $_POST['action'] )) {

                switch ( esc_attr($_POST['action']) ) {

                    case 'start_translation_from_taxonomies_form':

                        $term_ids = esc_sql($_POST['term_id']);
                        //echo '<div class="notice notice-success is-dismissible"><p> bulk action' .implode("-", $term_ids) . '</p></div>';

                        foreach ($term_ids as $term_id) {
                            $tax_type = get_term_field('taxonomy', $term_id);
                            //$this->_tmy_create_sync_translation($term_id, "taxonomy");

                            //$this->_tmy_create_sync_translation($term_id, $tax_type);
                            $response = json_decode($this->_tmy_create_sync_translation($term_id, $tax_type));

                            //echo '<div class="notice notice-success is-dismissible"><p> Starting translation for: ' . $term_id . '</p></div>';
                            echo '<div class="notice notice-success is-dismissible"><p>' .  esc_html($response->message) . '</p></div>';
                        }

                        return;
                        break;

                    case 'remove_translation_from_taxonomies_form':

                        $term_ids = esc_sql($_POST['term_id']);

                        foreach ($term_ids as $term_id) {
                            $term_notify = "";
                            $all_langs = get_option('g11n_additional_lang', array());
                            $default_lang = get_option('g11n_default_lang');
                            unset($all_langs[$default_lang]);
                            $tax_type = get_term_field('taxonomy', $term_id);

                            if (is_array($all_langs)) {
                                foreach( $all_langs as $value => $code) {
                                    $translation_id = $this->translator->get_translation_id($term_id,$code,$tax_type);
                                    //$translation_id = $this->translator->get_translation_id($term_id,$code,"taxonomy");
                                    if (isset($translation_id)) {
                                        wp_delete_post( $translation_id );
                                        delete_post_meta( $translation_id, 'orig_post_id' );
                                        delete_post_meta( $translation_id, 'g11n_tmy_lang' );
                                        delete_post_meta( $translation_id, 'g11n_tmy_orig_type' );
                                        $term_notify .= "{$code}:{$translation_id} ";
                                    }
                                 }
                            }
                            if (strcmp($term_notify,"")===0) {
                                echo '<div class="notice notice-success is-dismissible"><p> Term ID: ' . esc_attr($term_id) . ", no translation found" . '</p></div>';
                            } else {
                                echo '<div class="notice notice-success is-dismissible"><p> Term ID: ' . esc_attr($term_id) . ", removed translation for: " . esc_attr($term_notify) . '</p></div>';
                            }
                        }

                        return;
                        break;


                    default:
                        // do nothing or something else
                        return;
                        break;
                }
            }

            return;

        }
        public function tmy_l10n_manager_page() {

		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		?>
                    <br>
                    <script>
                    function G11nGetLocalTranslationStatus() {
                        var div = document.getElementById('tmy_local_translation_status');

                        jQuery(document).ready(function($) {
                                var data = {
                                    'action': 'tmy_get_local_translation_status',
                                    'secret': 8763
                                };
                                jQuery.post(ajaxurl, data, function(response) {
                                    div.innerHTML = response.slice(0, -1) ;
                                });
                                return;
                        });
                    }

                    function G11nmyGetServerStatus() {
                        var div = document.getElementById('tmy_server_status');
                        div.innerHTML = "<div class=\"tmy_loader\"></div>   Connecting to server ....";

                        jQuery(document).ready(function($) {
                                var data = {
                                    'action': 'tmy_get_project_status',
                                    'secret': 8763
                                };
                                jQuery.post(ajaxurl, data, function(response) {
                                    div.innerHTML = response;
                                });
                                return;
                        });
                    }

                    </script>
                <div class="wrap"><h1> <img src="<?php echo plugin_dir_url( __FILE__ ) . 'include/tmy-full.png'; ?>" width="64" alt="TMY"> <?php esc_html_e('Globalization Dashboard', 'tmy-globalization') ?></h1>
                <!--- 
		<h2>Translation Status:</h2>
                <button type="button" onclick="G11nGetLocalTranslationStatus()">Refresh Translation Status</button>
		<div class="wrap">
                <div id="tmy_local_translation_status">
                -->
		<?php

                //global $wpdb;
                //$rows = $wpdb->get_results( 'SELECT ID, post_title, meta_value, post_modified  FROM '.
                //                                  $wpdb->prefix.'posts,'.
                //                                  $wpdb->prefix.'postmeta where post_type="g11n_translation" and '.
                //                                  $wpdb->prefix.'posts.post_status != "trash" and '.
                //                                  $wpdb->prefix.'posts.ID='.
                //                                  $wpdb->prefix.'postmeta.post_id and '.
                //                                  $wpdb->prefix.'postmeta.meta_key="g11n_tmy_lang" ORDER BY ID');

                //if ( WP_TMY_G11N_DEBUG ) {
                //    error_log("In tmy_l10n_manager_page sql:" . esc_attr(json_encode($rows)));
                //}

                //$this->tmy_g11n_get_local_translation_status();

                //echo "</div>";
           
		echo "<h2>" . __('Translation Server Status', 'tmy-globalization') . ":</h2>";

                if ((strcmp('', get_option('g11n_server_user','')) === 0) || (strcmp('', get_option('g11n_server_token','')) === 0)) {
                     echo __("Translation server is not in use, configure the Translation Server(Optional) section in the setup page to start<br>", 'tmy-globalization');
                } else {

                    $query_string = array(
                        'user' => esc_attr(get_option('g11n_server_user','')),
                        'token' => esc_attr(get_option('g11n_server_token','')),
                        'projectname' => esc_attr(get_option('g11n_server_project')),
                        'version' => esc_attr(get_option('g11n_server_version'))
                    );

                    $machine_trans_url = esc_url("https://www.tmysoft.com/myprojects.html?tmycode=" . urlencode(http_build_query($query_string)));

                    ?>
                    <br>
                    <br>
                    <button onclick="location.href=('<?php echo $machine_trans_url; ?>')" target="_blank" type="button">Applying Machine Translation on tmysoft.com</button>
                    <br>
                    <br>
                    <?php
                    echo "<a href ='" . $machine_trans_url . "' target='_blank' >" . $machine_trans_url . "</a>";
                    echo '<br>';
                    echo '<br>';
                    echo '<br>';
                    echo '<button type="button" onclick="G11nmyGetServerStatus()">Click This Button To Sync with Translation Server</button> ';
                    echo '<br>';
                    echo "<br>Translation Server: ". esc_attr(get_option('g11n_server_url')) . '<br> ';
                    echo "Project: <b>" . esc_attr(get_option('g11n_server_project')). "</b> ";
                    echo "Version: <b>" . esc_attr(get_option('g11n_server_version')) . "</b><br>";
                    $rest_url = rtrim(get_option('g11n_server_url'),"/") .  "/rest/version";
                    $server_reply = $this->translator->rest_get_translation_server($rest_url);
                    echo "Server Version: " . esc_attr($server_reply["payload"]->versionNo) . "<br><br>";
        	    echo '<div id="tmy_server_status">';
        	    echo '</div>';


               }

        }

        public function tmy_support_manager_page() {

		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );

    			/*Collecting System Running Information*/

		}


		?>
		<div class="wrap">
                <div class="wrap"><h1> <img src="<?php echo plugin_dir_url( __FILE__ ) . 'include/tmy-full.png'; ?>" width="64" alt="TMY"> <?php esc_html_e('Globalization Diagnosis', 'tmy-globalization') ?></h1><br>
              <?php _e('This Diagnosis tool provides advanced system information on how your site is running and collects the following information:<br><br>
                 - Base system(phpinfo)<br>
                 - TMYSoft plugin version<br>
                 - Default theme<br>
                 - Some system options that can impact how the plugin may be functioning<br><br>
                 If you need further assistance to trouble shoot your site, please review the below diagnostic information to ensure there is no sensitive information included, click copy and email it to <a href="mailto:yu.shao.gm@gmail.com">yu.shao.gm@gmail.com</a> in email.', 'tmy-globalization') ?>
		  <br>
		  <br>
		    <button onclick="g11ncopysysteminfotext()"><?php esc_html_e('Copy Text', 'tmy-globalization') ?></button>
		  <br>
		<?php

                //echo date_default_timezone_get();
		$g11n_sys_info = "\n***** G11n Plugin Diagnosis *****\n" . date("F d, Y l H:s e") . "\n";
    		//$g11n_sys_info .= phpversion() . "\n";
    		//$g11n_sys_info .= var_export(get_loaded_extensions(),true) . "\n";
    		//$g11n_sys_info .= var_export(apache_getenv(),true) . "\n";
    		//global $_ENV;
    		//$g11n_sys_info .= var_export($_ENV,true) . "\n";
    		//$g11n_sys_info .= var_export(ini_get_all(),true) . "\n";
    		//$g11n_sys_info .= json_encode(ini_get_all(),true) . "\n";
    		$g11n_sys_info .= "*** Wordpress version: " . get_bloginfo('version') . "\n";
    		$g11n_sys_info .= "*** Wordpress name: " . get_bloginfo('name') . "\n";
    		$g11n_sys_info .= "*** Wordpress description: " . get_bloginfo('description') . "\n";
    		$g11n_sys_info .= "*** Wordpress wpurl: " . get_bloginfo('wpurl') . "\n";
    		$g11n_sys_info .= "*** Wordpress url: " . get_bloginfo('url') . "\n";
    		$g11n_sys_info .= "*** Wordpress charset " . get_bloginfo('charset') . "\n";
    		$g11n_sys_info .= "*** Wordpress html_type: " . get_bloginfo('html_type') . "\n";
    		$g11n_sys_info .= "*** Wordpress text_direction: " . get_bloginfo('text_direction') . "\n";
    		$g11n_sys_info .= "*** Wordpress language: " . get_bloginfo('language') . "\n";

    		$g11n_sys_info .= "*** G11n Plugin Info: \n" . var_export(get_plugin_data( __FILE__ ),true) . "\n";


    		global $wpdb;
    		$no_g11n_config = $wpdb->get_results( 'select count(*) as count from '.
                                                      $wpdb->prefix.
                                                      'options where option_name like "g11n%"');
    		$no_g11n_trans = $wpdb->get_results( 'select count(*) as count from '.
                                                      $wpdb->prefix.
                                                      'posts where post_type="g11n_translation"');
    		$no_g11n_metas = $wpdb->get_results( 'select count(*) as count from '.
                                                     $wpdb->prefix.
                                                     'postmeta where meta_key="g11n_tmy_lang" or meta_key="orig_post_id" or meta_key="translation_push_status"');

    		$g11n_sys_info .= "*** There are ".
                                  $no_g11n_config[0]->count.
                                  " config entries, ".
                                  $no_g11n_trans[0]->count.
                                  " translations, ".
                                  $no_g11n_metas[0]->count.
                                  " meta data entries in system.\n";


    		$g11n_sys_info .= "*** Current Theme: \n" . var_export(wp_get_theme(),true) . "\n";
    		$g11n_sys_info .= "*** home_path: " . var_export(get_home_path(),true) . "\n";

        	//$home_trans_list = get_available_languages(get_home_path() . "wp-content/languages");
        	//$home_trans_list = scandir(get_home_path() . "wp-content/languages");

                if (is_dir(WP_CONTENT_DIR . "/languages")){
        	    $home_trans_list = scandir(WP_CONTENT_DIR . "/languages");
        	    $g11n_sys_info .= "Translation in " . WP_CONTENT_DIR . "/languages: \n";
        	    $g11n_sys_info .= var_export($home_trans_list,true) . "\n";
                } else {
        	    $g11n_sys_info .= "No direcotory in system: " . WP_CONTENT_DIR . "/languages: \n";
                }

    		$g11n_sys_info .= "*** theme_roots: " . var_export(get_theme_roots(),true) . "\n";
    		$g11n_sys_info .= "*** theme_root: " . var_export(get_theme_root(),true) . "\n";
    		$g11n_sys_info .= "*** template dir: " . var_export(get_template_directory(),true) . "\n";
    		$g11n_sys_info .= "*** stylesheet dir: " . var_export(get_stylesheet_directory(),true) . "\n";
    		//$g11n_sys_info .= "WP plugin_dir_path: " . var_export(plugin_dir_path(__FILE__),true) . "\n";
    		//$g11n_sys_info .= "WP plugin_basename: " . var_export(plugin_basename(__FILE__),true) . "\n";

    		$theme_list = search_theme_directories();
    		//$g11n_sys_info .= "*** Themes List: " . var_export($theme_list,true) . "\n";
    		$g11n_sys_info .= "*** Themes List: " . json_encode($theme_list,true) . "\n";

    		foreach ($theme_list as $name => $locs) {
        		$trans_list = get_available_languages($locs['theme_root'] . "/" . $name);
        		$g11n_sys_info .= "*** Themes Translation in: " .$locs['theme_root']."/". $name . "\n";
        		$g11n_sys_info .= json_encode($trans_list,true) . "\n";
    		}
    
		//$g11n_sys_info .= "Wordpress Root: " . var_export(get_theme_roots(),true) . "\n";
    		//$g11n_sys_info .= "Wordpress Options: " . var_export(get_alloptions(),true) . "\n";
    
		$options_array = array ('g11n_default_lang',
                             'g11n_additional_lang',
                             'g11n_server_url',
                             'g11n_server_user',
                             //'g11n_server_token',
                             'g11n_server_project',
                             'g11n_server_version',
                             'g11n_l10n_props_blogname',
                             'g11n_l10n_props_blogdescription',
                             'g11n_site_lang_cookie',
                             'g11n_site_lang_session',
                             'g11n_site_lang_query_string',
                             'g11n_site_lang_browser',
                             'g11n_switcher_tagline',
                             'WP_LANG_DIR',
                             'g11n_switcher_post');

		$options_array = array_merge($options_array, 
                                             array_values(tmy_g11n_available_post_type_options()));

    		foreach ( $options_array as $op_row ) {
        		$g11n_sys_info .= "*** Wordpress Options: ". 
                                          $op_row . ":" . 
                                          var_export(get_option($op_row),true) . "\n";

    		}


    		$info_rest_url = rtrim(get_option('g11n_server_url'),"/") .  "/rest/stats/proj/" .
                get_option('g11n_server_project') .  "/iter/" .
                get_option('g11n_server_version') .  "?detail=true&word=false";

    		$info_server_reply = $this->translator->rest_get_translation_server($info_rest_url);

    		$test_payload = $info_server_reply["payload"];
    		$test_debug_log = $info_server_reply["server_msg"];

    		$g11n_sys_info .= "*** Translation Server Status: ". $info_rest_url. "\n";
    		//$g11n_sys_info .= "Testing Translation Server, Return Payload: ". var_export($test_payload,true) . "\n";
    		$g11n_sys_info .= "Extra Message : ". var_export($test_debug_log,true) . "\n";


    		global $wpdb;
    		$testing_local_rows = $wpdb->get_results( 'SELECT ID, post_title,post_id,meta_id,meta_value FROM '.
                                                          $wpdb->prefix.'posts,'.
                                                          $wpdb->prefix.'postmeta where post_type="g11n_translation" and '.
                                                          $wpdb->prefix.'posts.ID='.
                                                          $wpdb->prefix.'postmeta.post_id and '.
                                                          $wpdb->prefix.'postmeta.meta_key="g11n_tmy_lang"');

    		$g11n_sys_info .= "*** Local Translations: ". json_encode($testing_local_rows,JSON_UNESCAPED_UNICODE) . "\n";

    		$entitiesToUtf8 = function($input) {
        	// http://php.net/manual/en/function.html-entity-decode.php#104617
        	return preg_replace_callback("/(&#[0-9]+;)/", function($m) { 
                       return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES"); }, $input);
    		};
    		$plainText = function($input) use ($entitiesToUtf8) {
        	return trim(html_entity_decode($entitiesToUtf8(strip_tags($input))));
    		};
    		$titlePlainText = function($input) use ($plainText) {
        	return '# '.$plainText($input);
    		};
    
    		ob_start();
    		phpinfo(-1);
    
    		$phpinfo = array('phpinfo' => array());

    		// Strip everything after the <h1>Configuration</h1> tag (other h1's)
    		if (!preg_match('#(.*<h1[^>]*>\s*Configuration.*)<h1#s', ob_get_clean(), $matches)) {
        		return array();
    		}
    
    		$input = $matches[1];
    		$matches = array();

		    if(preg_match_all(
			'#(?:<h2.*?>(?:<a.*?>)?(.*?)(?:<\/a>)?<\/h2>)|'.
			'(?:<tr.*?><t[hd].*?>(.*?)\s*</t[hd]>(?:<t[hd].*?>(.*?)\s*</t[hd]>(?:<t[hd].*?>(.*?)\s*</t[hd]>)?)?</tr>)#s',
			$input, 
			$matches, 
			PREG_SET_ORDER
		    )) {
			foreach ($matches as $match) {
			    $fn = strpos($match[0], '<th') === false ? $plainText : $titlePlainText;
			    if (strlen($match[1])) {
				$phpinfo[$match[1]] = array();
			    } elseif (isset($match[3])) {
				$keys1 = array_keys($phpinfo);
				$phpinfo[end($keys1)][$fn($match[2])] = isset($match[4]) ? array($fn($match[3]), $fn($match[4])) : $fn($match[3]);
			    } else {
				$keys1 = array_keys($phpinfo);
				$phpinfo[end($keys1)][] = $fn($match[2]);
			    }

			}
		    }

    		$g11n_sys_info .= "*** PHPINFO \n ";
    		//$g11n_sys_info .= var_export($phpinfo,true) . "\n";
    		$g11n_sys_info .= json_encode($phpinfo,JSON_UNESCAPED_UNICODE) . "\n";



    		//echo "<pre>".$g11n_sys_info."</pre>";
		?><br>
		<textarea rows="30" cols="100" class="scrollabletextbox" spellcheck="false" 
                                               name="g11_sys_info_box" 	id="g11n_sys_info_box_id">

		<?php echo esc_attr($g11n_sys_info); ?>

		</textarea>
		<script>
			function g11ncopysysteminfotext() {
  				var copyText = document.getElementById("g11n_sys_info_box_id");
  				copyText.select();
  				document.execCommand("Copy");
  				//alert("Copied The Text");
			} 
			function g11ncleardata() {
    				var r = confirm("This will clear all configuration and translation, however you could rebuild easily.");
    				if (r == true) {
        			jQuery(document).ready(function($) {
        				var data = {
        				'action': 'tmy_create_clear_plugin_data',
        				'whatever': 8888
        				};
        				jQuery.post(ajaxurl, data, function(response) {
        					alert('Server Reply: ' + response);
        				});
                			return;
        				});
    				}
			} 
		</script>
		</h3>

		<br><br><br>
		<?php

    		echo esc_attr("There are ".$no_g11n_config[0]->count." config entries, ".
                      $no_g11n_trans[0]->count." translations, ".
                      $no_g11n_metas[0]->count." meta data entries in system.".
                      "click clear button to clear configuration and translations.");

		?>
		<button onclick="g11ncleardata()">Clear Data</button>
		<br></div>
		<?php
	
        }

	public function tmy_get_post_translation_status() {
            echo tmy_g11n_html_kses_esc($this->_get_tmy_g11n_metabox(intval($_POST['id']), $_POST['post_type']));

            echo "  ";
        }

	public function tmy_create_sync_translation() {

            if ( WP_TMY_G11N_DEBUG ) {
                error_log("In tmy_create_sync_translation,id:".intval($_POST['id']).",type:".sanitize_text_field($_POST['post_type']));
            }
            $post_id = intval($_POST['id']);
            $post_type = sanitize_text_field($_POST['post_type']);

	    $response = json_decode($this->_tmy_create_sync_translation($post_id, $post_type));

	    echo json_encode(array("message" => esc_html($response->message),
                                   "div_status" => tmy_g11n_html_kses_esc($response->div_status))
                            );
	    wp_die();

        }

	public function _tmy_create_sync_translation($post_id, $post_type) {

            $message = "Number of translation entries created for " . $post_id . ": ";

            $all_langs = get_option('g11n_additional_lang', array());
            $default_lang = get_option('g11n_default_lang');
            unset($all_langs[$default_lang]);

             if (strcmp($post_type, "g11n_translation") !== 0) {

                 //if (strcmp($post_type, "product") !== 0) {
                    
                     // creating language translations for each language
                     $num_success_entries = 0;
                     $num_langs = 0;
                     $qualified_taxonomies = get_taxonomies(array("public" => true, "show_ui"=> true), "names", "or");
                     if (is_array($all_langs)) {
                         $num_langs = count($all_langs);
                         foreach( $all_langs as $value => $code) {
                             $new_translation_id = $this->translator->get_translation_id($post_id,$code,$post_type);
                             if ( WP_TMY_G11N_DEBUG ) { 
                                 error_log("In tmy_create_sync_translation, translation_id = " . esc_attr($new_translation_id));
                             }
                             if (! isset($new_translation_id)) {
                                 $num_success_entries += 1;
                                 //$message .= " $value($code)";
                                 //error_log("in create_sync_translation, no translation_id");
             
                                 if (array_key_exists($post_type, $qualified_taxonomies)) {
                                 //if (strcmp($post_type, "taxonomy") === 0) {
                                     $term_name = get_term_field('name', $post_id);
                                     $g11n_translation_post = array(
                                           'post_title'    => $term_name,
                                           'post_content'  => "",
                                           'post_type'  => "g11n_translation"
                                     );
                                 } else {
                                     $translation_title = get_post_field('post_title', $post_id);
                                     $translation_contents = get_post_field('post_content', $post_id);
                                     $translation_excerpt = get_post_field('post_excerpt', $post_id);
                                     $g11n_translation_post = array(
                                           'post_title'    => $translation_title,
                                           'post_content'  => $translation_contents,
                                           'post_excerpt'  => $translation_excerpt,
                                           'post_type'  => "g11n_translation"
                                     );
                                 }
                                 $new_translation_id = wp_insert_post( $g11n_translation_post );
                                 add_post_meta( $new_translation_id, 'orig_post_id', $post_id, true );
                                 add_post_meta( $new_translation_id, 'g11n_tmy_lang', $code, true );
                                 add_post_meta( $new_translation_id, 'g11n_tmy_orig_type', $post_type, true );
                             }
                             $this->_update_g11n_translation_status($new_translation_id);
                             if ( WP_TMY_G11N_DEBUG ) { 
                                 error_log("In tmy_create_sync_translation, new_translation_id = " . esc_attr($new_translation_id));
                             }
                         }
                     }
                     $message .= $num_success_entries." (no. of languages configured: ". $num_langs.").";

                     // creating language translations for each language

                     // push to translation if all setup
                     if ((strcmp('', get_option('g11n_server_user','')) !== 0) && 
                         (strcmp('', get_option('g11n_server_token','')) !== 0) &&
                         (strcmp('', get_option('g11n_server_url','')) !== 0)) {

                         if (array_key_exists($post_type, $qualified_taxonomies)) {
                        // if (strcmp($post_type, "taxonomy") === 0) {
                             $content_title = get_term_field('name', $post_id);
                             $content_excerpt = "";
                             $tmp_array = array();
                             $contents_array = array();
                             $json_file_name = "WordpressG11nAret-" . $post_type . "-" . $post_id;
                             array_push($contents_array, $content_title);
                         } else {
                             $content_title = get_post_field('post_title', $post_id);
                             $post_excertpt = get_post_field('post_excerpt', $post_id);
                             if ($post_excertpt === "") {
                                 $content_excerpt = get_post_field('post_content', $post_id);
                             } else {
                                 $content_excerpt = get_post_field('post_content', $post_id) . "\n" . $post_excertpt;
                             }

                             //$tmp_array = preg_split('/(\n)/', get_post_field('post_content', $post_id),-1, PREG_SPLIT_DELIM_CAPTURE);
                             $tmp_array = preg_split('/(\n)/', $content_excerpt, -1, PREG_SPLIT_DELIM_CAPTURE);
                             $contents_array = array();

                            if (strcmp(get_post_field('post_title', $post_id),'blogname') === 0){
                                $json_file_name = "WordpressG11nAret-" . "blogname" . "-" . $post_id;
                            } elseif (strcmp(get_post_field('post_title', $post_id),'blogdescription') === 0){
                                $json_file_name = "WordpressG11nAret-" . "blogdescription" . "-" . $post_id;
                            } else {
                                $json_file_name = "WordpressG11nAret-" . $post_type . "-" . $post_id;
                                array_push($contents_array, $content_title);
                            }
                         }

                         $paragraph = "";
                         foreach ($tmp_array as $line) {
                            $paragraph .= $line;
                            if (strlen($paragraph) > get_option('g11n_server_trunksize',900)) {
                                array_push($contents_array, $paragraph);
                                $paragraph = "";
                            }
                         }
                         if (strlen($paragraph)>0) array_push($contents_array, $paragraph);
                         //error_log("MYSQL" . var_export($contents_array,true));
                         $push_return_msg = $this->translator->push_contents_to_translation_server($json_file_name, $contents_array);
                         $message .= " " . $json_file_name . " is pushed to Translation Server: ".$push_return_msg;
                         if ( WP_TMY_G11N_DEBUG ) {
                              error_log("In tmy_create_sync_translation,filename:".esc_attr($json_file_name));
                         }
                     } else {
                        $message .= " No translation server setup.";
                     }
                     // push to translation if all setup
                 //}  // post_type check
             }

             $return_msg = json_encode(array("message" => esc_attr($message),
                                             "div_status" => $this->_get_tmy_g11n_metabox($post_id, $post_type)
                                      ));
             //echo esc_attr($message);
             return $return_msg . "  ";

        }

	public function _tmy_g11n_create_server_project($project_name,$version_num,$lang_list) {

                if ( WP_TMY_G11N_DEBUG ) {
                    error_log("In _tmy_g11n_create_server_project :". esc_attr($project_name) . " " . esc_attr($version_num) . " " . esc_attr(json_encode($lang_list)));
                }

                //update_option("g11n_server_project", esc_attr($project_name));
                //update_option("g11n_server_version", esc_attr($version_num));

	        $ch = curl_init();

                $ret_msg = "";
                //
                // create the project 
                //
		$rest_url = rtrim(get_option('g11n_server_url'),"/") . "/rest/projects/p/" . $project_name;
		$payload = json_encode(array(
			     "id" => $project_name,
			     "defaultType" => "Gettext",
			     "name" => $project_name,
			     "description" => "Introduction"
			    ));

 	        curl_reset($ch);

                if ( WP_TMY_G11N_DEBUG ) {
	            error_log("REST URL" . esc_attr($rest_url));
                }

                $args = array(
                    'headers' => array('X-Auth-User' => get_option('g11n_server_user'),
                                       'X-Auth-Token' => get_option('g11n_server_token'),
                                       'Content-Type' => 'application/json'),
                    'method' => 'PUT',
                    'body' => $payload,
                    'timeout' => 10
                );
                $response = wp_remote_post( $rest_url, $args );

                if ( is_array( $response ) && ! is_wp_error( $response ) ) {
                    $output = $response['body'];
                    $payload = json_decode($output);
                } else {
                    if ( WP_TMY_G11N_DEBUG ) {
                        error_log("In _tmy_g11n_create_server_project, Error: " . esc_attr($response->get_error_message()));
                    }
                }
	        $http_code = wp_remote_retrieve_response_code( $response );
	        $http_code_msg = array(
		    200 => 'Already exists, read for use',
		    201 => 'Created',
		    401 => 'Authentication error',
		    403 => 'Operation forbidden',
		    500 => 'Internal server error');

                $ret_msg .= $project_name . ": " . $http_code_msg[$http_code] . " (" . $http_code . ")";

                //
                // create the version 
                //
	        $rest_url = rtrim(get_option('g11n_server_url'),"/") . "/rest/project/" . $project_name . "/version/" . $version_num;
		$payload = json_encode(array(
			     "id" => $version_num,
			     "defaultType" => "Gettext",
			     "status" => "ACTIVE",
			     "privateProject" => 0
			    ));

                /*******************************************************************/
                $args = array(
                    'headers' => array('X-Auth-User' => get_option('g11n_server_user'),
                                       'X-Auth-Token' => get_option('g11n_server_token'),
                                       'Content-Type' => 'application/json'),
                    'method' => 'PUT',
                    'body' => $payload,
                    'timeout' => 10
                );
                $response = wp_remote_post( $rest_url, $args );

                if ( is_array( $response ) && ! is_wp_error( $response ) ) {
                    $output = $response['body'];
                    $payload = json_decode($output);
                } else {
                    if ( WP_TMY_G11N_DEBUG ) {
                        error_log("In _tmy_g11n_create_server_project, Error: " . esc_attr($response->get_error_message()));
                    }
                }
	        $http_code = wp_remote_retrieve_response_code( $response );

                $ret_msg .= "; " . $version_num . ": " . $http_code_msg[$http_code] . " (" . $http_code . ")";

                //
                // add language setting to the version 
                //

		curl_reset($ch);
		//error_log("create project optional lang list: " . json_encode($lang_list));

		$rest_url = "https://tmysoft.com/api/project/" . $project_name . "/version/" . $version_num . "/locales";

                /*******************************************************************/
                $args = array(
                    'headers' => array('X-Auth-User' => get_option('g11n_server_user'),
                                       'X-Auth-Token' => get_option('g11n_server_token'),
                                       'Content-Type' => 'application/json'),
                    'method' => 'PUT',
                    'body' => json_encode(array("data" => $lang_list)),
                    'timeout' => 10
                );
                $response = wp_remote_post( $rest_url, $args );

                if ( is_array( $response ) && ! is_wp_error( $response ) ) {
                    $output = $response['body'];
                    $payload = json_decode($output);
                } else {
                    if ( WP_TMY_G11N_DEBUG ) {
                        error_log("In _tmy_g11n_create_server_project, Error: " . esc_attr($response->get_error_message()));
                    }
                }
	        $http_code = wp_remote_retrieve_response_code( $response );

                if ($http_code === 200) {
                    $ret_msg .= "; Language Setting Updated";
                } else {
                    $ret_msg .= "; Language Setting Is Not Updated";
                }
	        curl_close($ch);
                return $ret_msg;

        }
	public function tmy_g11n_create_server_project() {

	    if (strcmp(sanitize_text_field($_POST['action_type']),"project")!==0) {
                echo "Wrong Action";
 	        wp_die();
            }

	    if ((strcmp(rtrim(sanitize_text_field($_POST['proj_name'])),"")===0) || (strcmp(rtrim(sanitize_text_field($_POST['proj_ver'])),"")===0)) {
                echo "Please enter both project name and version";
            } else {

                $default_language = get_option('g11n_default_lang');
                $language_options = get_option('g11n_additional_lang', array());
                unset($language_options[$default_language]);
                $selected_langs = array_values($language_options);
                foreach ($selected_langs as &$value) {
                        $value = str_replace("_", "-", $value);
                }
		//error_log("create project optional lang list: " . json_encode($selected_langs));

	        $return_msg = $this->_tmy_g11n_create_server_project(rtrim(sanitize_text_field($_POST['proj_name'])),
                                                       rtrim(sanitize_text_field($_POST['proj_ver'])),
                                                       $selected_langs);
                echo esc_attr($return_msg);
            }

 	    wp_die();

	}

	public function tmy_g11n_pull_translation($id, $locale) {

        
            // will probable need to remove this function
            /* Pulling translation in */
	    $rest_url = rtrim(get_option('g11n_server_url'),"/") . "/rest" . "/projects/p/" . 
	                    get_option('g11n_server_project') . "/iterations/i/" . 
	                    get_option('g11n_server_version') . "/r/" . 
	                    $id . "/translations/" . $locale . "?ext=gettext&ext=comment";
        
        
	    $server_reply = $this->translator->rest_get_translation_server($rest_url);
	    $translation_payload = $server_reply["payload"];
        
	                //$debug_log .= $server_reply["server_msg"];
        
	    if (isset($translation_payload->textFlowTargets[0]->content)) {
	        $translation_title = $translation_payload->textFlowTargets[0]->content;
	        //error_log("SYNC TRANSLATION TITLE = " . $translation_title);
	    } 
       
            $payload_size = count($translation_payload->textFlowTargets);
            $translation_contents = '';
            for ($i = 1; $i < $payload_size; $i++) {
                $translation_contents .= $translation_payload->textFlowTargets[$i]->content ;
            }
        
		               //if (isset($translation_payload->textFlowTargets[1]->content)) {
		               //     $translation_contents = $translation_payload->textFlowTargets[1]->content;
		               //     error_log("SYNC TRANSLATION CONTENTS = " . $translation_contents);
		               //} 
    
		               //$row->id is in the format of "Wordpress-post-23"
            $g11n_res_filename = preg_split("/-/", $id);
    
	    /* change the locale - to _ */
	    $locale = str_replace("-", "_", $locale);
   
            $default_lang_post_id = $g11n_res_filename[2];
            $payload_post_type = $g11n_res_filename[1];

                 //$debug_log .= " Finished translation,id=".$default_lang_post_id."post type=" . $payload_post_type;


            $translation_id = $this->translator->get_translation_id($default_lang_post_id,$locale,$payload_post_type);

            $all_post_types = tmy_g11n_available_post_types();

            if (array_key_exists($payload_post_type, $all_post_types)) {
            //if ((strcmp($payload_post_type,"post") === 0)||(strcmp($payload_post_type,"page") === 0)) {
                     if (isset($translation_id)) {
                        //$debug_log .= " Found local item,id=".$translation_id." ".$payload_post_type;
                         $g11n_translation_post = array(
                           'ID'    => $translation_id,
                           'post_title'    => $translation_title,
                           'post_content'  => $translation_contents,
                           'post_type'  => "g11n_translation"
                         );
                         $update_post_id = wp_update_post( $g11n_translation_post );
                         //$debug_log .= " updated id=".$update_post_id;
                     } else {
                         $g11n_translation_post = array(
                           'post_title'    => $translation_title,
                           'post_content'  => $translation_contents,
                           'post_type'  => "g11n_translation"
                         );
                         $new_translation_id = wp_insert_post( $g11n_translation_post );
                         //$debug_log .= " created trans post id=".$new_translation_id;
                         add_post_meta( $new_translation_id, 'orig_post_id', $default_lang_post_id, true );
                         add_post_meta( $new_translation_id, 'g11n_tmy_lang', $locale, true );
                     }
                     $this->_update_g11n_translation_status($new_translation_id);
            }

            if ((strcmp($payload_post_type,"blogname") === 0)) {
                     if (isset($translation_id)) {
                      //error_log("TRANSLATION LANG ID = " .  $translation_id);
                      $g11n_translation_post = array(
                        'ID'    => $translation_id,
                        'post_title'    => "Blog Name Translation",
                        'post_content'  => $translation_title,
                        'post_type'  => "g11n_translation"
                      );
                      $update_post_id = wp_update_post( $g11n_translation_post );
                      //$debug_log .= " found trans post id=".$update_post_id." ".$payload_post_type;
                     } else {
                      //error_log("TRANSLATION LANG ID = empty ");
                      $g11n_translation_post = array(
                        'post_title'    => "Blog Name Translation",
                        'post_content'  => $translation_title,
                        'post_type'  => "g11n_translation"
                      );
                      $new_translation_id = wp_insert_post( $g11n_translation_post );
                      //$debug_log .= " new trans post id=".$new_translation_id." ".$payload_post_type;
                      add_post_meta( $new_translation_id, 'option_name', "blogname", true );
                      add_post_meta( $new_translation_id, 'g11n_tmy_lang', $locale, true );
                     }
                     $this->_update_g11n_translation_status($new_translation_id);
            }

            if ((strcmp($payload_post_type,"blogdescription") === 0)) {
                     if (isset($translation_id)) {
                         //error_log("TRANSLATION LANG ID = " .  $translation_id);
                         $g11n_translation_post = array(
                           'ID'    => $translation_id,
                           'post_title'    => "Blog Tag Translation",
                           'post_content'  => $translation_title,
                           'post_type'  => "g11n_translation"
                         );
                         $update_post_id = wp_update_post( $g11n_translation_post );
                         //$debug_log .= " found trans post id=".$update_post_id." ".$payload_post_type;
                     } else {
                         //error_log("TRANSLATION LANG ID = empty ");
                         $g11n_translation_post = array(
                           'post_title'    => "Blog Tag Translation",
                           'post_content'  => $translation_title,
                           'post_type'  => "g11n_translation"
                         );
                         $new_translation_id = wp_insert_post( $g11n_translation_post );
                         //$debug_log .= " new trans post id=".$new_translation_id." ".$payload_post_type;
                         add_post_meta( $new_translation_id, 'option_name', "blogdescription", true );
                         add_post_meta( $new_translation_id, 'g11n_tmy_lang', $locale, true );
                     }
                     $this->_update_g11n_translation_status($new_translation_id);
            }

	}

	public function tmy_g11n_get_local_translation_status() {

            global $wpdb;
            $rows = $wpdb->get_results( 'SELECT ID, post_title, meta_value, post_modified  FROM '.
                                                  $wpdb->prefix.'posts,'.
                                                  $wpdb->prefix.'postmeta where post_type="g11n_translation" and '.
                                                  $wpdb->prefix.'posts.post_status != "trash" and '.
                                                  $wpdb->prefix.'posts.ID='.
                                                  $wpdb->prefix.'postmeta.post_id and '.
                                                  $wpdb->prefix.'postmeta.meta_key="g11n_tmy_lang" ORDER BY ID');

            $ret_msg = "<table>";
            $ret_msg .=  '<tr><td><b>Orig. ID</b></td>' .
                                        '<td><b>Trans. ID</b></td>' .
                                        '<td><b>Title</b></td>' .
                                        '<td><b>Lang</b></td>' .
                                        '<td><b>Last Modified</b></td></tr>';

            $row_arr = array();
            foreach ( $rows as $row ) {
                        $post_info = $this->translator->get_translation_info($row->ID);
                        if (isset($post_info[0])) {
                            $row_arr[] = array("TID" => $post_info[0]->ID,
                                               "ID" => $row->ID,
                                               "post_title" => $row->post_title,
                                               "meta_value" => $row->meta_value,
                                               "post_modified" => $row->post_modified);
                        }
            }
            sort($row_arr);
            //$return_msg .= json_encode($row_arr);
            $ret_msg .= "<br>";
            if ( WP_TMY_G11N_DEBUG ) {
                error_log("In tmy_g11n_get_local_translation_status, rows:".esc_attr(json_encode($row_arr)));
            }
            foreach ( $row_arr as $row ) {
                //if (isset($post_info[0])) {
                $ret_msg .=  '<tr><td><a href="' . esc_url(get_edit_post_link(esc_attr($row["TID"]))) . '" target="_blank">'.esc_attr($row["TID"]) . '</td><td>' .
                                     '<a href="' . esc_url(get_edit_post_link(esc_attr($row["ID"]))) . '" target="_blank">'.esc_attr($row["ID"]) . '</td><td>' .
                                                         esc_attr($row["post_title"]) . '</td><td>' .
                                                         esc_attr($row["meta_value"]) . '</td><td>' .
                                                         esc_attr($row["post_modified"]) . '</td></tr>';
                //}
            }
            $ret_msg .= "<tr><td>". time() ."</td></tr>";
            $ret_msg .= "</table>  ";
            //echo $ret_msg;
            echo tmy_g11n_html_kses_esc($ret_msg);
            if ( WP_TMY_G11N_DEBUG ) {
                error_log("In tmy_g11n_get_local_translation_status, ret:".esc_attr(json_encode($ret_msg)));
            }

        }

	public function tmy_g11n_get_refresh_table_status() {

        }

	public function tmy_g11n_get_project_status() {

                $http_erro_code_msg = array(
                        200 => 'OK',
                        401 => 'Authentication error, check user and token',
                        404 => 'No project or version on translation server',
                        500 => 'Internal server error'
                        );

                $return_msg = '';
		//$return_msg .= "<br>Translation Server URL: ". esc_attr(get_option('g11n_server_url')) . ' ';

      	        $rest_url = rtrim(get_option('g11n_server_url'),"/") .  "/rest/version";
    		$server_reply = $this->translator->rest_get_translation_server($rest_url);

    	        if ($server_reply["http_code"] == 200) {
                    //$return_msg .= "Sever Version: " . esc_attr($server_reply["payload"]->versionNo) . "<br>";
                    //$return_msg .= "Project Name: <b>" . esc_attr(get_option('g11n_server_project')). "</b> ";
                    //$return_msg .= "Project Version: <b>" . esc_attr(get_option('g11n_server_version')) . "</b><br>";
                    $return_msg .= "<br>Translations Hosted on the Server: <br>";

                    $translation_server_status = True;
                    $rest_url = rtrim(get_option('g11n_server_url'),"/") .  "/rest/stats/proj/" .
                                get_option('g11n_server_project') .  "/iter/" .
                                get_option('g11n_server_version') .  "?detail=true&word=false";
                    $server_reply = $this->translator->rest_get_translation_server($rest_url);
                    $payload = $server_reply["payload"];

                    $return_msg .= "Progress Overview (Translated/Total): <br>";

                    if (! is_null($payload)){
                        if (is_array($payload->stats)){
                            foreach ( $payload->stats as $row ) {
                                $return_msg .= esc_attr($row->locale) . ": ". esc_attr($row->translated) . "/" . esc_attr($row->total) . " ";
                            }
                            $return_msg .=  "<br>";
                        }
		        if (is_array($payload->detailedStats)) {
                            $return_msg .= "<br>Document List(s):";
                            $return_msg .= "<br>Fully translated will be in <b>bold</b> and and pulling down to local database.<br><br>";
                            $return_msg .= "<table>";
                            //$return_msg .= var_export($payload,true);
                            //$return_msg .= json_encode($payload);
                            foreach ( $payload->detailedStats as $row ) {
                                if (is_array($row->stats)) {
                                    $doc_lang_str = "";
		                    //$row->id is in the format of "Wordpress-post-23"
		                    $g11n_res_filename = preg_split("/-/", $row->id);
    
		                    $default_lang_post_id = $g11n_res_filename[2];
		                    $payload_post_type = $g11n_res_filename[1];
    
                                    $default_post_title =  get_the_title($default_lang_post_id);
                                    $default_post_title =  substr($default_post_title, 0 , 30);
                                    
                                    if ( strcmp($g11n_res_filename[0], "WordpressG11nAret") !== 0 ) {
                                        $return_msg .= "<tr><td><b>" . esc_attr($row->id) ."</b></td><td>" . $default_post_title . "</td><td colspan=\"".esc_attr(count($row->stats)).
                                                       "\"> Skipping </td></tr>";

                                    } else {

                                        foreach ( $row->stats as $stat_row ) {
                                             if  ($stat_row->translated == $stat_row->total) {

                                                 //begin fully translated, need to pull the translation down to local WP database

		                                 /* Pulling translation in */
		                                 $rest_url = rtrim(get_option('g11n_server_url'),"/") . "/rest" . "/projects/p/" . 
		                                       get_option('g11n_server_project') . "/iterations/i/" . 
		                                       get_option('g11n_server_version') . "/r/" . 
		                                       $row->id . "/translations/" . $stat_row->locale . "?ext=gettext&ext=comment";
        
        
		                                 $server_reply = $this->translator->rest_get_translation_server($rest_url);
		                                 $translation_payload = $server_reply["payload"];
        
		                                   //$debug_log .= $server_reply["server_msg"];
            
		                                 if (isset($translation_payload->textFlowTargets[0]->content)) {
		                                      $translation_title = $translation_payload->textFlowTargets[0]->content;
		                                        //error_log("SYNC TRANSLATION TITLE = " . $translation_title);
		                                 } 
        
		                                 $payload_size = count($translation_payload->textFlowTargets);
		                                 $translation_contents = '';
		                                 for ($i = 1; $i < $payload_size; $i++) {
		                                     $translation_contents .= $translation_payload->textFlowTargets[$i]->content ;
		                                 }
        
		                                   //if (isset($translation_payload->textFlowTargets[1]->content)) {
		                                   //     $translation_contents = $translation_payload->textFlowTargets[1]->content;
		                                   //     error_log("SYNC TRANSLATION CONTENTS = " . $translation_contents);
		                                   //} 
    
    
		                                 /* change the locale - to _ */
		                                 $stat_row->locale = str_replace("-", "_", $stat_row->locale);

		                                 $translation_id = $this->translator->get_translation_id($default_lang_post_id,$stat_row->locale,$payload_post_type);
                                                 if ( WP_TMY_G11N_DEBUG ) {
                                                     error_log("In tmy_g11n_get_project_status, id:".esc_attr($default_lang_post_id)." locale:".
                                                                                         esc_attr($stat_row->locale)." type: ".esc_attr($payload_post_type));
                                                     error_log("In tmy_g11n_get_project_status, translation id:".esc_attr($translation_id));
                                                 }
                                                 if (strcmp($payload_post_type,'blogname') === 0){
		                                     $translation_contents = $translation_title . $translation_contents;
		                                     $translation_title = "blogname";
                                                 } elseif (strcmp($payload_post_type,'blogdescription') === 0){
		                                     $translation_contents = $translation_title . $translation_contents;
		                                     $translation_title = "blogdescription";
                                                 } 

                                                 // this language on server is fully translated, and there is a local translation
                                                 // started, and id is set and found, then update the translation. If no local translation
                                                 // id, means out of sync, will show no id
                                                

		                                 if (isset($translation_id)) {
		                                     $update_post_id = wp_update_post(array(
		                                                        'ID'    => $translation_id,
		                                                        'post_title'    => $translation_title,
		                                                        'post_content'  => $translation_contents,
		                                                        'post_type'  => "g11n_translation"));
                                                     $doc_lang_str .= "<td><b>" . esc_attr($stat_row->locale) . ": ". 
                                                       esc_attr($stat_row->translated) . "/" . esc_attr($stat_row->total) . "(ID:".
                                                       '<a href="' . esc_url(get_edit_post_link(esc_attr($translation_id))) . 
                                                       '" target="_blank">'.esc_attr($translation_id) . '</a>' .
                                                       ")</b></td> ";
		                                 } else {
		                                     //$translation_id = wp_insert_post(array(
		                                     //                   'post_title'    => $translation_title,
		                                     //                   'post_content'  => $translation_contents,
		                                     //                   'post_type'  => "g11n_translation"));
		                                     //add_post_meta( $translation_id, 'orig_post_id', $default_lang_post_id, true );
		                                     //add_post_meta( $translation_id, 'g11n_tmy_lang', $stat_row->locale, true );
                                                     $doc_lang_str .= "<td><b>" . esc_attr($stat_row->locale) . ": ". 
                                                       esc_attr($stat_row->translated) . "/" . esc_attr($stat_row->total) . 
                                                       "(No Local ID)</b></td> ";
		                                 }
                                                 $this->_update_g11n_translation_status($translation_id);

	                                         //tmy_g11n_pull_translation($stat_row->id, $stat_row->locale);
                                                 //finish fully translated, need to pull the translation down to local WP database


                                             } else {
		                                 $stat_row->locale = str_replace("-", "_", $stat_row->locale);
                                                 $doc_lang_str .= "<td>" . esc_attr($stat_row->locale) . ": ". 
                                                       esc_attr($stat_row->translated) . "/" . esc_attr($stat_row->total) . "</td> ";
                                             }
                                        }//foreach language

                                        $return_msg .= "<tr><td><b>" . esc_attr($row->id) ."</b></td><td>" .$default_post_title . "</td>". tmy_g11n_html_kses_esc($doc_lang_str) . "</tr>";
                                    }

                                }// if (is_array($row->stats))
                            }//for each document row
                            echo "</table>";
                        }// if (is_array($payload->detailedStats))
                    } // if (! is_null($payload))
                } else {
                    $return_msg .= "Sever is not reachable: <br>";
                    $return_msg .= esc_attr(var_export($server_reply,true));
                }

                echo tmy_g11n_html_kses_esc($return_msg);
		wp_die();

        }
	public function tmy_g11n_clear_plugin_data() {

		global $wpdb; 
		$whatever = sanitize_text_field( $_POST['whatever'] );
		if ($whatever == 8888) {

		    $no_g11n_config = $wpdb->get_results( 'delete from '.$wpdb->prefix.'options where option_name like "g11n%"');
		    $no_g11n_trans = $wpdb->get_results( 'delete from '.$wpdb->prefix.'posts where post_type="g11n_translation" or post_title="blogname" or post_title="blogdescription"');
		    $no_g11n_metas = $wpdb->get_results( 'delete from '.$wpdb->prefix.'postmeta where meta_key="g11n_tmy_lang" or 
                                                                                                      meta_key="g11n_tmy_lang_status" or 
                                                                                                      meta_key="g11n_tmy_orig_type" or 
                                                                                                      meta_key="orig_post_id" or 
                                                                                                      meta_key="translation_push_status"');
		    //error_log(var_export($no_g11n_config,true));
		    //error_log(var_export($no_g11n_trans,true));
		    //error_log(var_export($no_g11n_metas,true));
		    echo "Cleared Data";
		    wp_die();

		}

		echo "Wrong Action";
		wp_die();

        }

	public function tmy_plugin_option_update($value, $option, $old_value) {

            if ( WP_TMY_G11N_DEBUG ) {
                error_log("tmy_plugin_option_update:" . esc_attr(json_encode($option)) );
            } 

            if (((strcmp($option, "g11n_additional_lang")===0) && ($value != $old_value)) ||
                ((strcmp($option, "g11n_server_project")===0) && ($value != $old_value)) ||
                ((strcmp($option, "g11n_server_version")===0) && ($value != $old_value))) {
 
                if (strcmp($option, "g11n_server_project")===0) {
                    $project_name = $value;
                } else {
                    $project_name = get_option('g11n_server_project');
                }
                if (strcmp($option, "g11n_server_version")===0) {
                    $version_num = $value;
                } else {
                    $version_num = get_option('g11n_server_version');
                }
                if (strcmp($option, "g11n_additional_lang")===0) {
                    $lang_list = $value;
                } else {
                    $lang_list = get_option('g11n_additional_lang');
                }

                if ( WP_TMY_G11N_DEBUG ) {
                    error_log("tmy_plugin_option_update: additional_lang changed");
                }
                $default_language = get_option('g11n_default_lang');
                $language_options = $lang_list;
                unset($language_options[$default_language]);
                $selected_langs = array_values($language_options);
                foreach ($selected_langs as &$lang) {
                        $lang = str_replace("_", "-", $lang);
                }

	        $ret_msg = $this->_tmy_g11n_create_server_project(rtrim($project_name),
                                                       rtrim($version_num),
                                                       $selected_langs);

                if ( WP_TMY_G11N_DEBUG ) {
                    error_log("tmy_plugin_option_update: " . esc_attr($ret_msg));
                }

            }
            //if ((strcmp($option, "g11n_l10n_props_blogname")===0) && ($value != $old_value)) {
            if ((strcmp($option, "g11n_l10n_props_blogname")===0)) {
                if (strcmp($value,"Yes")==0){
                    // creating placeholder of the blogname entry as private post type
                    $title_post  = get_page_by_title('blogname',OBJECT,'post');

                    if (! is_null($title_post) && (strcmp($title_post->post_status,'trash')!==0)) {
                        $new_post_id = wp_insert_post(array('ID' => $title_post->ID, 
                                                            'post_title'    => 'blogname',
                                                            'post_content'  => get_bloginfo('name'),
                                                            'post_status'  => 'private',
                                                            'post_type'  => "post"));
                    } else {
                        $new_post_id = wp_insert_post(array('post_title'    => 'blogname',
                                                            'post_content'  => get_bloginfo('name'),
                                                            'post_status'  => 'private',
                                                            'post_type'  => "post"));
                    }
                    if ( WP_TMY_G11N_DEBUG ) {
                        error_log("tmy_plugin_option_update_blogname, blogname post ID: :" . esc_attr($new_post_id) );
                    } 
                }


            }
            //if ((strcmp($option, "g11n_l10n_props_blogdescription")===0) && ($value != $old_value)) {
            if ((strcmp($option, "g11n_l10n_props_blogdescription")===0)) {
                if ( WP_TMY_G11N_DEBUG ) {
                    error_log("tmy_plugin_option_update description: " . esc_attr($old_value). "->" . esc_attr($value) );
                }
                if (strcmp($value,"Yes")==0){
                    // creating placeholder entry as private post type
                    $title_post  = get_page_by_title('blogdescription',OBJECT,'post');

                    if (! is_null($title_post) && (strcmp($title_post->post_status,'trash')!==0)) {
                        $new_post_id = wp_insert_post(array('ID' => $title_post->ID,
                                                            'post_title'    => 'blogdescription',
                                                            'post_content'  => get_bloginfo('description'),
                                                            'post_status'  => 'private',
                                                            'post_type'  => "post"));
                    } else {
                        $new_post_id = wp_insert_post(array('post_title'    => 'blogdescription',
                                                            'post_content'  => get_bloginfo('description'),
                                                            'post_status'  => 'private',
                                                            'post_type'  => "post"));
                    }
                    if ( WP_TMY_G11N_DEBUG ) {
                        error_log("tmy_plugin_option_update_description, blog description post ID: :" . esc_attr($new_post_id) );
                    }
                }
            }

            return $value;

        }

        public function tmy_plugin_option_update_after($old, $new, $option) {

            if ( WP_TMY_G11N_DEBUG ) {
                error_log("In tmy_plugin_option_update_after");
            }

            $all_post_type_options = tmy_g11n_available_post_type_options();

            if ((array_key_exists($option, $all_post_type_options))  && ($new != $old)) {

            //if (((strcmp($option, "g11n_l10n_props_blogname")===0) && ($new != $old)) ||
            //    ((strcmp($option, "g11n_l10n_props_blogdescription")===0) && ($new != $old)) ||
            //    ((strcmp($option, "g11n_l10n_props_pages")===0) && ($new != $old)) ||
            //    ((strcmp($option, "g11n_l10n_props_posts")===0) && ($new != $old))) {

                $selected_posts = new WP_Query(array(
                                               'post_type' => 'g11n_translation',
                                               'nopaging' => true 
                                                   ));
                if ( $selected_posts->have_posts() ) {
                    while ( $selected_posts->have_posts() ) {
                        $selected_posts->the_post();
		        $this->_update_g11n_translation_status($selected_posts->post->ID);
                    }
                }
                wp_reset_postdata();
            }

        }


	function g11n_edit_posts_views( $views ) {
            if ( WP_TMY_G11N_DEBUG ) {
    		foreach ( $views as $index => $view ) {
        		//$views[ $index ] = preg_replace( '/ <span class="count">\([0-9]+\)<\/span>/', '', $view );
        		//$views[ $index ] = "AAAAA";
		        //error_log("edit_posts_views = " . $views[ $index ]);
    		}
        	array_push($views, "Sent for Translation (XX)");
        	array_push($views, "Translation Completed (XX)");
           }
    	   return $views;
        }

        function tmy_plugin_page_set_columns( $columns ){

            $columns['translation_started']     = 'Translation Started';
            return $columns;
        }

        function tmy_plugin_post_set_columns( $columns, $post_type ){

            if (strcmp($post_type, "g11n_translation") !== 0) {
                //error_log("tmy_plugin_post_set_columns: " . esc_attr($post_type));
                //unset($columns['date']);
                $columns['translation_started']     = 'Translation Started';
                //$columns['date']     = 'Date';
            }
            return $columns;
        }

        function tmy_plugin_g11n_translation_set_columns( $columns ){

            unset($columns['date']);
            $columns['post_id']     = __('ID', 'tmy-globalization');
            $columns['origin_type']     = __('Type', 'tmy-globalization');
            $columns['original_id']     = __('Original Post ID', 'tmy-globalization');
            $columns['Language']     = __('Language', 'tmy-globalization');
            $columns['post_status']     = __('Translation Status', 'tmy-globalization');
            $columns['tmy_server_status']     = __('Translation Server Status', 'tmy-globalization');
            $columns['date']     = __('Date', 'tmy-globalization');
            return $columns;

        }

        function tmy_plugin_post_set_sortable( $columns ) {

            $columns['translation_started']     = 'translation_started';
            return $columns;
        }
        function tmy_plugin_g11n_translation_set_sortable( $columns ) {

            $columns['post_id']     = 'post_id';
            $columns['Language']     = 'Language';
            $columns['original_id']     = 'original_id';
            $columns['post_status']     = 'post_status';
            return $columns;
        }
        function tmy_plugin_g11n_translation_set_columns_orderby( $query ) {

            if ( ! is_admin() )
                return;

            $orderby = $query->get( 'orderby');

            if ( 'original_id' == $orderby ) {
                $query->set( 'meta_key', 'orig_post_id' );
                $query->set( 'orderby', 'meta_value_num' );
            }
            if ( 'Language' == $orderby ) {
                $query->set( 'meta_key', 'g11n_tmy_lang' );
                $query->set( 'orderby', 'meta_value' );
            }
            if ( 'post_id' == $orderby ) {
                $query->set( 'orderby', 'ID' );
            }
            if ( 'post_status' == $orderby ) {
                $query->set( 'meta_key', 'g11n_tmy_lang_status' );
                $query->set( 'orderby', 'meta_value' );
            }
        }
        function tmy_plugin_post_set_columns_value( $column, $post_id ) {
            switch ( $column ) {
                case 'translation_started'     :
                    global $wpdb;
                    $result = $wpdb->get_results("select post_id from {$wpdb->prefix}postmeta where meta_key = 'orig_post_id' and meta_value = " . $post_id);
                    if (isset($result[0]->post_id)) {
                        echo 'Yes';
                    } else {
                         echo '';
                    }

                break;
            }
        }

        function tmy_plugin_g11n_translation_set_columns_value( $column, $post_id ) {

            switch ( $column ) {
                case 'post_id'     :
                    //echo $post_id;
                    $edit_link = 'post.php?post=' . esc_attr($post_id) . '&action=edit';
                    echo '<a href="' . admin_url($edit_link) . '" target="_blank">' . esc_attr($post_id) . '</a>';

                break;
                case 'Language'     :
                    $lang_code = get_post_meta( $post_id, 'g11n_tmy_lang', true );
                    echo '<img style="display: inline; border: #00a6d3 1px outset" src="' .
                                                 plugins_url('includes/flags/24/', __DIR__) .
                                                 strtoupper($lang_code) . '.png" title="'.
                                                 esc_attr($lang_code) .'" alt="' . esc_attr($lang_code) . "\" > " . $lang_code;
                break;
                case 'original_id'     :
                    $original_id = get_post_meta($post_id, 'orig_post_id', true );
                    $edit_link = 'post.php?post=' . esc_attr($original_id) . '&action=edit';
                    echo '<a href="' . admin_url($edit_link) . '" target="_blank">' . esc_attr($original_id) . '</a>';
                break;
                case 'post_status'     :
                    //echo get_post_meta( $post_id, 'g11n_tmy_lang_status', true );
                    echo tmy_g11n_html_kses_esc($this->_update_g11n_translation_status($post_id, true));
                break;
                case 'origin_type'     :
                    echo get_post_meta( $post_id, 'g11n_tmy_orig_type', true );
                break;
                case 'tmy_server_status'     :
                    echo 'N/A';
                    //echo get_post_meta( $post_id, 'g11n_tmy_lang_status', true );
                    //echo $this->_update_g11n_translation_status($post_id, true);
                break;
            }
        }
        function tmy_plugin_g11n_edit_form_top() {
            echo "TMY Glolbalization";
        }

        function tmy_plugin_g11n_register_bulk_actions( $bulk_actions ) {
            $bulk_actions['start_sync_tranlations'] = __( 'Start or Sync Translation', 'tmy-globalization');
            return $bulk_actions;
        }

        function tmy_plugin_g11n_bulk_action_handler( $redirect_to, $doaction, $post_ids ) {
            if ( $doaction !== 'start_sync_tranlations' ) {
                return $redirect_to;
            }
            $result = '<br>';
            foreach ( $post_ids as $post_id ) {
               $post_type = get_post_type($post_id);
	       $post_result = $this->_tmy_create_sync_translation($post_id, $post_type);
	       $post_result_var = json_decode($post_result);
               $result .= $post_result_var->message ."<br>";
               //error_log($post_result);
            }
            $result .= "<br>";
            //$redirect_to = add_query_arg( 'start_sync_tranlations', count( $post_ids ), $redirect_to );
            $redirect_to = add_query_arg( 'start_sync_tranlations', $result, $redirect_to );
            return $redirect_to;
        }

        function tmy_plugin_g11n_admin_notice() {

	    if( ! empty( $_REQUEST[ 'start_sync_tranlations' ] ) ) {
	        ?>
			    <div class="updated notice is-dismissible">
				    <?php echo $_REQUEST['start_sync_tranlations'] ?>
			    </div>
	        <?php
	        //$redirect_to = remove_query_arg( 'start_sync_tranlations' );
		//return $redirect_to;
	    }
        }
        function tmy_plugin_g11n_admin_head() {

            echo '<style type="text/css">
                     .column-translation_started { text-align: left; width:100px !important; overflow:hidden }
                 </style>';
             ?>
               <style>
               .tmy_loader {
                 border: 4px solid #01a6d7;
                 border-radius: 50%;
                 display: inline-block;
                 border-top: 4px solid #F3F3F3;
                 width: 18px;
                 height: 18px;
                 -webkit-animation: spin 2s linear infinite; /* Safari */
                 animation: spin 2s linear infinite;
               }
               
               /* Safari */
               @-webkit-keyframes spin {
                 0% { -webkit-transform: rotate(0deg); }
                 100% { -webkit-transform: rotate(360deg); }
               }

               @keyframes spin {
                 0% { transform: rotate(0deg); }
                 100% { transform: rotate(360deg); }
               }
               </style>
             <?php

        }
        function tmy_admin_head_nav_menus() {

            add_meta_box( 'tmy_nav_link_box',
                          'TMY Language Switch Links',
                           array( $this, 'tmy_nav_menu_links_callback' ), 
                           'nav-menus', 'side', 'low' );

        }
        function tmy_nav_menu_links_callback() {

            $tmy_g11n_dir = dirname( __FILE__ );
   	    require_once "{$tmy_g11n_dir}/include/g11n-lang-list.php";
            ?>
            <div id="posttype-tmy-endpoints" class="posttypediv">
                <div id="tabs-panel-tmy-endpoints" class="tabs-panel tabs-panel-active">
                    <ul id="tmy-endpoints-checklist" class="categorychecklist form-no-clear">
                        <li>
                            <label class="menu-item-title">
                                 <input type="checkbox" class="menu-item-checkbox" name="menu-item[1][menu-item-object-id]" value="tmy-languages" /> Choose Language
                            </label>
                                 <input type="hidden" class="menu-item-type" name="menu-item[1][menu-item-type]" value="custom" />
                                 <input type="hidden" class="menu-item-title" name="menu-item[1][menu-item-title]" value="Choose Language" />
                                 <input type="hidden" class="menu-item-url" name="menu-item[1][menu-item-url]" 
                                     value="<?php echo site_url(); ?>/?tmy_dynamic_main=yes" />
                                 <input type="hidden" class="menu-item-classes" name="menu-item[1][menu-item-classes]" />
                        </li>

                        <?php 
                            $i = 2;
                            $all_configed_langs = get_option('g11n_additional_lang', array()); /* array format ((English -> en), ...) */
                            foreach ( $all_configed_langs as $key => $value ) :
                                $lang_native = "";
                                if (array_key_exists($value, $lang_native_script)) {
                                    $lang_native = $lang_native_script[$value];
                                }
                                $lang_path = strtolower(str_replace('_', '-', $value));

                        ?>

                        <li>
                            <label class="menu-item-title">
                                 <input type="checkbox" class="menu-item-checkbox" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-object-id]" value="tmy-<?php echo esc_attr($value); ?>" /> <?php echo esc_attr($key); echo " " . esc_attr($lang_native); ?>
                            </label>
                                 <input type="hidden" class="menu-item-type" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-type]" value="custom" />
                                 <input type="hidden" class="menu-item-title" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-title]" value="<?php echo esc_attr($key); echo " " . esc_attr($lang_native); ?>" />
                                 <input type="hidden" class="menu-item-url" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-url]" 
                                     value="<?php echo site_url(); ?>/?tmy_dynamic_url=<?php echo esc_attr($lang_path); ?>" />
                                 <input type="hidden" class="menu-item-classes" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-classes]" />
                        </li>

                        <?php
                            $i++;
                            endforeach;
                        ?>

                    </ul>
               </div>
               <p class="button-controls">
                <span class="add-to-menu">
                    <button type="submit" class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e( 'Add to menu', 'tmy-globalization' ); ?>" name="add-post-type-menu-item" id="submit-posttype-tmy-endpoints">Add to menu</button>
                <span class="spinner"></span>
                </span>
               </p>
            </div>

           <?php
                     
        }

        function tmy_woocommerce_email_locale_admin_action( $order_id ) {

            $order_lang = get_post_meta($order_id, "tmy_order_lang_code", True);

            if (strcmp($order_lang, "") !== 0) {
                switch_to_locale($order_lang);
            }

        }
        function tmy_woocommerce_email_locale_action( $order_id ) {

            $order_lang = get_post_meta($order_id, "tmy_order_lang_code", True);
            if (strcmp($order_lang, "") !== 0) {
                switch_to_locale($order_lang);
            }

        }

        function tmy_woocommerce_before_resend_order_emails( $order, $string ) {

            $order_lang = get_post_meta($order->get_id(), "tmy_order_lang_code", True);
            if (strcmp($order_lang, "") !== 0) {
                switch_to_locale($order_lang);
            }

        }

        function tmy_plugin_g11n_update_htaccess() {


            $current_seo_option = esc_attr(get_option('g11n_seo_url_enable','No'));
            if (strcmp($current_seo_option, "")===0) {
                $current_seo_option = "No";
            }

            if ($current_seo_option === "Yes") {
                $home_root = parse_url( home_url() );
                if ( isset( $home_root['path'] ) ) {
                   $home_root = trailingslashit( $home_root['path'] );
                } else {
                   $home_root = '/';
                }
                $all_langs = get_option('g11n_additional_lang', array());
                $rewrite_rules = strtolower(implode("|", $all_langs));
                $rewrite_rules = str_replace('_', '-', $rewrite_rules);
    
                $content = array(
                    '<IfModule mod_rewrite.c>',
                    'RewriteEngine On',
                    'RewriteCond %{REQUEST_FILENAME} -d',
                    'RewriteCond %{REQUEST_URI} /+[^\.]+$',
                    'RewriteRule ^(.+[^/])$ %{REQUEST_URI}/ [R=301,L]',
                    'RewriteCond %{HTTPS} off',
                    'RewriteRule ^(' . $rewrite_rules . ')/(.*) http://%{HTTP_HOST}' . esc_attr($home_root) .'$2?g11n_tmy_lang_code=$1 [QSA,P,NC]',
                    'RewriteCond %{HTTPS} on',
                    'RewriteRule ^(' . $rewrite_rules . ')/(.*) https://%{HTTP_HOST}' . esc_attr($home_root) .'$2?g11n_tmy_lang_code=$1 [QSA,P,NC]',
                    '</IfModule>'
                );
            } else {
                $content = array();
            }
            $htaccess_location = get_home_path() . '.htaccess';
            $ret = insert_with_markers_default_front( $htaccess_location, 'TMY_G11N_RULES', $content );

        }

}
