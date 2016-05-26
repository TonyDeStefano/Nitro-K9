<?php

namespace NitroK9;

class Entry {

	const TABLE_NAME = 'nitro_k9_entries';

	const STEP_EMAIL = 'email';
	const STEP_ENTRIES = 'entries';
	const STEP_BIO = 'bio';
	const STEP_PET_COUNT = 'pet_count';
	const STEP_PET_INFO = 'pet_info';
	const STEP_PET_SERVICES = 'pet_services';
	const STEP_PET_AGGRESSION = 'pet_aggression';
	const STEP_CONFIRM = 'confirm';

	const HOW_HEARD_FRIEND = 'Friend';
	const HOW_HEARD_FAMILY = 'Family';
	const HOW_HEARD_AD = 'Ad';
	const HOW_HEARD_VET = 'Vet';
	const HOW_HEARD_YELP = 'Yelp';
	const HOW_HEARD_INTERNET = 'Internet';
	const HOW_HEARD_OTHER = 'Other';
	
	private $id;
	private $first_name;
	private $last_name;
	private $email;
	private $address;
	private $city;
	private $state;
	private $zip;
	private $home_phone;
	private $cell_phone;
	private $work_phone;
	private $em_contact;
	private $em_relationship;
	private $em_home_phone;
	private $em_cell_phone;
	private $em_work_phone;
	private $how_heard;
	private $large_dogs;
	private $small_dogs;
	private $current_step;
	private $current_pet;
	private $created_at;
	private $updated_at;
	private $completed_at;
	
	/** @var Pet[] $pets */
	private $pets;

	private $steps;
	private $how_heards;

	public function __construct( $id=NULL )
	{
		$this->steps = self::getAllSteps();
		$this->how_heards = self::getAllHowHeards();

		$this
			->setId( $id )
			->setCurrentStep( self::STEP_EMAIL )
			->read();
	}

	public function create()
	{
		global $wpdb;

		if ( $this->email !== NULL )
		{
			$this
				->setCreatedAt( time() )
				->setUpdatedAt( $this->created_at );

			$wpdb->insert(
				$wpdb->prefix . self::TABLE_NAME,
				array(
					'first_name' => $this->first_name,
					'last_name' => $this->last_name,
					'email' => $this->email,
					'address' => $this->address,
					'city' => $this->city,
					'state' => $this->state,
					'zip' => $this->zip,
					'home_phone' => $this->home_phone,
					'cell_phone' => $this->cell_phone,
					'work_phone' => $this->work_phone,
					'em_contact' => $this->em_contact,
					'em_relationship' => $this->em_relationship,
					'em_home_phone' => $this->em_home_phone,
					'em_cell_phone' => $this->em_cell_phone,
					'em_work_phone' => $this->em_work_phone,
					'how_heard' => $this->how_heard,
					'pets' => $this->getPets( TRUE ),
					'large_dogs' => $this->getLargeDogs(),
					'small_dogs' => $this->getSmallDogs(),
					'current_step' => self::STEP_BIO,
					'current_pet' => $this->getCurrentPet(),
					'created_at' => $this->getCreatedAt( 'Y-m-d H:i:s' ),
					'updated_at' => $this->getUpdatedAt( 'Y-m-d H:i:s' )
				),
				array(
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%d',
					'%d',
					'%s',
					'%d',
					'%s',
					'%s'
				)
			);

			$this->setId( $wpdb->insert_id );
		}
	}

	public function read()
	{
		global $wpdb;

		if ( $this->id !== NULL )
		{
			$sql = $wpdb->prepare("
				SELECT
					*
				FROM
					" . $wpdb->prefix . self::TABLE_NAME . "
				WHERE
					id = %d",
				$this->id
			);

			if ( $row = $wpdb->get_row( $sql ) )
			{
				$this->loadFromRow( $row );
			}
			else
			{
				$this->setId( NULL );
			}
		}
	}

