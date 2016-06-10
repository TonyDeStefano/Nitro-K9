<?php

namespace NitroK9;

class Owner {

	private $info;

	/**
	 * Owner constructor.
	 *
	 * @param null $json
	 */
	public function __construct( $json=NULL )
	{
		$this->info = array();

		if ( $json !== NULL )
		{
			$array = json_decode( $json, TRUE );
			if ( is_array( $array ) )
			{
				if ( isset( $array['info'] ) )
				{
					$this->info = $array['info'];
				}
			}
		}
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
	 * @return array
	 */
	public function toArray()
	{
		return array(
			'info' => $this->getInfo()
		);
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
					array( 'email', 'Email Address', TRUE, $this->getInfoItem( 'email' ), 'email' ),
					array( 'first_name', 'First Name', TRUE, $this->getInfoItem( 'first_name' ) ),
					array( 'last_name', 'Last Name', TRUE, $this->getInfoItem( 'last_name' ) ),
					array( 'address', 'Address', TRUE, $this->getInfoItem( 'address' ) ),
					array( 'city', 'City', TRUE, $this->getInfoItem( 'city' ) ),
					array( 'state', 'State', TRUE, $this->getInfoItem( 'state' ) ),
					array( 'zip', 'Zip', TRUE, $this->getInfoItem( 'zip' ) ),
					array( 'home_phone', 'Home Phone', FALSE, $this->getInfoItem( 'home_phone' ) ),
					array( 'work_phone', 'Work Phone', FALSE, $this->getInfoItem( 'work_phone' ) ),
					array( 'cell_phone', 'Cell Phone', FALSE, $this->getInfoItem( 'cell_phone' ) )
				);

			case 2:
				return array(
					array( 'em_contact', 'Emergency Contact', FALSE, $this->getInfoItem( 'em_contact' ) ),
					array( 'em_relationship', 'Relationship', FALSE, $this->getInfoItem( 'em_relationship' ) ),
					array( 'em_home_phone', 'Home Phone', FALSE, $this->getInfoItem( 'em_home_phone' ) ),
					array( 'em_work_phone', 'Work Phone', FALSE, $this->getInfoItem( 'em_work_phone' ) ),
					array( 'em_cell_phone', 'Cell Phone', FALSE, $this->getInfoItem( 'em_cell_phone' ) )
				);

			default:
				return array();
		}
	}
}