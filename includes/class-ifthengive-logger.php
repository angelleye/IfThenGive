<?php

/**
 * Allows log files to be written to for debugging purposes.
 *
 * @class       AngellEYE_IfThenGive_Logger
 * @version	1.0.0
 * @package     IfThenGive
 * @subpackage  IfThenGive/includes
 * @category	Class
 * @author      Angell EYE <service@angelleye.com>
 */
class AngellEYE_IfThenGive_Logger {

	/**
	 * @var array Stores open file _handles.
	 * @access private
	 */
	private $_handles;

	/**
	 * Constructor for the logger.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		$this->_handles = array();
	}

	/**
	 * Destructor.
	 *
	 * @access public
	 * @return void
	 */
	public function __destruct() {
		foreach ($this->_handles as $handle) {
			@fclose(escapeshellarg($handle));
		}
	}

	/**
	 * Open log file for writing.
	 *
	 * @access private
	 * @param mixed $handle
	 * @return bool success
	 */
	private function open($handle, $path) {
		if (isset($this->_handles[$handle])) {
			return true;
		}

		if ($path == 'connect_to_paypal') {
			if ($this->_handles[$handle] = @fopen($this->ifthengive_for_wordpress_get_connect_to_paypal_log_file_path($handle), 'a')) {
				return true;
			}
		}

		if ($path == 'transactions') {
			if ($this->_handles[$handle] = @fopen($this->ifthengive_for_wordpress_get_transactions_log_file_path($handle), 'a')) {
				return true;
			}
		}

		if ($path == 'express_checkout') {
			if ($this->_handles[$handle] = @fopen($this->ifthengive_for_wordpress_get_express_checkout_log_file_path($handle), 'a')) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Add a log entry to chosen file.
	 *
	 * @access public
	 * @param mixed $handle
	 * @param mixed $message
	 * @return void
	 */
	public function add($handle, $message, $path) {
		if ($this->open($handle, $path) && is_resource($this->_handles[$handle])) {
			$time = date_i18n('m-d-Y @ H:i:s -'); // Grab Time
			@fwrite($this->_handles[$handle], $time . " " . $message . "\n");
		}
	}

	/**
	 * Clear entries from chosen file.
	 *
	 * @access public
	 * @param mixed $handle
	 * @return void
	 */
	public function clear($handle) {
		if ($this->open($handle) && is_resource($this->_handles[$handle])) {
			@ftruncate($this->_handles[$handle], 0);
		}
	}

	/**
	 * Get file path
	 *
	 * @access public
	 * @param mixed $handle
	 * @return void
	 */
	public function ifthengive_for_wordpress_get_log_file_path($handle) {
		return trailingslashit(ITG_LOG_DIR) . $handle . '-' . sanitize_file_name(wp_hash($handle)) . '.log';
	}

	/**
	 * Get file path of connect to paypal log
	 *
	 * @access public
	 * @param mixed $handle
	 * @return void
	 */
	public function ifthengive_for_wordpress_get_connect_to_paypal_log_file_path($handle) {
		return trailingslashit(ITG_LOG_DIR) . '/connect_to_paypal/' . $handle . '-' . sanitize_file_name(wp_hash($handle)) . '.log';
	}

	/**
	 * Get file path transaction
	 *
	 * @access public
	 * @param mixed $handle
	 * @return void
	 */
	public function ifthengive_for_wordpress_get_transactions_log_file_path($handle) {
		return trailingslashit(ITG_LOG_DIR) . '/transactions/' . $handle . '-' . sanitize_file_name(wp_hash($handle)) . '.log';
	}

	/**
	 * Get file path Express Checkout
	 *
	 * @access public
	 * @param mixed $handle
	 * @return void
	 */
	public function ifthengive_for_wordpress_get_express_checkout_log_file_path($handle) {
		return trailingslashit(ITG_LOG_DIR) . '/express_checkout/' . $handle . '-' . sanitize_file_name(wp_hash($handle)) . '.log';
	}

}
