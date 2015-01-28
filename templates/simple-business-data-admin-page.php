<div class="wrap">
  <div class="simple-business-data-text-container">
  <!-- Options Title -->
  <h2 class="simple-business-data-title">Simple Business Data Information</h2>
  <p>Add your business information in the form below. Then you can simply use the Shortcode to add it to any page, post body, and/or text widget output. Please note, the shortcode will not be displayed in a post excerpt, only the actual post body (i.e. when viewing the post on a page by itself).</p>
  </div>

  <form action="options.php" method="POST" class="simple-business-data-form-admin">

    <?php
      // This prints out all the hidden setting fields
      settings_fields('simple_business_data_options');
      do_settings_sections('simple_business_data_options');
    ?>

    <table class="simple-business-data-table-admin">
      <thead>
        <tr>
          <th>Information Type</th><th>Business Information Value</th>
        </tr>
      </thead>
      <tfoot>
        <tr>

        </tr>
      </tfoot>
      <tbody>
        <tr><td>Business Address: <span class="fa fa-map-marker"></span> </td><td><textarea id="simple_business_data_address" name="simple_business_data_address" class="widefat" ><?php echo esc_textarea( get_option('simple_business_data_address') ); ?></textarea></td></tr>
        <tr><td>Shortcode: </td><td>[simple_business_data type="address"]</td></tr>
        <tr><td>Business telephone: <span class="fa fa-phone"></span> </td><td><input type="text" id="simple_business_data_telephone" name="simple_business_data_telephone" value="<?php echo sanitize_text_field( get_option('simple_business_data_telephone') ); ?>" class="widefat" /></td></tr>
        <tr><td>Shortcode: </td><td>[simple_business_data type="telephone"]</td></tr>
        <tr><td>Business Fax: <span class="fa fa-fax"></span> </td><td><input type="text" id="simple_business_data_fax" name="simple_business_data_fax" value="<?php echo sanitize_text_field( get_option('simple_business_data_fax') ); ?>" class="widefat" /></td></tr>
        <tr><td>Shortcode: </td><td>[simple_business_data type="fax"]</td></tr>
        <tr><td>&nbsp;</td><td>Any text added to the Business Email Name Display will be shown. If left blank the email address will be shown.</td></tr>
        <tr><td>Business Email Name Display: </td><td><input type="text" id="simple_business_data_email_display" name="simple_business_data_email_display" value="<?php echo sanitize_text_field( get_option('simple_business_data_email_display') ); ?>" class="widefat" /></td></tr>
        <tr><td>Business Email Contact: <span class="fa fa-envelope-o"></span> </td><td><input type="email" id="simple_business_data_email" name="simple_business_data_email" value="<?php echo sanitize_text_field( get_option('simple_business_data_email') ); ?>" class="widefat" /></td></tr>
        <tr><td>Shortcode: </td><td>[simple_business_data type="email"]</td></tr>
      </tbody>
    </table>

  <?php submit_button(); ?>

    <table class="simple-business-data-table-admin social">
      <thead>
        <tr>
          <th>Social Media Title</th><th>Add the full url for your Social Media page</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <th>Social Media Title</th><th>Add the full url for your Social Media page</th>
        </tr>
      </tfoot>
      <tbody>
        <tr><td>Shortcode: </td><td>[simple_business_data type="social"]</td></tr>

    <?php
      if(class_exists('SimpleBusinessDataInformation'))
        $simple_business_data = new SimpleBusinessDataInformation();

      // Get list of available social media from the database
      $sites = $simple_business_data->Get_Available_Social_Media_Sites();

      // Loop through each site to create admin table
      foreach( $sites as $site_key => $site_value )
      {
        $table_row = '<tr><td>' . $site_value . ': <span class="fa fa-';
        // some FontAwesome icons require '-square' to be appended to the selector. If required, add the name to the array:
        $table_row .= ( in_array( $site_key, array('vimeo') ) ) ? $site_key.'-square' : $site_key;
        $table_row .= '"></span></td><td>';
        $table_row .= '<input type="url" id="simple_business_data_options_' . $site_key . '" name="simple_business_data_options_' . $site_key . '" value="';
        $table_row .= esc_url( get_option('simple_business_data_options_' . $site_key) );
        $table_row .= '" class="widefat" /></td></tr>';
        echo $table_row;
      }
      //clear variables
      unset($sites); $site_key = $site_value = $table_row = null;
    ?>

      </tbody>
    </table>

    <?php submit_button(); ?>

  </form>
  <div class="simple-business-data-text-container">
    <h3>Tips</h3>
  <ol>
  <li>To put your City/State/Zip on a second line from your Street Address, or to add any secondary line(s), simply hit &lt;enter&gt; at the end of your line.</li>
  <li>Your phone number will be an html5 link (for mobile devices) and can be any format you like.</li>
  <li>In the section below add the absolute url (the full url including the http:// or https://) for your social media page that you want linked from the icon. It is advisable to simply copy and paste the url from your social media page. When hovering on the icon, the 'Social Media Title' is displayed.</li>
  </ol>
  <h3>Shortcode Usage</h3>
  <p>The Shortcode for this plugin is [simple_business_data] with a single required attribute: <strong>type</strong> and a second optional attribute: <strong>show_icon</strong>. The first attribute is the identifier for the icon that will be displayed. The second is an on/off switch to show the icon. If you wish to only show the text then you need to add "show_icon="no" to the shortcode when you use it. To make life easy, simply copy and paste the Shortcode you wish to use.</p>
    <p class="aligncenter">Thank you for using the Simple Business Data Information plugin.</p>
  </div>
</div>

