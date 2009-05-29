<?php
/*
 Plugin Name: Cool tags
 Plugin URI: http://www.eyike.com/html/y2009/cool-tags.html
 Description: A plugin for WordPress, You can control the tag cloud(include color,font size,number,and so on) of sidebar.
 Version: 0.0.1
 Author: Soncy
 Author URI: http://www.eyike.com
 */
add_filter('wp_tag_cloud', 'cooltags',  1);
add_action('admin_menu', 'cooltags_menu');
add_action('plugins_loaded', 'cooltags_init');
function cooltags($text){
	$tag_min_size = 12;
	$tag_max_size = 22;
	$fontSizeNum = 5;
	$colors = array(0 => "#ccc",1 => "#555",2 => "#06c",3 => "#468c00",4 => "#f60",5 => "#f00");
	$colors[1] = get_option("x-small");
	if($colors[1] == "") $colors[1] = "#555";
	$colors[2] = get_option("small");
	if($colors[2] == "") $colors[2] = "#06c";
	$colors[3] = get_option("medium");
	if($colors[3] == "") $colors[3] = "#468c00";
	$colors[4] = get_option("large");
	if($colors[4] == "") $colors[4] = "#f60";
	$colors[5] = get_option("x-large");
	if($colors[5] == "") $colors[5] = "#f00";
	$num_tags_all = wp_count_terms('post_tag');
	$num_all = number_format_i18n( $num_tags_all );
	$fontSizeSpeed = ($tag_max_size - $tag_min_size) / $fontSizeNum;
	$str_pattern = "/(\<a(.*?)\<\/a\>)/is"; 
	if (preg_match_all($str_pattern, $text, $matches)) {
		$counts = array();
		for ($i = 0; $i < count($matches[0]); $i++) {
			$hcontent = $matches[1][$i];
			$sc = preg_match('/(\<a(.*?)\>)/is', $hcontent, $matcht);
			if ($sc) {
				$htitle = $matcht[1];
			}
			if (preg_match('/title=\'(\d).*?\'/is', $htitle, $match)) {
				$tag_num = $match[1];
			}
			$counts[$i] = $tag_num;
		}
	}
	
	/*$min_count = min( $counts );
	$spread = max( $counts ) - $min_count;
	if ( $spread <= 0 )
		$spread = 1;
	$font_spread = $tag_max_size - $tag_min_size;
	if ( $font_spread < 0 )
		$font_spread = 1;
	$font_step = $font_spread / $spread;
	*/
	if (preg_match_all($str_pattern, $text, $matches)) {
		for ($i = 0; $i < count($matches[0]); $i++) {
			$hcontent = $matches[1][$i];
			$num_tags_speed = $counts[$i] / max($counts);
			//$num_tagsC_spedd = $counts[$i] / count($counts);
			//$fontSize = ( $tag_min_size + ( ( $counts[$i] - $min_count ) * $font_step ) );
			$fontColor = $colors[setSpeed($num_tags_speed,$fontSizeNum)];
			/*if(preg_match("/(style=\'(.*?)\')/i", $hcontent, $matchy)){
				$oldStyle = $matchy[1];
			}*/
			$newStyle = "style='color:{$fontColor};$2'";
			$oldStyle = "/(style='(.*?)')/is";
			$newtext = preg_replace($oldStyle,$newStyle,$hcontent);
			$a[] = $newtext;
			if(count(explode("<ul class='wp-tag-cloud'>",$text)) > 1){
				$textnew = "<ul class='wp-tag-cloud'>\n\t<li>";
				$textnew .= join( "</li>\n\t<li>", $a );
				$textnew .= "</li>\n</ul>\n";
			}else{
				$textnew = join("\n",$a);
			}
			$text .= "<textarea>".setSpeed($num_tags_speed,$fontSizeNum)."</textarea>";
		}
		
	}
	echo $textnew;
}
function setSpeed($num,$fontSizeNum = 5){
	for($i = 1;$i <= $fontSizeNum;$i++){
		if($num > 1){
			return $fontSizeNum;
		}
		else if(((1/$fontSizeNum) * $i >= $num ) && ((1/$fontSizeNum) * ($i - 1) < $num) ){
			return $i;
		}
	}
}

