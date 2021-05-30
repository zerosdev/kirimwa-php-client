# KirimWA PHP Client
Client SDK Library for Kirimwa.cloud Server Application

## Requirements
- PHP 5.6+
- PHP JSON Extension
- [Guzzle, PHP HTTP Client](https://github.com/guzzle/guzzle)

## Installation

1. Run command
<pre><code>composer require zerosdev/kirimwa-php-client</code></pre>

### The following steps only needed if you are using Laravel

> For installation on Laravel 5.5+, **SKIP steps 2 & 3** because we have used the Package Discovery feature, Laravel will automatically register the Service Provider and Alias during installation.

2. Open your **config/app.php** and add this code to the providers array, it will looks like:
<pre><code>'providers' => [

      // other providers

      ZerosDev\KirimWA\Laravel\ServiceProvider::class,

],</code></pre>

3. Add this code to your class aliases array
<pre><code>'aliases' => [

      // other aliases

      'KirimWA' => ZerosDev\KirimWA\Laravel\Facade::class,

],</code></pre>

4. Run command
<pre><code>composer dump-autoload</code></pre>

5. Then
<pre><code>php artisan vendor:publish --provider="ZerosDev\KirimWA\Laravel\ServiceProvider"</code></pre>

6. Edit **config/kirimwa.php** and put your KirimWA Server information like API host and sender list

## Basic Usage

### Laravel Usage

```php
<?php

namespace App\Http\Controllers;

use KirimWA;

class YourController extends Controller
{
    public function index()
    {
        KirimWA::sendText('6281234567890', 'This is message to be sent');

        if( KirimWA::hasError() ) {
            dd(KirimWA::error());
        }
        else {
            dd(KirimWA::response());
        }
    }
}
```

### Non-Laravel Usage

```php
<?php

require 'path/to/your/vendor/autoload.php';

use ZerosDev\KirimWA\Client;
use ZerosDev\KirimWA\Config;

Config::apply([
    'default_host'  => 'http://yourwaserver.com',
    'senders'       => [
        '6281234567890' => [
            'host'  => ':3001', // just place port number to follow 'default_host'
            'key'   => 'your api key here'
        ],
        '6280987654321' => [
            'host'  => 'http://youranotherwaserver.com:3002', // or place full API host with port to use different host than the 'default_host'
            'key'   => 'your api key here'
        ],
    ]
]);

$kirimwa = new Client();
$kirimwa->sendText('6281234567890', 'This is message to be sent');

if( $kirimwa->hasError() ) {
    echo $kirimwa->error();
}
else {
    echo $kirimwa->response();
}
```
