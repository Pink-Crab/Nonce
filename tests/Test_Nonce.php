<?php

declare(strict_types=1);

/**
 * Tests for the MemoizeAware Trait.
 *
 * @since 0.2.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Memoize
 */

namespace PinkCrab\Nonce\Tests;

use PinkCrab\Nonce\Nonce;
use PinkCrab\PHPUnit_Helpers\Output;
use PinkCrab\PHPUnit_Helpers\Reflection;

class Test_Nonce extends \WP_UnitTestCase {

	public function test_can_create_nonce(): void {
		$nonce = new Nonce( 'test' );
		$this->assertInstanceOf( Nonce::class, $nonce );

		// Ensure the properties are set.
		$this->assertEquals( 'test', Reflection::get_private_property( $nonce, 'action' ) );
		$this->assertNotEmpty( Reflection::get_private_property( $nonce, 'nonce_token' ) );
	}

	/**
	 * Ensure the nonce token created internally, matches that of native wp_create_nonce.
	 *
	 * @return void
	 */
	public function test_ensure_nonce_token_created_correctly_us_wp_create_nonce(): void {
		$nonce    = new Nonce( 'test' );
		$wp_nonce = \wp_create_nonce( 'test' );

		$this->assertSame( $nonce->token(), $wp_nonce );
	}

	/**
	 * Ensure the nonce is added to a url is passed.
	 *
	 * @return void
	 */
	public function test_nonce_added_to_url(): void {
		$simple_url  = 'https://www.test.com';
		$complex_url = 'https://www.test.com?spoon=man&other=thing';

		$nonce = new Nonce( 'test_nonce_added_to_url' );

		// Using custom param.
		$this->assertEquals(
			"https://www.test.com?custom_nonce={$nonce->token()}",
			$nonce->as_url( $simple_url, 'custom_nonce' )
		);

		$this->assertEquals(
			"https://www.test.com?spoon=man&other=thing&custom_nonce={$nonce->token()}",
			$nonce->as_url( $complex_url, 'custom_nonce' )
		);

		// WIthout custom parameter
		$this->assertEquals(
			"https://www.test.com?_wpnonce={$nonce->token()}",
			$nonce->as_url( $simple_url )
		);

		$this->assertEquals(
			"https://www.test.com?spoon=man&other=thing&_wpnonce={$nonce->token()}",
			$nonce->as_url( $complex_url )
		);
	}

	/**
	 * Ensure the nonce value can be validated
	 *
	 * @return void
	 */
	public function test_can_verify_nonce(): void {
		$nonce = new Nonce( 'test' );

		$this->assertTrue( $nonce->validate( $nonce->token() ) );
		$this->assertFalse( $nonce->validate( 'FAILED' ) );
	}

	/**
	 * Test the nonce field is generated
	 *
	 * @return void
	 */
	public function test_can_generate_nonce_field(): void {
		$nonce = new Nonce( 'test' );
		$this->assertStringContainsString( $nonce->token(), $nonce->nonce_field() );
		$this->assertStringContainsString( '<input type="hidden"', $nonce->nonce_field() );
		$this->assertStringContainsString( 'name="_wpnonce"', $nonce->nonce_field() );
		$this->assertStringContainsString( 'id="_wpnonce"', $nonce->nonce_field() );
	}

	/**
	 * Test the nonce can be verified form a URL.
	 *
	 * @return void
	 */
	public function test_can_valiadte_url(): void {
		$nonce = new Nonce( 'test' );

		// Mock in global request.
		$_REQUEST['_wpnonce'] = $nonce->token();
		$this->assertTrue( $nonce->admin_referer() );

		// Unset and call again, expect wp_die.
		unset( $_REQUEST['_wpnonce'] );
		try {
			$nonce->admin_referer();
		} catch ( \Throwable $th ) {
			$this->assertInstanceOf( \WPDieException::class, $th );
		}

	}

	/**
	 * Test that a nonce can be serialised and unserialised.
	 *
	 * @return void
	 */
	public function test_can_serialise_nonce(): void {
		$nonce = new Nonce( 'serialised' );

		// Serialise and unserialise the nonce object.
		$s_nonce = \serialize( $nonce );
		$u_nonce = \unserialize( $s_nonce );

		$this->assertEquals( $nonce->token(), $u_nonce->token() );
	}
}
