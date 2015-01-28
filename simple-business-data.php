<?php
defined('ABSPATH') or die("No script kiddies please!");

/**
  Plugin Name: Simple Business Data
  Plugin URI: http://dmbwebdesigns.com/wp/plugins?name=simple-business-data
  Description: Add your business data information in the backend, and then easily add it to your theme.
  Version: 1.0.1
  Author: DMBarber
  Author URI: http://dmbwebdesigns.com
  Text Domain:
  Domain Path:
  Network: false
  License: GPL2

  Copyright 2014  Dennis M. Barber  (email : dennis@dmbwebdesigns.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

 */

if ( !class_exists('SimpleBusinessDataInformation') )
{

  class SimpleBusinessDataInformation
  {
    /**
     * Start here
     */
    public function __construct()
    {
      /* Create Admin menu tab under Settings */
      add_action( 'admin_menu', array( $this, 'simple_business_data_menu' ) );
      /* Create options for admin page */
      add_action( 'admin_init', array( $this, 'simple_business_data_admin_init') );

      // Add shortcode support for widgets
      if( !has_filter( 'widget_text', 'do_shortcode' ) )
        add_filter('widget_text', 'do_shortcode');

      /* Create shortcode to retrieve and display data */
      add_shortcode( 'simple_business_data', array( $this, 'simple_business_data_shortcode' ) );
      add_shortcode( 'simple_business_data_social', array( $this, 'simple_business_data_social_shortcode' ) );

    }

    /**
     * Activate the plugin
     */
    public static function simple_business_data_activate()
    {
      // Check permissions
      if( !current_user_can( 'activate_plugins' ) )
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );

      $plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
      check_admin_referer( "activate-plugin_{$plugin}" );
    }

    /**
     * Deactivate the plugin
     */
    public static function simple_business_data_deactivate()
    {
      // Check permissions
      if( !current_user_can( 'activate_plugins' ) )
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );

      $plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
      check_admin_referer( "deactivate-plugin_{$plugin}" );
    }

    /**
     * Uninstall the plugin
     */
    protected function simple_business_data_uninstall()
    {
      if( !current_user_can( 'activate_plugins' ) )
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );

      check_admin_referer('bulk-plugins');

      // Render the settings template
      include( sprintf( "%s/uninstall.php", dirname(__FILE__) ) );

    }

    /**
     *  Add the admin settings
     */
    public function simple_business_data_admin_init()
    {
      /* Register new settings with the WP Settings API register_setting( $option_group, $option_name, $sanitize_callback ); */
      register_setting( 'simple_business_data_options', 'simple_business_data_address', array(  $this, 'sbd_sanitize_address' ) );
      register_setting( 'simple_business_data_options', 'simple_business_data_telephone', array(  $this, 'sbd_sanitize_telefax' ) );
      register_setting( 'simple_business_data_options', 'simple_business_data_fax', array(  $this, 'sbd_sanitize_telefax' ) );
      register_setting( 'simple_business_data_options', 'simple_business_data_email_display', array(  $this, 'sbd_sanitize_email' ) );
      register_setting( 'simple_business_data_options', 'simple_business_data_email', array(  $this, 'sbd_sanitize_email' ) );
      foreach( $this->Get_Social_Media_Site_Names() as $name )
      {
        $option_name = 'simple_business_data_options_' . $name;
        register_setting( 'simple_business_data_options', $option_name, array( $this, 'sbd_sanitize_url' ) );
      }
    }

    /**
     * Sanitize simple business data information inputs
     */

    public function sbd_sanitize_address( $input )
    {
      // Escape text from textarea
      if( !empty( $input['simple_business_data_address'] ) )
        $input['simple_business_data_address'] = esc_textarea( trim( $input['simple_business_data_address'] ) );

      return $input;
    }

    public function sbd_sanitize_telefax( $input )
    {
      // Sanitize telephone and fax numbers
      foreach( $input as $key => $value )
      {
        $input[$key] = sanitize_text_field( $value );
      }
      return $input;
    }

    public function sbd_sanitize_email( $input )
    {
      // Sanitize user email display
      if( !empty( $input['simple_business_data_email_display'] ) )
        $input['simple_business_data_email_display'] = sanitize_text_field( $input['simple_business_data_email_display'] );

      // Sanitize user email
      if( !empty( $input['simple_business_data_email'] ) )
        $input['simple_business_data_email'] = sanitize_email( $input['simple_business_data_email'] );

      return $input;
    }

    public function sbd_sanitize_url( $input )
    {
      // Get all available social media sites and filter urls
      foreach( $this->Get_Social_Media_Site_Names() as $name )
      {
        $option_name = 'simple_business_data_options_' . $name;
        if( !empty( $input[$option_name] ) )
        {
          $var = $input[$option_name];
          $input[$option_name] = filter_var($var, FILTER_SANITIZE_URL, array('flags'=>FILTER_NULL_ON_FAILURE));
        }
      }

      return $input;
    }

    /**
     * Clean Social Media URL for Simple Business Data
     */
    public function sbd_clean_url( $input )
    {
      // Get all available social media sites and filter urls
      foreach( $input as $value )
      {
        if( !empty( $value ) )
        {
          $var = trim( $value );
          return filter_var($var, FILTER_SANITIZE_URL, array('flags'=>FILTER_NULL_ON_FAILURE));
        }
      }
    }

    /**
     * Create admin menu
     */
    public function simple_business_data_menu()
    {
      // The link to the settings page will be under "Settings"
      add_options_page(
        'Simple Business Data Options',
        'Simple Business Data Information',
        'manage_options',
        'simple-business-data',
        array( $this, 'simple_business_data_admin_page' )
      );
    }

    /**
     * Display of  options page
     */
    public function simple_business_data_admin_page()
    {

      if( !current_user_can( 'manage_options' ) )
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );

      // Render the settings template
      include( sprintf( "%s/templates/simple-business-data-admin-page.php", dirname(__FILE__) ) );

    }

    /**
     * Display business data information using shortcode option
     */
    public function simple_business_data_shortcode( $atts )
    {
      // WP Shortcode API will parse attributes
      $a = shortcode_atts( array(
        'type' => '',
        'show_icon' => 'yes',
      ), $atts );

      // Display simple business data information according to shortcode attribute used
      if( strtolower( $a['type'] ) === 'social' )
        return $this->Display_social_icons( $a['type'] );
      else
        return $this->simple_business_data_information_display( $a['type'], $a['show_icon'] );
    }

    /**
     * Display business data using shortcode option "type"
     * @return html template for shortcode type
     */
    public function simple_business_data_information_display( $type, $show_icon = 'yes' )
    {
      /**
       * Render the settings template
       * $type is used in the template
       */
      ob_start();
      include( sprintf( "%s/templates/simple-business-data-template.php", dirname(__FILE__) ) );
      $return = ob_get_contents();
      ob_end_clean();

      return $return;
    }

    /**
     * Static method used to show the data inside user template
     */
    public static function SBD_Info( $type, $show_icon = 'yes' )
    {
      return self::simple_business_data_information_display($type, $show_icon = 'yes');
    }

    /**
     * Get a list of names only of all available social media sites for this plugin
     * @return array
     */
    public function Get_Social_Media_Site_Names()
    {
      // List of sites available with this plugin
      $sites = $this->Social_Media_Sites_Array();

      // Make an array of the social media site names from the $sites array
      $site_names = array_keys( $sites );

      return $site_names;

    }

    /**
     * Get a list of all the user registered social media site urls
     * @return array
     */
    public function Get_Social_Media_Site_Urls()
    {
      $sites = $this->Social_Media_Sites_Array();

      $site_names = $this->Get_Social_Media_Site_Names();

      // Loop through the array and get the url if it is not empty
      foreach( $site_names as $site_name )
      {
        $url = get_option( 'simple_business_data_options_' . $site_name );

        if( !empty($url) )
          $sites[$site_name]['url'] = $url;
      }

      return $sites;
    }

    /**
     * Get the social media sites that are available for this plugin
     */
    public function Get_Available_Social_Media_Sites()
    {
      // Get list of all available sites
      $sites = $this->Social_Media_Sites_Array();

      // Instantiate return array
      $site_titles = array();

      /**
       * Loop through list of all available sites and set the social media key and title
       * $key is the name of the site and $value is an array of 'title' and 'url'
       */
      foreach($sites as $key => $value)
        $site_titles[$key] = $value['title'];

      return $site_titles;
    }

    /**
     * Creates an array of the Social Media sites available to the user for this plugin
     *
     * @TODO: $add_new_sites is a provision to add other sites to the array by the user. This
     * is not available to the general public yet. Plan to use this in a following update.
     *
     * @param array $add_new_sites
     * @return array of social media sites for user list in admin table and display output on template
     */
    public function Social_Media_Sites_Array( $add_new_sites = array() )
    {
      $sites = array (
        'codepen'        => array( 'title' => 'Checkout our Codepen', 'url' => '' ),
        'facebook'       => array( 'title' => 'Find us on Facebook', 'url' => '' ),
        'flickr'         => array( 'title' => 'See our Flickr', 'url' => '' ),
        'foursquare'     => array( 'title' => 'Friend us on Foursquare', 'url' => '' ),
        'github'         => array( 'title' => 'Our Github repository', 'url' => '' ),
        'google-plus'    => array( 'title' => 'Connect with us on Google+', 'url' => '' ),
        'jsfiddle'       => array( 'title' => 'Checkout our JSFiddle', 'url' => '' ),
        'instagram'      => array( 'title' => 'See us on Instagram', 'url' => '' ),
        'linkedin'       => array( 'title' => 'Connect with us on LinkedIn', 'url' => '' ),
        'pinterest'      => array( 'title' => 'Pin us at Pinterest', 'url' => '' ),
        'rss'            => array( 'title' => 'Follow our RSS Feed', 'url' => '' ),
        'stack-exchange' => array( 'title' => 'Ask us on Stack Exchange', 'url' => '' ),
        'stack-overflow' => array( 'title' => 'Ask us on Stack Overflow', 'url' => '' ),
        'stumbleupon'    => array( 'title' => 'Discover us at StumbleUpon', 'url' => '' ),
        'tumblr'         => array( 'title' => 'Join us on Tumblr', 'url' => '' ),
        'twitter'        => array( 'title' => 'Follow us on Twitter', 'url' => '' ),
        'vimeo'          => array( 'title' => 'Share with us on Vimeo', 'url' => '' ),
        'yelp'           => array( 'title' => 'Find us on Yelp!', 'url' => '' ),
        'youtube'        => array( 'title' => 'Watch us on YouTube', 'url' => '' ),
      );

      if( !empty( $add_new_sites ) )
        $sites = array_merge( $sites, $add_new_sites );

      return $sites;

    }

    /**
     * Outputs the html container for the social media icon bar and corresponding links
     * @param string $type
     * @return html output
     */
    public function Display_social_icons( $type )
    {
      if( $type === 'social' )
      {
        // The $sites variable is used as part of the template
        $sites = $this->Get_Social_Media_Site_Urls();
        ob_start();
        include( sprintf( "%s/templates/simple-business-data-social-template.php", dirname(__FILE__) ) );
        $return = ob_get_contents();
        ob_end_clean();
      }
      return $return;
    }

    /**
     * Get the plugin version number
     * @return string
     */
    public function plugin_get_version()
    {
      $plugin_data = get_plugin_data( __FILE__ );
      $plugin_version = $plugin_data['Version'];
      return $plugin_version;
    }
  }
}


