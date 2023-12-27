<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

if ( ! class_exists( 'TMY_G11N_Table' ) ) :
    class TMY_G11N_Table extends WP_List_Table {


public function __construct() {
 
    global $status, $page;
 
        parent::__construct(
            array(
                'singular'  => 'movie',
                'plural'    => 'movies',
                'ajax'      => true
                ));
        }

        public function get_columns() {
            return array(
                'cb'  => '<input type="checkbox" />',
                'term_id'      => __('Term ID', 'tmy-globalization'),
                'name'   => __('Name', 'tmy-globalization'),
                'slug'   => __('Slug', 'tmy-globalization'),
                'taxonomy'   => __('Taxonomy', 'tmy-globalization'),
                'translations'   => __('Translations', 'tmy-globalization'),
            );
        }

        function column_cb($item) {
            return sprintf(
                '<input type="checkbox" name="term_id[]" value="%s" />', esc_attr($item["term_id"])
                           );    
        }

        public function prepare_items() {

            $this->process_bulk_action();

            $qualified_taxonomies = get_taxonomies(array("public" => true, "show_ui"=> true), "names", "or");
            unset($qualified_taxonomies['translation_priority']);

            global $wpdb;
            $sql = "select {$wpdb->prefix}terms.term_id, name, slug, taxonomy
                from {$wpdb->prefix}terms,{$wpdb->prefix}term_taxonomy
                where {$wpdb->prefix}terms.term_id={$wpdb->prefix}term_taxonomy.term_id";
            $rows = $wpdb->get_results( $sql, "ARRAY_A" );

            $qualified_rows = array();
            foreach ($rows as $row) {
                if (array_key_exists($row["taxonomy"], $qualified_taxonomies)) {
                   $sql = "select id_meta.post_id,
                                  lang_meta.meta_value
                             from {$wpdb->prefix}postmeta as id_meta,
                                  {$wpdb->prefix}postmeta as lang_meta,
                                  {$wpdb->prefix}postmeta as type_meta
                            where id_meta.meta_key=\"orig_post_id\" and
                                  id_meta.meta_value={$row["term_id"]} and
                                  type_meta.meta_key=\"g11n_tmy_orig_type\" and
                                  type_meta.meta_value=\"{$row["taxonomy"]}\" and
                                  lang_meta.meta_key=\"g11n_tmy_lang\" and
                                  lang_meta.post_id=type_meta.post_id and
                                  lang_meta.post_id=id_meta.post_id";
                   $lang_rows = $wpdb->get_results( $sql, "ARRAY_A" );
                   $lang_info = "";

                   foreach ($lang_rows as $lang_row) {
                       $lang_info .= esc_attr($lang_row["meta_value"]) . "(<a href=\"" .
                             esc_url( get_edit_post_link($lang_row["post_id"]) ) . "\">" .
                             esc_attr($lang_row["post_id"]) . "</a>) ";
                   }
                   //$row[] = $lang_info;
                   $id_link =  "<a href=\"" . esc_url(get_edit_term_link($row["term_id"])) . "\">" .  esc_attr($row["term_id"]) . "</a>";
                   //$qualified_rows[] = $row;
                   $qualified_rows[] = array( "term_id"=>$row["term_id"],
                                              "name"=>$row["name"],
                                              "id_link"=>$id_link,
                                              "slug"=>$row["slug"],
                                              "taxonomy"=>$row["taxonomy"],
                                              "translations"=>$lang_info
                                            );

                   //echo "<br>" . json_encode($lang_rows) . "<br>";
                   //echo "<br>" . $lang_info . "<br>";
                }
            }
            $sortable = array('taxonomy' => array('taxonomy', false),
                              'term_id' => array('term_id', true));
            usort( $qualified_rows, array( &$this, 'usort_reorder' ) );
            $this->items = $qualified_rows;

            $per_page = 20;
            $current_page = $this->get_pagenum();
            $total_items = count($this->items);

            $found_data = array_slice($this->items,(($current_page-1)*$per_page),$per_page);

            $this->set_pagination_args( array(
              'total_items' => $total_items,                  //WE have to calculate the total number of items
              'per_page'    => $per_page                     //WE have to determine how many items to show on a page
            ) );
            $this->items = $found_data;



            $columns  = $this->get_columns();
            $hidden   = array();
            //$sortable = array();
            $primary  = 'name';
            $this->_column_headers = array( $columns, $hidden, $sortable, $primary );
            //$this->display();


        }


      // Sorting function
      function usort_reorder($a, $b)
      {
            // If no sort, set default
            $orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : 'taxonomy';
            // If no order, default to asc
            $order = (!empty($_GET['order'])) ? $_GET['order'] : 'asc';
            // Determine sort order
            $result = strcmp($a[$orderby], $b[$orderby]);
            // Send final sort direction to usort
            return ($order === 'asc') ? $result : -$result;
      }

        function get_bulk_actions() {
          $actions = array(
            'start_translation_from_taxonomies_form'    => 'Start or Sync Translation',
            'remove_translation_from_taxonomies_form'    => 'Remove Translation'
          );
          return $actions;
        }

        protected function column_default( $item, $column_name ) {
            switch ( $column_name ) {
                case 'term_id':
                    return  $item["id_link"];
                case 'name':
                    return esc_html( $item["name"] );
                case 'slug':
                    return esc_html( $item["slug"] );
                case 'taxonomy':
                    return esc_html( $item["taxonomy"] );
                case 'translations':
                    return $item["translations"] ;
                return 'Unknown';
            }
        }

        /**
         * Generates custom table navigation to prevent conflicting nonces.
         * 
         * @param string $which The location of the bulk actions: 'top' or 'bottom'.
         */
        protected function display_tablenav( $which ) {
            ?>
            <div class="tablenav <?php echo esc_attr( $which ); ?>">

                <div class="alignleft actions bulkactions">
                    <?php $this->bulk_actions( $which ); ?>
                </div>
                <?php
                $this->extra_tablenav( $which );
                $this->pagination( $which );
                
                ?>

                <br class="clear" />
            </div>
            <?php
        }
        public function single_row( $item ) {
            echo '<tr>';
            $this->single_row_columns( $item );
            echo '</tr>';
        }

    }
