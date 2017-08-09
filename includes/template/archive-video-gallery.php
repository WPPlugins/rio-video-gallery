<?php get_header();

$obj = get_post_type_object( 'video-gallery' );
$singular_name = $obj->labels->singular_name;

function pagination_bar() {
    global $wp_query;
 
    $total_pages = $wp_query->max_num_pages;
 
    if ($total_pages > 1){
        $current_page = max(1, get_query_var('paged'));
 
        echo paginate_links(array(
            'base' => get_pagenum_link(1) . '%_%',
            'format' => '/page/%#%',
            'current' => $current_page,
            'total' => $total_pages,
        ));
    }
}

function limit_text($string, $repl, $limit) {
  if(strlen($string) > $limit) 
  {
    return strip_tags(substr($string, 0, $limit) . $repl); 
  }
  else 
  {
    return strip_tags($string);
  }
}
 

?>
<?php
//get video_settings options in serialized format...
$data_results = get_option('video_gallery_settings');

$videog_main_title_res = $data_results['videog_main_title'];
$video_thumb_width = $data_results['video_thumb_width'];
$video_thumb_height = $data_results['video_thumb_height'];
if(empty($video_thumb_width) || empty($video_thumb_height))
{
$video_thumb_width = 300;
$video_thumb_height = 200;
}
// $video_count_gshort = $data_results['video_count'];
if(empty($data_results['video_count']))
{
$video_count = 5;
}

?>

