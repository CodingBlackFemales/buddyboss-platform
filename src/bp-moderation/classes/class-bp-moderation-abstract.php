<?php
/**
 * BuddyBoss Moderation items abstract Classes
 *
 * @package BuddyBoss\Moderation
 * @since   BuddyBoss 1.5.4
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Database interaction class for the BuddyBoss moderation items.
 *
 * @since BuddyBoss 1.5.4
 */
abstract class BP_Moderation_Abstract {

	/**
	 * Moderation classes
	 *
	 * @var array
	 */
	public static $Moderation;

	/**
	 * Item type
	 *
	 * @var string
	 */
	public $item_type;

	/**
	 * Item type
	 *
	 * @var string
	 */
	public $alias = 'mo';

	/**
	 * Prepare Join sql for exclude Blocked items
	 *
	 * @since BuddyBoss 1.5.4
	 *
	 * @param string $item_id_field Items ID field name with alias of table.
	 *
	 * @return string|void
	 */
	protected function exclude_joint_query( $item_id_field ) {
		global $wpdb;
		$bp = buddypress();

		return ' ' . $wpdb->prepare( "LEFT JOIN {$bp->moderation->table_name} {$this->alias} ON ( {$this->alias}.item_id = $item_id_field AND {$this->alias}.item_type = %s )", $this->item_type ); // phpcs:ignore
	}

	/**
	 * Prepare Where sql for exclude Blocked items
	 *
	 * @return string|void
	 *
	 * @since BuddyBoss 1.5.4
	 */
	protected function exclude_where_query() {
		return "( {$this->alias}.hide_sitewide = 0 OR {$this->alias}.hide_sitewide IS NULL )";
	}

	/**
	 * Retrieve sitewide hidden items ids of particular item type.
	 *
	 * @since BuddyBoss 1.5.4
	 *
	 * @param string $type Moderation items type.
	 *
	 * @return array $moderation See BP_Moderation::get() for description.
	 */
	public static function get_sitewide_hidden_item_ids( $type ) {
		$hidden_ids  = array();
		$moderations = bp_moderation_get_sitewide_hidden_item_ids( $type );

		if ( ! empty( $moderations ) && ! empty( $moderations['moderations'] ) ) {
			$hidden_ids = wp_list_pluck( $moderations['moderations'], 'item_id' );
		}

		return $hidden_ids;
	}

	/**
	 * Get Content owner id.
	 *
	 * @param integer $item_id         Content item id
	 */
	abstract public static function get_content_owner_id( $item_id );
}