if (class_exists('SimpleBusinessDataInformation') )
{
  // Instantiation and uninstallation hooks
  register_activation_hook(__FILE__, array('SimpleBusinessDataInformation', 'simple_business_data_activate') );
  register_deactivation_hook(__FILE__, array('SimpleBusinessDataInformation', 'simple_business_data_deactivate') );
  register_uninstall_hook(__FILE__, array('SimpleBusinessDataInformation', 'simple_business_data_uninstall') );

  // Instantiate the plugin class
  $simple_business_data = new SimpleBusinessDataInformation();
}

// Add a link to the settings page from the plugin page
if( isset( $simple_business_data ) )
{
  // Add the settings link to the plugins page
  function simple_business_data_settings_link( $links )
  {
    $settings_link = '<a href="options-general.php?page=simple-business-data">Settings</a>';
    array_unshift( $links, $settings_link );
    return $links;
  }

  $plugin = plugin_basename(__FILE__);
  add_filter( "plugin_action_links_$plugin", 'simple_business_data_settings_link' );

  // Add Font Awesome to the theme using CDN
  add_action( 'wp_enqueue_scripts', 'simple_business_data_enqueue_styles' );
  function simple_business_data_enqueue_styles()
  {
    // Add Font Awesome to the theme using CDN
    wp_enqueue_style( 'font-awesome-stylesheet', '//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css', array(), '4.2.0', 'all');

    // Regiser custom styles for Simple Business Data
    wp_register_style( 'simple-business-data-stylesheet', plugins_url( '/css/simple-business-data-style.css', __FILE__ ), array(), '1.0.0', 'all' );

    // Add style sheet to theme
    wp_enqueue_style( 'simple-business-data-stylesheet' );

  }

  // Add Font Awesome to the theme using CDN
  add_action( 'admin_enqueue_scripts', 'simple_business_data_admin_enqueue_style' );
  function simple_business_data_admin_enqueue_style()
  {
    // Add Font Awesome to the theme using CDN
    wp_enqueue_style( 'font-awesome-stylesheet', '//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css', array(), '4.2.0', 'all');

    // Regiser custom styles for Simple Business Data
    wp_register_style( 'simple-business-data-admin-stylesheet', plugins_url( '/css/simple-business-data-admin-style.css', __FILE__ ), array(), '1.0.0', 'all' );

    // Add style sheet to theme
    wp_enqueue_style( 'simple-business-data-admin-stylesheet' );

  }

}