	/**
	 * @param \stdClass $row
	 */
	public function loadFromRow( \stdClass $row )
	{
		$this
			->setId( $row->id )
			->setFirstName( $row->first_name )
			->setLastName( $row->last_name )
			->setEmail( $row->email )
			->setAddress( $row->address )
			->setCity( $row->city )
			->setState( $row->state )
			->setZip( $row->zip )
			->setHomePhone( $row->home_phone )
			->setCellPhone( $row->cell_phone )
			->setWorkPhone( $row->work_phone )
			->setEmContact( $row->em_contact )
			->setEmRelationship( $row->em_relationship )
			->setEmHomePhone( $row->em_home_phone )
			->setEmCellPhone( $row->em_cell_phone )
			->setEmWorkPhone( $row->em_work_phone )
			->setHowHeard( $row->how_heard )
			->setLargeDogs( $row->large_dogs )
			->setSmallDogs( $row->small_dogs )
			->setCurrentStep( $row->current_step )
			->setCurrentPet( $row->current_pet )
			->setCreatedAt( $row->created_at )
			->setUpdatedAt( $row->updated_at )
			->setCompletedAt( $row->completed_at );

		if ( strlen( $row->pets ) > 0 )
		{
			$pets = json_decode( $row->pets, TRUE );
			if ( is_array( $pets ) )
			{
				foreach ( $pets as $pet )
				{
					$p = new Pet( json_encode( $pet ) );
					$this->addPet( $p );
				}
			}
		}
	}

	public function update()
	{
		global $wpdb;

		if ( $this->id !== NULL )
		{
			$this->setUpdatedAt( $this->created_at );

			$wpdb->update(
				$wpdb->prefix . self::TABLE_NAME,
				array(
					'first_name' => $this->first_name,
					'last_name' => $this->last_name,
					'email' => $this->email,
					'address' => $this->address,
					'city' => $this->city,
					'state' => $this->state,
					'zip' => $this->zip,
					'home_phone' => $this->home_phone,
					'cell_phone' => $this->cell_phone,
					'work_phone' => $this->work_phone,
					'em_contact' => $this->em_contact,
					'em_relationship' => $this->em_relationship,
					'em_home_phone' => $this->em_home_phone,
					'em_cell_phone' => $this->em_cell_phone,
					'em_work_phone' => $this->em_work_phone,
					'how_heard' => $this->how_heard,
					'pets' => $this->getPets( TRUE ),
					'large_dogs' => $this->getLargeDogs(),
					'small_dogs' => $this->getSmallDogs(),
					'current_step' => $this->getCurrentStep(),
					'current_pet' => $this->getCurrentPet(),
					'updated_at' => $this->getUpdatedAt( 'Y-m-d H:i:s' ),
					'completed_at' => ( $this->getCompletedAt() === NULL ) ? NULL : $this->getCompletedAt( 'Y-m-d H:i:s' )
				),
				array(
					'id' => $this->id
				),
				array(
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%d',
					'%d',
					'%s',
					'%d',
					'%s',
					'%s'
				),
				array(
					'%d'
				)
			);
		}
	}

	public function delete()
	{
		global $wpdb;

		if ( $this->id !== NULL )
		{
			$this->setUpdatedAt( $this->created_at );

			$wpdb->delete(
				$wpdb->prefix . self::TABLE_NAME,
				array(
					'id' => $this->id
				),
				array(
					'%d'
				)
			);

			$this->setId( NULL );
		}
	}

	/**
	 * @return array
	 */
	public static function getAllSteps()
	{
		$reflection = new \ReflectionClass( '\NitroK9\Entry' );
		$constants = $reflection->getConstants();
		$return_array = array();
		foreach ( $constants as $key => $val )
		{
			if ( substr( $key, 0, 4 ) == 'STEP' )
			{
				$return_array[] = $val;
			}
		}

		return $return_array;
	}

