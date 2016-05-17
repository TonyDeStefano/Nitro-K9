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

		require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
		global $wpdb;
		
		add_option( self::OPTION_VERSION, self::VERSION );
		
		/* create the table (decided against using a custom post type for all this data) */

		$charset_collate = '';
		if ( ! empty( $wpdb->charset ) )
		{
			$charset_collate .= "DEFAULT CHARACTER SET " . $wpdb->charset;
		}
		if ( ! empty( $wpdb->collate ) )
		{
			$charset_collate .= " COLLATE " . $wpdb->collate;
		}

		$table = $wpdb->prefix . Entry::TABLE_NAME;
		if( $wpdb->get_var( "SHOW TABLES LIKE '" . $table . "'" ) != $table ) {
			$sql = "
				CREATE TABLE `" . $table . "`
				(
					`id` INT(11) NOT NULL AUTO_INCREMENT,
					`first_name` VARCHAR(50) DEFAULT NULL,
					`last_name` VARCHAR(50) DEFAULT NULL,
					`email` VARCHAR(50) DEFAULT NULL,
					`address` VARCHAR(50) DEFAULT NULL,
					`city` VARCHAR(50) DEFAULT NULL,
					`state` VARCHAR(2) DEFAULT NULL,
					`zip` VARCHAR(10) DEFAULT NULL,
					`home_phone` VARCHAR(50) DEFAULT NULL,
					`cell_phone` VARCHAR(50) DEFAULT NULL,
					`work_phone` VARCHAR(50) DEFAULT NULL,
					`em_contact` VARCHAR(50) DEFAULT NULL,
					`em_relationship` VARCHAR(50) DEFAULT NULL,
					`em_home_phone` VARCHAR(50) DEFAULT NULL,
					`em_cell_phone` VARCHAR(50) DEFAULT NULL,
					`em_work_phone` VARCHAR(50) DEFAULT NULL,
					`how_heard` VARCHAR(50) DEFAULT NULL,
					`pets` TEXT DEFAULT NULL,
					`large_dogs` INT(11) DEFAULT NULL,
					`small_dogs` INT(11) DEFAULT NULL,
					`current_step` VARCHAR(50) DEFAULT NULL,
					`current_pet` INT(11) DEFAULT NULL,
					`created_at` DATETIME DEFAULT NULL,
					`updated_at` DATETIME DEFAULT NULL,
					`completed_at` DATETIME DEFAULT NULL,
					PRIMARY KEY (`id`),
					KEY `email` (`email`)
					
				)";
			$sql .= $charset_collate . ";"; // new line to avoid PHP Storm syntax error
			dbDelta( $sql );
		}
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
	
	public function custom_post_type()
	{
		$labels = array (
			'name' => __( 'Thank You Emails' ),
			'singular_name' => __( 'Thank You Email' ),
			'add_new_item' => __( 'Add Thank You Email' ),
			'edit_item' => __( 'Edit Thank You Email' ),
			'new_item' => __( 'New Thank You Email' ),
			'view_item' => __( 'View Thank You Email' ),
			'search_items' => __( 'Search Thank You Emails' ),
			'not_found' => __( 'No thank you emails found.' )
		);

		$args = array (
			'labels' => $labels,
			'hierarchical' => FALSE,
			'description' => 'Thank You Emails',
			'supports' => array( 'title', 'editor' ),
			'public' => TRUE,
			'show_ui' => TRUE,
			'show_in_menu' => 'nitro_k9',
			'show_in_nav_menus' => TRUE,
			'publicly_queryable' => TRUE,
			'exclude_from_search' => FALSE,
			'has_archive' => TRUE
		);

		register_post_type( 'nitro_k9_ty_email', $args );
	}

	public function custom_enter_title( $input )
	{
		global $post_type;

		if( is_admin() && 'Enter title here' == $input && 'nitro_k9_ty_email' == $post_type )
		{
			return 'Enter Email Subject Line';
		}

		return $input;
	}

	public function extra_ty_email_meta()
	{
		add_meta_box( 'nitro-k9-ty-email-meta', 'Properties', array( $this, 'extra_ty_email_fields' ), 'nitro_k9_ty_email' );
	}

	public function extra_ty_email_fields()
	{
		include( dirname( dirname( __DIR__ ) ) . '/includes/extra-ty-email-meta.php' );
	}

	public function save_ty_email_post()
	{
		global $post;
		global $wpdb;

		if ( $post !== NULL && $post->post_type == 'nitro_k9_ty_email' && isset( $_POST['ty_email_is_active'] ) )
		{
			$active_email = $_POST['ty_email_is_active'];

			if ( $active_email == 1 )
			{
				$wpdb->update(
					$wpdb->prefix . 'postmeta',
					array (
						'meta_value' => 0
					),
					array (
						'meta_key' => 'nitro_k9_active_email'
					),
					array (
						'%s'
					),
					array(
						'%s'
					)
				);
			}

			update_post_meta( $post->ID, 'nitro_k9_active_email', $active_email );

		}
	}

	public function add_new_columns( $columns )
	{
		$new = array(
			'nitro_k9_active_email' => 'Active'
		);
		$columns = array_slice( $columns, 0, 2, TRUE ) + $new + array_slice( $columns, 2, NULL, TRUE );
		return $columns;
	}

	public function custom_columns( $column )
	{
		global $post;

		switch ( $column )
		{
			case 'nitro_k9_active_email':
				echo ( get_post_meta( $post->ID, 'nitro_k9_active_email', TRUE ) == '1') ? 'Yes' : 'No';
				break;
		}
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