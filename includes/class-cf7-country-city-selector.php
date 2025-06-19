<?php
/**
 * Main plugin class
 */
class CF7_Country_City_Selector {

    /**
     * Initialize the plugin
     */
    public function init() {
        // Make sure Contact Form 7 is loaded
        if (!function_exists('wpcf7_add_form_tag')) {
            return;
        }

        // Register form tags
        add_action('wpcf7_init', array($this, 'register_form_tags'));
        
        // Enqueue assets
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        
        // Add shortcode generator
        add_action('wpcf7_admin_init', array($this, 'add_tag_generator'));
        
        // Register REST API endpoint for cities
        add_action('rest_api_init', array($this, 'register_rest_routes'));
    }

    /**
     * Register custom form tags
     */
    public function register_form_tags() {
        wpcf7_add_form_tag(
            array('country', 'country*'),
            array($this, 'country_form_tag_handler'),
            array('name-attr' => true)
        );
        
        wpcf7_add_form_tag(
            array('city', 'city*'),
            array($this, 'city_form_tag_handler'),
            array('name-attr' => true)
        );
    }

    /**
     * Handle country form tag
     */
    public function country_form_tag_handler($tag) {
        $tag = new WPCF7_FormTag($tag);
        
        if (empty($tag->name)) {
            return '';
        }
        
        $validation_error = wpcf7_get_validation_error($tag->name);
        $class = wpcf7_form_controls_class($tag->type);
        
        if ($validation_error) {
            $class .= ' wpcf7-not-valid';
        }
        
        $atts = array();
        $atts['class'] = $tag->get_class_option($class);
        $atts['id'] = $tag->get_id_option();
        $atts['name'] = $tag->name;
        $atts['data-dependent-field'] = $tag->name . '-city'; // The city field that depends on this
        
        if ($tag->is_required()) {
            $atts['required'] = 'required';
            $atts['aria-required'] = 'true';
        }
        
        $countries = $this->get_countries();
        
        $html = sprintf(
            '<span class="wpcf7-form-control-wrap %1$s">',
            sanitize_html_class($tag->name)
        );
        
        $html .= sprintf('<select %s>', wpcf7_format_atts($atts));
        $html .= '<option value="">— ' . __('Select Country', 'cf7-country-city-selector') . ' —</option>';
        
        foreach ($countries as $code => $name) {
            $html .= sprintf(
                '<option value="%1$s">%2$s</option>',
                esc_attr($code),
                esc_html($name)
            );
        }
        
        $html .= '</select>';
        $html .= $validation_error;
        $html .= '</span>';
        
        return $html;
    }

    /**
     * Handle city form tag
     */
    public function city_form_tag_handler($tag) {
        $tag = new WPCF7_FormTag($tag);
        
        if (empty($tag->name)) {
            return '';
        }
        
        $validation_error = wpcf7_get_validation_error($tag->name);
        $class = wpcf7_form_controls_class($tag->type);
        
        if ($validation_error) {
            $class .= ' wpcf7-not-valid';
        }
        
        $atts = array();
        $atts['class'] = $tag->get_class_option($class);
        $atts['id'] = $tag->get_id_option();
        $atts['name'] = $tag->name;
        $atts['data-depends-on'] = str_replace('-city', '', $tag->name); // The country field this depends on
        $atts['disabled'] = 'disabled';
        
        if ($tag->is_required()) {
            $atts['required'] = 'required';
            $atts['aria-required'] = 'true';
        }
        
        $html = sprintf(
            '<span class="wpcf7-form-control-wrap %1$s">',
            sanitize_html_class($tag->name)
        );
        
        $html .= sprintf('<select %s>', wpcf7_format_atts($atts));
        $html .= '<option value="">— ' . __('Select City', 'cf7-country-city-selector') . ' —</option>';
        // Cities will be populated via JavaScript
        $html .= '</select>';
        $html .= $validation_error;
        $html .= '</span>';
        
        return $html;
    }

    /**
     * Enqueue scripts and styles
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            'cf7-country-city',
            CF7_COUNTRY_CITY_SELECTOR_URL . 'assets/js/cf7-country-city.js',
            array('jquery'),
            CF7_COUNTRY_CITY_SELECTOR_VERSION,
            true
        );
        
        wp_localize_script(
            'cf7-country-city',
            'cf7CountryCityData',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'rest_url' => rest_url('cf7-country-city/v1/cities'),
                'nonce' => wp_create_nonce('wp_rest'),
                'loading_text' => __('Loading cities...', 'cf7-country-city-selector'),
                'select_city_text' => __('Select City', 'cf7-country-city-selector'),
                'no_cities_text' => __('No cities found', 'cf7-country-city-selector'),
                'error_text' => __('Error loading cities', 'cf7-country-city-selector')
            )
        );
        
        wp_enqueue_style(
            'cf7-country-city',
            CF7_COUNTRY_CITY_SELECTOR_URL . 'assets/css/cf7-country-city.css',
            array(),
            CF7_COUNTRY_CITY_SELECTOR_VERSION
        );
    }

    /**
     * Add tag generator
     */
    public function add_tag_generator() {
        if (!class_exists('WPCF7_TagGenerator')) {
            return;
        }
        
        $tag_generator = WPCF7_TagGenerator::get_instance();
        
        $tag_generator->add(
            'country',
            __('Country Dropdown', 'cf7-country-city-selector'),
            array($this, 'tag_generator_country')
        );
        
        $tag_generator->add(
            'city',
            __('City Dropdown', 'cf7-country-city-selector'),
            array($this, 'tag_generator_city')
        );
    }

