<?php

// if uninstall not called from WordPress exit
if( !defined( 'WP_UNINSTALL_PLUGIN' ) )
  exit();

// Attempt to remove all simple_business_data from the database

function Remove_Simple_Business_Data()
{
  global $wpdb;

  // Array of all option names added to the database
  $table = $wpdb->prefix."options";
  $sbd_options = $wpdb->get_results(
    "
    SELECT
      option_name
    FROM
      $table
    WHERE
      option_name LIKE 'simple_business_data%'
    ",
    ARRAY_N
  );

  // Loop through the options and delete from the database
  if( !empty( $sbd_options ) )
  {
    foreach( $sbd_options as $sbd_option )
      delete_option( $sbd_option );
  }
}

//Execute function
Remove_Simple_Business_Data();
