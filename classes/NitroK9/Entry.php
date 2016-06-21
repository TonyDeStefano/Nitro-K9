<?php

namespace NitroK9;

class Entry {

	const TABLE_NAME = 'nitro_k9_entries';

	const STEP_EMAIL = 'email';
	const STEP_ENTRIES = 'entries';
	const STEP_BIO = 'bio';
	const STEP_OWNER = 'owner';
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
	private $current_owner;
	private $created_at;
	private $updated_at;
	private $completed_at;
	
	/** @var Pet[] $pets */
	private $pets;
	
	/** @var  Owner[] $owners */
	private $owners;

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
					'additional_owners' => $this->getOwners( TRUE ),
					'large_dogs' => $this->getLargeDogs(),
					'small_dogs' => $this->getSmallDogs(),
					'current_step' => self::STEP_BIO,
					'current_pet' => $this->getCurrentPet(),
					'current_owner' => $this->getCurrentOwner(),
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
					'%s',
					'%d',
					'%d',
					'%s',
					'%d',
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
			->setCurrentOwner( $row->current_owner )
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

		if ( strlen( $row->additional_owners ) > 0 )
		{
			$owners = json_decode( $row->additional_owners, TRUE );
			if ( is_array( $owners ) )
			{
				foreach ( $owners as $owner )
				{
					$o = new Owner( json_encode( $owner ) );
					$this->addOwner( $o );
				}
			}
		}
	}
	
	public function complete()
	{
		$this
			->setCompletedAt( time() )
			->update();
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
					'additional_owners' => $this->getOwners( TRUE ),
					'large_dogs' => $this->getLargeDogs(),
					'small_dogs' => $this->getSmallDogs(),
					'current_step' => $this->getCurrentStep(),
					'current_pet' => $this->getCurrentPet(),
					'current_owner' => $this->getCurrentOwner(),
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
					'%s',
					'%d',
					'%d',
					'%s',
					'%d',
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
	 * @return string
	 */
	public function getFullName()
	{
		return $this->getFirstName() . ' ' . $this->getLastName();
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
		if ( count( $this->getPets() ) == 0 )
		{
			return NULL;
		}
		elseif ( $this->current_pet === NULL )
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
		$this->current_pet = ( is_numeric( $current_pet ) ) ? intval( $current_pet ) : NULL;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getCurrentOwner()
	{
		if ( count( $this->getOwners() ) == 0 )
		{
			return NULL;
		}
		elseif ( $this->current_owner === NULL )
		{
			return ( count( $this->getOwners() ) == 0 ) ? NULL : 0;
		}
		else
		{
			return ( $this->current_owner >= count( $this->getOwners() ) ) ? count( $this->getOwners() ) - 1 : $this->current_owner;
		}
	}

	/**
	 * @param mixed $current_owner
	 *
	 * @return Entry
	 */
	public function setCurrentOwner( $current_owner )
	{
		$this->current_owner = ( is_numeric( $current_owner ) ) ? intval( $current_owner ) : NULL;

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
	 * @param Owner[] $owners
	 *
	 * @return Entry
	 */
	public function setOwners( array $owners )
	{
		$this->owners = $owners;

		return $this;
	}

	/**
	 * @param bool $as_json
	 *
	 * @return array|mixed|Owner[]|string|void
	 */
	public function getOwners( $as_json=FALSE )
	{
		if ( $as_json )
		{
			$owners = [];
			if ( $this->owners !== NULL )
			{
				foreach ( $this->owners as $owner )
				{
					$owners[] = $owner->toArray();
				}
			}

			return json_encode( $owners );
		}

		return ( $this->owners === NULL ) ? array() : $this->owners;
	}

	/**
	 * @param Owner $owner
	 *
	 * @return $this
	 */
	public function addOwner( $owner )
	{
		if ( $this->owners === NULL )
		{
			$this->owners = array();
		}

		$this->owners[] = $owner;

		return $this;
	}

	/**
	 * @param $index
	 */
	public function deleteOwner( $index )
	{
		if ( $this->owners !== NULL )
		{
			if ( isset( $this->owners[ $index ] ) )
			{
				unset ( $this->owners[ $index ] );
			}
		}
	}

	/**
	 * @param $index
	 */
	public function deletePet( $index )
	{
		if ( $this->pets !== NULL )
		{
			if ( isset( $this->pets[ $index ] ) )
			{
				if ( $this->pets[ $index ]->getType() == Pet::TYPE_LARGE_DOG )
				{
					$this->large_dogs--;
				}
				else
				{
					$this->small_dogs--;
				}

				unset ( $this->pets[ $index ] );
			}
		}
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
				email = %s
				AND completed_at IS NULL",
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
	 * @param bool $wider
	 */
	public static function drawFormRow( $name, $label, $is_required, $default_value="", $type='text', $options=array(), $wider=FALSE )
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
				<label for="' . $uniqid . '" class="col-sm-' . ( ( $wider ) ? '5' : '3' ) . ' control-label"> 
		        ' . ( ( $is_required ) ? '<span style="color:red">*</span>' : '' ) . $label . '
		        </label>
				<div class="col-sm-' . ( ( $wider ) ? '7' : '9' ) . '">';

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

				echo '<input maxlength="255" type="email" name="' . $name . '" id="' . $uniqid . '" class="form-control" value="' . ( ( isset( $_POST[ $name ] ) ) ? htmlspecialchars( $_POST[ $name ] ) : htmlspecialchars( $default_value ) ) . '">';
				break;

			case 'textarea':

				echo '<textarea name="' . $name . '" id="' . $uniqid . '" class="form-control">' . ( ( isset( $_POST[ $name ] ) ) ? htmlspecialchars( $_POST[ $name ] ) : htmlspecialchars( $default_value ) ) . '</textarea>';
				break;

			default:

				echo '<input maxlength="255" name="' . $name . '" id="' . $uniqid . '" class="form-control" value="' . ( ( isset( $_POST[ $name ] ) ) ? htmlspecialchars( $_POST[ $name ] ) : htmlspecialchars( $default_value ) ) . '">';
		}

        echo '
				</div>
			</div>';
	}

	/**
	 * @param $label
	 * @param $value
	 */
	public static function drawConfirmationRow( $label, $value )
	{
		if ( strlen( $value ) > 0 )
		{
			echo '
				<div class="row">
					<div class="col-sm-6"> 
				        <strong>' . $label . '</strong>
				    </div>
					<div class="col-sm-6">
						' . $value . '
					</div>
				</div>';
		}
	}

	/**
	 * @param $label
	 * @param $value
	 *
	 * @return string
	 */
	public static function returnConfirmationRow( $label, $value )
	{
		if ( strlen( $value ) > 0 )
		{
			return '
				<tr>
					<th>' . $label . '</th>
					<td>' . $value . '</td>
				</tr>';
		}

		return '';
	}

	/**
	 * @param PriceGroup $price_group
	 * @param Pet $pet
	 */
	public static function drawFormPriceRow( &$price_group, &$pet )
	{
		if ( ! $price_group->isActive() )
		{
			return;
		}

		echo '
			<div class="form-group">
				<div class="col-sm-8"> 
		            <strong>' . $price_group->getTitle() . '</strong>
		        </div>';

		foreach( $price_group->getPrices() as $index => $price )
		{
			if ( ! $price->isActive() )
			{
				continue;
			}

			if ( $price_group->getPriceCount() > 1 )
			{
				echo '
					</div>
					<div class="form-group">
					<div class="col-sm-7 col-sm-offset-1"> 
			            ' . $price->getTitle() . '
			        </div>';
			}

			echo '
				<div class="col-sm-2">';

			if ( $price->getPrice() > 0 )
			{
				echo '$' . number_format( $price->getPrice(), 2 );
			}

			echo '
				</div>
				<div class="col-sm-2">';

			$value = $pet->getServicesItem( 'price_' . $price_group->getId() );

			if ( $price_group->getPriceCount() == 1 )
			{
				echo '
					<input type="checkbox" name="price_' . $price_group->getId() . '" value="' . $index . '"' . ( ( strlen( $value ) > 0 && $value == $index ) ? ' checked' : '' ) . '>';
			}
			else
			{
				echo '
					<input type="radio" name="price_' . $price_group->getId() . '" value="' . $index . '"' . ( ( strlen( $value ) > 0 && $value == $index ) ? ' checked' : '' ) . '>';
			}

			echo '
				</div>';
		}

		echo '
			</div>';
	}

	/**
	 * @param PriceGroup $price_group
	 * @param Pet $pet
	 */
	public static function drawConfirmationPriceRow( &$price_group, &$pet )
	{
		echo '
			<div class="row">
				<div class="col-sm-6"> 
		            <strong>' . $price_group->getTitle() . '</strong>
		        </div>';

		foreach( $price_group->getPrices() as $index => $price )
		{
			$value = $pet->getServicesItem( 'price_' . $price_group->getId() );

			if ( strlen( $value ) > 0 && $value == $index )
			{
				if ( $price_group->getPriceCount() > 1 )
				{
					echo '
						</div>
						<div class="row">
							<div class="col-sm-5 col-sm-offset-1"> 
					            ' . $price->getTitle() . '
					        </div>';
				}

				echo '<div class="col-sm-6">';

				if ( $price->getPrice() > 0 )
				{
					echo '$' . number_format( $price->getPrice(), 2 );
				}
				else
				{
					echo 'X';
				}

				echo '</div>';

			}
		}

		echo '
			</div>';
	}

	/**
	 * @param PriceGroup $price_group
	 * @param Pet $pet
	 *
	 * @return string
	 */
	public static function returnConfirmationPriceRow( &$price_group, &$pet )
	{
		$return = '
			<tr>
				<th>' . $price_group->getTitle() . '</th>';

		foreach( $price_group->getPrices() as $index => $price )
		{
			$value = $pet->getServicesItem( 'price_' . $price_group->getId() );

			if ( strlen( $value ) > 0 && $value == $index )
			{
				if ( $price_group->getPriceCount() > 1 )
				{
					$return .= '
							<td></td>
						</tr>
						<tr>
							<td class="skinny"> 
					            &raquo; ' . $price->getTitle() . '
					        </td>';
				}

				$return .= '<td>';

				if ( $price->getPrice() > 0 )
				{
					$return .= '$' . number_format( $price->getPrice(), 2 );
				}
				else
				{
					$return .= 'X';
				}

				$return .= '</td>';

			}
		}

		$return .= '</tr>';

		return $return;
	}

	public function nextStep()
	{
		switch ( $this->getCurrentStep() )
		{
			case self::STEP_BIO:
				if ( count( $this->getOwners() ) > 0 )
				{
					$this
						->setCurrentOwner( 0 )
						->setCurrentStep( self::STEP_OWNER );
				}
				else
				{
					$this->setCurrentStep( self::STEP_PET_COUNT );
				}
				break;

			case self::STEP_OWNER:
				if ( count( $this->getOwners() ) == $this->getCurrentOwner() + 1 )
				{
					$this->setCurrentStep( self::STEP_PET_COUNT );
				}
				else
				{
					$this->setCurrentOwner( $this->getCurrentOwner() + 1 );
				}
				break;

			case self::STEP_PET_COUNT:
				$this
					->setCurrentPet( 0 )
					->setCurrentStep( self::STEP_PET_INFO );
				break;

			case self::STEP_PET_INFO:
				if ( count( $this->getPets() ) == $this->getCurrentPet() + 1 )
				{
					$this
						->setCurrentPet( 0 )
						->setCurrentStep( self::STEP_PET_SERVICES );
				}
				else
				{
					$this->setCurrentPet( $this->getCurrentPet() + 1 );
				}
				break;

			case self::STEP_PET_SERVICES:
				if ( count( $this->getPets() ) == $this->getCurrentPet() + 1 )
				{
					if ( $this->getAggressiveCount() > 0 )
					{
						$this
							->setCurrentPet( $this->getAggressiveNumbers()[0] )
							->setCurrentStep( self::STEP_PET_AGGRESSION );
					}
					else
					{
						$this->setCurrentStep( self::STEP_CONFIRM );
					}
				}
				else
				{
					$this->setCurrentPet( $this->getCurrentPet() + 1 );
				}
				break;

			case self::STEP_PET_AGGRESSION:
				if ( $this->getCurrentPet() == $this->getLastAggressiveNumber() )
				{
					$this->setCurrentStep( self::STEP_CONFIRM );
				}
				else
				{
					$this->setCurrentPet( $this->getNextAggressiveNumber() );
				}
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

			case self::STEP_OWNER:
				if ( $this->getCurrentOwner() == 0 )
				{
					$this->setCurrentStep( self::STEP_BIO );
				}
				else
				{
					$this->setCurrentOwner( $this->getCurrentOwner() - 1 );
				}
				break;

			case self::STEP_PET_COUNT:
				if ( count( $this->getOwners() ) == 0 )
				{
					$this->setCurrentStep( self::STEP_BIO );
				}
				else
				{
					$this
						->setCurrentStep( self::STEP_OWNER )
						->setCurrentOwner( count( $this->getOwners() ) - 1 );
				}
				break;

			case self::STEP_PET_INFO:
				if ( $this->getCurrentPet() == 0 )
				{
					$this->setCurrentStep( self::STEP_PET_COUNT );
				}
				else
				{
					$this->setCurrentPet( $this->getCurrentPet() - 1 );
				}
				break;

			case self::STEP_PET_SERVICES:
				if ( $this->getCurrentPet() == 0 )
				{
					$this
						->setCurrentPet( count( $this->getPets() ) - 1 )
						->setCurrentStep( self::STEP_PET_INFO );
				}
				else
				{
					$this->setCurrentPet( $this->getCurrentPet() - 1 );
				}
				break;

			case self::STEP_PET_AGGRESSION:
				if ( $this->getCurrentPet() == $this->getFirstAggressiveNumber() )
				{
					$this
						->setCurrentPet( count( $this->getPets() ) - 1 )
						->setCurrentStep( self::STEP_PET_SERVICES );
				}
				else
				{
					$this->setCurrentPet( $this->getPriorAggressiveNumber() );
				}
				break;

			case self::STEP_CONFIRM:
				if ( $this->getAggressiveCount() > 0 )
				{
					$this
						->setCurrentPet( $this->getLastAggressiveNumber() )
						->setCurrentStep( self::STEP_PET_AGGRESSION );
				}
				else
				{
					$this
						->setCurrentPet( count( $this->getPets() ) - 1 )
						->setCurrentStep( self::STEP_PET_SERVICES );
				}
				break;

			default:
				$this->setCurrentStep( self::STEP_BIO );
		}
	}

	public function getPriorStepName()
	{
		switch ( $this->getCurrentStep() )
		{
			case self::STEP_BIO:
				return 'Email';

			case self::STEP_OWNER:
				if ( $this->getCurrentOwner() == 0 )
				{
					return 'You';
				}
				else
				{
					if ( strlen( $this->getOwners()[ $this->getCurrentOwner() - 1 ]->getInfoItem( 'first_name' ) ) == 0 )
					{
						return 'Owner';
					}
					else
					{
						return $this->getOwners()[ $this->getCurrentOwner() - 1 ]->getInfoItem( 'first_name' );
					}
				}

			case self::STEP_PET_COUNT:
				if ( count( $this->getOwners() ) == 0 )
				{
					return 'Your Info';
				}
				else
				{
					if ( strlen( $this->getOwners()[ count( $this->getOwners() ) - 1 ]->getInfoItem( 'first_name' ) ) == 0 )
					{
						return 'Owner';
					}
					else
					{
						return $this->getOwners()[ count( $this->getOwners() ) - 1 ]->getInfoItem( 'first_name' );
					}
				}

			case self::STEP_PET_INFO:
				if ( $this->getCurrentPet() == 0 )
				{
					return 'Pet Count';
				}
				else
				{
					if ( strlen( $this->getPets()[ $this->getCurrentPet() - 1 ]->getInfoItem( 'name' ) ) == 0 )
					{
						return $this->getPets()[ $this->getCurrentPet() - 1 ]->getType();
					}
					else
					{
						return $this->getPets()[ $this->getCurrentPet() - 1 ]->getInfoItem( 'name' );
					}
				}

			case self::STEP_PET_SERVICES:
				if ( $this->getCurrentPet() == 0 )
				{
					return $this->getPets()[ count( $this->getPets() ) - 1 ]->getInfoItem( 'name' );
				}
				else
				{
					return 'Services for ' . $this->getPets()[ $this->getCurrentPet() - 1 ]->getInfoItem( 'name' );
				}

			case self::STEP_PET_AGGRESSION:
				if ( $this->getCurrentPet() == $this->getFirstAggressiveNumber() )
				{
					return 'Services for ' . $this->getPets()[ count( $this->getPets() ) - 1 ]->getInfoItem( 'name' );
				}
				else
				{
					return 'Aggression QA for ' . $this->getPets()[ $this->getPriorAggressiveNumber() ]->getInfoItem( 'name' );
				}

			case self::STEP_CONFIRM:
				if ( $this->getAggressiveCount() > 0 )
				{
					return 'Aggression QA for ' . $this->getPets()[ $this->getLastAggressiveNumber() ]->getInfoItem( 'name' );
				}
				else
				{
					return 'Services for ' . $this->getPets()[ count( $this->getPets() ) - 1 ]->getInfoItem( 'name' );
				}
		}

		return '';
	}

	public function getNextStepName()
	{
		switch ( $this->getCurrentStep() )
		{
			case self::STEP_EMAIL:
				return 'Your Info';

			case self::STEP_BIO:
				if ( count( $this->getOwners() ) == 0 )
				{
					return 'Pet Count';
				}
				else
				{
					if ( strlen( $this->getOwners()[0]->getInfoItem( 'first_name' ) ) == 0 )
					{
						return 'Owner';
					}
					else
					{
						return $this->getOwners()[0]->getInfoItem( 'first_name' );
					}
				}

			case self::STEP_OWNER:
				if ( count( $this->getOwners() ) - 1 == $this->getCurrentOwner() )
				{
					return 'Pet Count';
				}
				else
				{
					if ( strlen( $this->getOwners()[ $this->getCurrentOwner() + 1 ]->getInfoItem( 'first_name' ) ) == 0 )
					{
						return 'Owner';
					}
					else
					{
						return $this->getOwners()[ $this->getCurrentOwner() + 1 ]->getInfoItem( 'first_name' );
					}
				}

			case self::STEP_PET_COUNT:
				return 'Pet Info';

			case self::STEP_PET_INFO:
				if ( $this->getCurrentPet() == count( $this->getPets() ) - 1 )
				{
					return 'Services';
				}
				else
				{
					if ( strlen( $this->getPets()[ $this->getCurrentPet() + 1 ]->getInfoItem( 'name' ) ) == 0 )
					{
						return $this->getPets()[ $this->getCurrentPet() + 1 ]->getType();
					}
					else
					{
						return $this->getPets()[ $this->getCurrentPet() + 1 ]->getInfoItem( 'name' );
					}
				}

			case self::STEP_PET_SERVICES:
				if ( $this->getCurrentPet() == count( $this->getPets() ) - 1 )
				{
					if ( $this->getAggressiveCount() > 0 )
					{
						return 'Aggression QA for ' . $this->getPets()[ $this->getFirstAggressiveNumber() ]->getInfoItem( 'name' );
					}
					else
					{
						return 'Confirmation';
					}
				}
				else
				{
					return 'Services for ' . $this->getPets()[ $this->getCurrentPet() + 1 ]->getInfoItem( 'name' );
				}

			case self::STEP_PET_AGGRESSION:
				if ( $this->getCurrentPet() == $this->getLastAggressiveNumber() )
				{
					return 'Confirmation';
				}
				else
				{
					return 'Aggression QA for ' . $this->getPets()[ $this->getNextAggressiveNumber() ]->getInfoItem( 'name' );
				}

		}

		return '';
	}

	/**
	 * @param string $item
	 *
	 * @return bool
	 */
	public static function canAddItem( $item )
	{
		$excludes = array(
			'_wpnonce',
			'_wp_http_referer',
			'nitro_k9_id',
			'nitro_k9_hash',
			'next_step',
			'prior_step',
			'add_owner',
			'remove_owner',
			'remove_pet'
		);

		return ( substr( $item, 0, 9 ) !== 'required_' && ! in_array( $item, $excludes ) );
	}

	/**
	 * @return int
	 */
	public function getAggressiveCount()
	{
		$count = 0;

		foreach ( $this->getPets() as $pet )
		{
			if ( $pet->isAggressive() )
			{
				$count++;
			}
		}

		return $count;
	}

	/**
	 * @return array
	 */
	public function getAggressiveNumbers()
	{
		$numbers = array();

		foreach ( $this->getPets() as $index => $pet )
		{
			if ( $pet->isAggressive() )
			{
				$numbers[] = $index;
			}
		}

		return $numbers;
	}

	/**
	 * @return mixed
	 */
	public function getFirstAggressiveNumber()
	{
		$numbers = $this->getAggressiveNumbers();
		return $numbers[ 0 ];
	}

	/**
	 * @return mixed
	 */
	public function getLastAggressiveNumber()
	{
		$numbers = $this->getAggressiveNumbers();
		return $numbers[ count( $numbers ) - 1 ];
	}

	/**
	 * @return mixed
	 */
	public function getNextAggressiveNumber()
	{
		$numbers = $this->getAggressiveNumbers();
		$pos = array_search( $this->current_pet, $numbers );
		return $numbers[ $pos + 1 ];
	}

	/**
	 * @return mixed
	 */
	public function getPriorAggressiveNumber()
	{
		$numbers = $this->getAggressiveNumbers();
		$pos = array_search( $this->current_pet, $numbers );
		return $numbers[ $pos - 1 ];
	}

	/**
	 * @param int $section
	 *
	 * @return array
	 */
	public function getInfoQuestions( $section=1 )
	{
		switch ( $section )
		{
			case 1:
				return array(
					array( 'email', 'Email Address', TRUE, $this->getEmail(), 'email' ),
					array( 'first_name', 'First Name', TRUE, $this->getFirstName() ),
					array( 'last_name', 'Last Name', TRUE, $this->getLastName() ),
					array( 'address', 'Address', TRUE, $this->getAddress() ),
					array( 'city', 'City', TRUE, $this->getCity() ),
					array( 'state', 'State', TRUE, $this->getState() ),
					array( 'zip', 'Zip', TRUE, $this->getZip() ),
					array( 'home_phone', 'Home Phone', FALSE, $this->getHomePhone() ),
					array( 'work_phone', 'Work Phone', FALSE, $this->getWorkPhone() ),
					array( 'cell_phone', 'Cell Phone', FALSE, $this->getCellPhone() ),
					array( 'how_heard', 'How did you hear about us?', FALSE, $this->getHowHeard(), 'select', self::getAllHowHeards() )
				);
			
			case 2:
				return array(
					array( 'em_contact', 'Emergency Contact', FALSE, $this->getEmContact() ),
					array( 'em_relationship', 'Relationship', FALSE, $this->getEmRelationship() ),
					array( 'em_home_phone', 'Home Phone', FALSE, $this->getEmHomePhone() ),
					array( 'em_work_phone', 'Work Phone', FALSE, $this->getEmWorkPhone() ),
					array( 'em_cell_phone', 'Cell Phone', FALSE, $this->getEmCellPhone() )
				);

			default:
				return array();
		}
	}

	/**
	 * @param Controller $controller
	 *
	 * @return string
	 */
	public function getNotificationEmail( &$controller )
	{
		$html = '';
		
		if ( $this->id !== NULL )
		{
			$html = '
				<style>
					table { width: 100%; border-collapse: collapse; }
					td { padding: 5px; width: 65%; }
					td.skinny { width: 35%; }
					td.full { width: 35%; }
					td.full th,
					td.full td { width: 18%; }
					th { padding: 5px; text-align: left; }
					th.heading { background: #EEE; width: 100%; }
				</style>

				<p>Dear Steve,</p>
				<p>A new sign-up form has been submitted by ' . $this->getFullName() . ' (' . $this->getEmail() . ').</p>
				<h2>Information About ' . $this->getFirstName() . '</h2>
				<table border="1">
					<tr>
						<th>Submitted On</th>
						<td>' . $this->getCompletedAt( 'l - F j, Y' ) . '</td>
					</tr>';

			$questions = array_merge( $this->getInfoQuestions( 1 ), $this->getInfoQuestions( 2 ) );

			foreach ( $questions as $array )
			{
				$html .= self::returnConfirmationRow(
					$array[1],
					$array[3]
				);
			}

			$html .= '
				</table>';

			foreach ( $this->getOwners() as $owner )
			{
				$html .= '
					<h2>Information About ' . $owner->getInfoItem( 'first_name' ) . '</h2>
					<table border="1">';

				$questions = array_merge( $owner->getInfoQuestions( 1 ), $owner->getInfoQuestions( 2 ) );

				foreach ( $questions as $array )
				{
					$html .= self::returnConfirmationRow(
						$array[1],
						$array[3]
					);
				}

				$html .= '</table>';
			}

			foreach ( $this->getPets() as $pet )
			{
				$html .= '
					<h2>Info About ' . $pet->getInfoItem( 'name' ) . '</h2>
					<table border="1">';

				$categories = $pet->getInfoQuestions( TRUE );

				foreach ( $categories as $category => $questions )
				{
					if ( strlen( $category ) > 0 )
					{
						$html .= '
							<tr>
								<th colspan="2" class="heading">' . strtoupper( $category ) . '</th>
							</tr>';
					}

					foreach ( $questions as $array )
					{
						$html .= self::returnConfirmationRow(
							$array[1],
							( $array[0] == 'is_aggressive' ) ? ( $pet->isAggressive() ) ? 'Yes' : 'No' : $array[3]
						);
					}
				}

				$html .= '</table>';
			}
			
			foreach ( $this->getPets() as $pet )
			{
				$html .= '
					<h2>Services for ' . $pet->getInfoItem( 'name' ) . '</h2>
					<table border="1">';

				$categories = $pet->getPricingQuestions( TRUE );

				foreach ( $categories as $category => $price_groups )
				{
					foreach( $price_groups as $price_group )
					{
						$html .= self::returnConfirmationPriceRow( $controller->price_groups[ $price_group ], $pet );
					}
				}

				$html .= '</table>';
			}

			foreach ( $this->getPets() as $pet )
			{
				if ( $pet->isAggressive() )
				{
					$html .= '
						<h2>Aggression Questionnaire for ' . $pet->getInfoItem( 'name' ) . '</h2>
						<table border="1">';

					for ( $section=1; $section<=5; $section++ )
					{
						switch( $section )
						{
							case 1:
							case 3:
							case 5:

								$categories = $pet->getAggressionQuestions( $section, TRUE );

								foreach ( $categories as $category => $questions )
								{
									$html .= '
										<tr>
											<th class="heading" colspan="2">' . $category . '</th>
										</tr>';

									foreach ( $questions as $question )
									{
										$html .= self::returnConfirmationRow(
											$question[0],
											$pet->getAggressionItem( $question[1] )
										);
									}
								}

								break;

							case 2:

								if ( $pet->hasPercentAnswers() )
								{
									$html .= '
										<tr>
											<th class="heading" colspan="2">
												What percent of the time does your dog obey the following commands for each member of the family?
											</th>
										</tr>';

									$commands = $pet->getAggressionQuestions( $section );

									$html .= '
										<tr>
											<td colspan="2" class="full">

												<table border="1">
													<thead>
														<tr>';
									foreach ( $commands as $key => $command )
									{
										$html .= '<th>' . $command . '</th>';
									}
									$html .= '
												</tr>
											</thead>';
									for ( $x=1; $x<=10; $x++ )
									{
										$show = FALSE;
										foreach ( $commands as $key => $commmand )
										{
											if ( strlen( $pet->getAggressionItem( 'percent_' . $x . '_' . $key ) ) > 0 )
											{
												$show = TRUE;
												break;
											}
										}
										if ( $show )
										{
											echo '<tr>';
											foreach ( $commands as $key => $command )
											{
												$html .= '<td>' . $pet->getAggressionItem( 'percent_' . $x . '_' . $key ) . '</td>';
											}
											$html .= '</tr>';
										}
									}
									$html .= '
											</table>
										</td>
									</tr>';
								}

								break;

							case 4:

								if ( $pet->hasScreenAnswers() )
								{
									$html .= '
										<tr>
											<th class="heading" colspan="2">
												AGGRESSION SCREEN
											</th>
										</tr>';

									$responses = array(
										'growl' => 'Growl',
										'snarl' => 'Snarl / Bare Teeth',
										'snap' => 'Snap / Bite',
										'no' => 'No Reaction',
										'na' => 'N/A'
									);

									$causes = $pet->getAggressionQuestions( $section );

									$html .=
										'<tr>
											<td colspan="2" class="full">
									
												<table border="1">
													<thead>
														<tr>
															<th>Action</th>';
									foreach ( $responses as $key => $response )
									{
										$html .= '<th>' .  $response . '</th>';
									}
									$html .= '
												</tr>
											</thead>';
									foreach ( $causes as $index => $cause )
									{
										$show = FALSE;
										foreach ( $responses as $key => $response )
										{
											if ( strlen( $pet->getAggressionItem( 'screen_' . $index . '_' . $key ) ) > 0 )
											{
												$show = TRUE;
												break;
											}
										}
										if ( $show )
										{
											$html .= '
													<tr>
														<th>' . $cause . '</th>';
											foreach ( $responses as $key => $response )
											{
												$html .= '<td style="text-align:center">';
												if ( strlen( $pet->getAggressionItem( 'screen_' . $index . '_' . $key ) ) )
												{
													$html .= 'X';
												}
												$html .= '</td>';
											}
											$html .= '</tr>';
										}
									}
									$html .= '
											</table>
										</td>
									</tr>';
								}

								break;
						}
					}

					$html .= '</table>';
				}
			}

		}
		
		return $html;
	}
}