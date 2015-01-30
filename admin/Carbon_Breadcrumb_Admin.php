<?php
/**
 * The main breadcrumb administration class.
 * 
 * Includes, wraps and manages the administration functionality.
 */
final class Carbon_Breadcrumb_Admin {

	/**
	 * Instance container.
	 *
	 * @static
	 *
	 * @var Carbon_Breadcrumbs
	 */
	static $instance = null;

	/**
	 * Constructor.
	 * Private so only the get_instance() can instantiate it.
	 *
	 * Creates the administration functionality wrapper.
	 *
	 * @access private
	 */
	private function __construct() {
		// include the plugin files
		$this->include_files();

		// initialize
		add_action('admin_init', array($this, 'admin_init'));
	}

	/**
	 * Retrieve or create the Carbon_Breadcrumbs instance.
	 *
	 * @static
	 * @access public
	 *
	 * @return Carbon_Breadcrumbs $instance
	 */
	public static function get_instance() {
		if (self::$instance === null) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Include the plugin files.
	 *
	 * @access public
	 */
	public function include_files() {

	}

	/**
	 * Initialize administrator.
	 *
	 * @access public
	 */
	public function admin_init() {

		// if admin interface should not be enabled, bail
		if ( !$this->is_enabled() ) {
			return;
		}

	}
	
	/**
	 * Whether the administration interface should be enabled.
	 *
	 * @access public
	 *
	 * @return bool $is_enabled True if the admin interface is enabled.
	 */
	public function is_enabled() {

		// enabled if this plugin is installed as a regular WordPress plugin
		$plugin_path = untrailingslashit(ABSPATH) . DIRECTORY_SEPARATOR . 'wp-content' . DIRECTORY_SEPARATOR . 'plugins';
		$current_dir = dirname(__FILE__);
		if ( strpos($current_dir, $plugin_path) !== false ) {
			return true;
		}

		// enabled if the CARBON_BREADCRUMB_ENABLE_ADMIN is defined as `true`
		if ( defined('CARBON_BREADCRUMB_ENABLE_ADMIN') && CARBON_BREADCRUMB_ENABLE_ADMIN ) {
			return true;
		}

		// disabled otherwise
		return false;
	}

	/**
	 * Private __clone() to prevent cloning the singleton instance.
	 *
	 * @access private
	 */
	private function __clone() {}

	/**
	 * Private __wakeup() to prevent singleton instance unserialization.
	 *
	 * @access private
	 */
	private function __wakeup() {}

}

// initialize the admin interface
Carbon_Breadcrumb_Admin::get_instance();