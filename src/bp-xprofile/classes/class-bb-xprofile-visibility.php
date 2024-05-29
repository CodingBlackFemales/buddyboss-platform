<?php
/**
 * BuddyPress XProfile Visibility Class.
 *
 * @package BuddyBoss\XProfile\Classes
 *
 * @since BuddyBoss [BBVERSION]
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Class for XProfile visibility.
 *
 * @since BuddyBoss [BBVERSION]
 */
class BB_XProfile_Visibility {

	/**
	 * XProfile Visibility ID.
	 *
	 * @since BuddyBoss [BBVERSION]
	 *
	 * @var int $id
	 */
	public $id;

	/**
	 * User ID.
	 *
	 * @since BuddyBoss [BBVERSION]
	 *
	 * @var int $user_id
	 */
	public $user_id;

	/**
	 * XProfile field ID.
	 *
	 * @since BuddyBoss [BBVERSION]
	 *
	 * @var int $field_id
	 */
	public $field_id;

	/**
	 * XProfile field visibility value.
	 *
	 * @since BuddyBoss [BBVERSION]
	 *
	 * @var string $value
	 */
	public $value;

	/**
	 * XProfile field last updated time.
	 *
	 * @since BuddyBoss [BBVERSION]
	 *
	 * @var string $last_updated
	 */
	public $last_updated;

	/**
	 * BB_XProfile_Visibility constructor.
	 *
	 * @since BuddyBoss [BBVERSION]
	 *
	 * @param int|null $field_id Field ID to instantiate.
	 * @param int|null $user_id  User ID to instantiate for.
	 */
	public function __construct( $field_id = null, $user_id = null ) {
		if ( ! empty( $field_id ) ) {
			$this->populate( $field_id, $user_id );
		}
	}

	/**
	 * Populates the XProfile profile visibility data.
	 *
	 * @since BuddyBoss [BBVERSION]
	 *
	 * @param int $field_id Field ID to populate.
	 * @param int $user_id  User ID to populate for.
	 */
	public function populate( $field_id, $user_id ) {
		global $wpdb;

		$table_name = bp_core_get_table_prefix() . 'bb_xprofile_visibility';

		$sql                = $wpdb->prepare( "SELECT * FROM {$table_name} WHERE field_id = %d AND user_id = %d", $field_id, $user_id );
		$profile_visibility = $wpdb->get_row( $sql );

		if ( $profile_visibility ) {
			$this->id           = (int) $profile_visibility->id;
			$this->user_id      = (int) $profile_visibility->user_id;
			$this->field_id     = (int) $profile_visibility->field_id;
			$this->value        = stripslashes( $profile_visibility->value );
			$this->last_updated = $profile_visibility->last_updated;

		} else {
			// When no row is found, we'll need to set these properties manually.
			$this->field_id = (int) $field_id;
			$this->user_id  = (int) $user_id;
		}
	}

	/**
	 * Check if there is data already for the user.
	 *
	 * @since BuddyBoss [BBVERSION]
	 *
	 * @global wpdb  $wpdb WordPress database abstraction object.
	 * @global array $bp
	 *
	 * @return bool
	 */
	public function exists() {
		global $wpdb;

		$table  = bp_core_get_table_prefix() . 'bb_xprofile_visibility';
		$retval = $wpdb->get_row( $wpdb->prepare( "SELECT id FROM {$table} WHERE user_id = %d AND field_id = %d", $this->user_id, $this->field_id ) );

		/**
		 * Filters whether or not data already exists for the user.
		 *
		 * @since BuddyPress 1.2.7
		 *
		 * @param bool                   $retval Whether or not data already exists.
		 * @param BB_XProfile_Visibility $this   Instance of the current BB_XProfile_Visibility class.
		 */
		return apply_filters_ref_array( 'xprofile_visibility_exists', array( (bool) $retval, $this ) );
	}

