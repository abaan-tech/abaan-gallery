<?php
/*
Plugin Name: Abaan WP Galley
Plugin URI: http://baaan.info/abaan-gallery/
Description: WP Gallery to Fancybox Thumbnail Gallery.
Version: 1.0
Author: Abaan Outsourcing
Author URI: http://abaanoutsourcing.com
*/
add_image_size( 'featured-gallery', 600, 450, array( 'top', 'bottom' ) );
add_filter('use_default_gallery_style','__return_false');
remove_shortcode('gallery');
add_shortcode('gallery', 'abaan_gallery');

function abaan_gallery($attr) {
  $post = get_post();
  static $instance = 0;
  $instance++;
  if(!empty($attr['ids'])){
    if(empty($attr['orderby'])){ $attr['orderby'] = 'post__in'; }
    $attr['include'] = $attr['ids'];
  }
  $output = '';
  if(isset($attr['orderby'])){
    $attr['orderby'] = sanitize_sql_orderby($attr['orderby']);
    if(!$attr['orderby']) unset($attr['orderby']);
  }
  extract(shortcode_atts(array(
    'order'      => 'ASC',
    'orderby'    => 'menu_order ID',
    'id'         => $post ? $post->ID : 0,
    'icontag'    => 'figure',
    'captiontag' => 'p',
    'columns'    => 3,
    'size'       => 'thumbnail',
    'include'    => '',
    'exclude'    => '',
    'link'       => '',
    'class'      => ''
  ), $attr, 'gallery'));
  $id = intval($id);
  if('RAND' == $order) $orderby = 'none';
  if(!empty($include)){
    $_attachments = get_posts(array(
      'include' => $include,
      'post_status' => 'inherit',
      'post_type' => 'attachment',
      'post_mime_type' => 'image',
      'order' => $order,
      'orderby' => $orderby
    ));
    $attachments = array();
    foreach($_attachments as $key => $val){
      $attachments[$val->ID] = $_attachments[$key];
    }
  } elseif (!empty($exclude)){
    $attachments = get_children(array(
      'post_parent' => $id,
      'exclude' => $exclude,
      'post_status' => 'inherit',
      'post_type' => 'attachment',
      'post_mime_type' => 'image',
      'order' => $order,
      'orderby' => $orderby
    ));
  } else {
    $attachments = get_children(array(
      'post_parent' => $id,
      'post_status' => 'inherit',
      'post_type' => 'attachment',
      'post_mime_type' => 'image',
      'order' => $order,
      'orderby' => $orderby
    ));
  }
  if(empty($attachments)) return '';
  $selector = "gallery-{$instance}";
  $ccount=0;
	foreach ( $attachments as $id => $attachment ) {
		$img_url = wp_get_attachment_image_src($id,'featured-gallery',false);
		$output = '<div class="gallery_featured_img"><a id="'.$selector.'" href="javascript:;"><img src="'.$img_url[0].'" alt="'. wptexturize($attachment->post_excerpt) .'" /></a>
		<h4 class="gallery-caption">'. wptexturize($attachment->post_excerpt) .'</h4></div>';
		$ccount++;
		if($ccount>0) {break;}
	}
  $output2 = '<script type="text/javascript">
		jQuery(document).ready(function() {
			jQuery("#'.$selector.'").click(function() {
				jQuery.fancybox.open([';
  foreach ( $attachments as $id => $attachment ) {
	$img_url = wp_get_attachment_image_src($id,'full',false);
	$output2 .= "{ href : '".$img_url[0]."',title : '". wptexturize($attachment->post_excerpt) ."'},";
  }
  $output2 .= '], {padding: 1, helpers : {thumbs : {width: 75,height: 50}} }); }); }); </script>';
  return $output.$output2;
}

function abaan_gallery_scripts() {
    wp_enqueue_style( 'jquery.fancybox', plugins_url( 'assets/jquery.fancybox.css', __FILE__ ) );
	wp_enqueue_style( 'jquery.fancybox-thumbs', plugins_url( 'assets/helpers/jquery.fancybox-thumbs.css', __FILE__ ) );
    wp_enqueue_script( 'jquery.fancybox', plugins_url( 'assets/jquery.fancybox.js', __FILE__ ), array(), '1.0.0', true );
	wp_enqueue_script( 'jquery.fancybox-thumbs', plugins_url( 'assets/helpers/jquery.fancybox-thumbs.js', __FILE__ ), array(), '1.0.0', true );
}
add_action( 'wp_enqueue_scripts', 'abaan_gallery_scripts' );



function video_script() {?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery(".various").fancybox({
		maxWidth	: 800,
		maxHeight	: 600,
		fitToView	: false,
		width		: '100%',
        height		: '70%',
		autoSize	: true,
		closeClick	: true,
		openEffect	: 'none',
		closeEffect	: 'none'
	});
});
</script>
<?php }
add_action('wp_footer', 'video_script');