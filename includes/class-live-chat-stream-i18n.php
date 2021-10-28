<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://pbdigital.com.au/
 * @since      1.0.0
 *
 * @package    Live_Chat_Stream
 * @subpackage Live_Chat_Stream/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Live_Chat_Stream
 * @subpackage Live_Chat_Stream/includes
 * @author     Paul Bright <paul@pbdigital.com.au>
 */
class Live_Chat_Stream_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'live-chat-stream',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