    /**
     * Display tag generator for country
     */
    public function tag_generator_country($contact_form, $args = '') {
        $args = wp_parse_args($args, array());
        $type = 'country';
        
        $description = __('Generate a form-tag for a country selection dropdown.', 'cf7-country-city-selector');
        
        include CF7_COUNTRY_CITY_SELECTOR_PATH . 'includes/tag-generator-country.php';
    }

    /**
     * Display tag generator for city
     */
    public function tag_generator_city($contact_form, $args = '') {
        $args = wp_parse_args($args, array());
        $type = 'city';
        
        $description = __('Generate a form-tag for a city selection dropdown that depends on a country field.', 'cf7-country-city-selector');
        
        include CF7_COUNTRY_CITY_SELECTOR_PATH . 'includes/tag-generator-city.php';
    }

    /**
     * Register REST API routes
     */
    public function register_rest_routes() {
        register_rest_route('cf7-country-city/v1', '/cities/(?P<country>[\w-]+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_cities_for_country'),
            'permission_callback' => '__return_true',
        ));
    }

    /**
     * Get countries
     */
    private function get_countries() {
        $data_file = CF7_COUNTRY_CITY_SELECTOR_PATH . 'includes/data/countries.json';
        $data = json_decode(file_get_contents($data_file), true);
        
        $countries = array();
        foreach ($data as $country) {
            $countries[$country['code']] = $country['name'];
        }
        
        return $countries;
    }

    /**
     * Get cities for a country
     */
    private function get_cities($country_code) {
        // First check if we have a local cache file for this country
        $cache_file = CF7_COUNTRY_CITY_SELECTOR_PATH . 'includes/data/cities/' . $country_code . '.json';
        
        if (file_exists($cache_file)) {
            $data = json_decode(file_get_contents($cache_file), true);
            return $data;
        }
        
        // If no cache, fetch from API and cache it
        $cities = $this->fetch_cities_from_api($country_code);
        
        // Ensure we have a valid array even if API fails
        if (!is_array($cities)) {
            $cities = array();
        }
        
        // Return the cities
        return $cities;
    }
    
    /**
     * Get cities for a given country from REST API
     */
    public function get_cities_for_country($request) {
        $country_code = $request->get_param('country');
        if (empty($country_code)) {
            return new WP_Error('invalid_country', __('Invalid country code', 'cf7-country-city-selector'), array('status' => 400));
        }

        // Sanitize the country code
        $country_code = sanitize_text_field(strtoupper($country_code));

        // Get cities from the local JSON files via get_major_cities
        $cities = $this->get_major_cities($country_code);
        
        if (empty($cities)) {
            // If get_major_cities returns empty (e.g., file not found or empty), return a 404 type error
            return new WP_Error('no_cities', __('No cities found for this country', 'cf7-country-city-selector'), array('status' => 404));
        }
        
        return rest_ensure_response($cities);
    }
    
    /**
     * Get major cities for a given country by reading its local JSON file.
     */
    private function get_major_cities($country_code) {
        $cities = array();
        $data_file_path = CF7_COUNTRY_CITY_SELECTOR_PATH . 'includes/data/cities/' . strtoupper($country_code) . '.json';

        if (file_exists($data_file_path)) {
            $file_content = file_get_contents($data_file_path);
            if ($file_content !== false) {
                $decoded_cities = json_decode($file_content, true);
                if (is_array($decoded_cities)) {
                    // Ensure unique cities and sort them alphabetically
                    $cities = array_values(array_unique($decoded_cities));
                    sort($cities);
                } else {
                    // Log error if JSON is invalid
                    error_log('[CF7CS] Invalid JSON in city file: ' . $data_file_path);
                }
            } else {
                // Log error if file cannot be read
                error_log('[CF7CS] Could not read city file: ' . $data_file_path);
            }
        } else {
            // Log if specific country JSON file is missing (optional, can be noisy)
            // error_log('[CF7CS] City file not found: ' . $data_file_path);
        }
        
        return $cities;
    }
}
