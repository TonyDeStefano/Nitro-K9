<?php

namespace NitroK9;

class Controller {

	const VERSION = '1.0.0';
	const VERSION_CSS = '1.0.2';
	const VERSION_JS = '1.0.1';

	const OPTION_VERSION = 'nitro_k9_version';

	private $attributes;
	private $errors;
	private $error_field_names;
	
	/** @var PriceGroup[] $prices */
	public $price_groups;

	/**
	 * @return mixed
	 */
	public function getErrors()
	{
		return ( $this->errors === NULL) ? array() : $this->errors;
	}

	/**
	 * @param $error
	 */
	public function addError( $error )
	{
		if ( $this->errors === NULL )
		{
			$this->errors = array();
		}

		$this->errors[] = $error;
	}

	/**
	 * @param mixed $errors
	 *
	 * @return Controller
	 */
	public function setErrors( $errors )
	{
		$this->errors = $errors;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getErrorCount()
	{
		return ( $this->errors === NULL ) ? 0 : count( $this->errors );
	}

	/**
	 * @return mixed
	 */
	public function getErrorFieldNames()
	{
		return ( $this->error_field_names === NULL ) ? array() : $this->error_field_names;
	}

	/**
	 * @param array $error_field_names
	 *
	 * @return $this
	 */
	public function setErrorFieldNames( array $error_field_names )
	{
		$this->error_field_names = $error_field_names;

		return $this;
	}

	/**
	 * @param $field_name
	 */
	public function addErrorFieldName( $field_name )
	{
		if ( $this->error_field_names === NULL )
		{
			$this->error_field_names = array();
		}

		$this->error_field_names[] = $field_name;
	}

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
			$charset_collate .= " DEFAULT CHARACTER SET " . $wpdb->charset;
		}
		if ( ! empty( $wpdb->collate ) )
		{
			$charset_collate .= " COLLATE " . $wpdb->collate;
		}

