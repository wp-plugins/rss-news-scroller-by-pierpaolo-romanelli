<?php

/*
Plugin Name: RSS News  by Pierpaolo Romanelli
Description: RSS News  is a  WordPress plugin to create a list of news with images from RSS feed and display it in widget.
Author: 	 Pierpaolo Romanelli
Version: 	 1.0.0
Plugin URI:  http://www.bestourism.com
Author URI:  http://www.bestourism.com/
Donate link: http://www.bestourism.com/
*/

/**
 *     RSS News  by Pierpaolo Romanelli
 *     Copyright (C) 2015  Pierpaolo Romanelli
 * 
 *     This program is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 * 
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 * 
 *     You should have received a copy of the GNU General Public License
 *     along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */	
include_once( ABSPATH . WPINC . '/feed.php' );
function npr_rss_show()
{
	global $wpdb;
	
	$htmldata = "";
	$cnt = 0;
	$npr_news_items = get_option('npr_news_items');
	$npr_rss_url = get_option('npr_rss_url');
	
	global $rss_items;
	$rss = fetch_feed( $npr_rss_url );
	$maxitems = 0;
	$maxitems = $rss->get_item_quantity( $npr_news_items ); 
    // Build an array of all the items, starting with element 0 (first element).
    $rss_items = $rss->get_items( 0, $maxitems );
	
	
	//$htmlres = file_get_contents($npr_rss_url);
	//$htmlDom = simplexml_load_string($htmlres);
	
	foreach($rss_items as $item)
	{ 
		$descr_ = html_entity_decode ($item->get_description(), ENT_COMPAT, "UTF-8");
		$htmldata .= "<div class='slide'><strong>".  "<a href='" . $item->get_link() . "'>" . $item->get_title()."</a></strong><br/><br/>". $descr_ ."</div>";
		$cnt++;
		if($cnt == $npr_news_items)
			break;
	}
	
	
	
	$htmldata .= "";
	
	echo "<div id='npr_banner' class='slider1' style='width: 250px; height: 250px;'>$htmldata</div>";
	add_action('wp_footer', 'npr_start_slider');
}

function npr_start_slider()
{
	global $wpdb;
	$npr_speed = get_option('npr_speed');
	$npr_pause = get_option('npr_pause');
	$npr_direction = get_option('npr_direction');
	$npr_autoplay = filter_var(get_option('npr_autoplay'), FILTER_VALIDATE_BOOLEAN);
	$npr_opacity = get_option('npr_opacity');
	$npr_full3D = filter_var(get_option('npr_full3D'), FILTER_VALIDATE_BOOLEAN);
    
	$slide_option = "direction:'".$npr_direction."', speed:'".$npr_speed."',pause:'".$npr_pause."',shading:".($npr_shading?"true":"false").",opacity:'".$npr_opacity."',full3D:".($npr_full3D?"true":"false");
	
	$slide_option =
		"slideWidth: 250,
    	minSlides: 1,
    	maxSlides: 1,
    	slideMargin: 10,
		mode: '$npr_direction',
	  	tickerHover: false,
	  	responsive:true,
	  	controls:false,
	  	autoControls:true,
	  	autoStart:$npr_autoplay,
	  	auto:true,
	  	pause:$npr_pause,
	  	captions:true,
		preloadImages:false";
	
	
	echo "<script type='text/javascript'>jQuery(document).ready(function(){   jQuery('.slider1').bxSlider({".$slide_option."});  }); </script>";
}



function npr_install() 
{
	add_option('npr_title', "RSS News by Pierpaolo Romanelli");
	add_option('npr_direction', "horizontal");
	add_option('npr_news_items', 5);
	add_option('npr_speed', "5000");
	add_option('npr_pause', "5000");
	add_option('npr_autoplay', "1");
	add_option('npr_opacity', 0.8);
	add_option('npr_rss_title', "Pierpaolo Romanelli");
	add_option('npr_rss_url', "http://news.google.com/news?pz=1&cf=all&ned=en&hl=en&output=rss");
	add_option('npr_full3D', "1");
}

function npr_widget($args) 
{
	extract($args);
	if(get_option('npr_title') <> "")
	{
		echo $before_widget;
		echo $before_title;
		echo get_option('npr_title');
		echo $after_title;
	}
	npr_rss_show();
	if(get_option('npr_title') <> "")
	{
		echo $after_widget;
	}
}
	
function npr_control() 
{
	echo "RSS News  by bestourism";
	echo "<br>";
	echo "<a href='http://www.bestourism.com' target='_blank'>Check official website</a>";
	echo "<br>";
}

function npr_widget_init()
{
	if(function_exists('wp_register_sidebar_widget')) 
	{
		wp_register_sidebar_widget('rss-news-by-p-r', 'RSS News by Pierpaolo Romanelli', 'npr_widget');
	}
	
	if(function_exists('wp_register_widget_control')) 
	{
		wp_register_widget_control('rss-news-by-p-r', array('RSS News by Pierpaolo Romanelli', 'widgets'), 'npr_control');
	} 
	
}

function npr_load_scripts()
{
	wp_register_style( 'npr_rss_css', plugins_url('/jqueryslider/jquery.bxslider.css', __FILE__) );
	wp_enqueue_style( 'npr_rss_css', get_stylesheet_uri() );
	
	wp_register_script('npr_rss_script', plugins_url('/jqueryslider/jquery.bxslider.min.js', __FILE__));
	wp_enqueue_script('npr_rss_script',array('jquery'));
	
	
}

