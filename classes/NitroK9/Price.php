<?php

namespace NitroK9;

class Price {

	private $title;
	private $price;
	private $is_active;

	/**
	 * Price constructor.
	 *
	 * @param null $title
	 * @param null $price
	 * @param bool $is_active
	 */
	public function __construct( $title=NULL, $price=NULL, $is_active=TRUE )
	{
		$this
			->setTitle( $title )
			->setPrice( $price )
			->setIsActive( $is_active );
	}

	/**
	 * @return mixed
	 */
	public function getTitle()
	{
		return ( $this->title === NULL ) ? 0 : $this->title;
	}

	/**
	 * @param mixed $title
	 *
	 * @return Price
	 */
	public function setTitle( $title )
	{
		$this->title = $title;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getPrice()
	{
		return ( $this->price === NULL ) ? '0' : $this->price;
	}

	/**
	 * @param mixed $price
	 *
	 * @return Price
	 */
	public function setPrice( $price )
	{
		$this->price = ( is_numeric( $price ) ) ? $price : NULL;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function isActive()
	{
		return ( $this->is_active === TRUE );
	}

	/**
	 * @param mixed $is_active
	 *
	 * @return PriceGroup
	 */
	public function setIsActive( $is_active )
	{
		$this->is_active = ( $is_active === TRUE || $is_active == 1 );

		return $this;
	}
}