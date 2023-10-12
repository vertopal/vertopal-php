# Vertopal-PHP

Vertopal-PHP is a PHP library for easy access to
[Vertopal file conversion API](https://www.vertopal.com/en/developer/api).

Using Vertopal-PHP, you can get started quickly and easily implement
the support of converting +350 file formats into your project.

## Installing Vertopal-PHP

Vertopal-PHP is available on
[Packagist](https://packagist.org/packages/vertopal/vertopal-php) and
can be installed using [Composer](https://getcomposer.org/):

```bash
composer require vertopal/vertopal-php
```

If you're not using Composer, you can also download the most recent version of
Vertopal-PHP source code as a ZIP file from the
[release page](https://github.com/vertopal/vertopal-php/releases/latest)
and load each class file manually.

### Requirements

- **PHP 7.4.0** or higher
- **cURL** extension enabled

## Using Vertopal-PHP

To use Vertopal-PHP you need to
[obtain an App-ID and a Security Token](http://www.vertopal.com/en/account/api/app/new)
as client credentials for API authentication.

The following code illustrates
[GIF to APNG](https://www.vertopal.com/en/convert/gif-to-apng) conversion using
the Vertopal PHP library.

```php
<?php
// Import Vertopal classes into the global namespace
use Vertopal\API\Credential;
use Vertopal\API\Converter;

// Load Composer Autoloader
require "vendor/autoload.php";

// Create a client credential instance using your app ID and security token
$app = "your-app-id";
$token = "your-security-token";
$credential = new Credential($app, $token);

// Convert and download your file using the Converter class
$converter = new Converter("MickeyMouse.gif", $credential);
$converter->convert("apng");
$converter->wait();
if ($converter->isConverted()) {
    $converter->download();
}
```
