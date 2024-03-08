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

                    $default_language = get_option("g11n_default_lang", "English");
                    $all_configed_langs = get_option('g11n_additional_lang'); /* array format ((English -> en), ...) */
                    if (is_array($all_configed_langs)) {
                        if (isset($all_configed_langs[$default_language])) {
                            $pref_lang = $all_configed_langs[$default_language];
                        }
                    } else {
                            $pref_lang = "en_US";
                    }
                    $pref_lang = str_replace('_', '-', $pref_lang);
                    error_log("set language push doc to server: ". $pref_lang);

		    $payload_contents_array = array();
                    $md5index = array();
		    foreach ($contents_array as &$con) {
		        $con_id = md5($con);
                        $max_adj = 0;
                        while (in_array($con_id, $md5index)) {
                            $max_adj = $max_adj + 1;
                            if ($max_adj > 20) {
                                error_log("md5index maximum 20");
                                break;
                            }
                            $con = $con . " ";
		            $con_id = md5($con);
                        }
                        $md5index[] = $con_id;
		        array_push($payload_contents_array, array("extensions" => array(array("object-type" => "pot-entry-header",
				                                        "references" => array(),
				                                        "flags" => array(),
				                                        "context" => "")),
                                                        //Dec 2023, change to using default language
				                        //"lang" => "en-US",
				                        //"lang" => "zh-CN",
				                        "lang" => $pref_lang,
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
                    //error_log("pushing url: " . $rest_url);
                    $response = wp_remote_post( $rest_url, $args );
                    if ( is_array( $response ) && ! is_wp_error( $response ) ) {
                        $output = $response['body'];
                        $output = trim($output, '"');
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
                    //error_log("return output: " . $output);
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

        //todo copied this to translastor class already, need to sync up if there is any change
        //Dec 23 2023

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

        //todo copied this to translastor class already, need to sync up if there is any change
        //Dec 23 2023

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
                             $new_translation_id = $this->get_translation_id($post_id,$code,$post_type);
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
                                 //$message .= " " . $new_translation_id . " ";
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
                         $push_return_msg = $this->push_contents_to_translation_server($json_file_name, $contents_array);
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
                                            // "div_status" => $this->_get_tmy_g11n_metabox($post_id, $post_type)
                                             "div_status" => " "
                                            // todo, Jan 2024
                                      ));
             //echo esc_attr($message);
             return $return_msg . "  ";

        }


	public function get_preferred_language() {
           
                if (is_admin()) {
                    return get_option('g11n_default_lang');
                }
                $all_configed_langs = get_option('g11n_additional_lang'); /* array format ((English -> en), ...) */
                if (is_array($all_configed_langs) && (count($all_configed_langs) > 0)) {
                    if (isset($GLOBALS['wp_query'])) {
                        //error_log("GET LANG - if wp_query is ready");
		        $lang_var_code_from_query = filter_input(INPUT_GET, 'g11n_tmy_lang_code', FILTER_SANITIZE_SPECIAL_CHARS);
		        //$lang_var_code_from_query = get_query_var("g11n_tmy_lang_code", "");
                        if (! empty($lang_var_code_from_query)) {
                            $url_lang_code = str_replace('-', '_', $lang_var_code_from_query);
                            $url_lang_name = array_search(strtolower($url_lang_code), array_map('strtolower',$all_configed_langs));
                        } else {
                            $langs_pattern = '/\/(' . implode('|', array_values($all_configed_langs)) . ')(\/.*)?$/';
                            $langs_pattern = strtolower(str_replace('_', '-', $langs_pattern));
                            $request_uri = sanitize_url($_SERVER['REQUEST_URI']);
                            if (preg_match($langs_pattern, $request_uri, $matches)) {
                                $url_lang_code = str_replace('-', '_', $matches[1]);
                                $url_lang_name = array_search(strtolower($url_lang_code), array_map('strtolower',$all_configed_langs));
                            } else {
                                $lang_var_from_query = filter_input(INPUT_GET, 'g11n_tmy_lang', FILTER_SANITIZE_SPECIAL_CHARS);
                                if (! empty($lang_var_from_query)) {
                                    $url_lang_name = $lang_var_from_query;
                                    $url_lang_code = $all_configed_langs[$url_lang_name];
                                } else {
                                    $url_lang_name = "";
                                    $url_lang_code = "";
                                }
                            }
                        }
                    } else {
                        //error_log("GET LANG - if wp_query is not ready");
                        $langs_pattern = '/\/(' . implode('|', array_values($all_configed_langs)) . ')(\/.*)?$/';
                        $langs_pattern = strtolower(str_replace('_', '-', $langs_pattern));
                        $request_uri = sanitize_url($_SERVER['REQUEST_URI']);
                        if (preg_match($langs_pattern, $request_uri, $matches)) {
                            $url_lang_code = str_replace('-', '_', $matches[1]);
                            $url_lang_name = array_search(strtolower($url_lang_code), array_map('strtolower',$all_configed_langs));
                        } else {
                            $url_lang_name = "";
                            $url_lang_code = "";
                        }
                    }
                    //error_log("GET LANG -  " . $url_lang_code . " " . $url_lang_name);
                } else { //no additional langs configured
                    return get_option('g11n_default_lang');
                }

                if (session_status() !== PHP_SESSION_ACTIVE) {
                    if (! @session_start()) {
                        //error_log("GET LANG: start session failed");
                        if (empty($url_lang_name)) {
                            return get_option('g11n_default_lang');
                        } else {
                            return $url_lang_name;
                        }
                    }
                }

//error_log("GET LANG - valid session " . json_encode($_SESSION) . " url_lang_name: " . $url_lang_name . " " . $url_lang_code);

                if (! empty($url_lang_name)) {
                    $_SESSION['g11n_language'] = $url_lang_name;
                    @setcookie('g11n_language', $_SESSION['g11n_language'], strtotime('+1 day'));
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
//error_log("GET LANG - return  " . $_SESSION['g11n_language']);
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
