(function($) {
    'use strict';
    
    $(document).ready(function() {
        // Initialize any country fields that have a value on page load
        $('select[data-dependent-field]').each(function() {
            var countryField = $(this);
            if (countryField.val()) {
                countryField.trigger('change');
            }
        });
        
        // Handle country selection
        $(document).on('change', 'select[data-dependent-field]', function() {
            var countryField = $(this);
            var countryCode = countryField.val();
            var cityFieldName = countryField.data('dependent-field');
            var cityField = $('select[name="' + cityFieldName + '"]');
            
            // Reset city field
            cityField.prop('disabled', true);
            
            if (!countryCode) {
                cityField.html('<option value="">— ' + cf7CountryCityData.select_city_text + ' —</option>');
                return;
            }
            
            cityField.html('<option value="">— ' + cf7CountryCityData.loading_text + ' —</option>');
            
            // Get cities for the selected country
            $.ajax({
                url: cf7CountryCityData.rest_url + '/' + countryCode,
                method: 'GET',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', cf7CountryCityData.nonce);
                },
                success: function(response) {
                    if (response && response.length > 0) {
                        var options = '<option value="">— ' + cf7CountryCityData.select_city_text + ' —</option>';
                        
                        $.each(response, function(index, city) {
                            options += '<option value="' + city + '">' + city + '</option>';
                        });
                        
                        cityField.html(options);
                        cityField.prop('disabled', false);
                    } else {
                        cityField.html('<option value="">— ' + cf7CountryCityData.no_cities_text + ' —</option>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading cities:', error);
                    cityField.html('<option value="">— ' + cf7CountryCityData.error_text + ' —</option>');
                }
            });
        });
    });
})(jQuery);