		$table = $wpdb->prefix . Entry::TABLE_NAME;
		$sql = "CREATE TABLE " . $table . " (
				id INT(11) NOT NULL AUTO_INCREMENT,
				step VARCHAR(50) DEFAULT NULL,
				first_name VARCHAR(255) DEFAULT NULL,
				last_name VARCHAR(255) DEFAULT NULL,
				email VARCHAR(255) DEFAULT NULL,
				address VARCHAR(255) DEFAULT NULL,
				city VARCHAR(255) DEFAULT NULL,
				state VARCHAR(255) DEFAULT NULL,
				zip VARCHAR(255) DEFAULT NULL,
				home_phone VARCHAR(255) DEFAULT NULL,
				cell_phone VARCHAR(255) DEFAULT NULL,
				work_phone VARCHAR(255) DEFAULT NULL,
				em_contact VARCHAR(255) DEFAULT NULL,
				em_relationship VARCHAR(255) DEFAULT NULL,
				em_home_phone VARCHAR(255) DEFAULT NULL,
				em_cell_phone VARCHAR(255) DEFAULT NULL,
				em_work_phone VARCHAR(255) DEFAULT NULL,
				how_heard VARCHAR(255) DEFAULT NULL,
				pets TEXT DEFAULT NULL,
				large_dogs INT(11) DEFAULT NULL,
				small_dogs INT(11) DEFAULT NULL,
				additional_owners TEXT DEFAULT NULL,
				current_step VARCHAR(255) DEFAULT NULL,
				current_pet INT(11) DEFAULT NULL,
				current_owner INT(11) DEFAULT NULL,
				created_at DATETIME DEFAULT NULL,
				updated_at DATETIME DEFAULT NULL,
				completed_at DATETIME DEFAULT NULL,
				PRIMARY KEY  (id),
				KEY email (email),
				KEY current_step (current_step)
			)";
		$sql .= $charset_collate . ";"; // new line to avoid PHP Storm syntax error
		dbDelta( $sql );
	}

	/**
	 *
	 */
	public function init()
	{
		wp_enqueue_style( 'nitro-k9-fa', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css', array(),(WP_DEBUG) ? time() : self::VERSION_JS, TRUE );
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

	/**
	 *
	 */
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

	/**
	 * @param $columns
	 *
	 * @return array
	 */
	public function add_new_columns( $columns )
	{
		$new = array(
			'nitro_k9_active_email' => 'Active'
		);
		$columns = array_slice( $columns, 0, 2, TRUE ) + $new + array_slice( $columns, 2, NULL, TRUE );
		return $columns;
	}

	/**
	 * @param $column
	 */
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

	/**
	 *
	 */
	public function form_capture()
	{
		if ( isset( $_POST['nitro_k9_id'] ) && isset( $_POST['nitro_k9_hash'] ) )
		{
			$referrer = $_POST['_wp_http_referer'];
			$parts = explode( '?', $referrer );
			$page = $parts[0];
			$requests = array();
			if ( count( $parts ) == 2 )
			{
				$requests = explode( '&', $parts[1] );
				foreach ( $requests as $index => $request )
				{
					$parts = explode( '=', $request );
					if ( $parts[0] == 'nitro_k9_id' || $parts[0] == 'nitro_k9_hash' )
					{
						unset( $requests[$index] );
					}
				}
			}

			if ( strlen( $_POST['nitro_k9_id'] ) === 0 )
			{
				$entry = new Entry;

				if ( filter_var( $_POST['email'], FILTER_VALIDATE_EMAIL ) === FALSE )
				{
					$this->addError( 'Please enter a valid email address' );
				}
				else
				{
					$entry = Entry::getUnfinishedEntryFromEmail( $_POST['email'] );
					if ( $entry->getId() === NULL )
					{
						$entry
							->setEmail( $_POST['email'] )
							->create();
					}
					else
					{
						$entry
							->setCurrentStep( Entry::STEP_BIO )
							->update();
					}
				}
			}
			else
			{
				$entry = new Entry( $_POST['nitro_k9_id'] );

				if ( ! isset( $_POST['prior_step'] ) && ! isset( $_POST['remove_owner'] ) && ! isset( $_POST['remove_pet'] ) )
				{
					foreach ( $_POST as $key => $val )
					{
						if ( substr( $key, 0, 9 ) == 'required_' )
						{
							$field = substr( $key, 9 );
							if ( strlen( trim( $_POST[ $field ] ) ) == 0 )
							{
								$this->addError( $val . ' is required' );
								$this->addErrorFieldName( $field );
							}
						}
					}
				}

				switch ( $entry->getCurrentStep() )
				{
					case Entry::STEP_BIO:

						if ( ! isset( $_POST['prior_step'] ) )
						{
							if ( filter_var( $_POST['email'], FILTER_VALIDATE_EMAIL ) === FALSE )
							{
								$this->addError( 'Please enter a valid email address' );
							}
						}
						
						if ( $this->getErrorCount() == 0 )
						{
							$entry
								->setEmail( $_POST['email'] )
								->setFirstName( $_POST['first_name'] )
								->setLastName( $_POST['last_name'] )
								->setAddress( $_POST['address'] )
								->setCity( $_POST['city'] )
								->setState( $_POST['state'] )
								->setZip( $_POST['zip'] )
								->setHomePhone( $_POST['home_phone'] )
								->setWorkPhone( $_POST['work_phone'] )
								->setCellPhone( $_POST['cell_phone'] )
								->setEmContact( $_POST['em_contact'] )
								->setEmRelationship( $_POST['em_relationship'] )
								->setEmHomePhone( $_POST['em_home_phone'] )
								->setEmWorkPhone( $_POST['em_work_phone'] )
								->setEmCellPhone( $_POST['em_cell_phone'] )
								->setHowHeard( $_POST['how_heard'] );
						}
						
						break;

					case Entry::STEP_OWNER:

						if ( ! isset( $_POST['prior_step'] ) && ! isset( $_POST['remove_owner'] ) && ! isset( $_POST['remove_pet'] ) )
						{
							if ( filter_var( $_POST['email'], FILTER_VALIDATE_EMAIL ) === FALSE )
							{
								$this->addError( 'Please enter a valid email address' );
							}
						}

						if ( $this->getErrorCount() == 0 )
						{
							$entry->getOwners()[ $entry->getCurrentOwner() ]
								->setInfoItem( 'email', $_POST['email'] )
								->setInfoItem( 'first_name', $_POST['first_name'] )
								->setInfoItem( 'last_name', $_POST['last_name'] )
								->setInfoItem( 'address', $_POST['address'] )
								->setInfoItem( 'city', $_POST['city'] )
								->setInfoItem( 'state', $_POST['state'] )
								->setInfoItem( 'zip', $_POST['zip'] )
								->setInfoItem( 'home_phone', $_POST['home_phone'] )
								->setInfoItem( 'work_phone', $_POST['work_phone'] )
								->setInfoItem( 'cell_phone', $_POST['cell_phone'] )
								->setInfoItem( 'em_contact', $_POST['em_contact'] )
								->setInfoItem( 'em_relationship', $_POST['em_relationship'] )
								->setInfoItem( 'em_home_phone', $_POST['em_home_phone'] )
								->setInfoItem( 'em_work_phone', $_POST['em_work_phone'] )
								->setInfoItem( 'em_cell_phone', $_POST['em_cell_phone'] );
						}

						break;
					
					case Entry::STEP_PET_COUNT:

						if ( ! isset( $_POST['prior_step'] ) )
						{
							if ( $_POST['large_dogs'] == 0 && $_POST['small_dogs'] == 0 )
							{
								$this->addError( 'You must enroll at least one pet to continue' );
							}
						}

						if ( $this->getErrorCount() == 0 )
						{
							$entry
								->setLargeDogs( $_POST['large_dogs'] )
								->setSmallDogs( $_POST['small_dogs'] );

							if ( count( $entry->getPets() ) == 0 )
							{
								for ( $x=0; $x<$entry->getLargeDogs(); $x++ )
								{
									$pet = new Pet;
									$pet->setType( Pet::TYPE_LARGE_DOG );
									$entry->addPet( $pet );
								}

								for ( $x=0; $x<$entry->getSmallDogs(); $x++ )
								{
									$pet = new Pet;
									$pet->setType( Pet::TYPE_SMALL_DOG );
									$entry->addPet( $pet );
								}
							}
							else
							{
								/**
								 * @var Pet[] $large_dogs
								 * @var Pet[] $small_dogs
								 */
								$large_dogs = array();
								$small_dogs = array();

								foreach( $entry->getPets() as $pet )
								{
									if ( $pet->getType() == Pet::TYPE_LARGE_DOG )
									{
										$large_dogs[] = $pet;
									}

									if ( $pet->getType() == Pet::TYPE_SMALL_DOG )
									{
										$small_dogs[] = $pet;
									}
								}

								if ( $entry->getLargeDogs() > count( $large_dogs ) )
								{
									for ( $x=count( $large_dogs ); $x<$entry->getLargeDogs(); $x++ )
									{
										$pet = new Pet;
										$pet->setType( Pet::TYPE_LARGE_DOG );
										$large_dogs[] = $pet;
									}
								}
								elseif ( $entry->getLargeDogs() < count( $large_dogs ) )
								{
									$temp = array();
									if ( $entry->getLargeDogs() > 0 )
									{
										for ( $x = 0; $x < $entry->getLargeDogs(); $x ++ )
										{
											$temp[] = $large_dogs[ $x ];
										}
									}
									$large_dogs = $temp;
								}

								if ( $entry->getSmallDogs() > count( $small_dogs ) )
								{
									for ( $x=count( $small_dogs ); $x<$entry->getSmallDogs(); $x++ )
									{
										$pet = new Pet;
										$pet->setType( Pet::TYPE_SMALL_DOG );
										$small_dogs[] = $pet;
									}
								}
								elseif ( $entry->getSmallDogs() < count( $small_dogs ) )
								{
									$temp = array();
									if ( $entry->getSmallDogs() > 0 )
									{
										for ( $x = 0; $x < $entry->getSmallDogs(); $x ++ )
										{
											$temp[] = $small_dogs[ $x ];
										}
									}
									$small_dogs = $temp;
								}

								$entry->setPets( array_merge( $large_dogs, $small_dogs ) );
							}
						}
						
						break;
					
					case Entry::STEP_PET_INFO:

						$entry->getPets()[ $entry->getCurrentPet() ]
							->setIsAggressive( ( $_POST['is_aggressive'] == 1 ) )
							->setInfoItemsFromPost();

						break;
					
					case Entry::STEP_PET_SERVICES:

						$entry->getPets()[ $entry->getCurrentPet() ]
							->setServicesItemsFromPost();
						
						break;
					
					case Entry::STEP_PET_AGGRESSION:

						$entry->getPets()[ $entry->getCurrentPet() ]
							->setAggressionItemsFromPost();

						break;

					case Entry::STEP_CONFIRM:

						add_filter( 'wp_mail_content_type', array( $this, 'set_content_type' ) );

						$posts = get_posts( array(
							'post_type' => 'nitro_k9_ty_email',
							'post_status' => 'publish',
							'numberposts' => -1
						) );

						if ( count( $posts ) > 0 )
						{
							foreach ( $posts as $post )
							{
								$meta = get_post_meta( $post->ID, 'nitro_k9_active_email', TRUE );
								if ( $meta == 1 )
								{
									@wp_mail (
										$entry->getEmail(), 
										'Nitro K-9 Form Sign Up Form',
										'<p>Dear ' . $entry->getFirstName() . ',</p>' . $post->post_content,
										array( 'from' => 'Nitro K9 <no-reply@nitrok9.com>' )
									);
									break;
								}
							}
						}

						$entry->complete();

						@wp_mail (
							'Steve Walter <steve@nitrocanine.com>',
							'Nitro K-9 Form Sign Up Form',
							$entry->getNotificationEmail( $this ),
							array( 'from' => 'Nitro K9 <no-reply@nitrok9.com>' )
						);

						remove_filter( 'wp_mail_content_type', array( $this, 'set_content_type' ) );

						$requests[] = 'complete=true';
						header( 'Location:' . $page . '?' . implode( '&', $requests ) );
						exit;

						break;
						
				}

				if ( $this->getErrorCount() == 0 )
				{
					if ( isset( $_POST['prior_step'] ) )
					{
						$entry->priorStep();
					}
					elseif ( isset( $_POST['remove_owner'] ) )
					{
						$temp_current_owner = $entry->getCurrentOwner();
						$entry->priorStep();
						$entry->deleteOwner( $temp_current_owner );
					}
					elseif ( isset( $_POST['remove_pet'] ) )
					{
						$temp_current_pet = $entry->getCurrentPet();
						$entry->priorStep();
						$entry->deletePet( $temp_current_pet );
					}
					else
					{
						if ( isset( $_POST['add_owner'] ) )
						{
							$owner = new Owner;
							$owner
								->setInfoItem( 'address', $entry->getAddress() )
								->setInfoItem( 'city', $entry->getCity() )
								->setInfoItem( 'state', $entry->getState() )
								->setInfoItem( 'zip', $entry->getZip() );

							$entry->addOwner( $owner );
						}

						$entry->nextStep();
					}

					$entry->update();
				}
			}

			if ( $this->getErrorCount() == 0 )
			{
				if ( $entry->getId() !== NULL )
				{
					$requests[] = 'nitro_k9_id=' . $entry->getId();
					$requests[] = 'nitro_k9_hash=' . $entry->getHash();
				}

				header( 'Location:' . $page . '?' . implode( '&', $requests ) );
				exit;
			}
		}
	}

	function set_content_type( $content_type )
	{
		return 'text/html';
	}

	/**
	 *
	 */
	public function admin_menus()
	{
		add_menu_page( 'Nitro K9', 'Nitro K9', 'manage_options', 'nitro_k9', array( $this, 'pricing_page' ), 'dashicons-star-filled' );
		add_submenu_page( 'nitro_k9', 'Pricing', 'Pricing', 'manage_options', 'nitro_k9' );
		add_submenu_page( 'nitro_k9', 'Short Code', 'Short Code', 'manage_options', 'nitro_k9_shortcode', array( $this, 'short_code_page' ) );
		add_submenu_page( 'nitro_k9', 'Submissions', 'Submissions', 'manage_options', 'nitro_k9_submissions', array( $this, 'show_submissions' ) );
	}
	
	public function short_code_page()
	{
		include( dirname( dirname( __DIR__ ) ) . '/includes/shortcode.php');
	}
	
	public function pricing_page()
	{
		include( dirname( dirname( __DIR__ ) ) . '/includes/pricing.php');
	}

	public function show_submissions()
	{
		include( dirname( dirname( __DIR__ ) ) . '/includes/submissions.php');
	}

	public function view_submission()
	{
		include( dirname( dirname( __DIR__ ) ) . '/includes/submissions.php');
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
		include( dirname( dirname( __DIR__ ) ) . '/includes/form.php');
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}

	/**
	 * @return array
	 */
	public function getAttributes() 
	{
		return ( $this->attributes === NULL ) ? array() : $this->attributes;
	}

	/**
	 * @param array $attributes
	 *
	 * @return Controller
	 */
	public function setAttributes( array $attributes ) 
	{
		$this->attributes = $attributes;

		return $this;
	}

	/**
	 * @param string $key
	 * @param string $val
	 *
	 * @return Controller
	 */
	public function addAttribute( $key, $val ) 
	{
		if ( $this->attributes === NULL ) {
			$this->attributes = array();
		}
		$this->attributes[ $key ] = $val;

		return $this;
	}
}