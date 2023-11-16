<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @since      1.0.0
 *
 * @package    TMY_G11n
 * @subpackage TMY_G11n/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    TMY_G11n
 * @subpackage TMY_G11n/includes
 * @author     Yu Shao <yu.shao.gm@gmail.com>
 */
class TMY_G11n_Translator {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      TMY_G11n_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $translation_server;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

	}

	/**
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {


	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the TMY_G11n_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	public function rest_get_translation_server( $rest_url ) {

                if (strpos($rest_url,'version') !== false) {
                    $accept_fmt="application/vnd.zanata.Version+json";
                } else {
                    $accept_fmt="application/json";
                }

                $args = array(
                    'headers' => array('X-Auth-User' => get_option('g11n_server_user'),
                                       'X-Auth-Token' => get_option('g11n_server_token'),
                                       'Accept' => $accept_fmt),
                    'timeout' => 20
                );
                $response = wp_remote_get( $rest_url, $args );
                $http_response_code = wp_remote_retrieve_response_code( $response );
		$translation_server_log_messages = "Response Code: " . $http_response_code;

                if ( is_array( $response ) && ! is_wp_error( $response ) ) {
                    $output = $response['body'];
		    $payload = json_decode($output);
                } else {
		    $translation_server_log_messages .= ' Error: ' . $response->get_error_message();
		}

		$return_array = array('payload' => $payload,
				 'server_msg' => $translation_server_log_messages,
				 'http_code' => $http_response_code
				);
		return $return_array;

	}

	public function sync_translation_from_server( $post_id, $name_prefex, $language_name ) {

		//$name_prefex = "WordpressG11nAret-" . $wp_query->post->post_type . "-";
		//This part of the code will get translation directly from Translation server.
		//
		//$output = g11n_get_translation_server_rest($postid, $name_prefex, $language_name);
		//$payload = json_decode($output);
		//if (isset($payload->textFlowTargets[0]->content)) {
		//    $translation = $payload->textFlowTargets[0]->content;
		//} else {
		//    $translation = "NO TRANSLATION";
		//}

		$ch = curl_init();
		curl_reset($ch);

		$rest_url = rtrim(get_option('g11n_server_url'),"/") . "/rest/projects/p/" .
			    get_option('g11n_server_project') . "/iterations/i/" .
			    get_option('g11n_server_version') . "/r/";
		$rest_url .= $name_prefex . $postid . "/translations/" . $language_name . "?ext=gettext&ext=comment";


                if ( WP_TMY_G11N_DEBUG ) {
                    error_log("In sync_translation_from_server, " . esc_attr($post_id));
                }

                $args = array(
                    'headers' => array('X-Auth-User' => get_option('g11n_server_user'),
                                       'X-Auth-Token' => get_option('g11n_server_token'),
                                       'Accept' => 'application/json'),
                    'timeout' => 10
                );
                $response = wp_remote_get( $rest_url, $args );
                $http_response_code = wp_remote_retrieve_response_code( $response );

                if ( is_array( $response ) && ! is_wp_error( $response ) ) {
                    $output = $response['body'];
                    //$payload = json_decode($output);
                } else {
                    if ( WP_TMY_G11N_DEBUG ) {
                         error_log('In sync_translation_from_server, Error: ' . esc_attr($response->get_error_message()));
                    }
                }

		return $output;

	}

	public function push_contents_to_translation_server( $file_name, $contents_array ) {

                if ((strcmp('', get_option('g11n_server_user','')) !== 0) && 
                    (strcmp('', get_option('g11n_server_token','')) !== 0)) {

		    $ch = curl_init();
		    $payload_contents_array = array();
		    foreach ($contents_array as &$con) {
		        $con_id = md5($con);
		        array_push($payload_contents_array, array("extensions" => array(array("object-type" => "pot-entry-header",
				                                        "references" => array(),
				                                        "flags" => array(),
				                                        "context" => "")),
				                        "lang" => "en-US",
				                        "id" => "$con_id",
				                        "plural" => false,
				                        "content" => "$con"
				                       ));
		    }
		    $payload = array("name" => "$file_name",
			     "contentType" => "application/x-gettext",
			 "lang" => "en-US",
			     "extensions" => array(array("object-type" => "po-header",
				                   "entries" => array(),
				                   "comment" => "Globalization Wordpress plugin")),
			     "textFlows" => $payload_contents_array
			    );

		    $payload_string = json_encode($payload);
		    $rest_url = rtrim(get_option('g11n_server_url'),"/") . "/rest/projects/p/" . 
			    get_option('g11n_server_project') . "/iterations/i/" . 
			    get_option('g11n_server_version') . "/r/";
		    $rest_url .= $file_name;
		    $rest_url .= "?ext=gettext";

                    /***********************************************************************/
                    $args = array(
                        'headers' => array('X-Auth-User' => get_option('g11n_server_user'),
                                           'X-Auth-Token' => get_option('g11n_server_token'),
                                           'Content-Type' => 'application/json'),
                        'method' => 'PUT',
                        'body' => $payload_string,
                        'timeout' => 10
                    );
                    $response = wp_remote_post( $rest_url, $args );

                    if ( is_array( $response ) && ! is_wp_error( $response ) ) {
                        $output = $response['body'];
                        $payload = json_decode($output);
                    } else {
                        error_log("In push_contents_to_translation_server, Error: " . esc_attr($response->get_error_message()));
                    }
		    $return_msg = "Sent for translation " . $rest_url;
		    $return_msg .= " server return: " . wp_remote_retrieve_response_code( $response );


                    if (strcmp($output,'')===0 ) {
		        $return_msg .= "  output : " . "Successful";
                        $output = "Successful";
                    } else {
		        $return_msg .= "  output : " . $output;
                    }

                    $g11n_res_filename = preg_split("/-/", $file_name);
                    $default_post_id = $g11n_res_filename[2];

		    //$return_msg .= "  id : " . get_the_ID();
		    $return_msg .= "  id : " . $default_post_id;
    
		    //error_log("Server Push: " . $return_msg);
		    update_post_meta( $default_post_id, 'translation_push_status', $return_msg);

		    curl_close($ch);
                    return $output;
            }
	}

	public function check_translation_exist( $post_id, $locale_id, $post_type ) {

		global $wpdb;
		$sql = "select post_id from {$wpdb->prefix}postmeta as meta1 ".
			   "  where exists ( ".
				  "select post_id ".
				      "from {$wpdb->prefix}postmeta as meta2, {$wpdb->prefix}posts ".
				      "where meta1.post_id = {$wpdb->prefix}posts.ID and ".
				            //"{$wpdb->prefix}posts.post_status = 'publish' and ".
				            "{$wpdb->prefix}posts.post_status != 'trash' and ".
				            "meta1.post_id = meta2.post_id and ".
				            "meta1.meta_key = 'orig_post_id' and ".
				            "meta2.meta_key = 'g11n_tmy_lang'  and ".
				            "meta1.meta_value = " . $post_id . " and ".
				            "meta2.meta_value = '" . $locale_id . "')";
		    //error_log("GET TRANS SQL = " . $sql);
		    $result = $wpdb->get_results($sql);

		if (isset($result[0]->post_id)) {
		    //error_log("GET TRANS ID = " . $result[0]->post_id);
		    return ($result[0]->post_id);
		} else {
		    //error_log("GET TRANS ID = null");
		    return null;
		}
	}

	public function get_translation_id( $post_id, $locale_id, $post_type, $admin_user = true ) {

		global $wpdb;

                if ( ! $admin_user ) {
                    $post_title = get_post_field("post_title", $post_id);
                    if (! tmy_g11n_post_type_enabled($post_id, $post_title, $post_type) ) {
                        if ( WP_TMY_G11N_DEBUG ) {
                            error_log("In get_translation_id, translation is disabled");
                        }
                        return null;
                    }
                    $admin_query = "= \"publish\"";
                } else {
                    $admin_query = "!= \"trash\"";
                }
                $sql = "select id_meta.post_id
                       from {$wpdb->prefix}postmeta as id_meta,
                            {$wpdb->prefix}postmeta as lang_meta,
                            {$wpdb->prefix}postmeta as type_meta,
                            {$wpdb->prefix}posts as posts
                      where (posts.post_status {$admin_query}) and
                            (posts.post_type = \"g11n_translation\") and
                            (id_meta.post_id = posts.ID) and
                            (id_meta.meta_key = \"orig_post_id\") and
                            (id_meta.meta_value = {$post_id}) and
                            (lang_meta.post_id = posts.ID) and
                            (lang_meta.meta_key = \"g11n_tmy_lang\") and
                            (lang_meta.meta_value = \"{$locale_id}\") and
                            (type_meta.post_id = posts.ID) and
                            (type_meta.meta_key = \"g11n_tmy_orig_type\") and
                            (type_meta.meta_value = \"{$post_type}\")";

	        $result = $wpdb->get_results($sql);
                if ( WP_TMY_G11N_DEBUG ) {
                    error_log("In get_translation_id,".esc_attr($sql));
                    error_log("In get_translation_id,".esc_attr(json_encode($result)));
                }

		if (isset($result[0]->post_id)) {
		    //error_log("GET TRANS ID = " . $result[0]->post_id);
		    return ($result[0]->post_id);
		} else {
		    //error_log("GET TRANS ID = null");
		    return null;
		}

	}

	public function get_translation_info( $trans_id ) {
		global $wpdb;
		$sql = "select {$wpdb->prefix}posts.ID, 
                               {$wpdb->prefix}posts.post_title 
                          from {$wpdb->prefix}postmeta, 
                               {$wpdb->prefix}posts where " .
       			//"{$wpdb->prefix}posts.post_status != \"trash\" and " .
       			"{$wpdb->prefix}postmeta.meta_key = 'orig_post_id' and 
                         {$wpdb->prefix}postmeta.meta_value = {$wpdb->prefix}posts.ID and " .
       			"{$wpdb->prefix}postmeta.post_id = " . $trans_id;

                if ( WP_TMY_G11N_DEBUG ) {
		    error_log("GET POST SQL = " . esc_attr($sql));
                }
		$result = $wpdb->get_results($sql);
		return ($result);
	}


	public function get_language_switcher($position = 'default') {

                // position possible values: 'widget' 'floating' 'sidebar' 'content' 'description' 'blogname'
                // 
           
                if (strcmp(get_option('g11n_using_google_tookit','No'),'Yes' )===0) {
                    if (strcmp($position,'floating' )!==0) {
                        return '';
                    }
                }	
                include 'lang2googlelan.php';
		$current_url = sanitize_url($_SERVER['REQUEST_URI']);
                $site_url = get_site_url();

		$query_variable_name = "g11n_tmy_lang";

		//$g11n_current_language = tmy_g11n_lang_sanitize($_SESSION['g11n_language']);
		$g11n_current_language = $this->get_preferred_language();

		$language_options = get_option('g11n_additional_lang', array());
		//$language_switcher_html = '<span style="font-color:red; font-size: xx-small; font-family: sans-serif; display: inline-block;">';

		if (strcmp('Yes', get_option('g11n_using_google_tookit','Yes')) === 0) {

                    $seq_n = mt_rand(100,999);

                    $google_lang_list = "";                
		    foreach( $language_options as $value => $code) {
                        $google_lang_list .= $lang2googlelan[$code] . ",";                
                    }
                    $google_lang_list = rtrim($google_lang_list, ",");
                    //error_log("google_lang_list: " . json_encode($google_lang_list));                

		    $default_lang = get_option('g11n_default_lang','English');
                    $default_lang_code = $lang2googlelan[$default_lang];

		    $language_switcher_html = '<script type="text/javascript">
                        function googleTranslateElementInit() {
                            //new google.translate.TranslateElement({pageLanguage: "' . $default_lang_code . '",
                            new google.translate.TranslateElement({
                                                                   includedLanguages:"' . $google_lang_list . '",
                                                                   layout: google.translate.TranslateElement.InlineLayout.SIMPLE},
                            "google_translate_element");
                        }
                    </script>
                    <script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit" type="text/javascript"></script>
                    <style type="text/css">

                        #google_translate_element select{
                          background:#f6edfd;
                          color:#383ffa;
                          border: 3px;
                          border-radius:3px;
                          padding:6px 8px
                        }

                        #google_translate_element img
                        { display: none !important; }
                         .goog-te-banner-frame{
                          display:none !important;
                        }
                    </style>';
		    $language_switcher_html .= 'Languages: <div id="google_translate_element"></div>';
		    return $language_switcher_html;

                } else {

		    //$language_switcher_html = 
                    //'<div style="border:1px solid;background-color:#d7dbdd;color:#21618c;font-size:1rem;">';
                    //'<div style="border:1px solid;border-radius:2px;background-color:#d7dbdd;color:#21618c;z-index:10000;box-shadow: 0 0 0px 0 rgba(0,0,0,.4);padding:0.1rem 0.4rem;margin:0rem 0;right:1rem;font-size:1rem;">';

		    $language_switcher_html = '<span style="font-color:red; font-size: xx-small; font-family: sans-serif; display: block;">';
		    //$language_switcher_html = '<div style="border:1px solid;border-radius:5px;">';
		    foreach( $language_options as $value => $code) {
		        //<img src="./flags/24/CN.png" alt="CN">
                    
                         if (strcmp(trim(get_option('g11n_seo_url_enable')),'Yes')===0) {

                             global $wp;
		             $current_url = home_url( $wp->request );
                             //$current_url = str_replace($site_url, $site_url . '/lang/' . $value, $current_url);
                             $url_code = strtolower(str_replace('_', '-', $code));

                             if (isset(explode('/', str_replace($site_url, '', $current_url))[1])) {
                                 $lang_path = explode('/', str_replace($site_url, '', $current_url))[1];
                             } else {
                                 $lang_path = "";
                             }
                             //$lang_path = explode('/', str_replace($site_url, '', $current_url))[1];
                             $lang_path = str_replace('-', '_', $lang_path);
                             if (! array_search(strtolower($lang_path), array_map('strtolower',$language_options))) {
                                 $current_url = str_replace($site_url, $site_url . '/' . esc_attr($url_code), $current_url);
                                 $current_url = $current_url . '/';
                             }

                             parse_str($_SERVER['QUERY_STRING'], $query_str_arr);
                             if (array_key_exists("g11n_tmy_lang_code", $query_str_arr)) {
                                 unset($query_str_arr["g11n_tmy_lang_code"]);
                             }

                             //$current_url = $current_url . "?" . esc_attr($_SERVER['QUERY_STRING']);
                             $current_url = esc_url(add_query_arg($query_str_arr, $current_url));
                        } else {
		             $current_url = sanitize_url($_SERVER['REQUEST_URI']);
                             $current_url = add_query_arg($query_variable_name, $value, $current_url);
                        }

		        if (strcmp('Text', get_option('g11n_switcher_type','Text')) === 0) {
			    $href_text_ht = $value;
			    $href_text = $value;
		        }
		        if (strcmp('Flag', get_option('g11n_switcher_type','Text')) === 0) {
			    $href_text_ht = '<img style="display: inline-block; border: #FF0000 1px outset" src="' . 
				                 plugins_url('flags/', __FILE__ ) . "24/" . 
				                 strtoupper($code) . '.png" title="'. 
				                 $value .'" alt="' . 
				                 strtoupper($code) . "\" >";
			    $href_text = '<img style="display: inline-block" src="' . 
				                 plugins_url('flags/', __FILE__ ) . "24/" . 
				                 strtoupper($code) . '.png" title="'. 
				                 $value .'" alt="' . 
				                 strtoupper($code) . "\" >";
		        }
		        if (strcmp($value, $g11n_current_language) === 0) {
			    $language_switcher_html .= '<a href=' . 
				                   //add_query_arg($query_variable_name, $value, $current_url) . '><b>' .
				                   $current_url . '><b>' .
				                   $href_text_ht.'</b></a>';
		        } else {
			    $language_switcher_html .= '<a href=' . 
				                   //add_query_arg($query_variable_name, $value, $current_url) . '>' .
				                   $current_url . '>' .
				                   $href_text.'</a>';
		        }
                    }
		    $language_switcher_html .= "</span>";
		    //$language_switcher_html .= "</div>";
		    return $language_switcher_html;
		}

        }

	public function get_preferred_language() {

           
                if (is_admin()) {
      
                    //error_log(" In get_preferred_language is_admin");
                    return get_option('g11n_default_lang');
                }

                // error_log(" In get_preferred_language session status :". session_status() . " is_admin:" . is_admin());
                //if (session_status() !== PHP_SESSION_ACTIVE) { 
                //    session_start();
                //}

                //if (! isset($_SESSION)) {
                //    session_start();
                //}
                $seq_code = mt_rand(1000,9999);

                if ( WP_TMY_G11N_DEBUG ) {
                    if (isset($_SESSION)) {
                        error_log(esc_attr($seq_code) . " In get_preferred_language, ". sanitize_textarea_field(json_encode($_SESSION)));
                    } else {
                        error_log(esc_attr($seq_code) . " In get_preferred_language, _SESSION is not set");
                    }
                    error_log(esc_attr($seq_code) . " In get_preferred_language, session id ".esc_attr(session_id()));
                }
                if(!session_id()) {
                    if ( WP_TMY_G11N_DEBUG ) {
                        if (isset($_SESSION)) {
                            error_log(esc_attr($seq_code) . " In get_preferred_language, ".sanitize_textarea_field(json_encode($_SESSION)));
                        } else {
                            error_log(esc_attr($seq_code) . " In get_preferred_language, _SESSION is not set");
                        }
                    }
                    //session_start();
                    //this should not be needed anymore
                }
                if (!isset($_SESSION['g11n_language'])) {
                    $_SESSION['g11n_language'] = get_option('g11n_default_lang');
                    setcookie('g11n_language', $_SESSION['g11n_language'], strtotime('+1 day'));
                    if ( WP_TMY_G11N_DEBUG ) {
                        error_log(esc_attr($seq_code) . " Starting session, id=" . esc_attr(session_id()) . ",lang is not set, set as: " . esc_attr(get_option('g11n_default_lang')));
                    }
                } 

		$lang_var_from_query = tmy_g11n_lang_sanitize(filter_input(INPUT_GET, 'g11n_tmy_lang', FILTER_SANITIZE_SPECIAL_CHARS));

                $all_configed_langs = get_option('g11n_additional_lang'); /* array format ((English -> en), ...) */
		$lang_var_code_from_query = filter_input(INPUT_GET, 'g11n_tmy_lang_code', FILTER_SANITIZE_SPECIAL_CHARS);
		$lang_var_code_from_query = str_replace('-', '_', $lang_var_code_from_query);


	        if (!empty($lang_var_code_from_query)) {
                    $lang_var_from_query = array_search(strtolower($lang_var_code_from_query), array_map('strtolower',$all_configed_langs));
                }

                if ( WP_TMY_G11N_DEBUG ) {
		  error_log(esc_attr($seq_code) . " In get_preferred_language query lang = ". esc_attr($lang_var_from_query));
                }
		if (!empty($lang_var_from_query)) {

		   $_SESSION['g11n_language'] = $lang_var_from_query;
                   // header warning SHAO 
                   //setcookie('g11n_language', $_SESSION['g11n_language'], strtotime('+1 day'));
                   if ( WP_TMY_G11N_DEBUG ) {
		       error_log(esc_attr($seq_code) . " In get_preferred_language return query lang = ". esc_attr($lang_var_from_query));
                   }
		   return $lang_var_from_query;
		} 

		if ((isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) and (strcmp(get_option('g11n_site_lang_browser'),'Yes')===0)) {

                   if ( WP_TMY_G11N_DEBUG ) {
		       error_log(esc_attr($seq_code) . " In get_preferred_language checking browser setting: ". sanitize_textarea_field($_SERVER['HTTP_ACCEPT_LANGUAGE']));
		       error_log(esc_attr($seq_code) . " In get_preferred_language checking browser setting: ". esc_attr(get_option('g11n_site_lang_browser')));
                   }

		    $languages = explode(',', sanitize_textarea_field($_SERVER['HTTP_ACCEPT_LANGUAGE']));
		    $prefLocales = array();
		    foreach ($languages as $language) {
			$lang = explode(';q=', $language);
			// $lang == [language, weight], default weight = 1
			$prefLocales[$lang[0]] = isset($lang[1]) ? floatval($lang[1]) : 1;
		    }
		    arsort($prefLocales);

		    //$prefLocales = array_reduce(
		    //    explode(',', sanitize_textarea_field($_SERVER['HTTP_ACCEPT_LANGUAGE'])),
		    //    function ($res, $el) {
		    //        list($l, $q) = array_merge(explode(';q=', $el), [1]);
		    //        $res[$l] = (float) $q;
		    //        return $res;
		    //    }, []);
		    //arsort($prefLocales);

		    /* array format: ( [zh-CN] => 1 [zh] => 0.8 [en] => 0.6 [en-US] => 0.4) */

		    $all_configed_langs = get_option('g11n_additional_lang'); /* array format ((English -> en), ...) */

		    if (is_array($all_configed_langs)) {
			foreach( $prefLocales as $value => $pri) {
			    if (array_search($value, $all_configed_langs)) {
				$pref_lang = array_search($value, $all_configed_langs);
				break;
			    }
			    /* check after removing CN of zh-CN*/
			    $lang_code = preg_split("/-/", $value);
			    if (array_search($lang_code[0], $all_configed_langs)) {
				$pref_lang = array_search($lang_code[0], $all_configed_langs);
				break;
			    }
			}
		    }
		    if (isset($pref_lang)) { 
			$_SESSION['g11n_language'] = $pref_lang;
                        setcookie('g11n_language', $_SESSION['g11n_language'], strtotime('+1 day'));
			return $pref_lang; 
		    }
		}

		if ((isset($_COOKIE['g11n_language'])) and (strcmp(get_option('g11n_site_lang_cookie'),'Yes')===0)) {
		   $_SESSION['g11n_language'] = tmy_g11n_lang_sanitize($_COOKIE['g11n_language']);
                   setcookie('g11n_language', $_SESSION['g11n_language'], strtotime('+1 day'));
		   return tmy_g11n_lang_sanitize($_COOKIE['g11n_language']);
		}

		if (isset($_SESSION['g11n_language'])) {
                   if ( WP_TMY_G11N_DEBUG ) {
		      error_log(esc_attr($seq_code) . " In get_preferred_language return _SESSION g11n_language = ". tmy_g11n_lang_sanitize($_SESSION['g11n_language']));
                   }
		   return tmy_g11n_lang_sanitize($_SESSION['g11n_language']);
		}

		$_SESSION['g11n_language'] = get_option('g11n_default_lang','English');
		return tmy_g11n_lang_sanitize($_SESSION['g11n_language']);


	}
	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