function cooltags_menu(){
		load_plugin_textdomain('cooltags', 'wp-content/plugins/cool-tags');
		add_options_page('Cool Tags', 'Cool Tags', 8, __FILE__, 'cooltags_option');	
}
function cooltags_option(){
	$colors = array(0 => "#ccc",1 => "#555",2 => "#06c",3 => "#468c00",4 => "#f60",5 => "#f00");
	$colors[1] = get_option("x-small");
	if($colors[1] == "") $colors[1] = "#555";
	$colors[2] = get_option("small");
	if($colors[2] == "") $colors[2] = "#06c";
	$colors[3] = get_option("medium");
	if($colors[3] == "") $colors[3] = "#468c00";
	$colors[4] = get_option("large");
	if($colors[4] == "") $colors[4] = "#f60";
	$colors[5] = get_option("x-large");
	if($colors[5] == "") $colors[5] = "#f00";
	?>
	<div class="wrap">
		<h2>
			<?php _e('Cool Tags Options', 'cooltags') ?>
		</h2>

	<form name="form1" method="post" action="options.php">
	<?php wp_nonce_field('update-options'); ?>

	<table class="form-table">
		<tr valign="top">
			<th cope="row"><?php _e('the color of tags option(from cold to hot)','cooltags' ); ?></td>
			<td><?php _e('you can use like this: "#000" or "#000000" or "black" or "rgb(70, 140, 0)"','cooltags' ); ?></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Level:','cooltags' ); ?> 1</th>
			<td><input type="text" name="x-small" value="<?php echo $colors[1]; ?>" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Level:','cooltags' ); ?> 2</th>
			<td><input type="text" name="small" value="<?php echo $colors[2]; ?>" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Level:','cooltags' ); ?> 3</th>
			<td><input type="text" name="medium" value="<?php echo $colors[3]; ?>" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Level:','cooltags' ); ?> 4</th>
			<td><input type="text" name="large" value="<?php echo $colors[4]; ?>" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Level:','cooltags' ); ?> 5</th>
			<td><input type="text" name="x-large" value="<?php echo $colors[5]; ?>" /></td>
		</tr>

	</table>

	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="page_options" value="x-small,small,medium,large,x-large" />

	<p class="submit">
	<input type="submit" class="button-primary" name="Submit" value="<?php _e('Save Changes' ) ?>" />
	</p>

	</form>
	</div>
	<?php
}
function cooltags_init(){
	if ( !function_exists('wp_register_sidebar_widget') ) 
    return;
	function cool_tags_widget($args) { 
		extract($args);
		$options = get_option('cool_tags_option');
		$title = empty($options['title']) ? __('Tags') : apply_filters('widget_title', $options['title']);
		$num = (int) $options['num'];
		if(!$num || $num == "" || $num == 0) $num = 45;
		$maxsize = (int) $options['maxsize'];
		if(!$maxsize || $maxsize == "" || $maxsize == 0) $maxsize = 22;
		$minsize = (int) $options['minsize'];
		if(!$minsize || $minsize == "" || $minsize == 0) $minsize = 12;
		$unit = $options['unit'];
		if(!$unit || $unit == "") $unit = "px";
		$format = $options['format'];
		if(!$format || $format == "") $format = "flat";
		$orderby = $options['orderby'];
		if(!$orderby || $orderby == "") $orderby = "name";
		$order = $options['order'];
		if(!$order || $order == "") $order = "ASC";
		
		echo $before_widget;
		echo $before_title . $title . $after_title;
		wp_tag_cloud("number=".$num."&largest=".$maxsize."&smallest=".$minsize."&unit=".$unit."&format=".$format."&orderby=".$orderby."&order=".$order);
		echo $after_widget;
	} 
	function cooltags_control() {
		$options = $newoptions = get_option('cool_tags_option');
		if ( isset($_POST['cool-tags-submit']) ) {
			$newoptions['title'] = strip_tags(stripslashes($_POST['cool-tags-title']));
			$newoptions['num'] = strip_tags(stripslashes($_POST['cool-tags-num']));
			$newoptions['maxsize'] = strip_tags(stripslashes($_POST['cool-tags-maxsize']));
			$newoptions['minsize'] = strip_tags(stripslashes($_POST['cool-tags-minsize']));
			$newoptions['unit'] = $_POST['cool-tags-unit'];
			$newoptions['format'] = $_POST['cool-tags-format'];
			$newoptions['orderby'] = $_POST['cool-tags-orderby'];
			$newoptions['order'] = $_POST['cool-tags-order'];
		}

		if ( $options != $newoptions ) {
			$options = $newoptions;
			update_option('cool_tags_option', $options);
		}

		$title = attribute_escape( $options['title'] );
		$num = attribute_escape( $options['num'] );
		$maxsize = attribute_escape( $options['maxsize'] );
		$minsize = attribute_escape( $options['minsize'] );
		$unit = attribute_escape( $options['unit'] );
		$format = attribute_escape( $options['format'] );
		$orderby = attribute_escape( $options['orderby'] );
		$order = attribute_escape( $options['order'] );
	?>
		<p>
			<label for="cool-tags-title">
				<?php _e('Title:') ?> <input type="text" class="widefat" id="cool-tags-title" name="cool-tags-title" value="<?php echo $title ?>" />
			</label>
		</p>
		<p>
			<label for="cool-tags-num">
				<?php _e('Max tags to display: (default: 45)','cooltags') ?><input type="text" class="widefat" id="cool-tags-num" name="cool-tags-num" value="<?php echo $num ?>" />
			</label>
		</p>
		<p>
			<label for="cool-tags-maxsize">
				<?php _e('Font size max: (default: 22)','cooltags') ?><input type="text" class="widefat" id="cool-tags-maxsize" name="cool-tags-maxsize" value="<?php echo $maxsize ?>" />
			</label>
		</p>
		<p>
			<label for="cool-tags-minsize">
				<?php _e('Font size min: (default: 12)','cooltags') ?><input type="text" class="widefat" id="cool-tags-minsize" name="cool-tags-minsize" value="<?php echo $minsize ?>" />
			</label>
		</p>
		<p>
			<?php _e('Unit font size: (default: px)','cooltags') ?>
			<select id="cool-tags-unit" name="cool-tags-unit">
				<option <?php if($unit == "px"){echo 'selected="selected"';} ?> value="px">px</option>
				<option <?php if($unit == "pt"){echo 'selected="selected"';} ?> value="pt">pt</option>
				<option <?php if($unit == "em"){echo 'selected="selected"';} ?> value="em">em</option>
			</select>	
		</p>
		<p>
			<?php _e('Format:','cooltags') ?>
			<select id="cool-tags-format" name="cool-tags-format">
				<option <?php if($format == "flat"){echo 'selected="selected"';} ?> value="flat"><?php _e('flat','cooltags') ?></option>
				<option <?php if($format == "list"){echo 'selected="selected"';} ?> value="list"><?php _e('list','cooltags') ?></option>
			</select>	
		</p>
		<p>
			<?php _e('Orderby:','cooltags') ?>
			<select id="cool-tags-orderby" name="cool-tags-orderby">
				<option <?php if($orderby == "name"){echo 'selected="selected"';} ?> value="name"><?php _e('name','cooltags') ?></option>
				<option <?php if($orderby == "count"){echo 'selected="selected"';} ?> value="count"><?php _e('count','cooltags') ?></option>
			</select>	
		</p>
		<p>
			<?php _e('Order:','cooltags') ?>
			<select id="cool-tags-order" name="cool-tags-order">
				<option <?php if($orderby == "ASC"){echo 'selected="selected"';} ?> value="ASC"><?php _e('ASC','cooltags') ?></option>
				<option <?php if($orderby == "DESC"){echo 'selected="selected"';} ?> value="DESC"><?php _e('DESC','cooltags') ?></option>
				<option <?php if($orderby == "RAND"){echo 'selected="selected"';} ?> value="RAND"><?php _e('RAND','cooltags') ?></option>
			</select>	
		</p>
		<input type="hidden" name="cool-tags-submit" id="cool-tags-submit" value="1" />
	<?php
	}
	
	$widget_opss = array('classname' => 'widget_tag_cloud', 'description' => __('Control your tags,include number,font size and so on.','cooltags') );
	wp_register_sidebar_widget("CoolTags",'CoolTags', 'cool_tags_widget',$widget_opss);
	wp_register_widget_control("CoolTags",__('Tag Cloud'), 'cooltags_control');
}
 ?>