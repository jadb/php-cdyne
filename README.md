# PHP Wrapper for CDYNE APIs

Looking to use [CDYNE](http://cdyne.com) [Data Quality](http://cdyne.com/products/data-quality.aspx) or
[Communication](http://cdyne.com/products/communications.aspx) APIs from a PHP application you've built?

## Installing

You only need to clone this repository somewhere in your application's libraries or vendors folder.

    $ git clone https://github.com/jadb/php-cdyne/php-cdyne.git
  
## Using

Here is an example of using `CdynePav::verifyAddress()`:

    <?php
    include 'path/to/php-cdyne/DataQuality/CdynePav.php';
    $Pav = new CdynePav('your-license-key');
    $data = array(
      'FirmOrRecipient' => 'CDYNE Corporation',
      'PrimaryAddressLine' => '505 Independence Pkwy Ste 300',
      'SecondaryAddressLine' => '',
      'Urbanization' => '',
      'CityName' => 'Chesapeake',
      'State' => 'Virginia',
      'ZipCode' => '23320'
    );
    $result = $Pav->verifyAddress($data);
    print_r($result);

## Testing

Tests require [PHPUnit 3.6](https://github.com/sebastianbergmann/phpunit).

    $ phpunit --verbose /path/to/php-cdyne/Tests/Cdyne
    $ phpunit --verbose /path/to/php-cdyne/Tests/DataQuality/CdynePav

You'll notice skipped tests. This is because they require a license key which you can define in `Tests/constants.php`
like so:

    <?php
    define('CDYNE_PAV_KEY', '000000000-000000-0000000000');

All constants use capital letters, start with 'CDYNE' and define the service associated with the key by its shortcode
(i.e. PN for Phone Notify!).

## Credits

* [Jad Bitar](https://github.com/jadb)

## Patches & Features

* Fork
* Mod, fix
* Test - this is important, so it's not unintentionally broken
* Commit - do not mess with license, todo, version, etc. (if you do change any, make them into commits of their own that I can ignore when I pull)
* Pull request - bonus point for topic branches

## Bugs & Feedback

https://github.com/jadb/php-cdyne/issues