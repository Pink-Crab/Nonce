# Nonce

A minimal object-based wrapper around the WordPress Nonce API. Each `Nonce` instance holds an action and a lazily-generated token, and exposes helpers for URL decoration, form-field rendering, validation, and admin-referer checks. Instances are serialisable so you can pass them through the request lifecycle without regenerating the token.

[![Latest Stable Version](https://poser.pugx.org/pinkcrab/wp-nonce/v)](https://packagist.org/packages/pinkcrab/wp-nonce)
[![Total Downloads](https://poser.pugx.org/pinkcrab/wp-nonce/downloads)](https://packagist.org/packages/pinkcrab/wp-nonce)
[![License](https://poser.pugx.org/pinkcrab/wp-nonce/license)](https://packagist.org/packages/pinkcrab/wp-nonce)
[![PHP Version Require](https://poser.pugx.org/pinkcrab/wp-nonce/require/php)](https://packagist.org/packages/pinkcrab/wp-nonce)
![GitHub contributors](https://img.shields.io/github/contributors/Pink-Crab/Nonce?label=Contributors)
![GitHub issues](https://img.shields.io/github/issues-raw/Pink-Crab/Nonce)

[![WP 6.6 [PHP8.0-8.4] Tests](https://github.com/Pink-Crab/Nonce/actions/workflows/WP_6_6.yaml/badge.svg)](https://github.com/Pink-Crab/Nonce/actions/workflows/WP_6_6.yaml)
[![WP 6.7 [PHP8.0-8.4] Tests](https://github.com/Pink-Crab/Nonce/actions/workflows/WP_6_7.yaml/badge.svg)](https://github.com/Pink-Crab/Nonce/actions/workflows/WP_6_7.yaml)
[![WP 6.8 [PHP8.0-8.4] Tests](https://github.com/Pink-Crab/Nonce/actions/workflows/WP_6_8.yaml/badge.svg)](https://github.com/Pink-Crab/Nonce/actions/workflows/WP_6_8.yaml)
[![WP 6.9 [PHP8.0-8.4] Tests](https://github.com/Pink-Crab/Nonce/actions/workflows/WP_6_9.yaml/badge.svg)](https://github.com/Pink-Crab/Nonce/actions/workflows/WP_6_9.yaml)

[![codecov](https://codecov.io/gh/Pink-Crab/Nonce/branch/master/graph/badge.svg?token=R3SB4WDL8Z)](https://codecov.io/gh/Pink-Crab/Nonce)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Pink-Crab/Nonce/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Pink-Crab/Nonce/?branch=master)

For more details please visit the docs site: https://perique.info/lib/WP_Nonce.html

## Why?

WordPress's native nonce API is procedural — `wp_create_nonce()`, `wp_verify_nonce()`, `wp_nonce_field()`, `check_admin_referer()` — and leaves the caller to juggle the action handle alongside the token at every touch-point. In practice nonces are a single unit: one action string, one token, one lifecycle. This library wraps that unit in a small class so it can be passed around, serialised, and stored alongside whatever else needs the nonce (a form model, an AJAX endpoint spec, a REST request builder).

## Install

```bash
composer require pinkcrab/wp-nonce
```

Then include the Composer autoloader in your project:

```php
require_once __DIR__ . '/vendor/autoload.php';
```

## Usage

```php
use PinkCrab\Nonce\Nonce;

$nonce = new Nonce( 'my_action' );

// Get the current token.
$nonce->token();

// Render a <input type="hidden"> nonce field.
echo $nonce->nonce_field();

// Validate a submitted token.
if ( ! $nonce->validate( $_POST['_wpnonce'] ?? '' ) ) {
    wp_die( 'Invalid nonce' );
}

// Decorate a URL with the nonce token.
$url = $nonce->as_url( 'https://example.com/admin-action' );
```

## Methods (Setters)

### __construct
**__construct( string $action )**
> @param string $action The action handle that scopes the nonce (same semantics as WordPress's `wp_create_nonce($action)`).

Creates a nonce instance bound to a single action handle. The token is generated lazily on first access.

*Example*
```php
$nonce = new Nonce( 'save_settings' );
```

## Methods (Getters & Helpers)

### token
**token(): string**
> @return string The nonce token string, generated via `wp_create_nonce()` the first time it's called.

Returns the nonce token. Matches what `wp_create_nonce($action)` would produce for the same action.

*Example*
```php
$nonce = new Nonce( 'save_settings' );
echo $nonce->token(); // e.g. "31b31db189"
```

### as_url
**as_url( string $url, string $arg = '_wpnonce' ): string**
> @param string $url The base URL to append the nonce to.  
> @param string $arg Query-string parameter name the nonce is appended as. Defaults to `_wpnonce`.  
> @return string The URL with `?<arg>=<token>` (or `&<arg>=<token>`) appended.

Appends the nonce token to a URL as a query-string parameter. Handles both URLs without an existing query string and URLs that already have one.

*Example*
```php
$nonce = new Nonce( 'url_action' );

$nonce->as_url( 'https://example.com/do' );
// https://example.com/do?_wpnonce=<token>

$nonce->as_url( 'https://example.com/do?id=42', 'my_nonce' );
// https://example.com/do?id=42&my_nonce=<token>
```

> This does not use the admin-referer mechanism — for that use `admin_referer()` below.

### nonce_field
**nonce_field( string $name = '_wpnonce' ): string**
> @param string $name The input name/id used for the hidden field. Defaults to `_wpnonce`.  
> @return string The HTML for a hidden `<input>` containing the nonce token.

Returns (does not echo) the HTML for a hidden `<input>` carrying the nonce token. Useful for form-building code that prefers to compose output itself.

*Example*
```php
$nonce = new Nonce( 'form_submit' );

echo $nonce->nonce_field( 'settings_nonce' );
// <input type="hidden" id="settings_nonce" name="settings_nonce" value="<token>">

echo $nonce->nonce_field();
// <input type="hidden" id="_wpnonce" name="_wpnonce" value="<token>">
```

> The nonce field is returned, not echoed. Call `echo` yourself.

### validate
**validate( string $nonce ): bool**
> @param string $nonce The token string to validate against the nonce's action.  
> @return bool `true` if the token matches; `false` otherwise.

Validates a token string against the nonce's action. Returns `true` for a match, `false` otherwise. Wraps `wp_verify_nonce()` with a strict-bool return.

*Example*
```php
$nonce = new Nonce( 'my_action' );

$nonce->validate( $nonce->token() ); // true
$nonce->validate( 'anything_else' ); // false
```

### admin_referer
**admin_referer( string $name = '_wpnonce' ): bool**
> @param string $name The `$_REQUEST` key holding the nonce. Defaults to `_wpnonce`.  
> @return bool `true` if the admin-referer check passes.

Runs WordPress's `check_admin_referer()` against the token found in `$_REQUEST[$name]`. Returns `true` on success; WordPress itself will `wp_die()` on failure (which in tests throws `WPDieException`).

*Example*
```php
$nonce = new Nonce( 'admin_save' );

if ( $nonce->admin_referer() ) {
    // OK to proceed.
}
```

## Serialisation

`Nonce` implements `Serializable` (via PHP's magic methods), so instances can be stored alongside other request state and recovered later without regenerating the token:

```php
$nonce    = new Nonce( 'serialised' );
$s_nonce  = serialize( $nonce );
$restored = unserialize( $s_nonce );

$restored->token() === $nonce->token(); // true
```

## Tested Against

* PHP 8.0, 8.1, 8.2, 8.3 & 8.4
* WP 6.6, 6.7, 6.8 & 6.9
* MySQL 8.4

## License

### MIT License

http://www.opensource.org/licenses/mit-license.html

## Change Log

* 1.0.0 - First stable release. Drop PHP 7.x, require PHP 8.0+. Modernise the tooling chain (PHPStan 2.x, PHPUnit 8|9, WPCS 3.x, `yoast/phpunit-polyfills` widened to include v4). Replace the single GitHub_CI workflow with the WP 6.6–6.9 matrix (PHP 8.0–8.4, `mysql:8.4`) using `codecov/codecov-action@v4`. Suppress the WP 6.8 `wp_is_block_theme` early-call notice in `tests/wp-config.php`. Swap the custom VCS-sourced `pinkcrab/phpunit-helpers` dev dep for the packaged `gin0115/wpunit-helpers`; migrate `Test_Nonce` accordingly (`Reflection::get_private_property` → `Objects::get_property`). Remove the `repositories` block and `object-calisthenics/phpcs-calisthenics-rules` from dev-deps. README reformatted to the shared lib template.
* 0.1.0 - Created from part of PC Framework 0.1.0
