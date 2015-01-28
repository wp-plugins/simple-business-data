<?php
/**
 * HTML content used to display the output of the simple busisness data class.
 * Will display the correct html according to the type attribute that is used when
 * calling this template.
 *
 * @param string $type The type of data to be displayed
 * @output string html of interpreted variables from simple business data class.
 */

$html = '
<div class="simple-business-data-block';
if( $show_icon === 'yes' )
  $html .= ' simple-business-data-'.$type;
else
  $html .= ' simple_business_data='.$type.'-no-icon';
$html .='">
  <span>';
if( $type === 'address' )
  $html .= nl2br( esc_attr( get_option( 'simple_business_data_address' ) ) );
elseif( $type === 'telephone' )
{
  $telephone_number = esc_attr( get_option( 'simple_business_data_telephone' ) );
  $stripped = preg_replace('/[^0-9+]/', '', $telephone_number);
  $html .= '<a href="tel:'.$stripped.'">'.$telephone_number.'</a>';
}
elseif( $type === 'email' )
{
  $sbd_email_display = esc_attr( get_option( 'simple_business_data_email_display' ) );
  $sbd_email = esc_attr( get_option( 'simple_business_data_email' ) );
  $html .= '<a href="mailto:'.$sbd_email.'">';
  $html .= ( !empty( $sbd_email_display ) ) ? $sbd_email_display : $sbd_email;
  $html .= '</a>';
}
else
  $html .= esc_attr( get_option( "simple_business_data_{$type}") );
$html .= '
  </span>
</div>';

print $html;