	/**
	 * Check if this data is for a valid field.
	 *
	 * @since BuddyBoss [BBVERSION]
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 *
	 * @return bool
	 */
	public function is_valid_field() {
		global $wpdb;
		$table = bp_core_get_table_prefix() . 'bp_xprofile_fields';

		$cache_key = 'bp_xprofile_is_valid_field_' . $this->field_id;
		$retval    = wp_cache_get( $cache_key, 'bp_xprofile' );

		if ( false === $retval ) {
			$retval = $wpdb->get_row( $wpdb->prepare( "SELECT id FROM {$table} WHERE id = %d", $this->field_id ) );
			wp_cache_set( $cache_key, $retval, 'bp_xprofile' );
		}

		/**
		 * Filters whether or not data is for a valid field.
		 *
		 * @since BuddyBoss [BBVERSION]
		 *
		 * @param bool                   $retval Whether or not data is valid.
		 * @param BB_XProfile_Visibility $this   Instance of the current BB_XProfile_Visibility class.
		 */
		return apply_filters_ref_array( 'xprofile_visibility_is_valid_field', array( (bool) $retval, $this ) );
	}

	/**
	 * Save the data for the XProfile field.
	 *
	 * @since BuddyBoss [BBVERSION]
	 *
	 * @return bool
	 */
	public function save() {
		global $wpdb;

		$bp = buddypress();

		/**
		 * Filters the data's user ID before saving to the database.
		 *
		 * @since BuddyBoss [BBVERSION]
		 *
		 * @param int $user_id The user ID.
		 * @param int $data_id The field data ID.
		 */
		$this->user_id = apply_filters( 'xprofile_visibility_user_id_before_save', $this->user_id, $this->id );

		/**
		 * Filters the data's field ID before saving to the database.
		 *
		 * @since BuddyBoss [BBVERSION]
		 *
		 * @param int $field_id The field ID.
		 * @param int $data_id  The field data ID.
		 */
		$this->field_id = apply_filters( 'xprofile_visibility_field_id_before_save', $this->field_id, $this->id );

		/**
		 * Filters the data's value before saving to the database.
		 *
		 * @since BuddyBoss [BBVERSION]
		 *
		 * @param string                 $field_value The field value.
		 * @param int                    $data_id     The field data ID.
		 * @param bool                   $reserialize Whether to reserialize arrays before returning. Defaults to true.
		 * @param BB_XProfile_Visibility $this        Current instance of the profile data being saved.
		 */
		$this->value = apply_filters( 'xprofile_visibility_value_before_save', $this->value, $this->id, true, $this );

		/**
		 * Filters the data's last updated timestamp before saving to the database.
		 *
		 * @since BuddyBoss [BBVERSION]
		 *
		 * @param int $last_updated The last updated timestamp.
		 * @param int $data_id      The field data ID.
		 */
		$this->last_updated = apply_filters( 'xprofile_visibility_last_updated_before_save', bp_core_current_time(), $this->id );

		/**
		 * Fires before the current profile data instance gets saved.
		 *
		 * Please use this hook to filter the properties above. Each part will be passed in.
		 *
		 * @since BuddyBoss [BBVERSION]
		 *
		 * @param BB_XProfile_Visibility $this Current instance of the profile data being saved.
		 */
		do_action_ref_array( 'xprofile_visibility_before_save', array( $this ) );

		if ( $this->is_valid_field() ) {
			if ( $this->exists() && strlen( trim( $this->value ) ) ) {
				$result = $wpdb->query( $wpdb->prepare( "UPDATE {$bp->profile->table_name_visibility} SET value = %s, last_updated = %s WHERE user_id = %d AND field_id = %d", $this->value, $this->last_updated, $this->user_id, $this->field_id ) );

			} elseif ( $this->exists() && empty( $this->value ) ) {
				// Data removed, delete the entry.
				$result = $this->delete();

			} else {
				$table    = bp_core_get_table_prefix() . 'bb_xprofile_visibility';
				$result   = $wpdb->query( $wpdb->prepare( "INSERT INTO {$table} (user_id, field_id, value, last_updated) VALUES (%d, %d, %s, %s)", $this->user_id, $this->field_id, $this->value, $this->last_updated ) );
				$this->id = $wpdb->insert_id;
			}

			if ( false === $result ) {
				return false;
			}

			/**
			 * Fires after the current profile data instance gets saved.
			 *
			 * @since BuddyBoss [BBVERSION]
			 *
			 * @param BB_XProfile_Visibility $this Current instance of the profile data being saved.
			 */
			do_action_ref_array( 'xprofile_visibility_after_save', array( $this ) );

			return true;
		}

		return false;
	}

