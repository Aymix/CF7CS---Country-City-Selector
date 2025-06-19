<?php
/**
 * Plugin Name: CF7CS - Country City Selector
 * Description: Adds dynamic country and city select fields to Contact Form 7
 * Version: 1.0.0
 * Author: Cascade AI   
 * Text Domain: cf7cs-country-city-selector
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('CF7_COUNTRY_CITY_SELECTOR_VERSION', '1.0.0');
define('CF7_COUNTRY_CITY_SELECTOR_PATH', plugin_dir_path(__FILE__));
define('CF7_COUNTRY_CITY_SELECTOR_URL', plugin_dir_url(__FILE__));

// Check if Contact Form 7 is active
add_action('admin_init', 'cf7_country_city_selector_check_dependencies');
function cf7_country_city_selector_check_dependencies() {
    if (!function_exists('is_plugin_active')) {
        include_once(ABSPATH . 'wp-admin/includes/plugin.php');
    }
    if (!is_plugin_active('contact-form-7/wp-contact-form-7.php')) {
        add_action('admin_notices', 'cf7_country_city_selector_admin_notice');
        deactivate_plugins(plugin_basename(__FILE__));
    }
}

function cf7_country_city_selector_admin_notice() {
    echo '<div class="error"><p>Contact Form 7 - Country City Selector requires Contact Form 7 to be installed and activated.</p></div>';
}

// Load the main plugin class
require_once CF7_COUNTRY_CITY_SELECTOR_PATH . 'includes/class-cf7-country-city-selector.php';

// Initialize the plugin
function cf7_country_city_selector_init() {
    $cf7_country_city = new CF7_Country_City_Selector();
    $cf7_country_city->init();
}
add_action('plugins_loaded', 'cf7_country_city_selector_init');
