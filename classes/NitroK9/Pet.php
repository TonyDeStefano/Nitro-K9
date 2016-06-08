<?php

namespace NitroK9;

class Pet {

	const TYPE_LARGE_DOG = 'Large Dog';
	const TYPE_SMALL_DOG = 'Small Dog';
	
	private $type;
	private $is_aggressive = FALSE;
	private $info;
	private $services;
	private $aggression;

	/**
	 * Pet constructor.
	 *
	 * @param null $json
	 */
	public function __construct( $json=NULL )
	{
		$this->info = array();
		$this->services = array();
		$this->aggression = array();
		
		if ( $json !== NULL )
		{
			$array = json_decode( $json, TRUE );
			if ( is_array( $array ) )
			{
				if ( isset( $array['is_aggressive'] ) )
				{
					$this->setIsAggressive( $array['is_aggressive'] );
				}

				if ( isset( $array['type'] ) )
				{
					$this->setType( $array['type'] );
				}
				
				if ( isset( $array['info'] ) )
				{
					$this->info = $array['info'];
				}

				if ( isset( $array['services'] ) )
				{
					$this->services = $array['services'];
				}

				if ( isset( $array['aggression'] ) )
				{
					$this->aggression = $array['aggression'];
				}
			}
		}
	}

	/**
	 * @return mixed
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @param mixed $type
	 *
	 * @return Pet
	 */
	public function setType( $type )
	{
		$this->type = ( $type == self::TYPE_LARGE_DOG ) ? self::TYPE_LARGE_DOG : self::TYPE_SMALL_DOG;

		return $this;
	}

	/**
	 * @return boolean
	 */
	public function isAggressive()
	{
		return ( $this->is_aggressive === TRUE );
	}

	/**
	 * @param boolean $is_aggressive
	 *
	 * @return Pet
	 */
	public function setIsAggressive( $is_aggressive )
	{
		$this->is_aggressive = ( $is_aggressive == 1 || $is_aggressive === TRUE );

		return $this;
	}

	/**
	 * @param $item
	 *
	 * @return mixed|string
	 */
	public function getInfoItem( $item )
	{
		return $this->getItem( 'info', $item );
	}

	/**
	 * @param $item
	 *
	 * @return mixed|string
	 */
	public function getServicesItem( $item )
	{
		return $this->getItem( 'services', $item );
	}

	/**
	 * @param $item
	 *
	 * @return mixed|string
	 */
	public function getAggressionItem( $item )
	{
		return $this->getItem( 'aggression', $item );
	}

	/**
	 * @param $property
	 * @param $item
	 *
	 * @return mixed|string
	 */
	public function getItem( $property, $item )
	{
		if ( isset( $this->$property ) )
		{
			$array = $this->$property;
			
			if ( is_array( $array ) && isset( $array[ $item ] ) )
			{
				return $array[ $item ];
			}
		}

		return '';
	}

	/**
	 * @param $item
	 * @param $value
	 *
	 * @return $this
	 */
	public function setInfoItem( $item, $value )
	{
		$this->setItem( 'info', $item, $value );

		return $this;
	}

	/**
	 * I guess you can't assign a value like this `$this->$property[ $item ] = $value;`
	 * So that's why there is a temp array there.
	 *
	 * @param $property
	 * @param $item
	 * @param $value
	 *
	 * @return $this
	 */
	public function setItem( $property, $item, $value )
	{
		$array = $this->$property;
		if ( $array === NULL )
		{
			$array = array();
		}

		$array[ $item ] = $value;
		$this->$property = $array;

		return $this;
	}

	public function setInfoItemsFromPost()
	{
		$this->setItemsFromPost( 'info' );
	}

	public function setItemsFromPost( $property )
	{
		foreach ( $_POST as $key => $val )
		{
			if ( Entry::canAddItem( $key ) )
			{
				$this->setItem( $property, $key, $val );
			}
		}
	}

	/**
	 * @return mixed
	 */
	public function getInfo()
	{
		return ( $this->info === NULL ) ? array() : $this->info;
	}

	/**
	 * @param mixed $info
	 *
	 * @return Pet
	 */
	public function setInfo( $info )
	{
		$this->info = $info;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getServices()
	{
		return ( $this->services === NULL ) ? array() : $this->services;
	}

	/**
	 * @param mixed $services
	 *
	 * @return Pet
	 */
	public function setServices( $services )
	{
		$this->services = $services;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getAggression()
	{
		return ( $this->aggression === NULL ) ? array() : $this->aggression;
	}

	/**
	 * @param mixed $aggression
	 *
	 * @return Pet
	 */
	public function setAggression( $aggression )
	{
		$this->aggression = $aggression;

		return $this;
	}

	/**
	 * @return array
	 */
	public function toArray()
	{
		return array(
			'type' => $this->getType(),
			'is_aggressive' => ( $this->is_aggressive ) ? 1 : 0,
			'info' => $this->getInfo(),
			'services' => $this->getServices(),
			'aggression' => $this->getAggression()
		);
	}
}