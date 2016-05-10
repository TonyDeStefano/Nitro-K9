<?php

namespace NitroK9;

class Controller {

	const VERSION = '1.0.0';
	const VERSION_CSS = '1.0.0';
	const VERSION_JS = '1.0.0';

	const OPTION_VERSION = 'nitro_k9_version';

	private $attributes;

	/**
	 *
	 */
	public function activate()
	{
		add_option( self::OPTION_VERSION, self::VERSION );
	}

	/**
	 *
	 */
	public function init()
	{
		add_thickbox();
		wp_enqueue_script( 'nitro-k9-js', plugin_dir_url( dirname( __DIR__ )  ) . 'js/nitro-k9.js', array( 'jquery' ), (WP_DEBUG) ? time() : self::VERSION_JS, TRUE );
		wp_enqueue_style( 'nitro-k9-css', plugin_dir_url( dirname( __DIR__ ) ) . 'css/nitro-k9.css', array(), (WP_DEBUG) ? time() : self::VERSION_CSS );
		wp_enqueue_style( 'nitro-k9-bootstrap-css', plugin_dir_url( dirname( __DIR__ ) ) . 'css/bootstrap.css', array(), (WP_DEBUG) ? time() : self::VERSION_CSS );
	}

	/**
	 * @return array
	 */
	public function getAttributes() {
		return ( $this->attributes === NULL ) ? array() : $this->attributes;
	}

	/**
	 * @param array $attributes
	 *
	 * @return Controller
	 */
	public function setAttributes( array $attributes ) {
		$this->attributes = $attributes;

		return $this;
	}

	/**
	 * @param string $key
	 * @param string $val
	 *
	 * @return Controller
	 */
	public function addAttribute( $key, $val ) {
		if ( $this->attributes === NULL ) {
			$this->attributes = array();
		}
		$this->attributes[ $key ] = $val;

		return $this;
	}

}