endif;

if ( ! class_exists( 'TMY_G11N_Text_Table' ) ) :
    class TMY_G11N_Text_Table extends WP_List_Table {


public function __construct() {
 
    global $status, $page;
 
        parent::__construct(
            array(
                'singular'  => 'movie',
                'plural'    => 'movies',
                'ajax'      => true
                ));
        }

        public function get_columns() {
            return array(
                'cb'  => '<input type="checkbox" />',
                'text_str'   => __('Text', 'tmy-globalization'),
                'taxonomy'   => __('Taxonomy', 'tmy-globalization'),
                'place_holder_id'   => __('Translation Place Holder ID', 'tmy-globalization'),
                'translations'   => __('Translations', 'tmy-globalization'),
            );
        }

        function column_cb($item) {
            return sprintf(
                '<input type="checkbox" name="text_str[]" value="%s" />', esc_attr($item["text_str"])
                           );    
        }

        public function prepare_items() {

            $this->process_bulk_action();

            global $wpdb;

            // find all menu labels
            $sql = "select distinct post_title 
                             from {$wpdb->prefix}posts 
                            where post_type=\"nav_menu_item\" and post_title!=\"\"";
            $rows = $wpdb->get_results( $sql, "ARRAY_A" );

            $qualified_rows = array();
            foreach ($rows as $row) {
                   $qualified_rows[] = array( "text_str"=>$row["post_title"],
                                              "taxonomy"=>"Menu Label",
                                              "translations"=>""
                                            );

            }
     
            // find all woo commerce product attributes
            $sql = "select distinct attribute_label 
                             from {$wpdb->prefix}woocommerce_attribute_taxonomies";
            $rows = $wpdb->get_results( $sql, "ARRAY_A" );

            foreach ($rows as $row) {
                   $qualified_rows[] = array( "text_str"=>$row["attribute_label"],
                                              "taxonomy"=>"WooCommerce Product Attribute",
                                              "translations"=>""
                                            );

            }
            // find all woo commerce options

            $woocommerce_cod_settings = maybe_unserialize(get_option("woocommerce_cod_settings", ""));
            if (isset($woocommerce_cod_settings['title'])) {
                $qualified_rows[] = array( "text_str"=>$woocommerce_cod_settings['title'],
                                           "taxonomy"=>"WooCommerce Code Payment Title",
                                           "translations"=>""
                                         );
            }
            if (isset($woocommerce_cod_settings['description'])) {
                $qualified_rows[] = array( "text_str"=>$woocommerce_cod_settings['description'],
                                           "taxonomy"=>"WooCommerce Code Payment Desc",
                                           "translations"=>""
                                         );
            }
            if (isset($woocommerce_cod_settings['instructions'])) {
                $qualified_rows[] = array( "text_str"=>$woocommerce_cod_settings['instructions'],
                                           "taxonomy"=>"WooCommerce Code Payment Instructions",
                                           "translations"=>""
                                         );
            }



            $woocommerce_cheque_settings = maybe_unserialize(get_option("woocommerce_cheque_settings", ""));
            if (isset($woocommerce_cheque_settings['title'])) {
                $qualified_rows[] = array( "text_str"=>$woocommerce_cheque_settings['title'],
                                           "taxonomy"=>"WooCommerce ChequePayment Title",
                                           "translations"=>""
                                         );
            }
            if (isset($woocommerce_cheque_settings['description'])) {
                $qualified_rows[] = array( "text_str"=>$woocommerce_cheque_settings['description'],
                                           "taxonomy"=>"WooCommerce ChequePayment Desc",
                                           "translations"=>""
                                         );
            }
            if (isset($woocommerce_cheque_settings['instructions'])) {
                $qualified_rows[] = array( "text_str"=>$woocommerce_cheque_settings['instructions'],
                                           "taxonomy"=>"WooCommerce ChequePayment Instructions",
                                           "translations"=>""
                                         );
            }

            // checking if the private post has been created and if translation started
            foreach ($qualified_rows as &$row) {
                $sql = "select ID from {$wpdb->prefix}posts where post_title=\"" . esc_sql($row["text_str"]) . "\" and post_status=\"private\"";
                $result = $wpdb->get_results($sql);
                if (isset($result[0]->ID)) {

                    $sql = "select id_meta.post_id,
                                  lang_meta.meta_value
                             from {$wpdb->prefix}postmeta as id_meta,
                                  {$wpdb->prefix}postmeta as lang_meta,
                                  {$wpdb->prefix}postmeta as type_meta
                            where id_meta.meta_key=\"orig_post_id\" and
                                  id_meta.meta_value={$result[0]->ID} and
                                  type_meta.meta_key=\"g11n_tmy_orig_type\" and
                                  type_meta.meta_value=\"post\" and
                                  lang_meta.meta_key=\"g11n_tmy_lang\" and
                                  lang_meta.post_id=type_meta.post_id and
                                  lang_meta.post_id=id_meta.post_id";
                    $lang_rows = $wpdb->get_results( $sql, "ARRAY_A" );
                    $lang_info = "";
                    foreach ($lang_rows as $lang_row) {
                       $lang_info .= esc_attr($lang_row["meta_value"]) . "(<a href=\"" .
                             esc_url( get_edit_post_link($lang_row["post_id"]) ) . "\">" .
                             esc_attr($lang_row["post_id"]) . "</a>) ";
                    }
                    //$row[] = $lang_info;
                    $row["place_holder_id"] = "<a href=\"" . esc_url(get_edit_post_link($result[0]->ID)) . "\">" .  esc_attr($result[0]->ID) . "</a>";
                    $row["translations"] = $lang_info;
                } else {
                    $row["place_holder_id"] = "";
                    $row["translations"] = "";
                }
            }

            $sortable = array('text_str' => array('text_str', false),
                              'taxonomy' => array('taxonomy', true));
            usort( $qualified_rows, array( &$this, 'usort_reorder' ) );
            $this->items = $qualified_rows;

            $per_page = 20;
            $current_page = $this->get_pagenum();
            $total_items = count($this->items);

            $found_data = array_slice($this->items,(($current_page-1)*$per_page),$per_page);

            $this->set_pagination_args( array(
              'total_items' => $total_items,                  //WE have to calculate the total number of items
              'per_page'    => $per_page                     //WE have to determine how many items to show on a page
            ) );
            $this->items = $found_data;



            $columns  = $this->get_columns();
            $hidden   = array();
            //$sortable = array();
            $primary  = 'name';
            $this->_column_headers = array( $columns, $hidden, $sortable, $primary );
            //$this->display();


        }


      // Sorting function
      function usort_reorder($a, $b)
      {
            // If no sort, set default
            $orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : 'taxonomy';
            // If no order, default to asc
            $order = (!empty($_GET['order'])) ? $_GET['order'] : 'asc';
            // Determine sort order
            $result = strcmp($a[$orderby], $b[$orderby]);
            // Send final sort direction to usort
            return ($order === 'asc') ? $result : -$result;
      }

        function get_bulk_actions() {
          $actions = array(
            'start_translation_from_text_form'    => 'Start or Sync Translation',
            'remove_translation_from_text_form'    => 'Remove Translation'
          );
          return $actions;
        }

        protected function column_default( $item, $column_name ) {
            switch ( $column_name ) {
                case 'text_str':
                    return esc_html( $item["text_str"] );
                case 'taxonomy':
                    return esc_html( $item["taxonomy"] );
                case 'place_holder_id':
                    return $item["place_holder_id"];
                case 'translations':
                    return $item["translations"] ;
                return 'Unknown';
            }
        }

        /**
         * Generates custom table navigation to prevent conflicting nonces.
         * 
         * @param string $which The location of the bulk actions: 'top' or 'bottom'.
         */
        protected function display_tablenav( $which ) {
            ?>
            <div class="tablenav <?php echo esc_attr( $which ); ?>">

                <div class="alignleft actions bulkactions">
                    <?php $this->bulk_actions( $which ); ?>
                </div>
                <?php
                $this->extra_tablenav( $which );
                $this->pagination( $which );
                
                ?>

                <br class="clear" />
            </div>
            <?php
        }
        public function single_row( $item ) {
            echo '<tr>';
            $this->single_row_columns( $item );
            echo '</tr>';
        }

    }