	/**
	 * Delete specific XProfile field data.
	 *
	 * @since BuddyBoss [BBVERSION]
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 *
	 * @return boolean
	 */
	public function delete() {
		global $wpdb;

		$bp         = buddypress();
		$table_name = bp_core_get_table_prefix() . 'bb_xprofile_visibility';

		/**
		 * Fires before the current profile data instance gets deleted.
		 *
		 * @since BuddyBoss [BBVERSION]
		 *
		 * @param BB_XProfile_Visibility $this Current instance of the profile data being deleted.
		 */
		do_action_ref_array( 'xprofile_visibility_before_delete', array( $this ) );

		$deleted = $wpdb->query( $wpdb->prepare( "DELETE FROM {$table_name} WHERE field_id = %d AND user_id = %d", $this->field_id, $this->user_id ) );
		if ( empty( $deleted ) ) {
			return false;
		}

		/**
		 * Fires after the current profile data instance gets deleted.
		 *
		 * @since BuddyBoss [BBVERSION]
		 *
		 * @param BB_XProfile_Visibility $this Current instance of the profile data being deleted.
		 */
		do_action_ref_array( 'xprofile_visibility_after_delete', array( $this ) );

		return true;
	}
	/**
	 * Delete field.
	 *
	 * @since BuddyBoss [BBVERSION]
	 *
	 * @param int $field_id ID of the field to delete.
	 *
	 * @return bool
	 */
	public static function delete_for_field( $field_id ) {
		global $wpdb;

		$bp      = buddypress();
		$deleted = $wpdb->query( $wpdb->prepare( "DELETE FROM {$bp->profile->table_name_visibility} WHERE field_id = %d", $field_id ) );
		if ( empty( $deleted ) || is_wp_error( $deleted ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Delete all data for provided user ID.
	 *
	 * @since BuddyBoss [BBVERSION]
	 *
	 * @param int $user_id User ID to remove data for.
	 *
	 * @return false|int
	 */
	public static function delete_data_for_user( $user_id ) {
		global $wpdb;

		$bp = buddypress();

		$field_ids = $wpdb->get_col( $wpdb->prepare( "SELECT field_id FROM {$bp->profile->table_name_visibility} WHERE user_id = %d", $user_id ) );

		if ( ! $field_ids ) {
			return false;
		}

		foreach ( $field_ids as $field_id ) {
			xprofile_delete_field_data( $field_id, $user_id );
		}

		return count( $field_ids );
	}

	/**
	 * Get the XProfile profile visibility data.
	 *
	 * @since BuddyBoss [BBVERSION]
	 *
	 * @param int $user_id User ID to get fields for.
	 *
	 * @return array
	 */
	public static function get_user_field_ids_by_visibility_levels( $user_id, $levels = array() ) {
		global $wpdb;

		$bp     = buddypress();
		$fields = array();

		if ( empty( $user_id ) || $levels = array() ) {
			return $fields;
		}

		$sql    = $wpdb->prepare( "SELECT DISTINCT field_id FROM {$bp->profile->table_name_visibility} WHERE user_id = %d AND value IN ( %s )" , $user_id, implode( ',', $levels ) );
		$fields = $wpdb->get_results( $sql, OBJECT_K );

		return $fields;
	}

}