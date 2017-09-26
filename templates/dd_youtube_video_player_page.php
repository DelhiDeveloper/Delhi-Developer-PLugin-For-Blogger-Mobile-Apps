<?php
/*
	* Template Name: Delhi Developer Youtube Video Player
	*/



	
	
	
	
$videos = false;
$youtube_channel_id = '';
$dd_mobile_app_youtube_channel_settings = json_decode(get_option('dd_mobile_app_youtube_channel_settings'));
if( 	$dd_mobile_app_youtube_channel_settings
	&&	$dd_mobile_app_youtube_channel_settings->enable_youtube_channel == 'Yes' 
) {
	
	$videos = json_decode( get_option( 'dd_mobile_app_youtube_videos' ) );
	
	$first_video = reset($videos);

	$current_video = $first_video;
	
	$youtube_channel_id = $dd_mobile_app_youtube_channel_settings->youtube_channel_id;

}
	
	


	
	
	
//echo json_encode($videos);exit;
	


	
	
	


/*********************************************************************************************************************
************************************ /Fetching Data & Handling Get & Post ********************************************
*********************************************************************************************************************/








get_header();

$is_page_builder_used = et_pb_is_pagebuilder_used( get_the_ID() );

?>

<div id="main-content">

<?php if ( ! $is_page_builder_used ) : ?>

	<div class="container">
		<div id="content-area" class="clearfix">
			<div id="left-area">

