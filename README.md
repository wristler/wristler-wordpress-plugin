# Wristler WordPress Plugin #
**Contributors:** [jponsen](https://profiles.wordpress.org/jponsen)  
**Tags:** wordpress, wristler  
**Requires at least:** 6.0  
**Requires PHP:** >= 7.4   
**Tested up to:** 6.2.2  
**Stable tag:** 1.1.3  
**License:** MIT  
**License URI:** https://opensource.org/licenses/MIT  

## Description ##
This plug-in enables you to sync your stock with Wristler.

## Installation ##

Simply install as a normal WordPress plugin and activate. Don't forget to fill in your security token at WooCommerce -> Integration -> Wristler.

## Filters ##

The filters below can be used to change various settings within the plug-in.

### Update image urls
`wristler_image_url(string $imageUrl): string`

Example:
```php
add_filter('wristler_image_url', function ($url) {
    return str_ireplace('old-domain.com', 'new-domain.com', $url);
});
```