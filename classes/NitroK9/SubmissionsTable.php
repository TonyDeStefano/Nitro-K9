<?php

namespace NitroK9;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class SubmissionsTable extends \WP_List_Table {

	/**
	 * SubmissionsTable constructor.
	 */
	public function __construct()
	{
		parent::__construct( array(
			'singular' => 'Submission',
			'plural' => 'Submissions',
			'ajax' => TRUE
		) );
	}

	/**
	 * @return array
	 */
	public function get_columns()
	{
		$return = array(
			'name' => 'Name',
			'email' => 'Email',
			'location' => 'Location',
			'pets' => 'Pets',
			'date' => 'Date',
			'view' => ''
		);

		return $return;
	}

	/**
	 * @return array
	 */
	public function get_sortable_columns()
	{
		$return =  array(
			'date' => array( 'completed_at', TRUE )
		);

		return $return;
	}

	/**
	 * @param object $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	public function column_default( $item, $column_name )
	{
		$entry = new Entry;
		$entry->loadFromRow( $item );

		switch( $column_name )
		{
			case 'name':
				return $entry->getFullName();
			case 'email':
				return '<a href="mailto:' . $entry->getEmail() . '">' . $entry->getEmail() . '</a>';
			case 'location':
				return $entry->getCity() . ', ' . $entry->getState();
			case 'pets':
				$return = array();
				foreach ( $entry->getPets() as $pet )
				{
					$return[] = $pet->getInfoItem( 'name' ) . ' ( ' . ( ( $pet->getType() == Pet::TYPE_LARGE_DOG ) ? 'Large' : 'Small' ) . ( ( $pet->isAggressive() ) ? ' / Aggressive' : '' ) . ' )';
				}
				return implode( '<br>', $return );
			case 'date':
				return $entry->getCompletedAt( 'F j, Y' );
			case 'view':
				return '<a href="?page=' . $_REQUEST['page'] . '&action=view&id=' . $entry->getId() . '" class="button-primary">View</a>';
			default:
				return $item->$column_name;

		}
	}

	/**
	 *
	 */
	public function prepare_items()
	{
		global $wpdb;

		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();

		$sql = "
			SELECT
				*
			FROM
				" . $wpdb->prefix . Entry::TABLE_NAME . "
			WHERE
				completed_at IS " . ( ( isset( $_REQUEST['unfinished'] ) ) ? "" : "NOT" ) . " NULL";
		if ( isset( $_GET[ 'orderby' ] ) )
		{
			foreach ( $sortable as $s )
			{
				if ( $s[ 0 ] == $_GET[ 'orderby' ] )
				{
					$sql .= "
						ORDER BY " . $_GET[ 'orderby' ] . " " . ( ( isset( $_GET['order']) && strtolower( $_GET['order'] == 'desc' ) ) ? "DESC" : "ASC" );
					break;
				}
			}
		}
		else
		{
			$sql .= "
				ORDER BY 
					completed_at DESC,
					id DESC";
		}

		$total_items = $wpdb->query($sql);

		$max_per_page = 50;
		$paged = ( isset( $_GET[ 'paged' ] ) && is_numeric( $_GET['paged'] ) ) ? abs( round( $_GET[ 'paged' ])) : 1;
		$total_pages = ceil( $total_items / $max_per_page );

		if ( $paged > $total_pages )
		{
			$paged = $total_pages;
		}

		$offset = ( $paged - 1 ) * $max_per_page;
		$offset = ( $offset < 0 ) ? 0 : $offset; //MySQL freaks out about LIMIT -10, 10 type stuff.

		$sql .= "
			LIMIT " . $offset . ", " . $max_per_page;

		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'total_pages' => $total_pages,
			'per_page' => $max_per_page
		) );

		$this->_column_headers = array( $columns, $hidden, $sortable );
		$this->items = $wpdb->get_results( $sql );
	}
}