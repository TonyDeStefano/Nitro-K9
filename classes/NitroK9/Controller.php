<?php

namespace NitroK9;

class Controller {

	const VERSION = '1.0.0';
	const VERSION_CSS = '1.0.0';
	const VERSION_JS = '1.0.0';

	const OPTION_VERSION = 'nitro_k9_version';

	private $attributes;
	
	/** @var PriceGroup[] $prices */
	public $price_groups;

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
		wp_enqueue_script( 'nitro-k9-js', plugin_dir_url( dirname( __DIR__ )  ) . 'js/nitro-k9.js', array( 'jquery' ), (WP_DEBUG) ? time() : self::VERSION_JS, TRUE );
		wp_enqueue_style( 'nitro-k9-css', plugin_dir_url( dirname( __DIR__ ) ) . 'css/nitro-k9.css', array(), (WP_DEBUG) ? time() : self::VERSION_CSS );
		wp_enqueue_style( 'nitro-k9-bootstrap-css', plugin_dir_url( dirname( __DIR__ ) ) . 'css/bootstrap.css', array(), (WP_DEBUG) ? time() : self::VERSION_CSS );
		$this->price_groups = PriceGroup::loadPrices();
	}

	public function form_capture()
	{

	}

	/**
	 *
	 */
	public function admin_menus()
	{
		add_menu_page('Nitro K9', 'Nitro K9', 'manage_options', 'nitro_k9', array( $this, 'pricing_page' ), 'dashicons-star-filled');
		add_submenu_page('nitro_k9', 'Pricing', 'Pricing', 'manage_options', 'nitro_k9');
		add_submenu_page('nitro_k9', 'Short Code', 'Short Code', 'manage_options', 'nitro_k9_shortcode', array($this, 'short_code_page'));
		//add_submenu_page('nitro_k9', 'Cruise Entries', 'Cruise Entries', 'manage_options', 'outspokane_cruise', array($this, 'showCruiseEntries'));

		/* I guess this is how to add a page without adding a menu */
		//add_submenu_page(NULL, 'Edit Entry', 'Edit Entry', 'manage_options', 'outspokane_edit_entry', array($this, 'editEntry'));
	}

	/**
	 *
	 */
	public function short_code_page()
	{
		include( dirname( dirname( __DIR__ ) ) . '/includes/shortcode.php');
	}
	
	/**
	 *
	 */
	public function pricing_page()
	{
		include( dirname( dirname( __DIR__ ) ) . '/includes/pricing.php');
	}

	public function register_settings()
	{
		register_setting( 'nitro_k9_settings', 'nitro_k9_pricing' );
	}

	public function admin_scripts()
	{
		wp_enqueue_script( 'nitro-k9-admin-js', plugin_dir_url( dirname( __DIR__ )  ) . 'js/admin.js', array( 'jquery' ), (WP_DEBUG) ? time() : self::VERSION_JS, TRUE );
		wp_enqueue_style( 'nitro-k9-admin-css', plugin_dir_url( dirname( __DIR__ ) ) . 'css/admin.css', array(), (WP_DEBUG) ? time() : self::VERSION_CSS );
	}

	/**
	 * @param $attributes
	 *
	 * @return string
	 */
	public function short_code( $attributes )
	{
		$this->attributes = shortcode_atts( array(), $attributes );

		ob_start();
		include( dirname( dirname( __DIR__ ) ) . '/includes/shortcode.php');
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
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