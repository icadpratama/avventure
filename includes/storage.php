<?php
/**
 * Theme storage manipulations
 *
 * @package WordPress
 * @subpackage AVVENTURE
 * @since AVVENTURE 1.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) {
	exit; }

// Get theme variable
if ( ! function_exists( 'avventure_storage_get' ) ) {
	function avventure_storage_get( $var_name, $default = '' ) {
		global $AVVENTURE_STORAGE;
		return isset( $AVVENTURE_STORAGE[ $var_name ] ) ? $AVVENTURE_STORAGE[ $var_name ] : $default;
	}
}

// Set theme variable
if ( ! function_exists( 'avventure_storage_set' ) ) {
	function avventure_storage_set( $var_name, $value ) {
		global $AVVENTURE_STORAGE;
		$AVVENTURE_STORAGE[ $var_name ] = $value;
	}
}

// Check if theme variable is empty
if ( ! function_exists( 'avventure_storage_empty' ) ) {
	function avventure_storage_empty( $var_name, $key = '', $key2 = '' ) {
		global $AVVENTURE_STORAGE;
		if ( ! empty( $key ) && ! empty( $key2 ) ) {
			return empty( $AVVENTURE_STORAGE[ $var_name ][ $key ][ $key2 ] );
		} elseif ( ! empty( $key ) ) {
			return empty( $AVVENTURE_STORAGE[ $var_name ][ $key ] );
		} else {
			return empty( $AVVENTURE_STORAGE[ $var_name ] );
		}
	}
}

// Check if theme variable is set
if ( ! function_exists( 'avventure_storage_isset' ) ) {
	function avventure_storage_isset( $var_name, $key = '', $key2 = '' ) {
		global $AVVENTURE_STORAGE;
		if ( ! empty( $key ) && ! empty( $key2 ) ) {
			return isset( $AVVENTURE_STORAGE[ $var_name ][ $key ][ $key2 ] );
		} elseif ( ! empty( $key ) ) {
			return isset( $AVVENTURE_STORAGE[ $var_name ][ $key ] );
		} else {
			return isset( $AVVENTURE_STORAGE[ $var_name ] );
		}
	}
}

// Inc/Dec theme variable with specified value
if ( ! function_exists( 'avventure_storage_inc' ) ) {
	function avventure_storage_inc( $var_name, $value = 1 ) {
		global $AVVENTURE_STORAGE;
		if ( empty( $AVVENTURE_STORAGE[ $var_name ] ) ) {
			$AVVENTURE_STORAGE[ $var_name ] = 0;
		}
		$AVVENTURE_STORAGE[ $var_name ] += $value;
	}
}

// Concatenate theme variable with specified value
if ( ! function_exists( 'avventure_storage_concat' ) ) {
	function avventure_storage_concat( $var_name, $value ) {
		global $AVVENTURE_STORAGE;
		if ( empty( $AVVENTURE_STORAGE[ $var_name ] ) ) {
			$AVVENTURE_STORAGE[ $var_name ] = '';
		}
		$AVVENTURE_STORAGE[ $var_name ] .= $value;
	}
}

// Get array (one or two dim) element
if ( ! function_exists( 'avventure_storage_get_array' ) ) {
	function avventure_storage_get_array( $var_name, $key, $key2 = '', $default = '' ) {
		global $AVVENTURE_STORAGE;
		if ( empty( $key2 ) ) {
			return ! empty( $var_name ) && ! empty( $key ) && isset( $AVVENTURE_STORAGE[ $var_name ][ $key ] ) ? $AVVENTURE_STORAGE[ $var_name ][ $key ] : $default;
		} else {
			return ! empty( $var_name ) && ! empty( $key ) && isset( $AVVENTURE_STORAGE[ $var_name ][ $key ][ $key2 ] ) ? $AVVENTURE_STORAGE[ $var_name ][ $key ][ $key2 ] : $default;
		}
	}
}

// Set array element
if ( ! function_exists( 'avventure_storage_set_array' ) ) {
	function avventure_storage_set_array( $var_name, $key, $value ) {
		global $AVVENTURE_STORAGE;
		if ( ! isset( $AVVENTURE_STORAGE[ $var_name ] ) ) {
			$AVVENTURE_STORAGE[ $var_name ] = array();
		}
		if ( '' === $key ) {
			$AVVENTURE_STORAGE[ $var_name ][] = $value;
		} else {
			$AVVENTURE_STORAGE[ $var_name ][ $key ] = $value;
		}
	}
}

// Set two-dim array element
if ( ! function_exists( 'avventure_storage_set_array2' ) ) {
	function avventure_storage_set_array2( $var_name, $key, $key2, $value ) {
		global $AVVENTURE_STORAGE;
		if ( ! isset( $AVVENTURE_STORAGE[ $var_name ] ) ) {
			$AVVENTURE_STORAGE[ $var_name ] = array();
		}
		if ( ! isset( $AVVENTURE_STORAGE[ $var_name ][ $key ] ) ) {
			$AVVENTURE_STORAGE[ $var_name ][ $key ] = array();
		}
		if ( '' === $key2 ) {
			$AVVENTURE_STORAGE[ $var_name ][ $key ][] = $value;
		} else {
			$AVVENTURE_STORAGE[ $var_name ][ $key ][ $key2 ] = $value;
		}
	}
}

// Merge array elements
if ( ! function_exists( 'avventure_storage_merge_array' ) ) {
	function avventure_storage_merge_array( $var_name, $key, $value ) {
		global $AVVENTURE_STORAGE;
		if ( ! isset( $AVVENTURE_STORAGE[ $var_name ] ) ) {
			$AVVENTURE_STORAGE[ $var_name ] = array();
		}
		if ( '' === $key ) {
			$AVVENTURE_STORAGE[ $var_name ] = array_merge( $AVVENTURE_STORAGE[ $var_name ], $value );
		} else {
			$AVVENTURE_STORAGE[ $var_name ][ $key ] = array_merge( $AVVENTURE_STORAGE[ $var_name ][ $key ], $value );
		}
	}
}

// Add array element after the key
if ( ! function_exists( 'avventure_storage_set_array_after' ) ) {
	function avventure_storage_set_array_after( $var_name, $after, $key, $value = '' ) {
		global $AVVENTURE_STORAGE;
		if ( ! isset( $AVVENTURE_STORAGE[ $var_name ] ) ) {
			$AVVENTURE_STORAGE[ $var_name ] = array();
		}
		if ( is_array( $key ) ) {
			avventure_array_insert_after( $AVVENTURE_STORAGE[ $var_name ], $after, $key );
		} else {
			avventure_array_insert_after( $AVVENTURE_STORAGE[ $var_name ], $after, array( $key => $value ) );
		}
	}
}

// Add array element before the key
if ( ! function_exists( 'avventure_storage_set_array_before' ) ) {
	function avventure_storage_set_array_before( $var_name, $before, $key, $value = '' ) {
		global $AVVENTURE_STORAGE;
		if ( ! isset( $AVVENTURE_STORAGE[ $var_name ] ) ) {
			$AVVENTURE_STORAGE[ $var_name ] = array();
		}
		if ( is_array( $key ) ) {
			avventure_array_insert_before( $AVVENTURE_STORAGE[ $var_name ], $before, $key );
		} else {
			avventure_array_insert_before( $AVVENTURE_STORAGE[ $var_name ], $before, array( $key => $value ) );
		}
	}
}

// Push element into array
if ( ! function_exists( 'avventure_storage_push_array' ) ) {
	function avventure_storage_push_array( $var_name, $key, $value ) {
		global $AVVENTURE_STORAGE;
		if ( ! isset( $AVVENTURE_STORAGE[ $var_name ] ) ) {
			$AVVENTURE_STORAGE[ $var_name ] = array();
		}
		if ( '' === $key ) {
			array_push( $AVVENTURE_STORAGE[ $var_name ], $value );
		} else {
			if ( ! isset( $AVVENTURE_STORAGE[ $var_name ][ $key ] ) ) {
				$AVVENTURE_STORAGE[ $var_name ][ $key ] = array();
			}
			array_push( $AVVENTURE_STORAGE[ $var_name ][ $key ], $value );
		}
	}
}

// Pop element from array
if ( ! function_exists( 'avventure_storage_pop_array' ) ) {
	function avventure_storage_pop_array( $var_name, $key = '', $defa = '' ) {
		global $AVVENTURE_STORAGE;
		$rez = $defa;
		if ( '' === $key ) {
			if ( isset( $AVVENTURE_STORAGE[ $var_name ] ) && is_array( $AVVENTURE_STORAGE[ $var_name ] ) && count( $AVVENTURE_STORAGE[ $var_name ] ) > 0 ) {
				$rez = array_pop( $AVVENTURE_STORAGE[ $var_name ] );
			}
		} else {
			if ( isset( $AVVENTURE_STORAGE[ $var_name ][ $key ] ) && is_array( $AVVENTURE_STORAGE[ $var_name ][ $key ] ) && count( $AVVENTURE_STORAGE[ $var_name ][ $key ] ) > 0 ) {
				$rez = array_pop( $AVVENTURE_STORAGE[ $var_name ][ $key ] );
			}
		}
		return $rez;
	}
}

// Inc/Dec array element with specified value
if ( ! function_exists( 'avventure_storage_inc_array' ) ) {
	function avventure_storage_inc_array( $var_name, $key, $value = 1 ) {
		global $AVVENTURE_STORAGE;
		if ( ! isset( $AVVENTURE_STORAGE[ $var_name ] ) ) {
			$AVVENTURE_STORAGE[ $var_name ] = array();
		}
		if ( empty( $AVVENTURE_STORAGE[ $var_name ][ $key ] ) ) {
			$AVVENTURE_STORAGE[ $var_name ][ $key ] = 0;
		}
		$AVVENTURE_STORAGE[ $var_name ][ $key ] += $value;
	}
}

// Concatenate array element with specified value
if ( ! function_exists( 'avventure_storage_concat_array' ) ) {
	function avventure_storage_concat_array( $var_name, $key, $value ) {
		global $AVVENTURE_STORAGE;
		if ( ! isset( $AVVENTURE_STORAGE[ $var_name ] ) ) {
			$AVVENTURE_STORAGE[ $var_name ] = array();
		}
		if ( empty( $AVVENTURE_STORAGE[ $var_name ][ $key ] ) ) {
			$AVVENTURE_STORAGE[ $var_name ][ $key ] = '';
		}
		$AVVENTURE_STORAGE[ $var_name ][ $key ] .= $value;
	}
}

// Call object's method
if ( ! function_exists( 'avventure_storage_call_obj_method' ) ) {
	function avventure_storage_call_obj_method( $var_name, $method, $param = null ) {
		global $AVVENTURE_STORAGE;
		if ( null === $param ) {
			return ! empty( $var_name ) && ! empty( $method ) && isset( $AVVENTURE_STORAGE[ $var_name ] ) ? $AVVENTURE_STORAGE[ $var_name ]->$method() : '';
		} else {
			return ! empty( $var_name ) && ! empty( $method ) && isset( $AVVENTURE_STORAGE[ $var_name ] ) ? $AVVENTURE_STORAGE[ $var_name ]->$method( $param ) : '';
		}
	}
}

// Get object's property
if ( ! function_exists( 'avventure_storage_get_obj_property' ) ) {
	function avventure_storage_get_obj_property( $var_name, $prop, $default = '' ) {
		global $AVVENTURE_STORAGE;
		return ! empty( $var_name ) && ! empty( $prop ) && isset( $AVVENTURE_STORAGE[ $var_name ]->$prop ) ? $AVVENTURE_STORAGE[ $var_name ]->$prop : $default;
	}
}