endif;

if ( ! class_exists( 'TMY_G11N_Dashboard_Sync_Table' ) ) :
    class TMY_G11N_Dashboard_Sync_Table extends WP_List_Table {


    private $translator;
public function __construct() {
 
    global $status, $page;

    $this->translator = new TMY_G11n_Translator();

        parent::__construct(
            array(
                'singular'  => 'movie',
                'plural'    => 'movies',
                'ajax'      => true
                ));
        }

        public function get_columns() {
            return array(
                'cb'  => '<input type="checkbox" />',
                'dashboard_name_str'   => __('Document', 'tmy-globalization'),
                'dashboard_title'   => __('Title', 'tmy-globalization'),
                'dashboard_post_id'   => __('Post ID', 'tmy-globalization'),
                'dashboard_language'   => __('Language', 'tmy-globalization'),
                'dashboard_language_ready'   => __('Ready', 'tmy-globalization'),
                'dashboard_trans_post_id'   => __('Translation Post ID', 'tmy-globalization'),
                'dashboard_last_modified'   => __('Last Modified', 'tmy-globalization'),
            );
        }

        function column_cb($item) {
            return sprintf(
                '<input type="checkbox" name="post_index[]" value="%s" />', esc_attr($item["post_index"])
                           );
        }

        public function prepare_items() {

            $return_rows = $this->process_bulk_action();

                error_log("prepare items acton page no =" . $this->get_pagenum());
                error_log("prepare items acton page action =" . $_POST['action']);

            $this->items = array();

            if ( isset( $_POST['action']) && strcmp(esc_attr($_POST['action']), "sync_translation_server_list")==0  || ($this->get_pagenum() > 1)) {
                error_log("prepare items acton set - " . esc_attr($_POST['action']));

                $rest_url = rtrim(get_option('g11n_server_url'),"/") .  "/rest/version";
                $server_reply = $this->translator->rest_get_translation_server($rest_url);

                if ($server_reply["http_code"] == 200) {
                    $return_msg .= "<br>Translations Hosted on the Server: <br>";

                    $translation_server_status = True;
                    $rest_url = rtrim(get_option('g11n_server_url'),"/") .  "/rest/stats/proj/" .
                                get_option('g11n_server_project') .  "/iter/" .
                                get_option('g11n_server_version') .  "?detail=true&word=false";
                    $server_reply = $this->translator->rest_get_translation_server($rest_url);
                    $payload = $server_reply["payload"];

                    $return_msg .= "Progress Overview (Translated/Total): <br>";

                    if (! is_null($payload)){

                        if (is_array($payload->detailedStats)) {
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
                                                /* change the locale - to _ */
                                                $stat_row->locale = str_replace("-", "_", $stat_row->locale);
                                                $trans_ready_str="&#10004;";
                                                $translation_id = $this->translator->get_translation_id($default_lang_post_id,$stat_row->locale,$payload_post_type);
                                                if (isset($translation_id)) {
                                                    $post_index=$default_lang_post_id . "-" . $stat_row->locale . "--" . $translation_id;
                                                    $trans_post_str = "<b>".
                                                      esc_attr($stat_row->translated) . "/" . esc_attr($stat_row->total) . "(ID:".
                                                      '<a href="' . esc_url(get_edit_post_link(esc_attr($translation_id))) .
                                                      '" target="_blank">'.esc_attr($translation_id) . '</a>' .
                                                      ")</b> ";
                                                    $post_last_modified_str = get_post_field('post_modified', $translation_id);
                                                } else {
                                                    $post_index=$default_lang_post_id . "-" . $stat_row->locale. "--NONE";
                                                    $post_last_modified_str = "";
                                                    $trans_post_str = "<b>". esc_attr($stat_row->translated) . "/" . esc_attr($stat_row->total) .
                                                      "(No Local ID)</b> ";
                                                }
                                            } else {
                                                $post_index=$default_lang_post_id . "-" . $stat_row->locale. "--NONE";
                                                $post_last_modified_str = "";
                                                $trans_ready_str=" ";
                                                $stat_row->locale = str_replace("-", "_", $stat_row->locale);
                                                $trans_post_str = esc_attr($stat_row->translated) . "/" . esc_attr($stat_row->total) . " ";
                                            }
                                            $post_last_modified = get_post_field('post_modified', $default_lang_post_id);
                                            $qualified_rows[] = array( 
                                                "post_id"=>$default_lang_post_id,
                                                "post_index"=>$post_index,
                                                "trans_post_id"=>$trans_post_str,
                                                "translation_ready"=>$trans_ready_str,
                                                "name_str"=>$row->id,
                                                "title"=>$default_post_title,
                                                "language"=>esc_attr($stat_row->locale),
                                                "last_modified"=>$post_last_modified_str
                                            );
                                        }

                                    }
                                }
                            }
                        } // payload->detailedStats array
                    } //payload null
                } // return code 200

                $this->items = $qualified_rows;
            }
            if ( isset( $_POST['action']) && strcmp(esc_attr($_POST['action']), "apply_translation_with_server")==0  ) {

                $post_indexs = esc_sql($_POST['post_index']);
                $valid_list[] = array();
                foreach ($post_indexs as $post_index) {
                    $post_index_seg = preg_split("/--/", $post_index);
                    if (strcmp($post_index_seg[1],"NONE")===0) {
                        continue;
                    }
                    $valid_list[$post_index_seg[0]] = $post_index_seg[1];
                }

                error_log("after switch post_ids " . json_encode($post_indexs));
                error_log("after switch valid_list " . json_encode($valid_list));

                $rest_url = rtrim(get_option('g11n_server_url'),"/") .  "/rest/version";
                $server_reply = $this->translator->rest_get_translation_server($rest_url);

                if ($server_reply["http_code"] == 200) {
                    $translation_server_status = True;
                    $rest_url = rtrim(get_option('g11n_server_url'),"/") .  "/rest/stats/proj/" .
                                get_option('g11n_server_project') .  "/iter/" .
                                get_option('g11n_server_version') .  "?detail=true&word=false";
                    $server_reply = $this->translator->rest_get_translation_server($rest_url);
                    $payload = $server_reply["payload"];
                    if (! is_null($payload)){
                        if (is_array($payload->detailedStats)) {
                            foreach ( $payload->detailedStats as $row ) {
                                if (is_array($row->stats)) {
                                    //$row->id is in the format of "Wordpress-post-23"
                                    $g11n_res_filename = preg_split("/-/", $row->id);

                                    $default_lang_post_id = $g11n_res_filename[2];
                                    $payload_post_type = $g11n_res_filename[1];
                                    $default_post_title =  get_the_title($default_lang_post_id);
                                    $default_post_title =  substr($default_post_title, 0 , 30);

                                    if ( strcmp($g11n_res_filename[0], "WordpressG11nAret") === 0 ) {
                                        foreach ( $row->stats as $stat_row ) {
                                            $tmy_local_locale = str_replace("-", "_", $stat_row->locale);
                                            if  ($stat_row->translated == $stat_row->total) {
                                                if (array_key_exists($default_lang_post_id . "-" . $tmy_local_locale,
                                                                     $valid_list)) {
                                                    //start copying down the translation to wordpress


                                                     /* Pulling translation in */
                                                     $rest_url = rtrim(get_option('g11n_server_url'),"/") . "/rest" . "/projects/p/" .
                                                           get_option('g11n_server_project') . "/iterations/i/" .
                                                           get_option('g11n_server_version') . "/r/" .
                                                           $row->id . "/translations/" . $stat_row->locale . "?ext=gettext&ext=comment";

                                                     $server_reply = $this->translator->rest_get_translation_server($rest_url);
                                                     $translation_payload = $server_reply["payload"];

                                                     if (isset($translation_payload->textFlowTargets[0]->content)) {
                                                          $translation_title = $translation_payload->textFlowTargets[0]->content;
                                                            //error_log("SYNC TRANSLATION TITLE = " . $translation_title);
                                                     }

                                                     $payload_size = count($translation_payload->textFlowTargets);
                                                     $translation_contents = '';
                                                     for ($i = 1; $i < $payload_size; $i++) {
                                                         $translation_contents .= $translation_payload->textFlowTargets[$i]->content ;
                                                     }

                                                     /* change the locale - to _ */
                                                     $stat_row->locale = str_replace("-", "_", $stat_row->locale);

                                                     $translation_id = $this->translator->get_translation_id($default_lang_post_id,$stat_row->locale,$payload_post_type);
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

                                                         if (strcmp(get_current_theme(), "Avada") === 0) {
                                                             //preg_match_all("/fusion_global=\"([^\]]*)\"/", $translation_contents, $gc_matches);
                                                             preg_match_all("/fusion_global id=\"([^\]]*)\"/", $translation_contents, $gc_matches);
                                                             foreach ($gc_matches[0] as $key => $full_match_str) {
                                                                 $gc_post_type = get_post_field("post_type", $gc_matches[1][$key]);
                                                                 $gc_translation_id = $this->translator->get_translation_id($gc_matches[1][$key],
                                                                                                                        $stat_row->locale,
                                                                                                                        $gc_post_type,
                                                                                                                        true);
                                                                 error_log( "AVADA global container adjutment $full_match_str " . $gc_matches[1][$key] . "->" . $gc_translation_id);
                                                                 if (isset($gc_translation_id)) {
                                                                     $new_global_id_str = str_replace($gc_matches[1][$key], $gc_translation_id, $full_match_str);
                                                                     $translation_contents = str_replace($full_match_str, $new_global_id_str, $translation_contents);
                                                                 }
                                                             }
                                                         }

                                                         $update_post_id = wp_update_post(array(
                                                                        'ID'    => $translation_id,
                                                                        'post_title'    => $translation_title,
                                                                        'post_content'  => $translation_contents,
                                                                        'post_status'  => "publish",
                                                                        'post_type'  => "g11n_translation"));
                                                     }
                                                     $this->translator->_update_g11n_translation_status($translation_id);

                                                     $qualified_rows[] = array( 
                                                        "post_id"=>$default_lang_post_id,
                                                        "post_index"=>$default_lang_post_id,
                                                        "trans_post_id"=>$translation_id,
                                                        "translation_ready"=>"&#10004;",
                                                        "name_str"=>$row->id,
                                                        "title"=>get_the_title($translation_id),
                                                        "language"=>esc_attr($stat_row->locale),
                                                        "last_modified"=>get_post_field('post_modified', $translation_id)

                                                     );
                                                     //start copying down the translation to wordpress
                                                }
                                            }
                                        }
                                    }

                                }
                            }
                        } // payload->detailedStats array
                    } //payload null
                } // return code 200


                $this->items = $qualified_rows;

            } 
            error_log("after switch ");


            $per_page = 100;
            $current_page = $this->get_pagenum();
            $total_items = count($this->items);

            $found_data = array_slice($this->items,(($current_page-1)*$per_page),$per_page);

            $this->set_pagination_args( array(
              'total_items' => $total_items,                  //WE have to calculate the total number of items
              'per_page'    => $per_page                     //WE have to determine how many items to show on a page
            ) );
            $this->items = $found_data;


            $columns  = $this->get_columns();
            $hidden   = array();
            //$sortable = array();
            $primary  = 'name';
            $this->_column_headers = array( $columns, $hidden, $sortable, $primary );
            //$this->display();

        }


      // Sorting function
      function usort_reorder($a, $b)
      {
            // If no sort, set default
            $orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : 'taxonomy';
            // If no order, default to asc
            $order = (!empty($_GET['order'])) ? $_GET['order'] : 'asc';
            // Determine sort order
            $result = strcmp($a[$orderby], $b[$orderby]);
            // Send final sort direction to usort
            return ($order === 'asc') ? $result : -$result;
      }

        function get_bulk_actions() {
          $actions = array(
            'sync_translation_server_list'    => 'Sync with Translation Sever',
            'apply_translation_with_server'    => 'Download and Apply Translation'
          );
          //return $actions;
        }

        protected function column_default( $item, $column_name ) {

            switch ( $column_name ) {
                case 'dashboard_name_str':
                    return esc_html( $item["name_str"] );
                case 'dashboard_post_id':
                    return esc_html( $item["post_id"] );
                case 'dashboard_title':
                    return esc_html( $item["title"] );
                case 'dashboard_language':
                    return $item["language"] ;
                case 'dashboard_language_ready':
                    return $item["translation_ready"] ;
                case 'dashboard_trans_post_id':
                    return $item["trans_post_id"];
                case 'dashboard_last_modified':
                    return $item["last_modified"] ;
                return 'Unknown';
            }
        }

        /**
         * Generates custom table navigation to prevent conflicting nonces.
         * 
         * @param string $which The location of the bulk actions: 'top' or 'bottom'.
         */
        protected function display_tablenav( $which ) {
            ?>
            <div class="tablenav <?php echo esc_attr( $which ); ?>">

                <div class="alignleft actions bulkactions">
                    <?php $this->bulk_actions( $which ); ?>
                </div>
                <?php
                $this->extra_tablenav( $which );
                $this->pagination( $which );
                
                ?>

                <br class="clear" />
            </div>
            <?php
        }
        public function single_row( $item ) {
            echo '<tr>';
            $this->single_row_columns( $item );
            echo '</tr>';
        }

    }
endif;
