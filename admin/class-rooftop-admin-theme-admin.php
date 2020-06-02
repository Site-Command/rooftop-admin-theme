<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://errorstudio.co.uk
 * @since      1.0.0
 *
 * @package    Rooftop_Admin_Theme
 * @subpackage Rooftop_Admin_Theme/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Rooftop_Admin_Theme
 * @subpackage Rooftop_Admin_Theme/admin
 * @author     Error <info@errorstudio.co.uk>
 */
class Rooftop_Admin_Theme_Admin {

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
		 * defined in Rooftop_Admin_Theme_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Rooftop_Admin_Theme_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/rooftop-admin-theme-admin.css', array(), $this->version, 'all' );

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
		 * defined in Rooftop_Admin_Theme_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Rooftop_Admin_Theme_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/rooftop-admin-theme-admin.js', array( 'jquery' ), $this->version, false );

	}

    public function add_rooftop_mimetypes($mimes) {
        $mimes['svg'] = 'image/svg+xml';
        return $mimes;
    }

    public function remove_kses_filters() {
        if( current_user_can( 'unfiltered_html' ) ) {
            kses_remove_filters();
        }
    }

    public function remove_multisite_filters( $caps, $cap, $user_id, $args ) {
        if ( $cap == 'unfiltered_html' ) {
            unset( $caps );
            $caps[] = $cap;
        }

        return $caps;
    }

    /**
     * Remove menu items from the Wordpress admin sidebar
     *
     * called in admin_menu
     */
    public function remove_admin_menus() {
        global $submenu, $menu;

        $find_menu = function($slug) use($menu) {
            foreach($menu as $key => $menu_item) {
                if($menu_item[2] == $slug) {
                    return array("id" => $key, "menu_item" => $menu_item);
                }
            }

            return false;
        };

        $find_child_menu = function($parent_slug, $child_slug) use($submenu) {
            $submenu_item = $submenu[$parent_slug];
            foreach($submenu_item as $key => $child) {
                if($child[2] === $child_slug) {
                    return array("id" => $key, "menu_item" => $child);
                }
            }

            return false;
        };

        // remove top-level menu items by setting their required capability to manage_network (only network admins
        // can access if they know they url) and removing them from the menu renderer
        $remove_menus = array("edit-comments.php");
        foreach($remove_menus as $menu_to_remove){
            $comments_menu = $find_menu("edit-comments.php");
            if($comments_menu) {
                $comments_menu[1] = "manage_network";
                remove_menu_page($menu_to_remove);
            }
        }

        // as above, but just for child menu items
        $remove_submenus = array(
            "options-general.php" => array("options-reading.php", "options-discussion.php"),
            "themes.php" => array("customize.php", "customize.php?return=%2Fwp-admin%2F")
        );

        foreach($remove_submenus as $submenu_parent => $submenus) {
            foreach($submenus as $submenu_to_remove) {
                $child_submenu = $find_child_menu($submenu_parent, $submenu_to_remove);

                if($child_submenu) {
                    $child_submenu["menu_item"][1] = "manage_network";

                    $submenu[$submenu_parent][$child_submenu["id"]] = $child_submenu["menu_item"];
                    remove_submenu_page($submenu_parent, $submenu_to_remove); // also remove the menu item for network admins
                }

            }
        }

        // remove certain menus based on a required capability
        $menu_capabilities = array(
            "tools.php" => array("capability" => "manage_network")
        );

        foreach($menu_capabilities as $submenu_parent => $submenu_options) {
            if(! current_user_can($submenu_options['capability'])) {
                remove_menu_page( $submenu_parent );
                $submenu_item = $submenu[$submenu_parent];
                foreach($submenu_item as $submenu_item_id => $submenu_items_child) {
                    $submenu_items_child[1] = $submenu_options['capability'];
                    $submenu_item[$submenu_item_id] = $submenu_items_child;
                }

                $submenu[$submenu_parent] = $submenu_item;
            }
        }
    }

    /**
     * When rendering the user-edit form, remove the API specific user roles from the roles that are available in the dropdown.
     */
    public function remove_api_roles_if_rest_request($roles){
        unset($roles['api-preview']);
        unset($roles['api-read-only']);
        unset($roles['api-read-write']);

        return $roles;
    }

    public function remove_rooftop_admin_footer_text($footer_text) {
        return "";
    }
    public function remove_rooftop_admin_footer_text_test($version_text) {
        return "";
    }

    public function remove_admin_metaboxes() {
        remove_meta_box( 'dashboard_primary', 'dashboard', 'normal' ); // wordpress news
    }

    public function remove_show_screen_tab($show) {
        return !$show;
    }

    public function configure_contributor_role() {
        $contributor = get_role( 'contributor' );
        $contributor->add_cap('edit_pages' );
    }

	public function custom_postmeta_form_keys( $string, $post ) {
		global $wpdb;

		$limit = apply_filters( 'postmeta_form_limit', 30 );

		$sql = "SELECT DISTINCT meta_key
			FROM $wpdb->postmeta
			WHERE meta_key NOT BETWEEN '_' AND '_z'
			HAVING meta_key NOT LIKE %s
			ORDER BY meta_key";

		$keys = $wpdb->get_col( $wpdb->prepare( $sql, $wpdb->esc_like( '_' ) . '%' ) );
		return array_slice( $keys, 0, $limit, true );
	}
}
