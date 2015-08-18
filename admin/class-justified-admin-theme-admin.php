<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://errorstudio.co.uk
 * @since      1.0.0
 *
 * @package    Justified_Admin_Theme
 * @subpackage Justified_Admin_Theme/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Justified_Admin_Theme
 * @subpackage Justified_Admin_Theme/admin
 * @author     Error <info@errorstudio.co.uk>
 */
class Justified_Admin_Theme_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Justified_Admin_Theme_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Justified_Admin_Theme_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/justified-admin-theme-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Justified_Admin_Theme_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Justified_Admin_Theme_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/justified-admin-theme-admin.js', array( 'jquery' ), $this->version, false );

	}


    /**
     * limit the available roles
     *
     * called in admin_init
     */
    public function remove_user_roles() {
        remove_role("subscriber");
        remove_role("author");
    }

    /**
     * Remove menu items from the Wordpress admin sidebar
     *
     * called in admin_menu
     */
    public function remove_admin_menus() {
        $remove_menus = array("edit-comments.php", "themes.php", "tools.php", "options-general.php");
        foreach($remove_menus as $menu_to_remove){
            remove_menu_page($menu_to_remove);
        }
    }

    /**
     * Re-order the menu - move 'Media' below the other content types
     *
     * called in admin_menu
     */
    public function reorder_admin_menu() {
        global $menu;

        if($menu[10] && $menu[10][0]=="Media" && !array_key_exists(50, $menu)){
            $m = $menu[10];
            unset($menu[10]);
            $menu[50] = $m;
        }
    }
}
