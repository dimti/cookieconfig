<?php
/**
 * @desc IDE helper
 */
if (!interface_exists('iCookieConfig')) {
	interface iCookieConfig {
		/**
		 * @return
		 */
		public static function getInstance();
	}
}