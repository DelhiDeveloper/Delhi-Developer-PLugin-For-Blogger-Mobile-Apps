<?php





add_shortcode( 'display_homepage_videos', 'display_homepage_videos' );
function display_homepage_videos() {
	
	
	
	
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
	
	// Getting Mobile User Verification Page Slug
	$dd_mobile_app_important_pages = json_decode( get_option( 'dd_mobile_app_important_pages' ) );
	if( 
			! $dd_mobile_app_important_pages
		||	$dd_mobile_app_important_pages->dd_youtube_video_player_page == ''
	) {
		// If no page has been assigned to the template
		$mobile_app_user_verification_page_slug = 'dd_youtube_video_player_page';
	} else {
		// If a page has been assigned to the template in plugin settings
		$dd_youtube_video_player_page = get_post( $dd_mobile_app_important_pages->dd_youtube_video_player_page );
		$dd_youtube_video_player_page_slug = $dd_youtube_video_player_page->post_name;
	}
	
	
	
	$sq = "'";
	
	$html = '
	
	<style>
		.col-xs-4 {
			display: inline-block;
			float: left;
			width: 33.33%;
			padding: 5px 10px;
		}
		.videos_list_item {
			cursor: pointer;
		}
	</style>

	<script type="text/javascript">

		$j = jQuery.noConflict();
		
		$j(function(){	

			$j(".videos_list_item").click(function(){
				
				var target = $j(this).attr("data-target");
				$j(".video-container").html("");
				$j(".video-container").html(
					'. $sq .'<iframe src="https://www.youtube.com/embed/'. $sq .' + target + '. $sq .'"?ecver=2&autoplay=1" width="640" height="360" frameborder="0" style="position:absolute;width:100%;left:0" allowfullscreen></iframe>'. $sq .'
				)
				.promise().done(function(){
					setTimeout( function(){ 
						$j(".video-container").css("height", $j(".video-container iframe").height() );
						$j(".video-container").parent().css("height", $j(".video-container iframe").height() );
					}  , 1000 );
				});
				
			});
			
			$j(".homepage_videos_row_2 .col-xs-4.videos_list_item").click();
			
		});


	</script>

	<div>
		<h3 style="float: left;">Latest Videos</h4>
		<h4 style="float: right;">
			<a href="'. DD_WEBSITE_SITEURL . '/' . $dd_youtube_video_player_page_slug .'">
				[ View All ]
			</a>
		</h5>
	</div>
	<div style="clear: both;"></div>
		
	<div class="homepage_videos_container">
		
		<div class="homepage_videos_row_1" 
			style="
				height: 300px;
			"
		>
			<div class="video-container"></div>
		</div>
		
		<div class="homepage_videos_row_2"
			style="
				margin-top: 10px;
			"
		>
		
			<div class="col-xs-4 videos_list_item" data-target="'. $videos[0]->id->videoId .'">			
				<img class="img-responsive dis_in" 
					src="'. $videos[0]->snippet->thumbnails->medium->url .'" 
				/>
				<span style="font-weight: bold;">'. $videos[0]->snippet->title .'</span>
			</div>
		
			<div class="col-xs-4 videos_list_item" data-target="'. $videos[1]->id->videoId .'">	
				<img class="img-responsive dis_in" 
					src="'. $videos[1]->snippet->thumbnails->medium->url .'" 
				/>
				<span style="font-weight: bold;">'. $videos[1]->snippet->title .'</span>
			</div>
		
			<div class="col-xs-4 videos_list_item" data-target="'. $videos[2]->id->videoId .'">				
				<img class="img-responsive dis_in" 
					src="'. $videos[2]->snippet->thumbnails->medium->url .'" 
				/>
				<span style="font-weight: bold;">'. $videos[2]->snippet->title .'</span>
			</div>
		
		</div>
		
	</div>
	<div style="clear: both;"></div>
	';
	
	return $html;
	
}














add_shortcode( 'display_homepage_posts', 'display_homepage_posts' );
function display_homepage_posts() {

	
	$posts = get_posts(
		array(
			'numberposts' => 3,
		)
	);
	
	//return( json_encode( $posts ) );
	
	$html = '
	<div class="homepage_posts_container">
	';
	
	foreach( $posts as $post ) {
		$html .= '
			<div class="homepage_post_container" style="margin-top: 10px;">
				<a href="https://prachyakarma.com/'. $post->post_name .'" class="col-xs-4">
					'.
						( 
							get_the_post_thumbnail($post->ID) 
							? 
							get_the_post_thumbnail($post->ID) 
							: 
							'<img class="img-responsive" src="https://prachyakarma.com/wp-content/uploads/2017/03/prachya-karma-logo-w300.png" />' 
						) 
					.'
				</a>
				<div class="col-xs-8">
					<a href="https://prachyakarma.com/'. $post->post_name .'" class="h4">
						'. $post->post_title .'
					</a>
					<p>
						'. mb_substr( $post->post_content , 0 , 100 , 'UTF-8' ) .'
					</p>
					<h6>
						'. date( 'M j, Y' , strtotime($post->post_date) ) .'
					</h6>
				</div>
			</div>
		';
	}
	$html .= '
	</div>
	';
	
	return $html;
	
}














?>