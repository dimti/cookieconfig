<?php

if (!interface_exists('iCookieConfig')) {
	interface iCookieConfig {
		/**
		 * @return
		 * @desc return parameter is empty for correct work with IDE helper
		 */
		public static function getInstance();
	}
}