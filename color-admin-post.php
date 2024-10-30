<?php

/*
Plugin Name: Color Admin Posts
Plugin URI: http://www.geekpress.fr/wordpress/extension/color-admin-posts/
Description: Change the background colors of the post/page within the admin based on the current status : Draft, Pending, Published, Future, Private.
Version: 1.0.3
Author: GeekPress
Author URI: http://www.geekpress.fr/

	Copyright 2011 Jonathan Buttigieg

	This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/


// Define contants
define( 'CAP_VERSION' , '1.0.2' );

class Color_Admin_Posts {
	private $options 	= array(); // Set $options in array
	private $settings 	= array(); // Set $setting in array
	private $textdomain	= 'color-admin-posts';

	function __construct() {
		// Add translations
		if ( function_exists( 'load_plugin_textdomain' ) ) {
			load_plugin_textdomain( $this->textdomain, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');
		}

		// Add menu page
		add_action( 'admin_menu', array( &$this, 'add_submenu' ) );

		// Settings API
		add_action( 'admin_init', array( &$this, 'settings_api_init' ) );

		// load the values recorded
		$this->options = get_option( '_color_admin_posts' );
		add_action( 'admin_print_styles-edit.php', array( &$this, 'load_color' ) );

		add_action( 'admin_head', array( &$this, 'load_farbtastic' ) );


		//tell wp what to do when plugin is activated
		if ( function_exists( 'register_activation_hook' ) ) {
			register_activation_hook( __FILE__ , array( &$this, 'activate' ) );
		}
	}


	/**
	 * method activate
	 *
	 * This function is called when plugin is activated.
	 *
	 * @since 1.0
	**/
	function activate() {
		$options = array(
				'color_draft' 		=> '#FFFF99',
				'color_pending' 	=> '#87C5D6',
		 		'color_published' 	=> '#',
		 		'color_future' 		=> '#CCFF99',
		 		'color_private' 	=> '#FFCC99',
		);

		if ( ! is_array( $this->options ) ) {
			update_option( '_color_admin_posts', $options );
		}
	}

	/**
	*  method load_update_notifications
	*
	* @since 1.0
	*/
	function load_color() {
		$options = $this->options;
		?>
		<style>
			.status-draft{background-color:<?php echo $options['color_draft'] ;?> !important;}
			.status-future{background-color:<?php echo $options['color_future'] ;?> !important;}
			.status-publish{background-color:<?php echo $options['color_published'] ;?> !important;}
			.status-pending{background-color:<?php echo $options['color_pending'] ;?> !important;}
			.status-private{background-color:<?php echo $options['color_private'] ;?> !important;}
		</style>
		<?php
	}


	/**
	 * method load_farbtastic
	 *
	 * Insert JS and CSS file for Farbtastic
	 *
	 * @since 1.0
	**/
	function load_farbtastic() {
		global $current_screen;

		if ( $current_screen->id == 'settings_page_color-admin-posts/color-admin-post' ) {
			wp_enqueue_style( 'farbtastic' );
  			wp_enqueue_script( 'farbtastic' );
		}
	}


	/*
	 * method get_settings
	 *
	 * @since 1.0
	*/
	function get_settings() {
		$this->settings['color_draft'] = array(
			'title'		=> __( 'Drafts Posts', $this->textdomain ),
		);

		$this->settings['color_pending'] = array(
			'section' 	=> 'general',
			'title'		=> __( 'Pendings Posts', $this->textdomain ),
		);

		$this->settings['color_published'] = array(
			'title'		=> __( 'Published Posts', $this->textdomain ),
		);

		$this->settings['color_future'] = array(
			'title'		=> __( 'Futures Posts', $this->textdomain ),
		);

		$this->settings['color_private'] = array(
			'title'		=> __( 'Privates Posts', $this->textdomain ),
		);

	}

	/*
	 * method create_setting
	 * $args : array
	 *
	 * @since 1.0
	*/
	function create_settings( $args = array() ) {
		extract( $args );

		$field_args = array(
			'id'        => $id,
			'label_for' => $id,
		);

		add_settings_field( $id, $title, array( $this, 'display_settings' ), __FILE__, 'general', $field_args );
	}


	/**
	 * method display_settings
	 *
	 * HTML output for text field
	 *
	 * @since 1.0
	 */
	public function display_settings( $args = array() ) {
		$id = $args['id'];

 		echo '<input class="regular-text" type="text" maxlength="7" id="' . $id . '" name="_color_admin_posts[' . $id . ']" value="' . esc_attr( $this->options[$id] ) . '" />
			  <br />
 			  <div id="farbtastic-' . $id . '" class="farbtastic"></div>';
	}

	/**
	 * method settings_api_init
	 *
	 * Register settings with the WP Settings API
	 *
	 * @since 1.0
	 */
	function settings_api_init() {
		register_setting('_color_admin_posts', '_color_admin_posts', array( &$this, 'validate_settings' ) );
		add_settings_section( 'general', '', array( &$this, 'general_section_callback' ), __FILE__ );

		// Get the configuration of fields
		$this->get_settings();

		// Generate fields
		foreach ( $this->settings as $id => $setting ) {
			$setting['id'] = $id;
			$this->create_settings( $setting );
		}
	}

	/**
	*  method general_section_callback
	*
	* @since 1.0
	*/
	function general_section_callback() {
		echo '<p>' . __( 'Leave "#" for the default color.', $this->textdomain ) . '</p>';
	}

	/**
	*  method validate_settings
	*
	* @since 1.0
	*/
	function validate_settings( $input ) {
		$input['color_draft'] 		= ( preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $input['color_draft']) ) ? $input['color_draft'] : '#';
		$input['color_pending'] 	= ( preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $input['color_pending']) ) ? $input['color_pending'] : '#';
		$input['color_published'] 	= ( preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $input['color_published']) ) ? $input['color_published'] : '#';
		$input['color_future'] 		= ( preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $input['color_future']) ) ? $input['color_future'] : '#';
		$input['color_private'] 	= ( preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $input['color_private']) ) ? $input['color_private'] : '#';

		return $input;
	}

	/**
	*  method add_submenu
	*
	* @since 1.0
	*/
	function add_submenu() {

		// Add submenu in menu "Settings"
		add_submenu_page( 'options-general.php', 'Color Admin Posts', 'Color Admin Posts', 'manage_options', __FILE__, array(&$this, 'display_page') );
	}

	/**
	*  method display_page
	*
	* @since 1.O
	*/
	function display_page() {
		?>
		<div class="wrap">
			<?php screen_icon(); ?>
			<h2>Color Admin Posts</h2>
			<form id="form-color-admin-posts" method="post" action="options.php">
			    <?php
			    	settings_fields('_color_admin_posts');
  					do_settings_sections(__FILE__);
					submit_button( __('Save Changes') );
				?>
			</form>
		</div>

		<script type="text/javascript">
			jQuery(function(){
				jQuery(document).ready(function() {
				    jQuery('.regular-text').each(function() {

				    	jQuery('#farbtastic-'+this.id).hide();
				    	jQuery('#farbtastic-'+this.id).farbtastic(this);
				    	jQuery(this).click(function(){jQuery('#farbtastic-'+this.id).fadeIn()});

				    	jQuery(this).keyup(function() {

				    		if( jQuery(this).val() == '' )
				    			jQuery(this).val('#');
				    	});

				    });

				    jQuery(document).mousedown(function() {
				        jQuery('.farbtastic').each(function() {
				            var display = jQuery('#'+this.id).css('display');
				            if ( display == 'block' )
				                jQuery('#'+this.id).fadeOut();
				        });
				    });

				  });
			});
		</script>
	<?php
	}
}

// Start this plugin once all other plugins are fully loaded
global $Color_Admin_Posts; $Color_Admin_Posts = new Color_Admin_Posts();