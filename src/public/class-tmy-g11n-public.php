<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    TMY_G11n
 * @subpackage TMY_G11n/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    TMY_G11n
 * @subpackage TMY_G11n/public
 * @author     Yu Shao <yu.shao.gm@gmail.com>
 */
class TMY_G11n_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $translator ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->translator = $translator;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/tmy-g11n-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/tmy-g11n-public.js', array( 'jquery' ), $this->version, false );

	}

        public function g11n_option_editor_change($use_block_editor, $post) {

                if ( WP_TMY_G11N_DEBUG ) {
                    error_log ("g11n_option_editor_change: " . esc_attr(get_option('g11n_editor_choice')));
                }

               	if ($post->post_type === "shop_order") {
                    return false;
                }

                if(strcmp(get_option('g11n_editor_choice','Yes'),'No')==0){
                    return (true);
                } else {
                    return (false);
                }
        }


	public function g11n_create_rewrite_rule() {


            //#RewriteRule ^(en-us|zh-cn|ja|el)/(.*) http://%{HTTP_HOST}/wordpress601/$2?g11n_tmy_lang_code=$1 [QSA,NC,P]

            //error_log("in g11n_create_rewrite_rule: " . json_encode($_SERVER));
            //global $wp_rewrite;

	    //add_rewrite_rule('^(zh-cn)/(.*)', '/$matches[1]', 'top');
	    //add_rewrite_rule('^(en-us|zh-cn|ja|el)/(.*)', 'index.php?g11n_tmy_lang_code=$matches[1]','top');
            //add_rewrite_rule('^shopshao/([^/]*)/?','index.php?page_id=1247&page=$matches[1]','top');

            //flush_rewrite_rules();
            //$wp_rewrite->flush_rules();

        }

        function tmy_rewrite_permalink_links( $permalink ) {

            if (! is_admin()) {

                $site_url = get_site_url();

                $all_configed_langs = get_option('g11n_additional_lang'); /* array format ((English -> en), ...) */

                $lang_code = get_locale();

                $lang_code = strtolower(str_replace('_', '-', $lang_code));

                $lang_path = explode('/', str_replace($site_url, '', $permalink))[1];
                $lang_path = str_replace('-', '_', $lang_path);

                if (! array_search(strtolower($lang_path), array_map('strtolower',$all_configed_langs))) {
                    $ret = str_replace($site_url, $site_url . '/' . esc_attr($lang_code), $permalink);
                    if ( WP_TMY_G11N_DEBUG ) {
                        error_log("rewrite url " . $permalink. "->" . $ret); 
                    }
	            return esc_url($ret);
                }
            }
            return $permalink;
        }
        function rewrite_tag_permalink_post_link( $permalink, $post, $leavename ) {

            if (! is_admin()) {
                $site_url = get_site_url();

                $all_configed_langs = get_option('g11n_additional_lang'); /* array format ((English -> en), ...) */

                $lang_code = get_locale();

                $lang_code = strtolower(str_replace('_', '-', $lang_code));
                $lang_path = explode('/', str_replace($site_url, '', $permalink))[1];
                $lang_path = str_replace('-', '_', $lang_path);
                if (! array_search(strtolower($lang_path), array_map('strtolower',$all_configed_langs))) {
                    $ret = str_replace($site_url, $site_url . '/' . esc_attr($lang_code), $permalink);
                    if ( WP_TMY_G11N_DEBUG ) {
                        error_log("rewrite url " . $permalink. "->" . $ret); 
                    }
	            return esc_url($ret);
                }
            }
            return $permalink;
        }



	public function G11nStartSession() {

            if (! is_admin()) {

                if (session_status() !== PHP_SESSION_ACTIVE) {
                      session_start();
    		}

                $all_configed_langs = get_option('g11n_additional_lang'); /* array format ((English -> en), ...) */
                $current_lang = "";
                $current_lang_code = "";
                if (isset($_SESSION['g11n_language'])) {
                    $current_lang = esc_attr($_SESSION['g11n_language']);
                } elseif (isset($_COOKIE['g11n_language'])) {
                    $current_lang = esc_attr($_COOKIE['g11n_language']);
                }
                if (strcmp($current_lang, "")===0) {
                    $current_lang = get_option('g11n_default_lang');
                }
                $current_lang_code = $all_configed_langs[$current_lang];

                $new_lang = "";
                $new_lang_code = "";

                $lang_var = filter_input(INPUT_GET, 'g11n_tmy_lang', FILTER_SANITIZE_SPECIAL_CHARS);
                $lang_var_code = filter_input(INPUT_GET, 'g11n_tmy_lang_code', FILTER_SANITIZE_SPECIAL_CHARS);

                if (!empty($lang_var_code)) {
                    $lang_var_code = esc_attr(str_replace('-', '_', $lang_var_code));
                    $lang_var_idx = array_search(strtolower($lang_var_code), array_map('strtolower',$all_configed_langs));
                    $new_lang = $lang_var_idx;
                    $new_lang_code = $all_configed_langs[$lang_var_idx];
                } elseif (!empty($lang_var)) {
                    $new_lang = tmy_g11n_lang_sanitize($lang_var);
                    $new_lang_code = $all_configed_langs[$new_lang];
                }

                if ( WP_TMY_G11N_DEBUG ) {
        	    error_log("In G11nStartSession current_lang " . $current_lang . " " . $current_lang_code);
        	    error_log("In G11nStartSession new_lang " . $new_lang . " " . $new_lang_code);
                }

                $template_name = get_template();
                $active_plugins = get_option('active_plugins'); 

                if (strcmp($new_lang, "")===0) {

                    $_SESSION['g11n_language'] = $current_lang;
        	    setcookie('g11n_language', $current_lang, strtotime('+1 day'));

                    if (strcmp($current_lang_code, "en_US") === 0) {
		        unload_textdomain($template_name);
                        unload_textdomain('default');

                        foreach ($active_plugins as $key => $value) {
                            $string = explode('/',$value);
                            unload_textdomain($string[0]);
                        }
                    } else {
                        if (! is_textdomain_loaded("default")) {
                            //error_log("In G11nStartSession default domain is NOT loaded");
                            load_textdomain( "default", WP_LANG_DIR . '/' . $current_lang_code . '.mo' );
                        }

                        if (! is_textdomain_loaded($template_name)) {
                            //error_log("In G11nStartSession {$template_name} is NOT loaded");
                            load_textdomain( $template_name, WP_LANG_DIR . '/themes/'. $template_name . '-' . $current_lang_code . '.mo' );
                        }
                        foreach ($active_plugins as $key => $value) {
                            $string = explode('/',$value);
                            if (! is_textdomain_loaded($string[0])) {
                                load_textdomain($string[0], WP_LANG_DIR . '/plugins/' . $string[0]. '-' . $current_lang_code . '.mo' );
                            }
                        }
                    }

                } elseif (strcmp($new_lang_code, $current_lang_code)!==0) {

        	    error_log("In G11nStartSession  locale changed " . $current_lang_code . " " . $new_lang_code);
                    if (strcmp($new_lang_code, "en_US") === 0) {

		        unload_textdomain($template_name);
                        unload_textdomain('default');

                        foreach ($active_plugins as $key => $value) {
                            $string = explode('/',$value);
                            unload_textdomain($string[0]);
                        }

                    } else {

		        unload_textdomain($template_name);
                        unload_textdomain('default');

                        $active_plugins = get_option('active_plugins'); 
                        foreach ($active_plugins as $key => $value) {
                            $string = explode('/',$value);
                            unload_textdomain($string[0]);
                        }

                        if (strcmp($lang_var_code, "en_US") !== 0) {
                            load_textdomain( "default", WP_LANG_DIR . '/' . $new_lang_code . '.mo' );
                            load_textdomain( $template_name, WP_LANG_DIR . '/themes/'. $template_name . '-' . $new_lang_code . '.mo' );
                            foreach ($active_plugins as $key => $value) {
                                $string = explode('/',$value);
                                load_textdomain($string[0], WP_LANG_DIR . '/plugins/' . $string[0]. '-' . $new_lang_code . '.mo' );
                            }
                        }
                    }
                    $_SESSION['g11n_language'] = $new_lang;
        	    setcookie('g11n_language', $new_lang, strtotime('+1 day'));
                }
            }
	}

	public function G11nEndSession() {
    		session_destroy ();
	}

	public function g11n_setcookie() {

            if (! is_admin()) {
	    	$lang_var = tmy_g11n_lang_sanitize(filter_input(INPUT_GET, 'g11n_tmy_lang', FILTER_SANITIZE_SPECIAL_CHARS));
                if ( WP_TMY_G11N_DEBUG ) {
	    	    error_log("In g11n_setcookie , lang_var " . esc_attr($lang_var));
                }
    		if (!empty($lang_var)) {
        		setcookie('g11n_language', $lang_var, strtotime('+1 day'));
                        if ( WP_TMY_G11N_DEBUG ) {
        		     error_log("In g11n_setcookie SET COOKIE from query string - " . esc_attr($lang_var));
                        }
    		} else {
        		setcookie('g11n_language', get_option('g11n_default_lang'), strtotime('+1 day'));
                        if ( WP_TMY_G11N_DEBUG ) {
        		     error_log("In g11n_setcookie SET COOKIE from wp language option - " .  esc_attr(get_option('g11n_default_lang')));
                        }
    		}
            }
	}

