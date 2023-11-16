<?php

/**
 * Fired during plugin activation
 *
 * @since      1.0.0
 *
 * @package    TMY_G11n
 * @subpackage TMY_G11n/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    TMY_G11n
 * @subpackage TMY_G11n/includes
 * @author     Yu Shao <yu.shao.gm@gmail.com>
 */
class TMY_G11n_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
    public static function activate( $network_wide ) {

        if ( is_multisite() && $network_wide ) {

                global $wpdb;

                $current_blog = $wpdb->blogid;
                $activated = array();

                error_log("BLOG ID " . $current_blog);

                $blog_ids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM %sblogs", $wpdb->prefix));
                foreach ($blog_ids as $blog_id) {
                        switch_to_blog($blog_id);
                        $activated[] = $blog_id;
                }

                switch_to_blog($current_blog);
                update_site_option('tmy_g11n_sites_activated', $activated);

        } else {

        }

    }

}
