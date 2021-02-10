<?php

declare(strict_types=1);

/**
 * OOP Nonce implentation
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Nonce
 */

namespace PinkCrab\Nonce;

class Nonce {

	/**
	 * The action key
	 *
	 * @var string
	 */
	protected $action;

	/**
	 * The nonce token
	 *
	 * @var string
	 */
	protected $nonce_token;

	public function __construct( string $action ) {
		$this->action      = $action;
		$this->nonce_token = \wp_create_nonce( $action );
	}

	/**
	 * Adds the nonce to a url.
	 *
	 * @param string $url
	 * @param string $arg
	 * @return string
	 */
	public function as_url( string $url, string $arg = '_wpnonce' ): string {
		return \add_query_arg( $arg, $this->nonce_token, $url );
	}

	/**
	 * Returns the generated token.
	 *
	 * @return string
	 */
	public function token(): string {
		return $this->nonce_token;
	}

	/**
	 * Returns nonce form fields as a string.
	 * Doesnt use the refer attribute.
	 *
	 * @param string $name The name of the field (HTML Attribute)
	 * @return string
	 */
	public function nonce_field( string $name = '_wpnonce' ): string {
		return \wp_nonce_field( $this->action, $name, false, false );
	}

	/**
	 * Validates a nonce token.
	 *
	 * @param string $token
	 * @return bool
	 */
	public function validate( string $token ): bool {
		return (bool) \wp_verify_nonce( $token, $this->action );
	}

	/**
	 * Checks the nonce on a url in wp-admin
	 *
	 * @param string|null $nonce_name If custom handle set, else uses _wpnonce as fallback.
	 * @return bool
	 */
	public function admin_referer( ?string $nonce_name = null ): bool {
		return (bool) \check_admin_referer( $this->action, $nonce_name ?? '_wpnonce' );
	}
}
