# Nonce

Simple class based WP Nonce solution  

![alt text](https://img.shields.io/badge/Current_Version-0.1.0-yellow.svg?style=flat " ") 
[![Open Source Love](https://badges.frapsoft.com/os/mit/mit.svg?v=102)](https://github.com/ellerbrock/open-source-badge/)

![](https://github.com/Pink-Crab/Nonce/workflows/GitHub_CI/badge.svg " ")
[![codecov](https://codecov.io/gh/Pink-Crab/Nonce/branch/master/graph/badge.svg?token=R3SB4WDL8Z)](https://codecov.io/gh/Pink-Crab/Nonce)



For more details please visit our docs.
https://app.gitbook.com/@glynn-quelch/s/pinkcrab/
 

## Version ##

**Release 0.1.0**

## Why? ##
Allows for use of Nonces in an oop mannor, while allowing serialisation/deserialsation of the object.

## Setup ##

```bash 
$ composer require pinkcrab/wp-nonce

``` 

```php
    $nonce = new Nonce('my_none_key');

    // To get the current nonce token
    $nonce->token();
    
    // To validate
    $nonce->validate($_POST['nonce']); // true/false

    // To add to url
    $url = $nonce->as_url('http://www.url.com', 'my_nonce'); // http://www.url.com?my_nonce={nonce_value}

    // Validate url.
    $nonce->admin_referer('my_nonce'); // true/false if set in url.
```

# Methods

## Create Instance

``` php

// Create with a custom key
$custom_nonce = new Nonce('custom_key');

```

> Once your nonce has been created, it can be serialised and/or passed around your codebase.

## as_url( string $url, string $arg='_wpnonce' ): string

``` php

$nonce = new Nonce('url_key');

$custom_key_in_url = $nonce->as_url('http://test.com', 'url_nonce');
// http://test.com?url_nonce={nonce_token}

$default_key_in_url = $nonce->as_url('http://test.com');
// http://test.com?_wpnonce={nonce_token}
```

> NOTICE! This doesnt make use of the refer value found in admin nonces.

## token(): string

``` php

$nonce = new Nonce('url_key');

// To get the current nonce value.
print $nonce->token(); // 31b31db189

$nonce_token = nonce->token(); // 31b31db189
```

## nonce_field($name = '_wpnonce'): string

``` php

$nonce = new Nonce('as_input');

// Create a nonce field, with a custom id/name for input
print $nonce->nonce_field('my_nonce'); 
// <input type="hidden" id="my_nonce" name="my_nonce" value="{nonce_token}">

// Create a nonce field, with a custom id/name for input
print $nonce->nonce_field(); 
// <input type="hidden" id="_wpnonce_" name="_wpnonce_" value="{nonce_token}">
```
> The nonce field is not automcatically printed

## validate($name = '_wpnonce'): string

``` php

$nonce = new Nonce('as_input');

// Create a nonce field, with a custom id/name for input
print $nonce->nonce_field('my_nonce'); 
// <input type="hidden" id="my_nonce" name="my_nonce" value="{nonce_token}">

// Create a nonce field, with a custom id/name for input
print $nonce->nonce_field(); 
// <input type="hidden" id="_wpnonce_" name="_wpnonce_" value="{nonce_token}">
```
> The nonce field is not automcatically printed

## Dependencies ##

* --NONE--

## License ##

### MIT License ###

http://www.opensource.org/licenses/mit-license.html  

## Change Log ##

0.1.0 - Created from part of PC Framework 0.1.0
