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

                $g11n_enable_html_translator = esc_attr(get_option('g11n_enable_html_translator','No'));
                if (strcmp($g11n_enable_html_translator, "")===0) {
                    $g11n_enable_html_translator = "No";
                }
                if  ($g11n_enable_html_translator === 'Yes') {

                    if (is_user_logged_in()) {
                       $current_user = wp_get_current_user();
                       if (in_array('administrator', (array)$current_user->roles)) {
		           wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/tmy-g11n-public.js', array( 'jquery' ), $this->version, false );
                       } else {
		           wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/tmy-g11n-public-nopriv.js', array( 'jquery' ), $this->version, false );
                       }
                    }  else {
		       wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/tmy-g11n-public-nopriv.js', array( 'jquery' ), $this->version, false );
                    }
                }

		//wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/tmy-g11n-public.js', array( 'jquery' ), $this->version, false );

                wp_localize_script( $this->plugin_name, 'tmy_g11n_ajax_obj',
                                    array( 'ajax_url' => admin_url( 'admin-ajax.php' ),
                                           'img_url' => plugins_url('includes', __DIR__) ) );
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

        function tmy_rewrite_permalink_links( $permalink ) {

            if (! is_admin()) {
                return($this->tmy_internal_rewrite_url($permalink));
            }
            return $permalink;
        }

        function tmy_internal_translate_url( $url, $to_lan, $reverse=false ) {

            $trans_slug_conf = esc_attr(get_option('g11n_translate_slug','No'));
            if (strcmp($trans_slug_conf, "")===0) {
                $trans_slug_conf = "No";
            }
            //$trans_slug_conf = "No";
            if ($trans_slug_conf === "Yes") {
                $slugs_confs = get_option('g11n_slugs_mappings_config_extra', array());
                if (!is_array($slugs_confs)) {
                    $slugs_confs = array();
                }
                //a:2:{s:5:"zh-cn";a:2:{s:3:"url";a:1:{i:0;s:8:"/关于/";}s:8:"url_orig";a:1:{i:0;s:7:"/about/";}}
                //     s:5:"pt-br";a:2:{s:3:"url";a:1:{i:0;s:7:"/sobre/";}s:8:"url_orig";a:1:{i:0;s:7:"/about/";}}}
/*
                $parsedUrl = parse_url($url);
                $fragment = isset($parsedUrl['fragment']) ? '#' . $parsedUrl['fragment'] : '';
                if (isset($parsedUrl['scheme'])) {
                    $baseUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . $parsedUrl['path'];
                } else {
                    if (isset($parsedUrl['host'])) {
                        $baseUrl = $parsedUrl['host'] . $parsedUrl['path'];
                    } else {
                        $baseUrl = isset($parsedUrl['path']) ? $parsedUrl['path'] : '';
                    }
                }
*/
                $parsedUrl = parse_url($url);
                $fragment = isset($parsedUrl['fragment']) ? '#' . $parsedUrl['fragment'] : '';
                if (isset($parsedUrl['scheme'])) {
                    $baseUrl = $parsedUrl['scheme'] . '://';
                    if (isset($parsedUrl['host'])) {
                        $baseUrl .= $parsedUrl['host'];
                        if (isset($parsedUrl['port'])) {
                            $baseUrl .= ':' . $parsedUrl['port'];
                        }
                    }
                    $baseUrl .= isset($parsedUrl['path']) ? $parsedUrl['path'] : '';
                } else {
                    if (isset($parsedUrl['host'])) {
                        $baseUrl = $parsedUrl['host'];
                        if (isset($parsedUrl['port'])) {
                            $baseUrl .= ':' . $parsedUrl['port'];
                        }
                        $baseUrl .= isset($parsedUrl['path']) ? $parsedUrl['path'] : '';
                    } else {
                        $baseUrl = isset($parsedUrl['path']) ? $parsedUrl['path'] : '';
                    }
                }


                if (is_array($slugs_confs)) {
                    if (array_key_exists($to_lan, $slugs_confs)) {
                        $patterns = array();
                        $replacements = array();
                        if ($reverse) {
                            $slugs_orig = $slugs_confs[$to_lan]["url"];
                            $slugs_url = $slugs_confs[$to_lan]["url_orig"];
                        } else {
                            $slugs_orig = $slugs_confs[$to_lan]["url_orig"];
                            $slugs_url = $slugs_confs[$to_lan]["url"];
                        }
                        foreach ($slugs_orig as &$value) {
                            $patterns[] = "~/" . $to_lan . "/" . $value . "/?$~";
                        }
                        foreach ($slugs_url as &$value) {
                            $replacements[] = "/" . $to_lan . "/" . $value ;
                        }
                        $url = preg_replace($patterns, $replacements, $baseUrl);
                        $url = $url . $fragment;
                    }
                }
            }
            return $url;
        }

        function tmy_internal_rewrite_url( $url ) {

            //error_log("IN tmy_internal_rewrite_url =         $url");
            $site_url = home_url();
            if (strpos($url, $site_url) === false) {
                return $url;
            }

            if (strcmp(trim(get_option('g11n_seo_url_enable')),'Yes')===0) {
                //error_log("IN tmy_internal_rewrite_url $url");
                $conf_lans = get_option('g11n_additional_lang');
                //a:3:{s:7:"English";s:5:"en_US";s:14:"Chinese(China)";s:5:"zh_CN";s:18:"Portuguese(Brazil)";s:5:"pt_BR";}

                if (is_array($conf_lans) && (count($conf_lans) > 0)) {
                    $conf_codes = array_values($conf_lans);
                    $pattern = '/' . implode('|', array_map('preg_quote', $conf_lans, array_fill(0, count($conf_lans), '/'))) . '/i';
                    $pattern = strtolower(str_replace('_', '-', $pattern));
                    if (preg_match($pattern, $url, $matches) !== 1) { 
                        // the url does not contain any language code
                        $lang_code = strtolower(str_replace('_', '-', get_locale()));
                        $site_url = get_site_url();
                        $url = str_replace($site_url, $site_url . '/' . esc_attr($lang_code), $url);
                        $url_lang = $lang_code;
                    } else {
                        //$url_lang = $matches[0];
                        return $url;
                    }

                    //error_log("                           " . $pattern . " " . $url_lang);
                    $trans_slug_conf = esc_attr(get_option('g11n_translate_slug','No'));
                    if (strcmp($trans_slug_conf, "")===0) {
                        $trans_slug_conf = "No";
                    }
                    //$trans_slug_conf = "No";
                    if ($trans_slug_conf === "Yes") {
   //error_log(" TRANSLATE SLUG: " . $url); 
                        $slugs_confs = get_option('g11n_slugs_mappings_config_extra', array());
                        if (!is_array($slugs_confs)) {
                            $slugs_confs = array();
                        }
                        //a:2:{s:5:"zh-cn";a:2:{s:3:"url";a:1:{i:0;s:8:"/关于/";}s:8:"url_orig";a:1:{i:0;s:7:"/about/";}}
                        //     s:5:"pt-br";a:2:{s:3:"url";a:1:{i:0;s:7:"/sobre/";}s:8:"url_orig";a:1:{i:0;s:7:"/about/";}}}
    
                        if (is_array($slugs_confs)) {
                            if (array_key_exists($url_lang, $slugs_confs)) {
                                $slugs_orig = $slugs_confs[$url_lang]["url_orig"];
                                $patterns = array();
                                foreach ($slugs_orig as &$value) { 
                                    $patterns[] = "~/" . $url_lang . "/" . $value . "/?$~"; 
                                }
                                $slugs_url = $slugs_confs[$url_lang]["url"];
                                $replacements = array();
                                foreach ($slugs_url as &$value) { 
                                    $replacements[] = "/" . $url_lang . "/" . $value ; 
                                }
   //error_log("PREG REPLACE : " . json_encode($patterns)); 
   //error_log("PREG REPLACE1: " . json_encode($replacements)); 
   //error_log("PREG REPLACE2: " . $url); 
                                //$url = str_replace($slugs_orig, $slugs_url, $url);
                                //$url = preg_replace($slugs_orig, $slugs_url, $url);
                                $url = preg_replace($patterns, $replacements, $url);
                            }
                        }
   //error_log(" TRANSLATE SLUG->" . $url); 
                    }
                }
                //error_log("                         -> $url");
            }
            //error_log(" IN tmy_internal_rewrite_url = --->  $url");
            return $url;
        }

        function rewrite_tag_permalink_post_link( $permalink, $post, $leavename ) {

            if (! is_admin()) {
                return($this->tmy_internal_rewrite_url($permalink));
            }
            return $permalink;
        }



	public function G11nStartSession() {

            if ( WP_TMY_G11N_DEBUG ) {
                global $l10n;
                foreach ($l10n as $name => &$entries) {
                    $entries_count = count((array)$l10n[$name]->entries);
                    $language = isset($l10n[$name]->headers['Language']) ? $l10n[$name]->headers['Language'] : "";
                    error_log(" $name textdomain($entries_count) $language");
                }
            }
            return;
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
                    echo '<br>Visit <a href="' . get_home_url() . '/wp-admin/edit.php?post_type=g11n_translation' . '">Translatio Translations</a> page for all translations';
                    echo '<br>Or, visit <a href="' . get_home_url() . '/wp-admin/admin.php?page=tmy-g11n-dashboard-menu' . '">Translatio Dashboard</a> for translation summary<br>';

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

	public function g11n_query_vars_filter($query_vars) {
            $query_vars[] = 'g11n_tmy_lang_code';
            return $query_vars;
        }

	public function g11n_pre_load_textdomain_filter($loaded, $domain, $mofile, $locale) {


	    $curr_lang = $this->translator->get_preferred_language();
            error_log("In g11n_pre_load_textdomain_filter $loaded, $domain, $mofile, $locale - $curr_lang");
            return $loaded;
        }

	public function g11n_determine_locale_filter($locale_in) {
            //error_log("In g11n_determine_locale_filter " . $locale_in . " uri: " . $_SERVER['REQUEST_URI']);
	    //return $this->translator->get_preferred_language();
        }
	public function g11n_locale_filter($locale_in) {

//error_log("== g11n_locale_filter " . $locale_in . " " . is_admin());
                if ( is_admin() || defined( 'REST_REQUEST' ) && REST_REQUEST ) {
                    return $locale_in; 
                }

                /* array format ((English -> en), ...) */
                $language_options = get_option('g11n_additional_lang', array());
                if (! is_array($language_options)) {
                    return $locale_in; 
                }
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
                        //if ( WP_TMY_G11N_DEBUG ) {
                            error_log("g11n_locale_ filter return based on post_data" . $language_options[$wp_ajax_lang_code]);
                        //}
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

error_log("== g11n_locale_filter calling get_perfered_language" . $locale_in );
		$pre_lang = $this->translator->get_preferred_language();
error_log("== g11n_locale_filter calling get_preferred_language return " . $pref_lang);
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
			'name' => __( 'Translations', 'tmy-globalization' ),
			'singular_name' => __( 'Translation', 'tmy-globalization' )
		      ),
		      'public' => true,
		      'show_ui' => true,
		      'show_in_menu' => 'tmy-g11n-main-menu',
		      'menu_position' => '3',
		      //'show_in_menu' => 'admin.php?page=tmy-g11n-setup-menu',
		      //'show_in_menu' => 'edit.php?post_type=g11n_translation',
                      'show_in_rest' => true,
                      'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'revisions' ),
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

        public function g11n_fusion_default_args_filter($this_args, $type, $args) {

            if ($type === "fusion_button") {
                if (! empty($this_args["link"])) {
                    $this_args["link"] = $this->tmy_internal_rewrite_url($this_args["link"]);
                }
            }
            if ($type === "fusion_title") {
                if (! empty($this_args["link_url"])) {
                    $this_args["link_url"] = $this->tmy_internal_rewrite_url($this_args["link_url"]);
                }
            }

            return $this_args;

        }
        public function g11n_fusion_shortcode_content_filter($content, $type, $args) {

            //error_log("g11n_fusion_shortcode_content_filter content: " . $content . " " . $type . " " . json_encode($args) );
            if (strcmp($type, "fusion_title")===0) {
                //error_log("g11n_fusion_shortcode_content_filter content: " . $content . " " . $type );
                $content_br = html_entity_decode(str_replace(array("\r\n", "\r", "\n"), "<br />", trim($content)),);
                $content_br_html = htmlspecialchars(esc_sql($content_br));
                $language_options = get_option('g11n_additional_lang');
                $g11n_current_language = $this->translator->get_preferred_language();
                if (! is_null($language_options)) {
                    $lang = $language_options[$g11n_current_language];

                    global $wpdb;
                    $sql = "select ID from {$wpdb->prefix}posts where post_title=\"" . esc_sql($content_br_html) . "\" and post_status=\"private\"";
                    $result = $wpdb->get_results($sql);
                    if (isset($result[0]->ID)) {
                        $translation_post_id = $this->translator->get_translation_id($result[0]->ID, $lang, "post", true);
                        if (isset($translation_post_id)) {
                            //$translation_str = html_entity_decode(get_post_field("post_content", $translation_post_id));
                            $translation_str = get_post_field("post_content", $translation_post_id);
                            $translation_str = preg_replace('/\<br(\s*)?\/?\>/i', "\n", $translation_str);
                            $translation_str = html_entity_decode(trim($translation_str));
                            //$translation_str = preg_replace('/\<br(\s*)?\/?\>/i', "\r\n", $translation_str);
                            return $translation_str;
                        }
                        //error_log("g11n_fusion_shortcode_content_filter content after text translator id: " . $translation_post_id);
                        //error_log("g11n_fusion_shortcode_content_filter content after text translator: " . $translation_str);
                    }
                }
                return $content;
            }

            if ((strcmp($type, "fusion_text")===0) && isset($args["dynamic_params"])) {

                //error_log("g11n_fusion_shortcode_content_filter content: " . $type . " " . $content );
                $dp_var = json_decode(base64_decode($args["dynamic_params"]), true);
                $language_options = get_option('g11n_additional_lang');
                $g11n_current_language = $this->translator->get_preferred_language();
                $lang = $language_options[$g11n_current_language];

                if (isset($dp_var["element_content"]["key"]) && (strcmp($dp_var["element_content"]["key"], "Subtitle Page")===0)) {
                    //error_log("g11n_fusion_shortcode_content_filter content: " . $content);
                    //error_log("g11n_fusion_shortcode_content_filter key: " . $dp_var["element_content"]["key"]);

                    return $this->tmy_text_translator( $content, $lang);
                }

                if (isset($dp_var["element_content"]["data"]) && (strcmp($dp_var["element_content"]["data"], "date")===0)) {
                    $orig_content = htmlentities(trim($dp_var["element_content"]["after"]));
                    //error_log(" TRANSLATION : " . $this->tmy_text_translator( $orig_content, $lang));
                    return  $dp_var["element_content"]["before"] .
                            wp_date($dp_var["element_content"]["format"]) . " " .
                            html_entity_decode($this->tmy_text_translator( $orig_content, $lang));

                }
            }
            return $content;
        }

        public function g11n_the_seo_metadesc_filter($meta_description, $presentation) {

            if (! empty($meta_description)) {
                $post_id = get_the_ID();
                $post_type = get_post_type($post_id);
                $language_options = get_option('g11n_additional_lang');
                $g11n_current_language = $this->translator->get_preferred_language();
                $lang = $language_options[$g11n_current_language];
                $translation_post_id = $this->translator->get_translation_id($post_id,
                                                                             $lang,
                                                                             $post_type,
                                                                             false);
                //error_log("g11n_the_seo_metadesc_filter: ". $meta_description . " ID: " . $post_id);
                //error_log("g11n_the_seo_metadesc_filter: ". $lang . " ID: " . $translation_post_id);
                if (isset($translation_post_id)) {
                    $metadesc_value = get_post_meta($translation_post_id, '_yoast_wpseo_metadesc', True);
                    if ( ! empty( $metadesc_value ) ) {
                        error_log("g11n_the_seo_metadesc_filter: ". $metadesc_value);
                        return $metadesc_value;
                    }
                }
            }
            return $meta_description;

        }
        public function g11n_ext_menumeta_filter($avada_meta, $item_id) {

                if (isset( $avada_meta['highlight_label'] ) && ( $avada_meta['highlight_label'] != '' )) {
		    $language_options = get_option('g11n_additional_lang');
                    $g11n_current_language = $this->translator->get_preferred_language();
                    if (! is_null($language_options)) {
                        $lang = $language_options[$g11n_current_language];
                        $avada_meta['highlight_label'] = $this->tmy_text_translator(wp_trim_words($avada_meta['highlight_label']), $lang);
                    }
		}
                if (isset( $avada_meta['select'] ) && ( $avada_meta['select'] != '' ) && ( $avada_meta['select'] != 0 )) {
                     $item_post_type = get_post_type($item_id);
                    if ( strcmp($item_post_type,"nav_menu_item")==0 ) {
                         $g11n_current_language = $this->translator->get_preferred_language();
                         $language_options = get_option('g11n_additional_lang');
                         $language_name = $language_options[$g11n_current_language];

                         $translation_post_id = $this->translator->get_translation_id($avada_meta['select'],
                                                                                 $language_name,
                                                                                 "fusion_element",
                                                                                 false);

                         if ( WP_TMY_G11N_DEBUG ) {
                             error_log("g11n_ext_menumeta_filter avada megamenu: ID " . $item_id. " " . $item_post_type . " " . $language_name);
                             error_log("g11n_ext_menumeta_filter avada megamenu: select " . $avada_meta['select']);
                             error_log("g11n_ext_menumeta_filter avada megamenu: transid " . $translation_post_id);
                             //error_log("g11n_ext_menumeta_filter avada megamenu: meta " . json_encode($avada_meta));
                         }
                         if (isset($translation_post_id)) {
                             $avada_meta['select']=$translation_post_id;
                         }
                    }
                }
                 return $avada_meta;
        }
	
	public function g11n_ext_translator_filter($post_id, $post_type, $ext1 = false, $ext2 = null) {

	       if ( strcmp($post_type,"content")==0 ) {
                   $wp_post_type = get_post_type($post_id);
                   if ( tmy_g11n_is_valid_post_type($wp_post_type) && (strcmp($wp_post_type,"fusion_tb_section")==0)) {
                       $g11n_current_language = $this->translator->get_preferred_language();
                       $language_options = get_option('g11n_additional_lang');
                       $language_name = $language_options[$g11n_current_language];
                       //error_log("In g11n_ext_translator_filter: content" . $wp_post_type . "-lang: " . $language_name);
                       $translation_post_id = $this->translator->get_translation_id($post_id,
                                                                                $language_name,
                                                                                $wp_post_type,
                                                                                false);
                       //error_log("In g11n_ext_translator_filter: footer tran id " . $translation_post_id);
                       if (isset($translation_post_id)) {
                           return $translation_post_id;
                   }

                   }
               }

               if (( strcmp($post_type,"footer")==0 ) || ( strcmp($post_type,"header")==0 )) {
                   $wp_post_type = get_post_type($post_id);
                   if ( tmy_g11n_is_valid_post_type($wp_post_type) && (strcmp($wp_post_type,"fusion_tb_section")==0)) {
                       $g11n_current_language = $this->translator->get_preferred_language();
                       $language_options = get_option('g11n_additional_lang');
                       if (is_array($language_options)) {
                           $language_name = $language_options[$g11n_current_language];
	                   //error_log("In g11n_ext_translator_filter: footer" . $wp_post_type . "-lang: " . $language_name);
                           $translation_post_id = $this->translator->get_translation_id($post_id,
                                                                                $language_name,
                                                                                $wp_post_type,
                                                                                false);
		       }
	               //error_log("In g11n_ext_translator_filter: footer tran id " . $translation_post_id);
                       if (isset($translation_post_id)) {
                           return $translation_post_id;
                   }

		   }
	       }
               if ( tmy_g11n_is_valid_post_type($post_type) && (strcmp($post_type,"fusion_element")==0)) {
                   $parent_post_id = get_the_ID();
                   $parent_post_type = get_post_type($parent_post_id);
                   $parent_lang = get_post_meta($parent_post_id,'g11n_tmy_lang',true);
                   $translation_post_id = $this->translator->get_translation_id($post_id,
                                                                                $parent_lang,
                                                                                $post_type,
                                                                                false);
                   if ( WP_TMY_G11N_DEBUG ) {
			   error_log("In g11n_ext_translator_filter: " . $parent_post_id . " " . $parent_post_type . " " . 
				                                              $post_id . " " . $post_type . " " . $translation_post_id);
                   }
                   if (( strcmp($parent_post_type,"g11n_translation")==0) && (isset($translation_post_id))) {
                       return $translation_post_id;
                   }
               }
               return $post_id;
	}

	public function g11n_the_post_filter($post, $query) {

                            $parent_post_id = get_the_ID();
                            $parent_post_type = get_post_type($parent_post_id);

                        if ( tmy_g11n_is_valid_post_type($post->post_type) && (strcmp($post->post_type,"fusion_elementAAA")==0)) {

                            $parent_lang = get_post_meta($parent_post_id,'g11n_tmy_lang',true);

                            $translation_post_id = $this->translator->get_translation_id($post->ID,
                                                                                         $parent_lang,
                                                                                         $post->post_type,
                                                                                         false);

                            if ( WP_TMY_G11N_DEBUG ) {
                                error_log("Fusion Element g11n_the_post_filter, post_id" . esc_attr($post->ID) . " post_type: " . esc_attr($post->post_type));
                                error_log("Fusion Element g11n_the_post_filter, parent" . esc_attr($parent_post_id) . " post_type: " . esc_attr($parent_post_type));
                                error_log("Fusion Element g11n_the_post_filter, parent lang" . $parent_lang);
                                error_log("Fusion Element g11n_the_post_filter, translation_id=[" . $translation_post_id . "]");
                            }
                            if (( strcmp($parent_post_type,"g11n_translation")==0) && (isset($translation_post_id))) {

                                error_log("Fusion Element g11n_the_post_filter, post_id" . esc_attr($post->ID) . " post_type: " . esc_attr($post->post_type));
                                error_log("Fusion Element g11n_the_post_filter, parent" . esc_attr($parent_post_id) . " post_type: " . esc_attr($parent_post_type));
                                error_log("Fusion Element g11n_the_post_filter, translation found=[" . $translation_post_id . "]");
                                $post->post_content=wpautop(get_post_field("post_content", $translation_post_id));
                                error_log("Fusion Element g11n_the_post_filter, translation contents=[" . $post->post_content . "]");
                            }
			}
	}


	public function g11n_the_posts_filter($posts, $query = false) {

                    if( is_search() ){
                    }

	            foreach ( $posts as $post ) {

                        if ( WP_TMY_G11N_DEBUG ) {
                            error_log("In g11n_the_posts_filter, post_id: " . esc_attr($post->ID) . " post_type: " . esc_attr($post->post_type));
                        }
		        if ( tmy_g11n_is_valid_post_type($post->post_type) && (strcmp($post->post_type,"fusion_tb_section")==0)) {

                            $g11n_current_language = $this->translator->get_preferred_language();
                            $language_options = get_option('g11n_additional_lang');
                            $language_name = $language_options[$g11n_current_language];
                            $translation_post_id = $this->translator->get_translation_id($post->ID,
                                                                                         $language_name,
                                                                                         $post->post_type,
                                                                                         false);
                            //if ( WP_TMY_G11N_DEBUG ) {
                                error_log("fusion_tb_section In g11n_the_posts_filter, excerpt post_id: " . esc_attr($post->ID) . " language: " . esc_attr($language_name));
                                error_log("fusion_tb_section In g11n_the_posts_filter, translation_id:  " . esc_attr($translation_post_id));
                                error_log("fusion_tb_section In g11n_the_posts_filter, SESSION:  " . esc_attr($_SESSION['g11n_language']));
                            //}
                            if (isset($translation_post_id)) {
                                $post->post_content=wpautop(get_post_field("post_content", $translation_post_id));
                            }

			}
		        if ( tmy_g11n_is_valid_post_type($post->post_type) && (strcmp($post->post_type,"fusion_element")==0)) {

                            $parent_post_id = get_the_ID();
                            $parent_post_type = get_post_type($parent_post_id);
			    $parent_lang = get_post_meta($parent_post_id,'g11n_tmy_lang',true);

			    $translation_post_id = $this->translator->get_translation_id($post->ID,
                                                                                         $parent_lang,
                                                                                         $post->post_type,
                                                                                         false);

                            if ( WP_TMY_G11N_DEBUG ) {
                                error_log("Fusion Element g11n_the_posts_filter, post_id" . esc_attr($post->ID) . " post_type: " . esc_attr($post->post_type));
                                error_log("Fusion Element g11n_the_posts_filter, parent" . esc_attr($parent_post_id) . " post_type: " . esc_attr($parent_post_type));
                                error_log("Fusion Element g11n_the_posts_filter, parent lang" . $language_name);
                                error_log("Fusion Element g11n_the_posts_filter, parent lang" . $parent_lang);
                                error_log("Fusion Element g11n_the_posts_filter, translation_id=[" . $translation_post_id . "]");
			    }
		            if (( strcmp($parent_post_type,"g11n_translation")==0) && (isset($translation_post_id))) {

                                error_log("Fusion Element g11n_the_posts_filter, post_id" . esc_attr($post->ID) . " post_type: " . esc_attr($post->post_type));
                                error_log("Fusion Element g11n_the_posts_filter, parent" . esc_attr($parent_post_id) . " post_type: " . esc_attr($parent_post_type));
                                error_log("Fusion Element g11n_the_posts_filter, translation found=[" . $translation_post_id . "]");
                                $post->post_content=wpautop(get_post_field("post_content", $translation_post_id));
                                //$post->post_content="AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA";
                                error_log("Fusion Element g11n_the_posts_filter, translation contents=[" . $post->post_content . "]");
			    }

			}

                        if ( tmy_g11n_is_valid_post_type($post->post_type) && (! empty($post->post_content))) {

                            $g11n_current_language = $this->translator->get_preferred_language();

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
                                $post->post_content=wpautop(get_post_field("post_content", $translation_post_id));
                            }
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

                    if ((! ( is_admin() || defined( 'REST_REQUEST' ) && REST_REQUEST )) &&
                        (strcmp(get_current_theme(), "Avada") !== 0)) {

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

			 //don't update contents here but in the posts_filter, compatibility with Avada 15 Dec 2023
			
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
			//return $input . "<br>" . $switcher_html;
                    }
		    return $input;
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

        public function g11n_pre_get_document_title_filter( $title ) {

            $post_id = get_the_ID();
            if ($post_id) {
                $post_type = get_post_type($post_id);
                $language_options = get_option('g11n_additional_lang');
                $g11n_current_language = $this->translator->get_preferred_language();
                $language_name = $language_options[$g11n_current_language];
                $translation_post_id = $this->translator->get_translation_id($post_id,$language_name,$post_type,false);
                if (isset($translation_post_id)) {
                    return get_post_field("post_title", $translation_post_id) . " - " . get_bloginfo("name");
                }
            }
            return $title;

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
              'translatio-lang-block',
              plugin_dir_url(__DIR__) . 'includes/tmy-block-language-switcher.js',
              array('wp-blocks','wp-editor','wp-server-side-render'),
              true
            );

            $return = register_block_type('translatio/translatio-chooser-box', array(
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

error_log("in template direct");
            if (! is_admin()) {
                $lang_var = tmy_g11n_lang_sanitize(filter_input(INPUT_GET, 'g11n_tmy_lang', FILTER_SANITIZE_SPECIAL_CHARS));

                $all_configed_langs = get_option('g11n_additional_lang'); /* array format ((English -> en), ...) */
                $lang_var_code_from_query = filter_input(INPUT_GET, 'g11n_tmy_lang_code', FILTER_SANITIZE_SPECIAL_CHARS);
                $lang_var_code_from_query = str_replace('-', '_', $lang_var_code_from_query);

                if (empty($lang_var_code_from_query)) {
                    $request_uri = sanitize_url($_SERVER['REQUEST_URI']);
                    $home_root = parse_url( home_url() );
                    if ( isset( $home_root['path'] ) ) {
                        $request_uri = str_replace($home_root['path'], "", $request_uri);
                    }
                    $request_uri = ltrim($request_uri,'/');
                    if (is_array($all_configed_langs) && (count($all_configed_langs) > 0)) {
                        $pattern = '/^(' . implode('|', array_map('preg_quote', $all_configed_langs, array_fill(0, count($all_configed_langs), '/'))) . ')(\/.*)?$/';
                        $pattern = strtolower(str_replace('_', '-', $pattern));
                        if (preg_match($pattern, $request_uri, $matches)) {
                            $lang_var_code_from_query = str_replace('-', '_', $matches[1]);
                        }
                    }
                }

                if (!empty($lang_var_code_from_query)) {
                    $lang_var = array_search(strtolower($lang_var_code_from_query), array_map('strtolower',$all_configed_langs));
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
error_log("exit template direct");
        }

	public function tmy_g11n_html_head_handler() {

            //<link rel="alternate" hreflang="de" href="https://de.example.com/index.html" />
            //<link rel="alternate" href="https://example.com/country-selector" hreflang="x-default" />

            $all_langs = get_option('g11n_additional_lang');
            $default_lang = get_option('g11n_default_lang');
            //unset($all_langs[$default_lang]);
            global $wp;
            $site_url = get_site_url();

            $language_options = get_option('g11n_additional_lang');
            $g11n_current_language = $this->translator->get_preferred_language();
            $language_name = $language_options[$g11n_current_language];
            $language_name = strtolower(str_replace("_", "-", $language_name));

            $g11n_lang = $this->translator->get_preferred_language();
            echo '<meta http-equiv="content-language" content="' . $language_name . '">';
            echo '<meta http-equiv="translatio-tmy-ref-id" content="' . get_the_ID() . '">';

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
        public function tmy_translation_get_text_filter( $text ) {
            //error_log("tmy_translation_get_text_filter: " . $text);
            $language_options = get_option('g11n_additional_lang');
            $g11n_current_language = $this->translator->get_preferred_language();
            if (! is_null($language_options)) {
                $lang = $language_options[$g11n_current_language];
                $text = $this->tmy_text_translator(wp_trim_words($text), $lang);
            }
            return $text;
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

        public function tmy_nav_menu_item_filter( $menu_item_data, $menu_item_db_id, $args) {

            return $menu_item_data;
        }

        public function tmy_wp_nav_menu_item_filter( $items, $args ) {

            //$pattern = '/href="([^"]+)"/';
            $pattern = '/href\s*=\s*"([^"]+)"/';
            $items = preg_replace_callback($pattern,
                         function ($matches) {
                             return "href=\"" . $this->tmy_internal_rewrite_url($matches[1]) . "\"";
                         },
                         $items);

            return $items;
        }

        public function tmy_nav_menu_item_args_filter( $args, $item, $depth ) {

            return $args;
        }
        public function tmy_nav_menu_item_title_filter( $title, $menu_item, $args, $depth ) {

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

            error_log("tmy_woocommerce_option_filter option, " . $option);

            if (($option === "woocommerce_cheque_settings") || ($option === "woocommerce_cod_settings")) {
                $language_options = get_option('g11n_additional_lang');
                $g11n_current_language = $this->translator->get_preferred_language();
                if (! is_null($language_options)) {
                   // $language_options = array();
                   // error_log(" 1307 1307 : " . json_encode($language_options));
                    $lang = $language_options[$g11n_current_language];

                    $value["title"] = $this->tmy_text_translator( $value["title"], $lang);
                    $value["description"] = $this->tmy_text_translator( $value["description"], $lang);
                    $value["instructions"] = $this->tmy_text_translator( $value["instructions"], $lang);

                }
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

            $all_configed_langs = get_option('g11n_additional_lang',array());
            //a:3:{s:7:"English";s:5:"en_US";s:14:"Chinese(China)";s:5:"zh_CN";s:18:"Portuguese(Brazil)";s:5:"pt_BR";}

            if (is_array($all_configed_langs) && (count($all_configed_langs) > 0)) {
                $conf_codes = array_values($all_configed_langs);
                $langs_pattern = '/' . implode('|', array_map('preg_quote', $all_configed_langs, array_fill(0, count($all_configed_langs), '/'))) . '/i';
                $langs_pattern = strtolower(str_replace('_', '-', $langs_pattern));
            }
            foreach ($sorted_menu_items as $menu_index => &$menu_item) {

//error_log("MENU ITEM:  " . $menu_item->url);
                $current_url = sanitize_url($_SERVER['REQUEST_URI']);
                $site_url = get_site_url();
                $current_url_arr = wp_parse_url($current_url);
                $site_url_arr = wp_parse_url($site_url);
                $url_arr = wp_parse_url($menu_item->url);
                if (isset($url_arr['query'])) {
                    parse_str($url_arr['query'], $url_query_arr);
                    if (array_key_exists("tmy_dynamic_main", $url_query_arr)) {
                        $tmy_dynamic_main = True;
                        $tmy_dynamic_index = $menu_index;
                        //$menu_item->url = "/";
                        $menu_item->url = $current_url;
                        continue;
                    }
                    if (array_key_exists("tmy_dynamic_url", $url_query_arr)) {
                        $current_url_switcher = urldecode(sanitize_url($_SERVER['REQUEST_URI']));
                        $current_url_switcher = $this->tmy_internal_translate_url( $current_url_switcher, $current_locale, true );
                        $current_url_arr_switcher = wp_parse_url($current_url_switcher);

                        $new_part = str_replace($site_url_arr["path"], "", $current_url_arr_switcher["path"]);
                        $url_lang = $url_query_arr["tmy_dynamic_url"];
                        $flag_lang = str_replace("-", "_", $url_lang);

                        if  ($current_seo_option === 'Yes') {
                            parse_str($_SERVER['QUERY_STRING'], $query_str_arr);
                            if (array_key_exists("g11n_tmy_lang_code", $query_str_arr)) {
                                unset($query_str_arr["g11n_tmy_lang_code"]);
                            }
                            if (preg_match($langs_pattern, $new_part, $matches) !== 1) {
                                // the part does not contain any language code
                                $new_part = "/" . $url_lang . $new_part; 
                            } else {
                                $new_part = str_replace($current_locale, $url_lang, $new_part);
                            }
                            $new_herf = $site_url . $new_part;
                  //error_log(" MENU LINK:  " . $new_herf);
                            $new_herf = $this->tmy_internal_translate_url( $new_herf, $url_lang, false );
                  //error_log(" MENU LINK: >" . $new_herf);
                            //$new_herf = $site_url . "/" . $url_lang . $new_part;
                            //$menu_item->url = esc_url(add_query_arg($query_str_arr, $new_herf));
                            $menu_item->url = esc_url($new_herf);
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
                    if ((isset($url_arr['host'])) && (isset($site_url_arr['host']))) {
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
                $menu_item->url = $this->tmy_internal_translate_url( $menu_item->url, $current_locale, false );
//error_log(" MENU ITEM:>" . $menu_item->url);
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


            return $atts;

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

        public function g11n_parse_request_action( $query ) {
            return $query;
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

        public function tmy_g11n_frontend_jquery_call() {

            if ( isset( $_POST['_wpnonce'] ) && ! empty( $_POST['_wpnonce'] ) ) {
                $nonce  = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING );
                if ( ! wp_verify_nonce( $nonce, $action ) )
                    wp_die( 'Nope! Security check failed!' );
            }

            $return_arr = array();
            $return_code = 0;
            $return_message = "";

            if ( isset( $_POST['operation'] )) {

                global $wpdb;
                $default_lang = get_option('g11n_default_lang');
                $language_options = get_option('g11n_additional_lang');
                $default_lang_code=$language_options[$default_lang];

                switch ( esc_attr($_POST['operation']) ) {
                    case 'tmy_ops_get_translation_table':
                        //error_log('tmy_ops_get_translation_table: ' . json_encode($_POST));
                        //{"action":"tmy_g11n_frontend_jquery_call","operation":"tmy_ops_get_translation_table","language":"zh-cn","referenceid":"2422"}
                        $referenceid = esc_sql($_POST['referenceid']);
                        $text_lang = esc_sql($_POST['language']);

                        $parts = explode('-', $text_lang);
                        if (count($parts) > 1) {
                            $lastPart = array_pop($parts);
                            $lang_code = implode('_', $parts) . '_' . strtoupper($lastPart);
                        } else {
                            $lang_code = $text_lang;
                        }

                        $page_place_holder_title = $referenceid . "-page-translation";
                        $sql = "select ID from {$wpdb->prefix}posts where post_title=\"" . esc_sql($page_place_holder_title) . "\" and post_status=\"private\"";
                        $result = $wpdb->get_results($sql);
                        $return_arr = array();
                        if (isset($result[0]->ID)) {
                            $orig_string =  get_post_field('post_content', $result[0]->ID);
                            $orig_string_array = explode('<br>', $orig_string);
                            $orig_string_array = array_map('trim', $orig_string_array);
                            $translation_id = $this->translator->get_translation_id($result[0]->ID,$lang_code,"post");
                            if (isset($translation_id)) {
                               $translation_content =  get_post_field('post_content', $translation_id);
                               $translation_content_array = explode("<!--end-->", $translation_content);
                               foreach ($translation_content_array as $item) {
                                  if (preg_match('/<!--(.*?)-->(.*)/s', trim($item), $matches)) {
                                      //echo "[" . trim($matches[1]) . "] -> [" . $matches[2] . "]\n";
                                      $return_arr[trim($matches[1])] = trim($matches[2]);
                                  }
                               }
                            }
                        } else {
                         //   $language_options = get_option('g11n_additional_lang');
                         //   $results = $wpdb->get_results("select ID, post_title from {$wpdb->prefix}posts where post_type='post' and post_status='private'");
                         //   foreach ($results as $result) {
                         //       //error_log("TRANSLATION TABLE: " . $result->ID . " " . $result->post_title);
                         //       foreach ($language_options as $language => $code) {
                         //           $translation_id = $this->translator->get_translation_id($result->ID,$code,"post");
                         //           if (isset($translation_id)) {
                         //               //error_log("    id $translation_id found for $code");
                         //               //error_log("        translation: " . get_post_field('post_content', $translation_id));
                         //               $return_arr[$result->post_title][strtolower(str_replace('_', '-', $code))] = get_post_field('post_content', $translation_id);
                         //           }
                         //       }
                         //   }
                            //error_log("TRANSLATION TABLE: " . json_encode($return_arr));
                        }
                        break;

                    case 'tmy_ops_get_translation':
                        $text_str = esc_sql($_POST['text']);
                        $text_lang = esc_sql($_POST['language']);

                        $parts = explode('-', $text_lang);

                        if (count($parts) > 1) {
                            $lastPart = array_pop($parts);
                            $lang_code = implode('_', $parts) . '_' . strtoupper($lastPart);
                        } else {
                            $lang_code = $text_lang;
                        }

                        $return_arr = array();


                        $orig_post_id = 0;
                        if ($default_lang_code === $lang_code) {
                            $sql = "select ID from {$wpdb->prefix}posts where post_content=\"" . esc_sql($text_str) . "\" and 
                                                                              post_type=\"post\" and 
                                                                              post_status=\"private\"";
                            $result = $wpdb->get_results($sql);
                            if (isset($result[0]->ID)) {
                                $orig_post_id = $result[0]->ID;
                            }
                        } else {
                            $sql = "select ID from {$wpdb->prefix}posts, {$wpdb->prefix}postmeta where 
                                    post_content=\"" . esc_sql($text_str) . "\" and 
                                    ID={$wpdb->prefix}postmeta.post_id and
                                    meta_value=\"" . esc_sql($lang_code) . "\"";
                            $result = $wpdb->get_results($sql);
                            if (isset($result[0]->ID)) {
                                $orig_post_id = get_post_meta($result[0]->ID,"orig_post_id", true);
                            }
                        }
                        //$g11n_current_language = $this->translator->get_preferred_language();
                        //error_log("JQUERY get_translation sql: " . $sql);
                        //error_log("JQUERY get_translation lang_code: " . $lang_code . " orig post id: " . $orig_post_id);

                        if (is_array($language_options)) {
                            foreach ($language_options as $language => $code) {
                                $text_str_new = $text_str;
                                if (($code === $default_lang_code) && ($orig_post_id <> 0)) {
                                    $text_str_new = get_post_field('post_content', $orig_post_id);
                                }
                                if (($code <> $default_lang_code) && ($orig_post_id <> 0)) {
                                    $translation_id = $this->translator->get_translation_id($orig_post_id,$code,"post");
			            if (isset($translation_id)) {
                                        $text_str_new = get_post_field('post_content', $translation_id);
                                    }
                                }

                                $return_arr[] = array('language' => $language,
                                                      'code' => $code,
                                                      'text' => $text_str_new
                                                     );
                            }
                        }
                        if ($orig_post_id <> 0) {
                            $return_message .= "Found Translation Ref". $orig_post_id;
                        } else {
                            $return_message .= "New Translation";
                        }
                        break;

                    case 'tmy_ops_save_page_translation':

                        //error_log("tmy_ops_save_page_translation: " . json_encode($_POST));
                        $object_var = esc_sql($_POST['obj']);
                        $referenceid = esc_sql($_POST['referenceid']);
                        //error_log("tmy_ops_save_page_translation id: " . $referenceid);
                        $holder_id = 0;
                        $page_place_holder_title = $referenceid . "-page-translation";

                        $page_table = "";
                        foreach($object_var as $item) {
                            $page_table .= "<!--" . $item . "-->" . $item . "<!--end--><br>\n";
                        }
                        //error_log("page table:" . $page_table);

                        $sql = "select ID from {$wpdb->prefix}posts where post_title=\"" . esc_sql($page_place_holder_title) . "\" and post_status=\"private\"";
                        //error_log("tmy_ops_save_page_translation sql: " . $sql);
                        $result = $wpdb->get_results($sql);
                        if (! isset($result[0]->ID)) {
                             $holder_id = wp_insert_post(
                                  array(
                                       'post_title'    => esc_attr($page_place_holder_title),
                                       //'post_content'  => implode("<br>\n", $object_var),
                                       'post_content'  => $page_table,
                                       'post_status' => 'private',
                                       'post_type'  => "post"
                                 ));
                         } else {
                             $holder_id = $result[0]->ID;
                             wp_insert_post(
                                  array(
                                       'ID' => $holder_id,
                                       'post_title'    => esc_attr($page_place_holder_title),
                                       //'post_content'  => implode("<br>\n", $object_var),
                                       'post_content'  => $page_table,
                                       'post_status' => 'private',
                                       'post_type'  => "post"
                                 ));
                        }
                        $this->translator->_tmy_create_sync_translation($holder_id, "post");

                        $return_message .= "Saved for Page Transatlion Ref" . $holder_id;
                        break;

                    case 'tmy_ops_save_translation':
                        if (isset($_POST['obj'])) {
                            $object_var = $_POST['obj']; 
                        } else {
                            $object_var = array();
                        }
                        $sentencelist = $_POST['sentencelist'];
                        error_log("tmy_ops_save_translation sentencelist: " . count($sentencelist));
                        //if (! is_array($object_var)) { $object_var = array(); }
                        $text_lang = esc_sql($_POST['language']);
                        $referenceid = esc_sql($_POST['referenceid']);
                        $parts = explode('-', $text_lang);
                        if (count($parts) > 1) {
                            $lastPart = array_pop($parts);
                            $lang_code = implode('_', $parts) . '_' . strtoupper($lastPart);
                        } else {
                            $lang_code = $text_lang;
                        }
                        error_log("tmy_ops_save_translation refID $referenceid, lang_code: " . $lang_code);
                      
                        $valid_trans_pattern = array();
                        $valid_trans_replacement = array();
                        foreach ($object_var as $orig => $value) {
                            if ($orig !== $value) {
                                //$valid_trans_pattern[] = "/" . preg_quote($orig, '/') . "/u";
                                $valid_trans_pattern[] = "/" . preg_quote($orig, '/') . "/";
                                $valid_trans_replacement[$orig] = $value;
                            }
                        }
                        //error_log("LINES: " . print_r($valid_trans_pattern, true));
                        //error_log("LINES: " . print_r($valid_trans_replacement, true));

                        $translation_id = $this->translator->get_translation_id($referenceid, $lang_code, get_post_type($referenceid));
                        if (isset($translation_id)) {
                            error_log("find translation id " . $translation_id);
                            $dep_list = array();
                            $dep_queue = array();
                            $dep_queue[] = $translation_id;
                            while (count($dep_queue) > 0) {
                                $dep_post_id = array_shift($dep_queue);
                                $dep_post_content = get_post_field('post_content', $dep_post_id);
                                $dep_post_id_need_update = false;
                                $dep_post_content =
                                    preg_replace_callback($valid_trans_pattern, function ($matches) use (&$match_count,
                                                                                                         &$valid_trans_replacement,
                                                                                                         &$dep_post_id_need_update,
                                                                                                         &$object_var,
                                                                                                         &$dep_post_id) {
                                    $dep_post_id_need_update = true;
                                    $match_count = $match_count + 1;
                                    unset($object_var[$matches[0]]);
                                    return $valid_trans_replacement[$matches[0]];
                                }, $dep_post_content);
    
                                $pattern = "/fusion_global id=\"([^\]]*)\"/";
                                preg_match_all($pattern, $dep_post_content, $dep_matches);
                                foreach ($dep_matches[1] as $global_id) {
                                    if (! in_array($global_id, $dep_queue)) {
                                        $dep_queue[] = $global_id;
                                    }
                                }
                                $dep_list[] = $dep_post_id;
                                if ($dep_post_id_need_update) {
                                    error_log("[" . $dep_post_id . "] needs update");
                                    $post_data = array(
                                        'ID'           => $dep_post_id,
                                        'post_content' => $dep_post_content,
                                    );
                                    wp_update_post($post_data);
                                }
                            }
                        } else {
                            error_log("could not find translation id");
                        }

                        $holder_id = 0;
                        $page_place_holder_title = $referenceid . "-page-translation";
                        $sql = "select ID from {$wpdb->prefix}posts where post_title=\"" . esc_sql($page_place_holder_title) . "\" and post_status=\"private\"";
                        $result = $wpdb->get_results($sql);

                        //$orig_array = array_keys($object_var);
                        $page_table = "";
                        $translation_table = "";
                        foreach ($sentencelist as $sentence) {
                            $page_table .= "<!--" . $sentence . "-->" . $sentence . "<!--end--><br>\n";
                        }
                        foreach($object_var as $key => $value) {
                            $translation_table .= "<!--" . $key . "-->" . $value . "<!--end--><br>\n";
                        }

                        if (! isset($result[0]->ID)) {
                            //create place holder post, start translation, and update the current language translation
                            $holder_id = wp_insert_post(
                                  array(
                                       'post_title'    => esc_attr($page_place_holder_title),
                                       //'post_content'  => implode("<br>\n", $orig_array),
                                       'post_content'  => $page_table,
                                       'post_status' => 'private',
                                       'post_type'  => "post"
                                 ));
                            $this->translator->_tmy_create_sync_translation($holder_id, "post");
                        } else {
                            $holder_id = $result[0]->ID;
                            $holder_id = wp_insert_post(
                                  array(
                                       'ID' => $holder_id,
                                       'post_title'    => esc_attr($page_place_holder_title),
                                       //'post_content'  => implode("<br>\n", $orig_array),
                                       'post_content'  => $page_table,
                                       'post_status' => 'private',
                                       'post_type'  => "post"
                                 ));
                        }
                        if ($holder_id <> 0) {
                            //get the corresponding translation id, update the translation
                            $translation_id = $this->translator->get_translation_id($holder_id, $lang_code, "post");
                            //error_log("translation id: " . json_encode($translation_id));
                            if (isset($translation_id)) {
                                 wp_insert_post(
                                  array(
                                       'ID' => $translation_id,
                                       'post_title'    => esc_attr($page_place_holder_title),
                                       'post_content'  => $translation_table,
                                       'post_status' => 'private',
                                       'post_type'  => "g11n_translation"
                                 ));
                            }
                        }
                        $return_message .= "Saved Ref" . $holder_id;
                        break;
                }
            }
            echo json_encode(array("return_data" => $return_arr,
                                   "return_code" => $return_code,
                                   "return_is_default_lang" => ($default_lang_code === $lang_code) ? true : false,
                                   "return_message" => $return_message
                                  )
                            );
            wp_die();
        }
}
