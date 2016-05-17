<?php

namespace NitroK9;

class Pet {

	private $info;
	private $services;
	private $aggression;

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
			$array = json_decode( $this->$property, TRUE );
			{
				if ( is_array( $array ) && isset( $array[$item] ) )
				{
					return $array[$item];
				}
			}
		}
		return '';
	}

	/**
	 * @return mixed
	 */
	public function getInfo()
	{
		return $this->info;
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
		return $this->services;
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
		return $this->aggression;
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

}