public function g11n_add_floating_menu() {

          $allowed_html = array('span' => array('style' => array('display' => array())),
                                'a' => array('href' => array()),
                                'script' => array('src' => array(),
                                                  'type' => array()),
                                'style' => array('type' => array()),
                                'div' => array('id' => array()),
                                'img' => array('style' => array(),
                                               'src' => array(),
                                               'title'=> array(),
                                               'alt' => array()
                                              )
                               );
           if(strcmp(get_option('g11n_switcher_floating'),"Yes")==0){
               echo '<div id="tmyfloatmenu" style="position:fixed;z-index:10001;bottom:5rem;left:3rem;"> <div style="border:1px solid;border-radius:2px;background-color:#d7dbdd;color:#21618c;z-index:10000;box-shadow: 0 0 0px 0 rgba(0,0,0,.4);padding:0.1rem 0.4rem;margin:0rem 0;right:1rem;font-size:1rem;">' . tmy_g11n_html_kses_esc($this->translator->get_language_switcher('floating')) . '</div></div>';

              ?>
                <script>
                //Make the DIV element draggagle:
                dragElement(document.getElementById("tmyfloatmenu"));

                function dragElement(elmnt) {
                  var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
                  if (document.getElementById(elmnt.id + "header")) {
                    /* if present, the header is where you move the DIV from:*/
                    document.getElementById(elmnt.id + "header").onmousedown = dragMouseDown;
                  } else {
                    /* otherwise, move the DIV from anywhere inside the DIV:*/
                    elmnt.onmousedown = dragMouseDown;
                  }

                  function dragMouseDown(e) {
                    e = e || window.event;
                    e.preventDefault();
                    // get the mouse cursor position at startup:
                    pos3 = e.clientX;
                    pos4 = e.clientY;
                    document.onmouseup = closeDragElement;
                    // call a function whenever the cursor moves:
                    document.onmousemove = elementDrag;
                  }

                  function elementDrag(e) {
                    e = e || window.event;
                    e.preventDefault();
                    // calculate the new cursor position:
                    pos1 = pos3 - e.clientX;
                    pos2 = pos4 - e.clientY;
                    pos3 = e.clientX;
                    pos4 = e.clientY;
                    // set the element's new position:
                    elmnt.style.top = (elmnt.offsetTop - pos2) + "px";
                    elmnt.style.left = (elmnt.offsetLeft - pos1) + "px";
                  }

                  function closeDragElement() {
                    /* stop moving when mouse button is released:*/
                    document.onmouseup = null;
                    document.onmousemove = null;
                  }
                }
                </script>
              <?php
            }
        }
	public function g11n_widget_title($title, $instance, $id_base) {
                error_log("g11n_widget_title");
                //return "aaaaaaaaaa";
        }


	public function g11n_push_status_div() {
        /***********************************************/
        /* action for edit_form_after_editor obsoleted */
        /***********************************************/

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
                <?php

                $post_id = get_the_ID();
                $post_type = get_post_type($post_id);
                $post_status = get_post_status($post_id);

                $all_post_types = tmy_g11n_available_post_types();

	    	if (strcmp($post_type,"g11n_translation")===0) {

                    echo '<div style="border:1px solid #A8A7A7;padding: 10px;">';
                    $trans_info = $this->translator->get_translation_info($post_id);
                    if (isset($trans_info[0])) {
                        $original_id = $trans_info[0]->ID;
                        $original_title = $trans_info[0]->post_title;
                    }
		    $trans_lang = get_post_meta($post_id,'g11n_tmy_lang',true);

                    echo '<b>This is the ' . esc_attr($trans_lang) . ' translation page of <a href="' . 
                         esc_url( get_edit_post_link($original_id) ) . '">' . esc_attr($original_title) . 
                       ' (ID:' . esc_attr($original_id) . ')</a>';

		    if (strcmp($post_status,"publish")===0) {
		        echo ' Status: Live</b></br>';
		    } else {
		        echo ' Status: Not Published Yet</b></br>';
	       	    }
                    echo "</div>";

                } elseif (array_key_exists($post_type, $all_post_types)) {
                //} elseif ((strcmp($post_type,"post")===0) || (strcmp($post_type,"page")===0)) {

                    echo '<div style="border:1px solid #A8A7A7;padding: 10px;">';
    		    echo '<b>Translation Satus:</b><br><br>'; 

                    $all_langs = get_option('g11n_additional_lang');
                    $default_lang = get_option('g11n_default_lang');
                    unset($all_langs[$default_lang]);
                    
                    if (is_array($all_langs)) {
                        foreach( $all_langs as $value => $code) {
                            $translation_id = $this->translator->get_translation_id($post_id,$code,$post_type);
			    if (isset($translation_id)) {
                                $translation_status = get_post_status($translation_id);
                                echo esc_attr($value) . '-' . esc_attr($code) . ' Translation page is at <a href="' . esc_url( get_edit_post_link($translation_id) ) . 
                                     '">ID ' . esc_attr($translation_id) . '</a>, status: ' . esc_attr($translation_status) . '</br>';
                            } else {
                                echo esc_attr($value) . '-' . esc_attr($code) . ' Not Started Yet </br>';
                            }

                         }
                    }

                    echo '<br>Click <button type="button" onclick="create_sync_translation(' . esc_attr($post_id) . ', \'' . esc_attr($post_type) . '\')">Start or Sync Translation</button> to send this page to translation server';
                    echo '<br>Visit <a href="' . get_home_url() . '/wp-admin/edit.php?post_type=g11n_translation' . '">TMY Translations</a> page for all translations';
                    echo '<br>Or, visit <a href="' . get_home_url() . '/wp-admin/admin.php?page=tmy-g11n-dashboard-menu' . '">TMY Dashboard</a> for translation summary<br>';

                    if ((strcmp('', get_option('g11n_server_user','')) !== 0) && (strcmp('', get_option('g11n_server_token','')) !== 0)) {
    		        echo '<br>Latest status with Translation Server:<div id="g11n_push_status_text_id"><h5>'. 
			    esc_attr(get_post_meta(get_the_ID(),'translation_push_status',true)) . '</h5></div>';
                    }
                    echo "</div>";
                    
                }
	}

	public function myprefix_edit_form_after_title($post) {

	}

	public function add_before_dynamic_siderbar( $current_widget ) {
	    global $wp_registered_widgets;

	    // Only run on the Widgets admin screen, not the front-end
	    //if ( ! is_admin() )
	    //	return;

	    // Get all sidebars and their widgets
	    $sidebars_widgets = wp_get_sidebars_widgets();

	    // Optionally remove looping through Inactive Widgets
	    unset( $sidebars_widgets['wp_inactive_widgets'] );

	    // Get current sidebar ID
	    foreach( $sidebars_widgets as $sidebars => $widgets ){
		for( $i = 0; $i < count( $widgets ); $i++ ) {
			if ( $current_widget['id'] === $widgets[$i]) {
				$current_sidebar_id = $sidebars;
				break 2;
			}
		}
	    }

	    // Bail if sidebar not found (e.g. Inactive Widgets, which we unset earlier)
	    if ( ! isset( $current_sidebar_id ) )
		    return;

	    // Get first widget ID in the current sidebar
	    foreach( $sidebars_widgets[$current_sidebar_id] as $key => $value ) {
		$first_widget_id = $value;
		break;
	    }

	    // Bail if we're not about to show the first widget form
	    if ( $first_widget_id !== $current_widget['id'] )
		return;

	    // Now echo something awesome at the top of each sidebar!
	    if(strcmp(get_option('g11n_switcher_sidebar'),"Yes")==0){
		echo '<div align="center">' . tmy_g11n_html_kses_esc($this->translator->get_language_switcher('sidebar')). '</div>';
            }



        }
	public function add_before_my_siderbar( $name ) 
	{
                global $locale;
                //error_log("in sidebar before: " . $locale);
                //$WP_Sys_Locale_Switcher = new WP_Locale_Switcher();
                //$success_switch = $WP_Sys_Locale_Switcher->switch_to_locale($locale);
		if(strcmp(get_option('g11n_switcher_sidebar'),"Yes")==0){
	        //	echo '<div align="center">' . $this->translator->get_language_switcher('sidebar'). '</div>';
		}

	}

	public function g11n_locale_filter($locale_in) {

          //error_log("g11n_locale_ filter MAIN " . $locale_in);
          //error_log("g11n_locale_ filter MAIN " . json_encode($_SERVER));
          //error_log("g11n_locale_ filter MAIN " . json_encode($_REQUEST));
                if ( is_admin() || defined( 'REST_REQUEST' ) && REST_REQUEST ) {
                    return $locale_in; 
                }

                /* array format ((English -> en), ...) */
                $language_options = get_option('g11n_additional_lang', array());

                /******
                $http_referer_path = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH);
                $site_url_path = parse_url(get_site_url(), PHP_URL_PATH);
                $http_referer_path = str_replace($site_url_path, "", $http_referer_path);
                $http_paths = explode('/', $http_referer_path);

                if (isset($http_paths[1])) {
                    $http_referer_lang = strtolower(str_replace('-', '_', $http_paths[1]));
                    $http_referer_code = array_search(strtolower($http_referer_lang), array_map('strtolower',$language_options));
                    if (isset($http_referer_code) && strcmp($http_referer_code,"")!==0) {
                        return $language_options[$http_referer_code];
                    }
                }
                ******/
                if (isset($_REQUEST['post_data'])) {
                    parse_str($_REQUEST['post_data'], $post_data_query_vars);
                    $ajax_wp_query = parse_url($post_data_query_vars['_wp_http_referer'], PHP_URL_QUERY);
                    parse_str($ajax_wp_query, $ajax_wp_query_vars);
                    if (isset($ajax_wp_query_vars['g11n_tmy_lang_code'])) {
                        $wp_ajax_lang = $ajax_wp_query_vars['g11n_tmy_lang_code'];
                        $wp_ajax_lang = strtolower(str_replace('-', '_', $wp_ajax_lang));
                        $wp_ajax_lang_code = array_search(strtolower($wp_ajax_lang), array_map('strtolower',$language_options));
                        if ( WP_TMY_G11N_DEBUG ) {
                            error_log("g11n_locale_ filter return based on post_data" . $language_options[$wp_ajax_lang_code]);
                        }
                        return $language_options[$wp_ajax_lang_code];

                    };
                }

                $current_lang = "";
                $current_lang_code = "";

                if (isset($_SESSION['g11n_language'])) {
                    $current_lang = $_SESSION['g11n_language'];
                } elseif (isset($_COOKIE['g11n_language'])) {
                    $current_lang = $_COOKIE['g11n_language'];
                }
                if (strcmp($current_lang, "")===0) {
                    $current_lang = get_option('g11n_default_lang');
                }
                $current_lang_code = $language_options[$current_lang];

                if ( WP_TMY_G11N_DEBUG ) {
                    error_log("g11n_locale_ filter set to " . $current_lang_code);
                }
                return $current_lang_code;

	        // if current locale code is not in the language list configured, return original locale to avoid dead lock
	        //if (!array_key_exists('$locale_in', $language_options)) {
	        //    return $locale_in;
		//}

		$pre_lang = $this->translator->get_preferred_language();
		if (array_key_exists($pre_lang, $language_options)) {
		    return $language_options[$pre_lang];
		} else {
		    return $locale_in;
		}
		return $locale_in;

	}

	public function g11n_create_post_type_translation() {

		register_post_type( 'g11n_translation',
		    array(
		      'labels' => array(
			'name' => __( 'TMY Translations', 'tmy-globalization' ),
			'singular_name' => __( 'TMY Translation', 'tmy-globalization' )
		      ),
		      'public' => true,
		      'show_ui' => true,
		      'show_in_menu' => 'tmy-g11n-main-menu',
		      'menu_position' => '3',
		      //'show_in_menu' => 'admin.php?page=tmy-g11n-setup-menu',
		      //'show_in_menu' => 'edit.php?post_type=g11n_translation',
                      'show_in_rest' => true,
                      'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ),
                      'capabilities' => array(
                          'create_posts' => 'do_not_allow', 
                       // Removes support for the "Add New" function ( use 'do_not_allow' instead of false for multisite set ups )
                       ),
                      'map_meta_cap' => true, 
		      'has_archive' => true,
		    )
		);

	}

	public function g11n_post_saved_notification( $ID, $post ) {

            if ( WP_TMY_G11N_DEBUG ) {
                error_log("In g11n_post_saved_notification, " . esc_attr($ID));
                error_log("In g11n_post_saved_notification, " . esc_attr(json_encode($post)));
            }

            //do_action('do_meta_boxes', null, 'normal', $post);
            //do_meta_boxes( null, 'normal', $post);
            //do_meta_boxes( $screen, $context, $object )

        }

	public function g11n_post_published_notification( $ID, $post ) {

		/* post data structure ref: 
		 * https://codex.wordpress.org/Class_Reference/WP_Post 
		 */

		    if (strcmp($post->post_type, "g11n_translation") === 0) {
			error_log("SKIP Sending for translation POST type: " . esc_attr($post->post_type));
			return;
		    }

		    if (strcmp($post->post_type, "product") === 0) {
                        if ( WP_TMY_G11N_DEBUG ) {
			    error_log("Publishing product id: " . esc_attr($post->ID));
                        }
			return;
		    }

		    $json_file_name = "WordpressG11nAret-" . $post->post_type . "-" . $ID;

		    $content_title = $post->post_title;
		    //$content = $post->post_content;
		    //$contents_array = array($content_title,$content);

                    if ( WP_TMY_G11N_DEBUG ) {
		        error_log("MYSQL" . esc_attr(var_export($post->post_content,true)));
                    }
		    $tmp_array = preg_split('/(\n)/', $post->post_content,-1, PREG_SPLIT_DELIM_CAPTURE);
		    //error_log("MYSQL" . var_export($tmp_array,true));
		    $contents_array = array();
		    array_push($contents_array, $content_title);
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
		    //$this->translator->push_contents_to_translation_server($json_file_name, $contents_array);
                    // disable this August 2022

	}

	public function g11n_pre_get_option_blogdescription( $output, $show ) {

                if ( WP_TMY_G11N_DEBUG ) {
                    error_log("IN g11n_pre_get_option_blogdescription: " . esc_attr($output) . "." . esc_attr($show) . ".");
                }

		remove_filter('pre_option_blogdescription',array($this, 'g11n_pre_get_option_blogdescription'),10);
                $output = get_option('blogdescription');
		add_filter('pre_option_blogdescription',array($this, 'g11n_pre_get_option_blogdescription'), 10, 2);

                if ( is_admin() || defined( 'REST_REQUEST' ) && REST_REQUEST ) {
                    return $output;
                }

                if (strcmp(get_option('g11n_l10n_props_blogdescription'),"Yes")==0){
                     $g11n_current_language = tmy_g11n_lang_sanitize($_SESSION['g11n_language']);
                     $language_options = get_option('g11n_additional_lang');
                     $language_name = $language_options[$g11n_current_language];

                     $title_post  = get_page_by_title('blogdescription',OBJECT,'post');
                     if (! is_null($title_post) && (strcmp($title_post->post_status,'trash')!==0)) {
                         $translation_post_id = $this->translator->get_translation_id($title_post->ID,$language_name,"post",false);
                         if (isset($translation_post_id)) {
                             if ( WP_TMY_G11N_DEBUG ) {
                                 error_log("In g11n_pre_get_option_blogdescription, translation id:" . esc_attr($translation_post_id));
                             }
                             $output = get_post_field("post_content", $translation_post_id);
                         }
                     }
                }
                if(strcmp(get_option('g11n_switcher_tagline'),"Yes")==0){
                    $switcher_html = $this->translator->get_language_switcher('description');
                    //$switcher_html = "";
                } else {
                    $switcher_html = "";
                }
                return $output . $switcher_html;


        }
	public function g11n_pre_get_option_blogname( $output, $show ) {

                if ( WP_TMY_G11N_DEBUG ) {
                    error_log("IN g11n_pre_get_option_blogname: " . esc_attr($output) . "." . esc_attr($show) . ".");
                }


		remove_filter('pre_option_blogname',array($this, 'g11n_pre_get_option_blogname'),10);
		remove_filter('bloginfo',array($this, 'g11n_wp_title_filter'),10);

                $output = get_option('blogname');

		add_filter('bloginfo',array($this, 'g11n_wp_title_filter'), 10, 2);
		add_filter('pre_option_blogname',array($this, 'g11n_pre_get_option_blogname'), 10, 2);

                if ( is_admin() || defined( 'REST_REQUEST' ) && REST_REQUEST ) {
                    return $output;
                }
                if ( $GLOBALS['pagenow'] === 'wp-login.php' ) {
                    return $output;
                }

                if (strcmp(get_option('g11n_l10n_props_blogname'),"Yes")==0){
                    $g11n_current_language = tmy_g11n_lang_sanitize($_SESSION['g11n_language']);
                    $language_options = get_option('g11n_additional_lang');
                    $language_name = $language_options[$g11n_current_language];

                    $title_post  = get_page_by_title('blogname',OBJECT,'post');

                    if (! is_null($title_post) && (strcmp($title_post->post_status,'trash')!==0)) {
                        $translation_post_id = $this->translator->get_translation_id($title_post->ID,$language_name,"post",false);
                        if (isset($translation_post_id)) {
                            if ( WP_TMY_G11N_DEBUG ) {
                                error_log("In g11n_pre_get_option_blogname, translation id:" . esc_attr($translation_post_id));
                            }
                            $output = get_post_field("post_content", $translation_post_id);
                        }
                    }
                }
                if ( WP_TMY_G11N_DEBUG ) {
                    error_log("In g11n_pre_get_option_blogname, g11n_switcher_title,".esc_attr(get_option('g11n_switcher_title')));
                }
                if(strcmp(get_option('g11n_switcher_title'),"Yes")==0){
                    $switcher_html = $this->translator->get_language_switcher('blogname');
                    //$switcher_html = "";
                } else {
                    $switcher_html = "";
                }
                if ( WP_TMY_G11N_DEBUG ) {
                    error_log("In g11n_pre_get_option_blogname, output:".esc_attr($output) . esc_attr($switcher_html));
                }
                return $output . $switcher_html;

        }

	public function g11n_pre_option_blogname( $in ) {

	        global $wp_query;

                if ( WP_TMY_G11N_DEBUG ) {
                    error_log("g11n_pre_option_blogname:" . esc_attr($in) );
                }
                if (strcmp(get_option('g11n_l10n_props_blogname'),"Yes")==0) {

                    $title_post  = get_page_by_title('blogname',OBJECT,'post');
                    if (! is_null($title_post) && (strcmp($title_post->post_status,'trash')!==0)) {
                        $new_post_id = wp_insert_post(array('ID' => $title_post->ID,
                                                            'post_title'    => 'blogname',
                                                            'post_content'  => $in,
                                                            'post_status'  => 'private',
                                                            'post_type'  => "post"));
                    } else {
                        $new_post_id = wp_insert_post(array('post_title'    => 'blogname',
                                                            'post_content'  => $in,
                                                            'post_status'  => 'private',
                                                            'post_type'  => "post"));
                    }
                    if ( WP_TMY_G11N_DEBUG ) {
                        error_log("g11n_pre_option_blogname post id:" . esc_attr($new_post_id) );
                    }
                }
		//error_log("PRE UPDATE BLOGNAME: " . $in);
		//$json_file_name = "WordpressG11nAret-blogname-0";
		//$contents_array = array($in);
		//$this->translator->push_contents_to_translation_server($json_file_name, $contents_array);

		return $in;
	}
	public function g11n_pre_option_blogdescription( $in ) {

	        global $wp_query;

                if ( WP_TMY_G11N_DEBUG ) {
                    error_log("g11n_pre_option_blogdescription:" . esc_attr($in) );
                }
                if (strcmp(get_option('g11n_l10n_props_blogdescription'),"Yes")==0) {

                    $title_post  = get_page_by_title('blogdescription',OBJECT,'post');
                    if (! is_null($title_post) && (strcmp($title_post->post_status,'trash')!==0)) {
                        $new_post_id = wp_insert_post(array('ID' => $title_post->ID,
                                                            'post_title'    => 'blogdescription',
                                                            'post_content'  => $in,
                                                            'post_status'  => 'private',
                                                            'post_type'  => "post"));
                    } else {
                        $new_post_id = wp_insert_post(array('post_title'    => 'blogdescription',
                                                            'post_content'  => $in,
                                                            'post_status'  => 'private',
                                                            'post_type'  => "post"));
                    }
                    if ( WP_TMY_G11N_DEBUG ) {
                        error_log("g11n_pre_option_blogdescription post id:" . esc_attr($new_post_id) );
                    }
                }
		 //   error_log("PRE UPDATE blogdescription: " . $in);
		 //   $json_file_name = "WordpressG11nAret-blogdescription-0";
		 //   $contents_array = array($in);
		 //   $this->translator->push_contents_to_translation_server($json_file_name, $contents_array);

	        return $in;
	}
	
	public function g11n_the_posts_filter($posts, $query = false) {

                    if( is_search() ){
                    }

	            foreach ( $posts as $post ) {
                        if ( WP_TMY_G11N_DEBUG ) {
                            error_log("In g11n_the_posts_filter, post_id: " . esc_attr($post->ID) . " excerpt: " . esc_attr($post->post_excerpt));
                        }
                        if ( tmy_g11n_is_valid_post_type($post->post_type) && (! empty($post->post_excerpt))) {
		        //if ((strcmp($post->post_type, "product") === 0) && (! empty($post->post_excerpt))) {

		            $g11n_current_language = $this->translator->get_preferred_language();

                            //$g11n_current_language = tmy_g11n_lang_sanitize($_SESSION['g11n_language']);
                            $language_options = get_option('g11n_additional_lang');
                            $language_name = $language_options[$g11n_current_language];
		            $translation_post_id = $this->translator->get_translation_id($post->ID,
                                                                                         $language_name,
                                                                                         $post->post_type,
                                                                                         false);
                            if ( WP_TMY_G11N_DEBUG ) {
                                error_log("In g11n_the_posts_filter, excerpt post_id: " . esc_attr($post->ID) . " language: " . esc_attr($language_name));
                                error_log("In g11n_the_posts_filter, translation_id:  " . esc_attr($translation_post_id));
                                error_log("In g11n_the_posts_filter, SESSION:  " . esc_attr($_SESSION['g11n_language']));
                            }
		            if (isset($translation_post_id)) {
                                $post->post_excerpt=get_the_excerpt($translation_post_id);
                            }
                            //$post->post_title="title l10n:". $post->post_title;
		        }
	            }
                    return $posts;
        }

	public function g11n_excerpt_filter($input) {

                    //if (! ( is_admin() || defined( 'REST_REQUEST' ) && REST_REQUEST )) {
		        global $wp_query; 

		        $postid = $wp_query->post->ID;
		        $posttype = $wp_query->post->post_type;

                        if ( WP_TMY_G11N_DEBUG ) {
		            error_log("In excerpt filter, post id =" . esc_attr($postid));
		            error_log("In excerpt filter, post type =" . esc_attr($posttype));
		            error_log("In excerpt filter, input =" . esc_attr($input));
                        }

		        if (strcmp($posttype,"product")==0) {
			    #return $input;
			    return "Translation of excerpt : " . $input;
		        }
                    //}

        }

	public function g11n_content_filter($input) {

                    if (! ( is_admin() || defined( 'REST_REQUEST' ) && REST_REQUEST )) {

		        global $wp_query; 

                        if ( WP_TMY_G11N_DEBUG ) {
		            error_log("In g11n_content_filter filter, session_id=" . esc_attr(session_id()) . "session lang=" . tmy_g11n_lang_sanitize($_SESSION['g11n_language']) );
		            error_log("In g11n_content_filter filter, session lang = [" . tmy_g11n_lang_sanitize($_SESSION['g11n_language']). "]");
		            error_log("In g11n_content_filter filter, cookie lang = [" . tmy_g11n_lang_sanitize($_COOKIE['g11n_language']) . "]");
		            error_log("In g11n_content_filter filter, browser lang = [" . sanitize_textarea_field($_SERVER['HTTP_ACCEPT_LANGUAGE']) . "]");
                        }

		        $postid = $wp_query->post->ID;
		        $posttype = $wp_query->post->post_type;

                        if ( WP_TMY_G11N_DEBUG ) {
		            error_log("In g11n_content_filter filter, postid, posttype:" . esc_attr($postid) . " " . esc_attr($posttype) );
                        }

                        if (! tmy_g11n_post_type_enabled($postid, $wp_query->post->post_title, $posttype)) {
			    return $input;
                        }

		        //if ((strcmp(get_option('g11n_l10n_props_posts'),"Yes")!=0) and 
			//    (strcmp($posttype,"post")==0)) {
			//    return $input;
		        //}

		        //if ((strcmp(get_option('g11n_l10n_props_pages'),"Yes")!=0) and 
			//    (strcmp($posttype,"page")==0)) {
			//    return $input;
		        //}

		        //if (strcmp($posttype,"product")==0) {
			//    return $input;
			//    //return "Translation of cts: " . $input;
		        //}

		        $language_options = get_option('g11n_additional_lang');
		        $g11n_current_language = tmy_g11n_lang_sanitize($_SESSION['g11n_language']);
		        $language_name = $language_options[$g11n_current_language];
		        //$translation_post_id = $this->translator->get_translation_id($postid,$language_name,$posttype);

		        $translation_post_id = $this->translator->get_translation_id($postid,$language_name,$posttype,false);

                        if ( WP_TMY_G11N_DEBUG ) {
		            error_log("In g11n_content_filter original post id = " . esc_attr($postid) . ".");
		            error_log("In g11n_content_filter language = " . esc_attr($language_name) . ".");
		            error_log("In g11n_content_filter type = " . esc_attr($posttype) . ".");
		            error_log("In g11n_content_filter translation_post_id = " . esc_attr($translation_post_id) . ".");
                        }

		        if(strcmp(get_option('g11n_switcher_post'),"Yes")==0){
			    $switcher_html = $this->translator->get_language_switcher('content');
		        } else {
			        $switcher_html = "";
		        }
    
		        if (isset($translation_post_id)) {
			    return wpautop(get_post_field("post_content", $translation_post_id)) . "<br>" . $switcher_html;
		        } else {
			    return $input . "<br>" . $switcher_html;
		        }
                    }
	}

	public function g11n_title_filter( $title, $id = 0 ) {
    

                    if ( is_admin() || defined( 'REST_REQUEST' ) && REST_REQUEST ) {
                        if ( WP_TMY_G11N_DEBUG ) {
                            error_log("g11n_title_filter: " . esc_attr($title));
                        }
                        return $title;
                    }

		    //global $wp_query; 
		    //$postid = $wp_query->post->ID;
		    //if (!isset($wp_query->post)) return $title;
		    //$posttype = $wp_query->post->post_type;

                    $posttype = get_post_type($id);

                    if (! tmy_g11n_post_type_enabled($id, $title, $posttype)) {
			return $title;
                    }

		    $language_options = get_option('g11n_additional_lang');

		    //$g11n_current_language = tmy_g11n_lang_sanitize($_SESSION['g11n_language']);
		    $g11n_current_language = $this->translator->get_preferred_language();
		    $language_name = $language_options[$g11n_current_language];

		    //$translation_post_id = $this->translator->get_translation_id($id,$language_name,$wp_query->post->post_type,false);
		    $translation_post_id = $this->translator->get_translation_id($id,$language_name,$posttype,false);

		    //error_log("G11N TITLE FILTER, {$title}-{$id}-{$language_name}-{$wp_query->post->post_type}-{$translation_post_id})");

		    if (isset($translation_post_id)) {
			return get_post_field("post_title", $translation_post_id);
		    } else {
			return $title;
		    }

	}

	public function g11n_wp_title_filter( $output, $show ) {

                    if ( WP_TMY_G11N_DEBUG ) {
                        error_log("In g11n_wp_title_filter starting with, output: " . esc_attr($output) . ",show: " . esc_attr($show));
                    }

		    if (strcmp($show,'description')==0) {

		        remove_filter('pre_option_blogdescription',array($this, 'g11n_pre_get_option_blogdescription'),10);
		        remove_filter('bloginfo',array($this, 'g11n_wp_title_filter'),10);
                        $output = get_option('blogdescription');
		        add_filter('bloginfo',array($this, 'g11n_wp_title_filter'), 10, 2);
		        add_filter('pre_option_blogdescription',array($this, 'g11n_pre_get_option_blogdescription'), 10, 2);
                        if ( is_admin() || defined( 'REST_REQUEST' ) && REST_REQUEST ) {
                            return $output;
                        }

                        if (strcmp(get_option('g11n_l10n_props_blogdescription'),"Yes")==0){
			     $g11n_current_language = tmy_g11n_lang_sanitize($_SESSION['g11n_language']);
			     $language_options = get_option('g11n_additional_lang');
			     $language_name = $language_options[$g11n_current_language];

                             $title_post  = get_page_by_title('blogdescription',OBJECT,'post');
                             if (! is_null($title_post) && (strcmp($title_post->post_status,'trash')!==0)) {
                                 $translation_post_id = $this->translator->get_translation_id($title_post->ID,$language_name,"post",false);
			         if (isset($translation_post_id)) {
                                     if ( WP_TMY_G11N_DEBUG ) {
                                         error_log("In g11n_wp_title_filter,blogdescription translation id:" . esc_attr($translation_post_id));
                                     }
			             $output = get_post_field("post_content", $translation_post_id);
			         }
		             }
                        }
			if(strcmp(get_option('g11n_switcher_tagline'),"Yes")==0){
			    $switcher_html = $this->translator->get_language_switcher('description');
			} else {
			    $switcher_html = "";
			}
		        return $output . $switcher_html;
                    }

		    if (strcmp($show,'name')==0) {

		        remove_filter('pre_option_blogname',array($this, 'g11n_pre_get_option_blogname'),10);
		        remove_filter('bloginfo',array($this, 'g11n_wp_title_filter'),10);

                        $output = get_option('blogname');

		        add_filter('bloginfo',array($this, 'g11n_wp_title_filter'), 10, 2);
		        add_filter('pre_option_blogname',array($this, 'g11n_pre_get_option_blogname'), 10, 2);

                        if ( is_admin() || defined( 'REST_REQUEST' ) && REST_REQUEST ) {
                            return $output;
                        }

                        if (strcmp(get_option('g11n_l10n_props_blogname'),"Yes")==0){
			    $g11n_current_language = tmy_g11n_lang_sanitize($_SESSION['g11n_language']);
			    $language_options = get_option('g11n_additional_lang');
			    $language_name = $language_options[$g11n_current_language];

                            $title_post  = get_page_by_title('blogname',OBJECT,'post');
                            if (! is_null($title_post) && (strcmp($title_post->post_status,'trash')!==0)) {
                                $translation_post_id = $this->translator->get_translation_id($title_post->ID,$language_name,"post",false);
			        if (isset($translation_post_id)) {
                                    if ( WP_TMY_G11N_DEBUG ) {
                                        error_log("In g11n_wp_title_filter,blogname translation id:" . esc_attr($translation_post_id));
                                    }
			            $output = get_post_field("post_content", $translation_post_id);
			        }
                            }
		        }
                        if ( WP_TMY_G11N_DEBUG ) {
                            error_log("In g11n_wp_title_filter, g11n_switcher_title,".esc_attr(get_option('g11n_switcher_title')));
                        }
			if(strcmp(get_option('g11n_switcher_title'),"Yes")==0){
			    $switcher_html = $this->translator->get_language_switcher('blogname');
			} else {
			    $switcher_html = "";
			}
                        if ( WP_TMY_G11N_DEBUG ) {
                            error_log("In g11n_wp_title_filter, output,".esc_attr($output));
                        }
                        return wp_strip_all_tags($output) . $switcher_html;
		    } 

		    return $output;
	}

	public function tmy_g11n_blocks_init() {

            wp_enqueue_script(
              'tmy-lang-block',
              plugin_dir_url(__DIR__) . 'includes/tmy-block-language-switcher.js',
              array('wp-blocks','wp-editor','wp-server-side-render'),
              true
            );

            $return = register_block_type('tmy/tmy-chooser-box', array(
                    'render_callback' => array($this,'tmy_lang_switcher_block_dynamic_render_cb')
            ));

        }

        function tmy_g11n_site_url ( $url, $path ) {

           //error_log("SITE URL ".$url . " path: " . $path);
           return $url;
        }

        function tmy_lang_switcher_block_dynamic_render_cb ( $att ) {

            $html = '<div>' . tmy_g11n_html_kses_esc($this->translator->get_language_switcher('block')) . '</div>';
            return $html;
        }

	public function tmy_g11n_template_redirect() {

            session_start();

            if (! is_admin()) {
                if ( WP_TMY_G11N_DEBUG ) {
                    if (isset($_SESSION)) {
                        error_log("In tmy_g11n_template_redirect, ". sanitize_textarea_field(json_encode($_SESSION)));
                    } else {
                        error_log("In tmy_g11n_template_redirect, _SESSION is not set");
                    }
                    error_log("In tmy_g11n_template_redirect, session id ".esc_attr(session_id()));
                }
            }

            if (! is_admin()) {
                $lang_var = tmy_g11n_lang_sanitize(filter_input(INPUT_GET, 'g11n_tmy_lang', FILTER_SANITIZE_SPECIAL_CHARS));


                $all_configed_langs = get_option('g11n_additional_lang'); /* array format ((English -> en), ...) */
                $lang_var_code_from_query = filter_input(INPUT_GET, 'g11n_tmy_lang_code', FILTER_SANITIZE_SPECIAL_CHARS);
                $lang_var_code_from_query = str_replace('-', '_', $lang_var_code_from_query);

                if (!empty($lang_var_code_from_query)) {
                    $lang_var = array_search(strtolower($lang_var_code_from_query), array_map('strtolower',$all_configed_langs));
                }



                if ( WP_TMY_G11N_DEBUG ) {
                    error_log("In g11n_setcookie , lang_var " . esc_attr($lang_var));
                }
                if (!empty($lang_var)) {
                        setcookie('g11n_language', $lang_var, strtotime('+1 day'));
                        if ( WP_TMY_G11N_DEBUG ) {
                             error_log("In g11n_setcookie SET COOKIE from query string - " . esc_attr($lang_var));
                        }
                } else {
                        setcookie('g11n_language', get_option('g11n_default_lang'), strtotime('+1 day'));
                        if ( WP_TMY_G11N_DEBUG ) {
                             error_log("In g11n_setcookie SET COOKIE from wp language option - " . esc_attr(get_option('g11n_default_lang')));
                        }
                }
            }

        }
	public function tmy_g11n_html_head_handler() {

            //<link rel="alternate" hreflang="de" href="https://de.example.com/index.html" />
            //<link rel="alternate" href="https://example.com/country-selector" hreflang="x-default" />

            $all_langs = get_option('g11n_additional_lang');
            $default_lang = get_option('g11n_default_lang');
            //unset($all_langs[$default_lang]);
            global $wp;
            $site_url = get_site_url();

            if (is_array($all_langs)) {
                foreach( $all_langs as $value => $code) {
                    $lang_code = strtolower($code);
                    $lang_code = str_replace('_', '-', $lang_code);
                    $current_url = home_url( $wp->request );
                    $current_url = str_replace($site_url, $site_url . '/' . esc_attr($lang_code), $current_url);
                    $current_url = $current_url . '/';
                    echo '<link rel="alternate" hreflang="' . esc_attr($lang_code) . '" href="' .
                    esc_url($current_url) . '" />' . "\n";
                }
            }
            $current_url = home_url( $wp->request );
            echo '<link rel="alternate" href="' . esc_url($current_url) . '" hreflang="x-default" />' . "\n";
        }
        public function tmy_translation_get_taxonomy_filter( $wp_term, $taxonomy ) {

            //error_log("In tmy_translation_get_taxonomy_filter: " . json_encode($wp_term));

            if ( ! is_admin() ) {

                //if (! tmy_g11n_post_type_enabled($wp_term->term_id, "", "taxonomy")) {
                //    return $wp_term;
                //}

                $all_configed_langs = get_option('g11n_additional_lang');
                $lang_code = $all_configed_langs[$this->translator->get_preferred_language()];
                //$translation_id = $this->translator->get_translation_id($wp_term->term_id, $lang_code, "taxonomy", false);
                $translation_id = $this->translator->get_translation_id($wp_term->term_id, $lang_code, $taxonomy, false);

                if (isset($translation_id)) {
                    $wp_term->name = get_post_field("post_title", $translation_id);
                }
            }
            return $wp_term;
        }

        public function tmy_nav_menu_item_filter( $items, $args ) {

            return $items;

        }
        public function tmy_nav_menu_item_title_filter( $title, $menu_item, $args, $depth ) {

            //error_log("In tmy_nav_menu_item_title_filter: " . json_encode($title) . " " . json_encode($menu_item));
            global $wpdb;
            $sql = "select ID from {$wpdb->prefix}posts where post_title=\"" . esc_sql($title) . "\" and post_status=\"private\"";
            $result = $wpdb->get_results($sql);

            if (isset($result[0]->ID)) {

                $language_options = get_option('g11n_additional_lang');
                $g11n_current_language = $this->translator->get_preferred_language();
                $language_name = $language_options[$g11n_current_language];
                $translation_post_id = $this->translator->get_translation_id($result[0]->ID,$language_name,"post",false);
                if (isset($translation_post_id)) {
                    return get_post_field("post_content", $translation_post_id);
                } else {
                    return $title;
                }

            } else {
                return $title;
            }

            return $title;

        }
        public function tmy_option_widget_block( $value, $option ) {

            foreach ($value as $key => &$option_value) {
                if (is_array($option_value)) {
                    $widget_title = wp_strip_all_tags($option_value["content"]);
                    if (! empty($widget_title)) {
                        $option_value["content"] = str_replace($widget_title, esc_html_x($widget_title, ""), $option_value["content"]);
                    }
                }
            }
            return $value;
        }
        public function tmy_woocommerce_option_filter( $value, $option ) {

            //error_log("tmy_woocommerce_option_filter option, " . $option);

            if (($option === "woocommerce_cheque_settings") || ($option === "woocommerce_cod_settings")) {
                $language_options = get_option('g11n_additional_lang');
                $g11n_current_language = $this->translator->get_preferred_language();
                $lang = $language_options[$g11n_current_language];

                $value["title"] = $this->tmy_text_translator( $value["title"], $lang);
                $value["description"] = $this->tmy_text_translator( $value["description"], $lang);
                $value["instructions"] = $this->tmy_text_translator( $value["instructions"], $lang);

                //error_log("tmy_woocommerce_option_filter title, " . $value["title"]);
                //error_log("tmy_woocommerce_option_filter description, " . $value["description"]);
                //error_log("tmy_woocommerce_option_filter instructions, " . $value["instructions"]);

                return $value;
            }
            
            return $value;
        }

        public function tmy_nav_menu_objects_filter( $sorted_menu_items, $args ) 
        {

            $current_locale = get_locale();
            $current_locale = strtolower(str_replace("_", "-", $current_locale));
            $current_active_label = "";

            $tmy_dynamic_main = False;
            $tmy_dynamic_index = 0;

            $include_flag = True; 
            if (strcmp('Text', get_option('g11n_switcher_type','Text')) === 0) {
                $include_flag = False; 
            }

            $current_seo_option = esc_attr(get_option('g11n_seo_url_enable','No'));
            if (strcmp($current_seo_option, "")===0) {
                $current_seo_option = "No";
            }

            $current_url = sanitize_url($_SERVER['REQUEST_URI']);
            $site_url = get_site_url();
            $current_url_arr = wp_parse_url($current_url);
            $site_url_arr = wp_parse_url($site_url);

            $all_configed_langs = get_option('g11n_additional_lang',array()); /* array format ((English -> en), ...) */

            foreach ($sorted_menu_items as $menu_index => &$menu_item) {
                $url_arr = wp_parse_url($menu_item->url);
                if (isset($url_arr['query'])) {
                    parse_str($url_arr['query'], $url_query_arr);
                    if (array_key_exists("tmy_dynamic_main", $url_query_arr)) {
                        $tmy_dynamic_main = True;
                        $tmy_dynamic_index = $menu_index;
                        $menu_item->url = "/";
                        continue;
                    }
                    if (array_key_exists("tmy_dynamic_url", $url_query_arr)) {

                        $new_part = str_replace($site_url_arr["path"], "", $current_url_arr["path"]);
                        $url_lang = $url_query_arr["tmy_dynamic_url"];
                        $flag_lang = str_replace("-", "_", $url_lang);

                        if  ($current_seo_option == 'Yes') {
                            parse_str($_SERVER['QUERY_STRING'], $query_str_arr);
                            if (array_key_exists("g11n_tmy_lang_code", $query_str_arr)) {
                                unset($query_str_arr["g11n_tmy_lang_code"]);
                            }
                            $new_herf = $site_url . "/" . $url_lang . $new_part;
                            $menu_item->url = esc_url(add_query_arg($query_str_arr, $new_herf));
                        } else {
                            $lang_code = strtolower(str_replace('-', '_', $url_query_arr["tmy_dynamic_url"]));
                            $language = array_search(strtolower($lang_code), array_map('strtolower',$all_configed_langs));
                            if (! $language) {
                                $language = get_option("g11n_default_lang", "English");
                            }
                            $menu_item->url = esc_url(add_query_arg( array( 'g11n_tmy_lang' => $language),
                                                           $current_url ));
                        }
                        if ( $include_flag ) {
                            $img_html = '<img style="display: inline-block" src="' .
                                     plugin_dir_url(__DIR__) . 'includes/flags/' . "24/" .
                                     strtoupper($flag_lang) . '.png" alt="' .
                                     strtoupper($flag_lang) . "\" > ";
                        } else {
                            $img_html = "";
                        }
                        $menu_item->title = $img_html . $menu_item->title;

                        if ($current_locale === trim($url_query_arr["tmy_dynamic_url"])) {
                            $current_active_label = "<b>" . $menu_item->title . "</b>";
                            $menu_item->title = "<u><b>"  . $menu_item->title . "</b></u>";
                        }
                        continue;
                    }
                }
                if  ($current_seo_option == 'Yes') {
                    if ($url_arr['host'] === $site_url_arr['host']) {
                        $lang_code = get_locale();
                        $lang_code = strtolower(str_replace('_', '-', $lang_code));

                        $lang_path = explode('/', str_replace($site_url, '', $menu_item->url))[1];
                        $lang_path = str_replace('-', '_', $lang_path);

                        if (! array_search(strtolower($lang_path), array_map('strtolower',$all_configed_langs))) {
                            $menu_item->url = esc_url(str_replace($site_url, $site_url . '/' . esc_attr($lang_code), $menu_item->url));
                        }
                    }
                }
            }
            if (($current_active_label !== "") && ($tmy_dynamic_main)) {
                $sorted_menu_items[$tmy_dynamic_index]->title = $current_active_label;
            }
            return $sorted_menu_items;
        }

        public function tmy_woocommerce_order_item_name( $item_name, $item, $false ) {

            $language_name = get_locale();

            $translation_post_id = $this->translator->get_translation_id($item->get_product_id(), $language_name, "product", false);
            if (isset($translation_post_id)) {
                return get_post_field("post_title", $translation_post_id);
            } else {
                return $item_name;
            }

        }

        public function tmy_woocommerce_cart_item_name( $title, $values, $cart_item_key ) {

            $language_name = get_locale();
            $translation_post_id = $this->translator->get_translation_id($values["product_id"], $language_name, "product", false);
            if (isset($translation_post_id)) {
                return get_post_field("post_title", $translation_post_id);
            } else {
                return $title;
            }

        }
        public function tmy_nav_menu_link_attributes_filter($atts, $item, $args, $depth) {


            $current_seo_option = esc_attr(get_option('g11n_seo_url_enable','No'));
            if (strcmp($current_seo_option, "")===0) {
                $current_seo_option = "No";
            }


            $current_url = sanitize_url($_SERVER['REQUEST_URI']);
            $site_url = get_site_url();

            $herf_arr = wp_parse_url($atts['href']);
            $current_url_arr = wp_parse_url($current_url);
            $site_url_arr = wp_parse_url($site_url);

            if (isset($herf_arr['query'])) {
                parse_str($herf_arr['query'], $query_arr);
                if (array_key_exists("tmy_dynamic_url", $query_arr)) {
                    $new_part = str_replace($site_url_arr["path"], "", $current_url_arr["path"]);
                    if  ($current_seo_option == 'Yes') {
                        parse_str($_SERVER['QUERY_STRING'], $query_str_arr);
                        if (array_key_exists("g11n_tmy_lang_code", $query_str_arr)) {
                            unset($query_str_arr["g11n_tmy_lang_code"]);
                        }
                        $new_herf = $site_url . "/" . $query_arr["tmy_dynamic_url"] . $new_part;
                        $atts['href'] =  esc_url(add_query_arg($query_str_arr, $new_herf)) ;
                        //$atts['href'] = esc_url($site_url . "/" . $query_arr["tmy_dynamic_url"] . $new_part . "?" . esc_attr($_SERVER['QUERY_STRING']));
                        return $atts;
                    } else {
                        $all_configed_langs = get_option('g11n_additional_lang',array()); /* array format ((English -> en), ...) */
                        $lang_code = strtolower(str_replace('-', '_', $query_arr["tmy_dynamic_url"]));
                        $language = array_search(strtolower($lang_code), array_map('strtolower',$all_configed_langs));
                        if (! $language) {
                            $language = get_option("g11n_default_lang", "English");
                        }
                        $atts['href'] = esc_url(add_query_arg( array( 'g11n_tmy_lang' => $language), 
                                                       $current_url ));
                        return $atts;
                    }
                }
            }

            if  ($current_seo_option == 'Yes') {
                if ($herf_arr['host'] !== $site_url_arr['host']) {
                    return $atts;
                }

                $all_configed_langs = get_option('g11n_additional_lang'); /* array format ((English -> en), ...) */
                $lang_code = get_locale();
                $lang_code = strtolower(str_replace('_', '-', $lang_code));

                $lang_path = explode('/', str_replace($site_url, '', $atts["href"]))[1];
                $lang_path = str_replace('-', '_', $lang_path);
    
                if (! array_search(strtolower($lang_path), array_map('strtolower',$all_configed_langs))) {
                    $atts["href"] = esc_url(str_replace($site_url, $site_url . '/' . esc_attr($lang_code), $atts["href"]));
                    return $atts;
                }
            }

            return $atts;

        }

        public function tmy_woocommerce_new_order( $order_id, $order ) {

            error_log("tmy_woocommerce_new_order id " . $order_id);
            error_log("tmy_woocommerce_new_order locale" . get_locale());
            add_post_meta( $order_id, 'tmy_order_lang_code', get_locale(), true );

        }

        public function tmy_woocommerce_attribute_label_filter( $label, $name, $product ) {
        
            //error_log("tmy_woocommerce_attribute_label_filterer {$label}, {$name}, {$product}");

            global $wpdb;
            $sql = "select ID from {$wpdb->prefix}posts where post_title=\"" . esc_sql($label) . "\" and post_status=\"private\"";
            $result = $wpdb->get_results($sql);

            if (isset($result[0]->ID)) {
                //error_log("tmy_woocommerce_attribute_label_filterer {$label}, {$name}, {$product} item_id {$result[0]->ID}");

                $language_options = get_option('g11n_additional_lang');
                $g11n_current_language = $this->translator->get_preferred_language();
                $language_name = $language_options[$g11n_current_language];
                $translation_post_id = $this->translator->get_translation_id($result[0]->ID,$language_name,"post",false);
                if (isset($translation_post_id)) {
                    return get_post_field("post_content", $translation_post_id);
                } else {
                    return $label;
                }

            } else {
                return $label;
            }

            return $label;

        }


        public function tmy_text_translator( $text, $lang ) {

            global $wpdb;
            $sql = "select ID from {$wpdb->prefix}posts where post_title=\"" . esc_sql($text) . "\" and post_status=\"private\"";
            $result = $wpdb->get_results($sql);
            if (isset($result[0]->ID)) {
                //error_log("tmy_text_translator {$text} {$result[0]->ID}");
                $translation_post_id = $this->translator->get_translation_id($result[0]->ID, $lang, "post", false);
                if (isset($translation_post_id)) {
                    return get_post_field("post_content", $translation_post_id);
                } else {
                    return $text;
                }
            } else {
                return $text;
            }
            return $text;
        }
}
