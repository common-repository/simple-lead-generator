<?php
/**
 * Plugin Name: Simple Lead Generator
 * Description: Generate the leads with easy to AJAX based form. Simply use the shortcode <code>[simple_lead_generator]</code> anywhere.
 * Plugin URI: https://profiles.wordpress.org/surror/
 * Author: Surror
 * Author URI: https://surror.com/
 * Version: 1.0.2
 * License: GNU General Public License v2.0
 * Text Domain: simple-lead-generator
 *
 * @package Simple Lead Generator
 */

// Set constants.
define( 'SIMPLE_LEAD_GENERATOR_VER', '1.0.2' );
define( 'SIMPLE_LEAD_GENERATOR_FILE', __FILE__ );
define( 'SIMPLE_LEAD_GENERATOR_BASE', plugin_basename( SIMPLE_LEAD_GENERATOR_FILE ) );
define( 'SIMPLE_LEAD_GENERATOR_DIR', plugin_dir_path( SIMPLE_LEAD_GENERATOR_FILE ) );
define( 'SIMPLE_LEAD_GENERATOR_URI', plugins_url( '/', SIMPLE_LEAD_GENERATOR_FILE ) );

require_once SIMPLE_LEAD_GENERATOR_DIR . 'classes/class-simple-lead-generator.php';
