<div class="simple-business-data-social-media-bar">
  <ul class="simple-business-data-social-media-links">
    <?php
    $list = '';
    $no_square_icon = array( 'codepen', 'delicious', 'flickr', 'foursquare', 'instagram', 'jsfiddle', 'stack-exchange', 'stack-overflow', 'stumbleupon', 'yelp' );
    foreach ($sites as $site => $site_values)
    {
      if( !empty( $site_values['url'] ) )
      {
        $list .= '
        <li>
        <a rel="nofollow" class="simple-business-data-social-tooltip" title="" data-title="'.$site_values['title'].'" href="'.$site_values['url'].'">
          <span class="fa fa-'.$site;
        $list .= (in_array( $site, $no_square_icon )) ? '' : '-square';
        $list .= '"></span>
        </a>
        </li>
          ';
      }
    }
    echo $list;
    ?>
  </ul>
</div>
