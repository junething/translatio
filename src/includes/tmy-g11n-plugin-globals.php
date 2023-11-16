<?php

/**
 * The file that defines the global variable and functions
 *
 * @since      1.0.0
 *
 * @package    TMY_G11n
 * @subpackage TMY_G11n/includes
 * @author     Yu Shao <yu.shao.gm@gmail.com>
 */

function tmy_g11n_lang_sanitize( $lang ) {

    $default_lang = get_option('g11n_default_lang', 'English');
    if (strcmp($lang,'')!==0) {
        $all_langs = get_option('g11n_additional_lang',array());
        if (array_key_exists($lang, $all_langs)) {
            return $lang;
        } else {
            error_log("Warning tmy_g11n_language_escape, invalid:" . esc_attr($lang) . " reset to: " . esc_attr($default_lang));
            return $default_lang;
        }
    } else {
        return $lang;
    }
}


function tmy_g11n_html_kses_esc( $html ) {

    // '<div style="border:1px solid;background-color:#d7dbdd;color:#21618c;font-size:1rem;">';


    $allowed_html = array('span' => array('style' => array('border' => array())),
                          'div' => array('style' => array('border' => array(),
                                                          'background-color' => array(),
                                                          'color' => array())),
                          'a' => array('href' => array(array()),
                                       'target' => array()
                                      ),
                          'script' => array('src' => array(),
                                            'type' => array()),
                          'style' => array('type' => array()),
                          'div' => array('id' => array()),
                          'tr' => array(),
                          'table' => array(),
                          'button' => array('class' => array(),
                                            'style' => array(),
                                            'onclick' => array(),
                                            'aria-disabled' => array(),
                                            'type' => array()),
                          'td' => array(),
                          'br' => array(),
                          'b' => array(),
                          'img' => array('style' => array(),
                                         'src' => array(),
                                         'title'=> array(),
                                         'alt' => array()
                                              )
                               );
    return wp_kses($html,$allowed_html);

}

function tmy_g11n_available_post_types() {
 
    $post_types = get_post_types( array( 'public' => true ));
    unset($post_types['g11n_translation']); 
    return $post_types;

}

function tmy_g11n_is_valid_post_type($post_type) {
 
    $post_types = get_post_types( array( 'public' => true ));
    unset($post_types['g11n_translation']); 
    return array_key_exists($post_type,$post_types);

}

function tmy_g11n_available_post_type_options() {

    $ret_array = array();
 
    $post_types = get_post_types( array( 'public' => true ));
    unset($post_types['g11n_translation']); 

    foreach ( $post_types  as $post_type ) {
        $ret_array['g11n_l10n_props_' . $post_type] = 'g11n_l10n_props_' . $post_type;
    }
    $ret_array['g11n_l10n_props_blogname'] = 'g11n_l10n_props_blogname';
    $ret_array['g11n_l10n_props_blogdescription'] = 'g11n_l10n_props_blogdescription';
    return $ret_array;

}

function tmy_g11n_post_type_enabled($post_id, $post_title, $type) {

    $qualified_taxonomies = get_taxonomies(array("public" => true, "show_ui"=> true), "names", "or");
    if (array_key_exists($type, $qualified_taxonomies)) {
        $all_configed_taxs = get_option('g11n_l10n_props_tax', array());
        if (empty($all_configed_taxs)) {
            return false;
        } else {
            if (array_key_exists($type, $all_configed_taxs)) {
                return true;
            } else {
                return false;
            }
        }
    } elseif ((strcmp($post_title,"blogname")===0) || (strcmp($post_title,"blogdescription")===0)) {
        $option_name = "g11n_l10n_props_" . $post_title;
    } else {
        $post_type = get_post_type($post_id);
        $option_name = "g11n_l10n_props_" . $post_type;
    }

    if (strcmp(get_option($option_name),"Yes")===0) {
        return true;
    } else {
        return false;
    }

}


function insert_with_markers_default_front( $filename, $marker, $insertion ) {
	if ( ! file_exists( $filename ) ) {
		if ( ! is_writable( dirname( $filename ) ) ) {
			return false;
		}

		if ( ! touch( $filename ) ) {
			return false;
		}

		// Make sure the file is created with a minimum set of permissions.
		$perms = fileperms( $filename );

		if ( $perms ) {
			chmod( $filename, $perms | 0644 );
		}
	} elseif ( ! is_writable( $filename ) ) {
		return false;
	}

	if ( ! is_array( $insertion ) ) {
		$insertion = explode( "\n", $insertion );
	}

	$switched_locale = switch_to_locale( get_locale() );

	$instructions = sprintf(
		/* translators: 1: Marker. */
		__(
			'The directives (lines) between "BEGIN %1$s" and "END %1$s" are
dynamically generated, and should only be modified via WordPress filters.
Any changes to the directives between these markers will be overwritten.'
		),
		$marker
	);

	$instructions = explode( "\n", $instructions );

	foreach ( $instructions as $line => $text ) {
		$instructions[ $line ] = '# ' . $text;
	}

	/**
	 * Filters the inline instructions inserted before the dynamically generated content.
	 *
	 * @since 5.3.0
	 *
	 * @param string[] $instructions Array of lines with inline instructions.
	 * @param string   $marker       The marker being inserted.
	 */
	$instructions = apply_filters( 'insert_with_markers_inline_instructions', $instructions, $marker );

	if ( $switched_locale ) {
		restore_previous_locale();
	}

	$insertion = array_merge( $instructions, $insertion );

	$start_marker = "# BEGIN {$marker}";
	$end_marker   = "# END {$marker}";

	$fp = fopen( $filename, 'r+' );

	if ( ! $fp ) {
		return false;
	}

	// Attempt to get a lock. If the filesystem supports locking, this will block until the lock is acquired.
	flock( $fp, LOCK_EX );

	$lines = array();

	while ( ! feof( $fp ) ) {
		$lines[] = rtrim( fgets( $fp ), "\r\n" );
	}

	// Split out the existing file into the preceding lines, and those that appear after the marker.
	$pre_lines        = array();
	$post_lines       = array();
	$existing_lines   = array();
	$found_marker     = false;
	$found_end_marker = false;

	foreach ( $lines as $line ) {
		if ( ! $found_marker && false !== strpos( $line, $start_marker ) ) {
			$found_marker = true;
			continue;
		} elseif ( ! $found_end_marker && false !== strpos( $line, $end_marker ) ) {
			$found_end_marker = true;
			continue;
		}

		if ( ! $found_marker ) {
			$pre_lines[] = $line;
		} elseif ( $found_marker && $found_end_marker ) {
			$post_lines[] = $line;
		} else {
			$existing_lines[] = $line;
		}
	}

	// Check to see if there was a change.
	if ( $existing_lines === $insertion ) {
		flock( $fp, LOCK_UN );
		fclose( $fp );

		return true;
	}

	// Generate the new file data.
	$new_file_data = implode(
		"\n",
		array_merge(
			array( $start_marker ),
			$insertion,
			array( $end_marker ),
			$pre_lines,
			$post_lines
		)
	);

	// Write to the start of the file, and truncate it to that length.
	fseek( $fp, 0 );
	$bytes = fwrite( $fp, $new_file_data );

	if ( $bytes ) {
		ftruncate( $fp, ftell( $fp ) );
	}

	fflush( $fp );
	flock( $fp, LOCK_UN );
	fclose( $fp );

	return (bool) $bytes;
}