	/**
	 * @return array
	 */
	public static function getAllHowHeards()
	{
		$reflection = new \ReflectionClass( '\NitroK9\Entry' );
		$constants = $reflection->getConstants();
		$return_array = array();
		foreach ( $constants as $key => $val )
		{
			if ( substr( $key, 0, 9 ) == 'HOW_HEARD' )
			{
				$return_array[ $val ] = $val;
			}
		}

		return $return_array;
	}

	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param mixed $id
	 *
	 * @return Entry
	 */
	public function setId( $id )
	{
		$this->id = ( is_numeric( $id ) ) ? intval( $id ) : NULL;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getFirstName()
	{
		return $this->first_name;
	}

	/**
	 * @param mixed $first_name
	 *
	 * @return Entry
	 */
	public function setFirstName( $first_name )
	{
		$this->first_name = $first_name;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getLastName()
	{
		return $this->last_name;
	}

	/**
	 * @param mixed $last_name
	 *
	 * @return Entry
	 */
	public function setLastName( $last_name )
	{
		$this->last_name = $last_name;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getEmail()
	{
		return $this->email;
	}

	/**
	 * @param mixed $email
	 *
	 * @return Entry
	 */
	public function setEmail( $email )
	{
		$this->email = $email;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getAddress()
	{
		return $this->address;
	}

	/**
	 * @param mixed $address
	 *
	 * @return Entry
	 */
	public function setAddress( $address )
	{
		$this->address = $address;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getCity()
	{
		return $this->city;
	}

	/**
	 * @param mixed $city
	 *
	 * @return Entry
	 */
	public function setCity( $city )
	{
		$this->city = $city;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getState()
	{
		return $this->state;
	}

	/**
	 * @param mixed $state
	 *
	 * @return Entry
	 */
	public function setState( $state )
	{
		$this->state = $state;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getZip()
	{
		return $this->zip;
	}

	/**
	 * @param mixed $zip
	 *
	 * @return Entry
	 */
	public function setZip( $zip )
	{
		$this->zip = $zip;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getHomePhone()
	{
		return $this->home_phone;
	}

	/**
	 * @param mixed $home_phone
	 *
	 * @return Entry
	 */
	public function setHomePhone( $home_phone )
	{
		$this->home_phone = $home_phone;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getCellPhone()
	{
		return $this->cell_phone;
	}

	/**
	 * @param mixed $cell_phone
	 *
	 * @return Entry
	 */
	public function setCellPhone( $cell_phone )
	{
		$this->cell_phone = $cell_phone;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getWorkPhone()
	{
		return $this->work_phone;
	}

	/**
	 * @param mixed $work_phone
	 *
	 * @return Entry
	 */
	public function setWorkPhone( $work_phone )
	{
		$this->work_phone = $work_phone;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getEmContact()
	{
		return $this->em_contact;
	}

	/**
	 * @param mixed $em_contact
	 *
	 * @return Entry
	 */
	public function setEmContact( $em_contact )
	{
		$this->em_contact = $em_contact;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getEmRelationship()
	{
		return $this->em_relationship;
	}

	/**
	 * @param mixed $em_relationship
	 *
	 * @return Entry
	 */
	public function setEmRelationship( $em_relationship )
	{
		$this->em_relationship = $em_relationship;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getEmHomePhone()
	{
		return $this->em_home_phone;
	}

	/**
	 * @param mixed $em_home_phone
	 *
	 * @return Entry
	 */
	public function setEmHomePhone( $em_home_phone )
	{
		$this->em_home_phone = $em_home_phone;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getEmCellPhone()
	{
		return $this->em_cell_phone;
	}

	/**
	 * @param mixed $em_cell_phone
	 *
	 * @return Entry
	 */
	public function setEmCellPhone( $em_cell_phone )
	{
		$this->em_cell_phone = $em_cell_phone;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getEmWorkPhone()
	{
		return $this->em_work_phone;
	}

	/**
	 * @param mixed $em_work_phone
	 *
	 * @return Entry
	 */
	public function setEmWorkPhone( $em_work_phone )
	{
		$this->em_work_phone = $em_work_phone;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getHowHeard()
	{
		return $this->how_heard;
	}

	/**
	 * @param mixed $how_heard
	 *
	 * @return Entry
	 */
	public function setHowHeard( $how_heard )
	{
		$this->how_heard = ( in_array( $how_heard, $this->how_heards ) ) ? $how_heard : self::HOW_HEARD_OTHER;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getLargeDogs()
	{
		return ( $this->large_dogs === NULL) ? 0 : $this->large_dogs;
	}

	/**
	 * @param mixed $large_dogs
	 *
	 * @return Entry
	 */
	public function setLargeDogs( $large_dogs )
	{
		$this->large_dogs = ( is_numeric( $large_dogs ) ) ? abs( round( $large_dogs ) ) : 0;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getSmallDogs()
	{
		return ( $this->small_dogs === NULL ) ? 0 : $this->small_dogs;
	}

	/**
	 * @param mixed $small_dogs
	 *
	 * @return Entry
	 */
	public function setSmallDogs( $small_dogs )
	{
		$this->small_dogs = ( is_numeric( $small_dogs ) ) ? abs( round( $small_dogs ) ) : 0;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getCurrentStep()
	{
		return $this->current_step;
	}

	/**
	 * @param mixed $current_step
	 *
	 * @return Entry
	 */
	public function setCurrentStep( $current_step )
	{
		$this->current_step = ( in_array( $current_step, $this->steps ) ) ? $current_step : $this->steps[0];

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getCurrentPet()
	{
		if ( $this->current_pet === NULL )
		{
			return ( count( $this->getPets() ) == 0 ) ? NULL : 0;
		}
		else
		{
			return ( $this->current_pet >= count( $this->getPets() ) ) ? count( $this->getPets() ) - 1 : $this->current_pet;
		}
	}

	/**
	 * @param mixed $current_pet
	 *
	 * @return Entry
	 */
	public function setCurrentPet( $current_pet )
	{
		$this->current_pet = $current_pet;

		return $this;
	}

	/**
	 * @param null $format
	 *
	 * @return bool|string
	 */
	public function getCreatedAt( $format=NULL )
	{
		if ( $format === NULL )
		{
			return $this->created_at;
		}

		return ( $this->created_at === NULL ) ? '' : date( $format, $this->created_at );
	}

	/**
	 * @param mixed $created_at
	 *
	 * @return Entry
	 */
	public function setCreatedAt( $created_at )
	{
		$this->created_at = ( $created_at === NULL || is_numeric( $created_at ) ) ? $created_at : strtotime( $created_at );

		return $this;
	}

	/**
	 * @param null $format
	 *
	 * @return bool|string
	 */
	public function getUpdatedAt( $format=NULL )
	{
		if ( $format === NULL )
		{
			return $this->updated_at;
		}

		return ( $this->updated_at === NULL ) ? '' : date( $format, $this->updated_at );
	}

	/**
	 * @param mixed $updated_at
	 *
	 * @return Entry
	 */
	public function setUpdatedAt( $updated_at )
	{
		$this->updated_at = ( $updated_at === NULL || is_numeric( $updated_at ) ) ? $updated_at : strtotime( $updated_at );

		return $this;
	}

	/**
	 * @param null $format
	 *
	 * @return mixed
	 */
	public function getCompletedAt( $format=NULL )
	{
		if ( $format === NULL )
		{
			return $this->completed_at;
		}

		return ( $this->completed_at === NULL ) ? '' : date( $format, $this->completed_at );
	}

	/**
	 * @param mixed $completed_at
	 *
	 * @return Entry
	 */
	public function setCompletedAt( $completed_at )
	{
		$this->completed_at = ( $completed_at === NULL || is_numeric( $completed_at ) ) ? $completed_at : strtotime( $completed_at );

		return $this;
	}

	/**
	 * @param bool $as_json
	 *
	 * @return array|mixed|Pet[]|string|void
	 */
	public function getPets( $as_json=FALSE )
	{
		if ( $as_json )
		{
			$pets = [];
			if ( $this->pets !== NULL )
			{
				foreach ( $this->pets as $pet )
				{
					$pets[] = $pet->toArray();
				}
			}

			return json_encode( $pets );
		}

		return ( $this->pets === NULL ) ? array() : $this->pets;
	}

	/**
	 * @return int
	 */
	public function getSmallDogCount()
	{
		$count = 0;

		foreach ( $this->getPets() as $pet )
		{
			if ( $pet->getType() == Pet::TYPE_SMALL_DOG )
			{
				$count++;
			}
		}

		return $count;
	}

	/**
	 * @return int
	 */
	public function getLargeDogCount()
	{
		$count = 0;

		foreach ( $this->getPets() as $pet )
		{
			if ( $pet->getType() == Pet::TYPE_LARGE_DOG )
			{
				$count++;
			}
		}

		return $count;
	}

	/**
	 * @param Pet[] $pets
	 *
	 * @return Entry
	 */
	public function setPets( array $pets )
	{
		$this->pets = $pets;

		return $this;
	}

	/**
	 * @param Pet $pet
	 *
	 * @return $this
	 */
	public function addPet( $pet )
	{
		if ( $this->pets === NULL )
		{
			$this->pets = array();
		}

		$this->pets[] = $pet;

		return $this;
	}

	/**
	 * @return array
	 */
	public function getSteps()
	{
		return $this->steps;
	}

	/**
	 * @return array
	 */
	public function getHowHeards()
	{
		return $this->how_heards;
	}

	public function getHash()
	{
		return ( $this->created_at === NULL ) ? '' : md5( $this->created_at );
	}

	/**
	 * @param $email
	 *
	 * @return Entry
	 */
	public static function getUnfinishedEntryFromEmail( $email )
	{
		global $wpdb;
		$entry = new Entry;

		$sql = $wpdb->prepare("
			SELECT
				*
			FROM
				" . $wpdb->prefix . self::TABLE_NAME . "
			WHERE
				email = %s",
			$email
		);

		if ( $row = $wpdb->get_row( $sql ) )
		{
			$entry->loadFromRow( $row );
		}
		
		return $entry;
	}

	/**
	 * @param $name
	 * @param $label
	 * @param $is_required
	 * @param string $default_value
	 * @param string $type
	 * @param array $options
	 */
	public static function drawFormRow( $name, $label, $is_required, $default_value="", $type='text', $options=array() )
	{
		/** @var Controller $nitro_k9_controller */
		global $nitro_k9_controller;

		$uniqid = uniqid();
		
		if ( $is_required )
		{
			echo '<input type="hidden" name="required_' . $name . '" value="' . htmlspecialchars( $label ) . '">';
		}

		echo '
			<div class="form-group ' . ( ( in_array( $name, $nitro_k9_controller->getErrorFieldNames() ) ) ? 'has-error' : '' ) . '">
				<label for="' . $uniqid . '" class="col-sm-3 control-label"> 
		        ' . ( ( $is_required ) ? '<span style="color:red">*</span>' : '' ) . $label . '
		        </label>
				<div class="col-sm-9">';

		switch ( $type )
		{
			case 'select':

				echo '<select name="' . $name . '" id="' . $uniqid . '" class="form-control">';
				foreach ( $options as $key => $val )
				{
					$selected = ( isset( $_POST[$name] ) ) ? $_POST[$name] : $default_value;
					echo
						'<option value="' . htmlspecialchars( $val ) . '"' . ( ( $val == $selected ) ? ' selected' : '' ) . '>'
						. htmlspecialchars( $key ) .
						'</option>';
				}
				echo '</select>';
				break;

			case 'email':

				echo '<input type="email" name="' . $name . '" id="' . $uniqid . '" class="form-control" value="' . ( ( isset( $_POST[$name] ) ) ? htmlspecialchars( $_POST[$name] ) : htmlspecialchars( $default_value ) ) . '">';
				break;

			default:

				echo '<input name="' . $name . '" id="' . $uniqid . '" class="form-control" value="' . ( ( isset( $_POST[$name] ) ) ? htmlspecialchars( $_POST[$name] ) : htmlspecialchars( $default_value ) ) . '">';
		}

        echo '
				</div>
			</div>';
	}

	public function nextStep()
	{
		switch ( $this->getCurrentStep() )
		{
			case self::STEP_BIO:
				$this->setCurrentStep( self::STEP_PET_COUNT );
				break;

			case self::STEP_PET_COUNT:
				$this
					->setCurrentPet( 0 )
					->setCurrentStep( self::STEP_PET_INFO );
				break;

			default:
				$this->setCurrentStep( self::STEP_BIO );
		}
	}

	public function priorStep()
	{
		switch ( $this->getCurrentStep() )
		{
			case self::STEP_BIO:
				$this->setCurrentStep( self::STEP_EMAIL );
				break;

			case self::STEP_PET_COUNT:
				$this->setCurrentStep( self::STEP_BIO );
				break;

			case self::STEP_PET_INFO:
				if ( $this->getCurrentPet() == 0 )
				{
					$this->setCurrentStep( self::STEP_PET_COUNT );
				}
				break;

			default:
				$this->setCurrentStep( self::STEP_BIO );
		}
	}
}