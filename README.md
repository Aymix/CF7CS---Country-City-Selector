# Contact Form 7 - Country City Selector

A WordPress plugin that adds dynamic country and city select fields to Contact Form 7.

## Features

- Adds two new form tags: `[country]` and `[city]`
- City dropdown dynamically updates based on country selection
- Fully integrates with Contact Form 7 validation
- Easy to use with the Contact Form 7 form editor
- Includes data for 15 countries and their major cities
- Automatic field dependency handling

## Usage

1. Install and activate the plugin
2. Edit a Contact Form 7 form
3. Add a country field using the "Country Dropdown" button
4. Add a city field using the "City Dropdown" button
5. Make sure to name your city field with the same prefix as your country field, followed by "-city" 
   (e.g., if your country field is named "your-country", your city field should be named "your-country-city")

### Example Form

```
<label>Country
    [country* your-country]
</label>

<label>City
    [city* your-country-city]
</label>

[submit "Submit"]
```

## Requirements

- WordPress 5.0 or higher
- Contact Form 7 5.0 or higher

## Installation

1. Upload the plugin files to the `/wp-content/plugins/cf7-country-city-selector` directory
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the new form tags in your Contact Form 7 forms

## Customization

If you need to add more countries or cities, you can modify the `includes/data/countries-cities.json` file.