<?php endif; ?>

			<?php while ( have_posts() ) : the_post(); ?>

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

				<?php if ( ! $is_page_builder_used ) : ?>

					<h1 class="entry-title main_title"><?php the_title(); ?></h1>
				<?php
					$thumb = '';

					$width = (int) apply_filters( 'et_pb_index_blog_image_width', 1080 );

					$height = (int) apply_filters( 'et_pb_index_blog_image_height', 675 );
					$classtext = 'et_featured_image';
					$titletext = get_the_title();
					$thumbnail = get_thumbnail( $width, $height, $classtext, $titletext, $titletext, false, 'Blogimage' );
					$thumb = $thumbnail["thumb"];

					if ( 'on' === et_get_option( 'divi_page_thumbnails', 'false' ) && '' !== $thumb )
						print_thumbnail( $thumb, $thumbnail["use_timthumb"], $titletext, $width, $height );
				?>

				<?php endif; ?>

					<div class="entry-content">
					<?php
						the_content();

						if ( ! $is_page_builder_used )
							wp_link_pages( array( 'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'Divi' ), 'after' => '</div>' ) );
					?>
					</div> <!-- .entry-content -->

				<?php
					if ( ! $is_page_builder_used && comments_open() && 'on' === et_get_option( 'divi_show_pagescomments', 'false' ) ) comments_template( '', true );
				?>

				</article> <!-- .et_pb_post -->

			<?php endwhile; ?>






<!--
**********************************************************************************************************************
***************************************************** My Layout ******************************************************
**********************************************************************************************************************
-->
	
<style>
	
	
	.dis_in {
		display: inline-block;
	}
	
	@media( min-width: 971px ) {
		.col-md-4 {
			display: inline-block;
			float: left;
			width: 33.33%;
		}		
		.col-md-8 {
			display: inline-block;
			float: left;
			width: 66.66%;
		}		
	}
	
	@media( max-width: 970px ) {
		.col-md-4 ,
		.col-md-8 {
			display: block;
			float: none;
			width: 100%;
		}
	}
	
	
	.video-container {
		position:relative;
		padding-bottom:56.25%;
		/* padding-top:30px; */
		height:0;
		overflow:hidden;
	}
	
	.video-container iframe, .video-container object, .video-container embed {
		position:absolute;
		top:0;
		left:0;
		width:100%;
		height:100%;
	}
	
	.videos_list_item {
		cursor: pointer;
	}
	.videos_list_item.active {
		background-color: #3A3A3A;
		pointer-events: none;
		cursor: default;
	}
	.videos_list_item:hover {
		background-color: #525252;
	}
	
	.videos_list_item .video_number .active {
		display: none;
	}
	.videos_list_item .video_number .inactive {
		display: block;
	}
	.videos_list_item.active .video_number .active {
		display: block;
	}
	.videos_list_item.active .video_number .inactive {
		display: none;
	}
	
</style>

<script type="text/javascript">

	$j = jQuery.noConflict();
	
	$j(function(){
		var current_video_column_height = $j('.current_video_column').css('height');
		$j('.videos_list_column').css( 'height' , current_video_column_height );	

		$j('.videos_list_item').click(function(){
			
			var target = $j(this).attr('data-target');
			$j('.video-container').html('');
			$j('.video-container').html('<iframe src="https://www.youtube.com/embed/' + target + '?ecver=2&autoplay=1" width="640" height="360" frameborder="0" style="position:absolute;width:100%;height:100%;left:0" allowfullscreen></iframe>');
			
			$j('.videos_list_item.active').removeClass('active');
			$j('.videos_list_item[data-target="' + target + '"]').addClass('active');
			
			$j('.video_description').css('display','none');
			$j('.video_description[data-video-id="' + target + '"]').css('display','block');
			
		});
		
		$j('.videos_list_item:first-child').click();
		
	});


</script>
<script src="https://apis.google.com/js/platform.js"></script>
<script>
	function onYtEvent(payload) {
		if (payload.eventType == 'subscribe') {
			// Add code to handle subscribe event.
		} else if (payload.eventType == 'unsubscribe') {
			// Add code to handle unsubscribe event.
		}
		if (window.console) { // for debugging only
			window.console.log('YT event: ', payload);
		}
	}
</script>
	
	
<div class="container section_divider_in_bottom" style="padding-top:0px; margin-top:40px;">
	
	
	<?php if( !$videos ) { ?>
		<h4 sty>
			No videos found to be loaded into the video player.
		</h4>
	<?php } ?>
	
	
	<?php if( $videos ) { ?>
	
	<div class="row" style="
		background-color: #010001;
		color: #fff;
	">
		
		<div class="col-md-8 no_pm current_video_column">
			
			<div class="video-container"></div>

		</div>
		
		<div class="col-md-4 no_pm videos_list_column" style="
			background-color: #1A1A1A;
			overflow-y: scroll;
		">
			
			<div class="videos_list" style="
				color: #fff;
				padding: 10px 0px;
			">
				<h4 style="
					color: #fff;
					padding: 5px;
				">
					<?php echo DD_WEBSITE_NAME; ?> Youtube Channel
				</h4>
				<ul style="list-style: none; padding: 0px;">
				<?php $i = 1; ?>
				<?php foreach( $videos as $video ) { ?>
					<li
						data-target="<?php echo $video->id->videoId;?>"
						class="videos_list_item<?php 
							if( $video->id->videoId == $current_video->id->videoId ) {
								echo ' active';
							}
						?>" 
						style="
							padding: 10px;
							margin-bottom: 0px;
						"
					>
						<div class="dis_in" style="width: 30%;">
							<div class="dis_in video_number" style="
								width: 20px;
								font-size: 12px;
								vertical-align: top;
							">
								<div class="active">
									<?php echo '<span style="color: #ec3131; font-size: 20px;">»</span>'; ?>
								</div>
								<div class="inactive">
									<?php echo $i++; ?>
								</div>
							</div>
							<img class="img-responsive dis_in" 
								src="<?php echo $video->snippet->thumbnails->medium->url;?>" 
								style="
									height: 30px;
								"
							/>
						</div>
						<div class="dis_in" style="
							width: 65%;
							font-size: 12px;
							vertical-align: top;
						">
							<?php echo $video->snippet->title;?>
						</div>
					
					</li>
				<?php } ?>
				</ul>
				
			</div>
			
		</div>
		
	</div>
	
	
	
	<div class="row" style="
	">
		
		<div class="col-md-8 no_pm current_video_description" style="
		padding: 20px;
		">
			
			<?php foreach( $videos as $video ) { ?>
			<div class="video_description" data-video-id="<?php echo $video->id->videoId;?>" style="
				display: none;
			">
			
				<h3><?php echo nl2br($video->snippet->title);?></h3>
				
				<div class="subscribe_button_wrapper" style="
					padding: 10px 0px 20px 0px;
				">
					<div class="g-ytsubscribe" data-channelid="<?php echo $youtube_channel_id; ?>" data-layout="full" data-count="default" data-onytevent="onYtEvent"></div>
				
				</div>
				
				<p>
					<?php echo nl2br($video->description);?>
				</p>
				
			</div>
			<?php } ?>
			
		</div>
	</div>
	
	
	<?php } ?>
	
</div>
<div style="clear:both;"></div>
        


		
		
        
<!--
**********************************************************************************************************************
***************************************************** /My Layout *****************************************************
**********************************************************************************************************************
-->


<!--
**********************************************************************************************************************
***************************************************** Some More Static Page Data *****************************************************
**********************************************************************************************************************
-->

<div class="container">
	
</div>
<div style="clear:both;"></div>

<!--
**********************************************************************************************************************
***************************************************** /Some More Static Page Data *****************************************************
**********************************************************************************************************************
-->












<?php if ( ! $is_page_builder_used ) : ?>

			</div> <!-- #left-area -->

			<?php get_sidebar(); ?>
		</div> <!-- #content-area -->
	</div> <!-- .container -->

<?php endif; ?>

</div> <!-- #main-content -->

<?php get_footer(); ?>