<h1><?php echo $singular_name;?></h1>
<section id="rio-video-gallery-container-archive"> 
 <!-- //Single news item -->
 <?php
			 $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    $qargs = array(
	'posts_per_page' => $video_count,
	'post_type' => 'video-gallery',
	'paged' => $paged,
	'meta_key' => 'video_post_order',
	'orderby' => 'meta_value_num',
	'order' => 'asc'
);

		$the_query = new WP_Query( $qargs );
		if ( $the_query->have_posts() ) : while ( $the_query->have_posts() ) : $the_query->the_post();
			$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' );
			$video_provider = get_post_meta(get_the_ID(), 'video_provider', true );
			$video_id = get_post_meta(get_the_ID(), 'video_id', true );
			$video_link_target = get_post_meta(get_the_ID(), 'video_link_target', true );
			$provider_shortres = get_post_meta(get_the_ID(), 'video_provider', true);
			$postid=$post->ID;
			$data_results = get_option('video_gallery_settings'); //video gallery settings option
			$vposted_date_display_gshort = $data_results['vposted_date_display'];
			$vpost_order_gshort = $data_results['vpost_order'];
			$vpost_orderby_gshort = $data_results['vpost_orderby'];
			$video_layout_gshort = $data_results['video_layout'];
			$video_thumb_width_gshort = $data_results['video_thumb_width'];
			$video_link_target = $data_results['video_link_target'];
			$video_sthumb_width_res = $data_results['video_sthumb_width'];
			$video_sthumb_height_res = $data_results['video_sthumb_height'];
			if (empty($video_sthumb_width_res)) {
			$video_sthumb_width_res = 600;
			}
			$video_sthumb_height_res = $data_results['video_sthumb_height'];
			if (empty($video_sthumb_height_res)) {
			$video_sthumb_height_res = 400;
			}			if(empty($video_link_target))
			{
				$video_link_target='popup';
			}
			?>
 <article itemscope itemtype="http://schema.org/VideoObject">
  <figure> <a <?php if($video_link_target == 'popup'){?> class="poup_here" id="<?php echo $postid; ?>"<?php } else {?> href="<?php the_permalink(); ?>"<?php }?>>
   <?php if(!empty($video_provider) && $video_provider == 'youtube') {?>
   <img alt="<?php the_title();?>" width="<?php echo $video_thumb_width; ?>" height="<?php echo $video_thumb_height; ?>" src="http://img.youtube.com/vi/<?php echo $video_id; ?>/0.jpg" itemprop="thumbnail">
   <?php } else if(!empty($video_provider) && $video_provider == 'vimeo') {
	   
			 	$imgid = $video_id;			
			$thumb = getVimeoInfo_details($imgid);
			if(empty($thumb))
			{
				$thumb=plugins_url().'/rio-video-gallery/img/video-failed.png';
			}
	  ?>     
   <img alt="<?php the_title();?>" width="<?php echo $video_thumb_width; ?>" height="<?php echo $video_thumb_height; ?>" src="<?php echo $thumb; ?>" itemprop="thumbnail">
   <?php } else if(!empty($video_provider) && $video_provider == 'dailymotion') {
	  ?>
   <img alt="<?php the_title();?>" width="<?php echo $video_thumb_width; ?>" height="<?php echo $video_thumb_height; ?>" src="http://www.dailymotion.com/thumbnail/video/<?php echo $video_id;?>" itemprop="thumbnail">
   <?php }?>
   </a></figure>
  <section>
   <header>
    <h1><a href="<?php the_permalink();?>" itemprop="name">
     <?php $title = get_the_title(); echo substr($title,0,50);?>
     </a></h1>
    <p>
     <?php if(!empty($video_provider)) { ?>
     <span class="video_provider"><?php echo $video_provider; ?></span>
     <?php } ?>
     <?php if(!empty($vposted_date_display_gshort)) { ?>
     <span itemprop="playCount"><?php echo rio_video_getPostViews($post->ID);?> Views</span>
   
     <span itemprop="datePublished"><?php echo human_time_diff( get_the_time('U'), current_time('timestamp') ) . ' ago';?></span>
     <?php }?>
     </p>
   </header>
   <p itemprop="description">
    <?php $content = get_the_content(); echo limit_text($content, "...", 200); ?>
   </p>
  </section>
 </article>
 <div class="poup_window show_content" id="show_content<?php echo $postid; ?>"> 
             
                        <figure>
                        <?php if(!empty($provider_shortres) && $provider_shortres == 'youtube') {?>
                        <iframe width="<?php echo $video_sthumb_width_res;?>" height="<?php echo $video_sthumb_height_res;?>" src="//www.youtube.com/embed/<?php echo $video_id_shortres;?>" frameborder="0" allowfullscreen></iframe>
                        <?php } else if(!empty($provider_shortres) && $provider_shortres == 'vimeo') {?>
                        <iframe src="//player.vimeo.com/video/<?php echo $video_id_shortres;?>" width="<?php echo $video_sthumb_width_res;?>" height="<?php echo $video_sthumb_height_res;?>"></iframe>
                        <?php } else if(!empty($provider_shortres) && $provider_shortres == 'dailymotion') {?>
                        <iframe frameborder="0" width="<?php echo $video_sthumb_width_res;?>" height="<?php echo $video_sthumb_height_res;?>" src="http://www.dailymotion.com/embed/video/<?php echo $video_id_shortres;?>"></iframe>
                        <?php }?>
                        <figcaption>
                          <h4><?php echo wp_trim_words(get_the_title(),10,'...'); ?></h4>
                           <?php if(!empty($vposted_date_display_gshort)) { ?>
                        <span>Views <?php echo rio_video_getPostViews($post_ID);?></span>
                       
                        <span><?php echo human_time_diff( get_the_time('U'), current_time('timestamp') ) . ' ago';?></span>
                        <?php }?>
                        
                        </figcaption>
                        </figure>
        
       				 </div>
 <?php endwhile; else : echo 'No videos found'; endif; wp_reset_postdata();?>
</section>
<div class="clearFixer">&nbsp;</div>
<p class="pagination">
 <?php
	if(function_exists('wp_pagenavi')){ wp_pagenavi(); }
	else
	{
 pagination_bar(); 

	}

?>
</p>
<?php wp_reset_query(); ?>
<?php get_footer(); ?>
<script src="<?php echo plugins_url();?>/rio-video-gallery/js/video-gallery-script.js"></script>