<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://swasis.com
 * @since      0.0.7
 *
 * @package    FISIF_Tools
 * @subpackage FISIF_Tools/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      0.0.7
 * @package    FISIF_Tools
 * @subpackage FISIF_Tools/includes
 * @author     James S. Moore <james@teamweb.us>
 */
class FISIF_Tools_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    0.0.7
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'fisif-tools',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