function npr_deactivation() 
{
	delete_option('npr_opacity');
	delete_option('npr_title');
	delete_option('npr_direction');
	delete_option('npr_news_items');
	delete_option('npr_speed');
	delete_option('npr_pause');
	delete_option('npr_autoplay');
	
	delete_option('npr_rss_title');
	delete_option('npr_rss_url');
	delete_option('npr_full3D');
}

function npr_option() 
{
	global $wpdb;
	echo '<h2>RSS News by Pierpaolo Romanelli</h2>';
	$npr_title = get_option('npr_title');
	
	$npr_speed = get_option('npr_speed');
	$npr_pause = get_option('npr_pause');
	$npr_direction = get_option('npr_direction');
	$npr_news_items = get_option('npr_news_items');
	$npr_autoplay = get_option('npr_autoplay');
	$npr_opacity = get_option('npr_opacity');
	$npr_rss_title = get_option('npr_rss_title');
	$npr_rss_url = get_option('npr_rss_url');
	$npr_full3D = get_option('npr_full3D');
	
	if (@$_POST['npr_submit']) 
	{
		$npr_title = stripslashes($_POST['npr_title']);
		
		$npr_speed = stripslashes($_POST['npr_speed']);
		$npr_pause = stripslashes($_POST['npr_pause']);
		$npr_direction = stripslashes($_POST['npr_direction']);
		$npr_news_items = stripslashes($_POST['npr_news_items']);
		$npr_autoplay = stripslashes($_POST['npr_autoplay']);
		$npr_opacity = stripslashes($_POST['npr_opacity']);
		$npr_rss_title = stripslashes($_POST['npr_rss_title']);
		$npr_rss_url = stripslashes($_POST['npr_rss_url']);
		$npr_full3D = stripslashes($_POST['npr_full3D']);
		
		update_option('npr_title', $npr_title );
		update_option('npr_speed', $npr_speed );
		update_option('npr_pause', $npr_pause );
		update_option('npr_direction', $npr_direction );
		update_option('npr_news_items', $npr_news_items );
		update_option('npr_autoplay', $npr_autoplay );
		update_option('npr_opacity', $npr_opacity );
		update_option('npr_rss_title', $npr_rss_title );
		update_option('npr_rss_url', $npr_rss_url );
		update_option('npr_full3D', $npr_full3D );
	}
	
	echo '<form name="npr_form" method="post" action="">';
	
	echo '<p>Title :<br><input  style="width: 350px;" type="text" value="';
	echo $npr_title . '" name="npr_title" id="npr_title" /></p>';
	
	echo '<p>Speed :<br><input  style="width: 100px;" type="text" value="';
	echo $npr_speed . '" name="npr_speed" id="npr_speed" />(Default 5000)</p>';
	
	echo '<p>Pause :<br><input  style="width: 100px;" type="text" value="';
	echo $npr_pause . '" name="npr_pause" id="npr_pause" />(Default 5000)</p>';
	
	echo '<p>Direction :<br><input  style="width: 100px;" type="text" value="';
	echo $npr_direction . '" name="npr_direction" id="npr_direction" /> (horizontal/vertical)</p>';

	echo '<p>News Items, articles or posts to display from feed :<br><input  style="width: 100px;" type="text" value="';
	echo $npr_news_items . '" name="npr_news_items" id="npr_news_items" /> (Default 5: 1,2, any integer)</p>';
	
	echo '<p>Opacity :<br><input  style="width: 350px;" type="text" value="';
	echo $npr_opacity . '" name="npr_opacity" id="npr_opacity" /></p>';
		
	echo '<p>RSS Feed Title:<br><input  style="width: 250px;" type="text" value="';
	echo $npr_rss_title . '" name="npr_rss_title" id="npr_rss_title" /></p>';
	
	echo '<p>RSS Feed URL: <br><input  style="width: 250px;" type="text" value="';
	echo $npr_rss_url . '" name="npr_rss_url" id="npr_rss_url" />';
	
	?>
	<p>Autoplay: <input type="radio" id="npr_autoplay" name="npr_autoplay"  value="1" <?php
	if ($npr_autoplay == 1) echo 'checked' ;?> /> Yes
	<input type="radio" id="npr_autoplay" name="npr_autoplay"  value="0" <?php
	if ($npr_autoplay == 0) echo 'checked' ; ?> /> No
	<p>Full3D: <input type="radio" id="npr_full3D" name="npr_full3D"  value="1" <?php
	if ($npr_full3D == 1) echo 'checked' ;?> /> Yes
	<input type="radio" id="npr_full3D" name="npr_full3D"  value="0" <?php
	if ($npr_full3D == 0) echo 'checked' ; ?> /> No
	

<?php
	echo '<br>';
	echo '<br>';

	echo '<input name="npr_submit" id="npr_submit" lang="publish" class="button-primary" value="Update" type="Submit" />';
	echo '</form>';
	echo 'For more features please consider to do a little donation.';
?>
	<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
		<input type="hidden" name="cmd" value="_donations">
		<input type="hidden" name="item_name" value="RSS News Contribution">
		<input type="hidden" name="business" value="programmo@gmail.com">
		<input type="image" src="https://www.paypalobjects.com/webstatic/en_US/btn/btn_donate_pp_142x27.png" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
		<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
	</form>
<?php
}

function npr_add_to_menu() 
{
	add_options_page('RSS News by Pierpaolo Romanelli', 'RSS News', 'manage_options', __FILE__, 'npr_option' );
}

add_action('admin_menu', 'npr_add_to_menu');
add_action("plugins_loaded", "npr_widget_init");
register_activation_hook(__FILE__, 'npr_install');
register_deactivation_hook(__FILE__, 'npr_deactivation');
add_action('init', 'npr_widget_init');
add_action('wp_enqueue_scripts','npr_load_scripts');
?>