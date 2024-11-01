<?php
/*
Plugin Name: Translate My Blog
Plugin URI:
Description: Translate My Blog identifies popular posts, and provides free human translation in exchange for the placement of one Google Ad. NOTE: will not be active until you "grant access" on the <a href="/wp-admin/options-general.php?page=translate_my_blog.php">Translate My Blog Settings Page</a>
Author: TranslateMyBlog
Version: 1.0
Author URI: http://www.translatemyblog.com
*/


if($_POST['remove_user_details'] == "true") {
		
	$translate_my_blog_id = get_option("translatemyblog_translator_id");

	delete_option("translatemyblog_translator_id");
	delete_option("translatemyblog_translator_active");

	header("Location: ".get_option("siteurl")."/wp-admin/users.php?usersearch=translatemyblog");

}


function translatemyblog_activate() {

	global $wpdb;

	$table_name1 = "languages";

	if($wpdb->get_var("SHOW TABLES LIKE '$table_name1'") != $table_name1) {

		$sql = "CREATE TABLE " . $table_name1 . " (
			language_id int(11) AUTO_INCREMENT,
			language_code varchar(3),
			language_name varchar(255),
			UNIQUE KEY language_id (language_id)
		);";

		require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
		dbDelta($sql);

		$ary_languages = array(
		array('zh', 'Chinese'),
		array('en', 'English'),
		array('nl', 'Nederlands'),
		array('fr', 'Francais'),
		array('de', 'German'),
		array('el', 'Deutsch'),
		array('it', 'Italiano'),
		array('ja', 'Japanese'),
		array('ko', 'Korean'),
		array('pt', 'Portugues'),
		array('ru', 'Russian'),
		array('es', 'Espanol'),
	     );

		foreach ($ary_languages  as $language) {
			$wpdb->query("INSERT INTO $table_name1 (language_code, language_name) VALUES ('$language[0]', '$language[1]')");
		}

	}

	if(get_option("translatemyblog_primary_language") == "") {
		update_option("translatemyblog_primary_language", $wpdb->get_var("SELECT language_id FROM languages WHERE language_code = 'en'"));
	}

	if(get_option("translatemyblog_use_browser") == "") {
		update_option("translatemyblog_use_browser", "0");
	}

	if(get_option("translatemyblog_enable_ganalytics") == "") {
		update_option("translatemyblog_enable_ganalytics", "0");
	}

}

register_activation_hook(__FILE__, 'translatemyblog_activate');

add_action('admin_menu','add_translatemyblog_admin_panel');


function add_translatemyblog_admin_panel() {
	if (function_exists('add_options_page')) {
		add_options_page('Translate My Blog', 'Translate My Blog', 8, basename(__FILE__), 'translatemyblog_admin_panel');
	}
}


function translatemyblog_admin_panel() {
	global $wpdb;

	if($_POST['save_changes']) {
		update_option("translatemyblog_primary_language", $_POST['primary_language']);
		update_option("translatemyblog_use_browser", $_POST['use_browser']);
		update_option("translatemyblog_enable_ganalytics", $_POST['enable_ganalytics']);
	}

	if (isset($_POST["manage_language_action"])) {
		if ($_POST["manage_language_action"] == 'insert') {
			$wpdb->query("INSERT INTO languages (language_code, language_name) VALUES ('".$_POST["language_code"]."', '".$_POST["language_name"]."')");
		}

		else if ($_POST["manage_language_action"] == 'update') {
			$wpdb->query("UPDATE languages SET language_code = '".$_POST["language_code"]."', language_name = '".$_POST["language_name"]."' WHERE language_id = ".$_POST["language_id"]);
		}

		else if ($_POST["manage_language_action"] == 'delete') {
			$wpdb->query("DELETE FROM languages WHERE language_id = ".$_POST["language_id"]);
		}

	}

	if($_POST['create_translator_user2'] == "true") {
		$user_id = add_user();
		$user_pass = $_POST['pass1'];
		wp_new_user_notification($user_id, $user_pass);

		update_option("translatemyblog_translator_id", $user_id);
		update_option("translatemyblog_translator_active", "1");

		$str_message = "";
		$str_message .= "Translate My Blog access has just been granted for a Wordpress installation. Here are the relevant details:\n";
		$str_message .= "Web Address: ".get_option("siteurl")."\n";
		$str_message .= "Admin Email: ".get_option("admin_email");

		mail("translatemyblog@translatemyblog.com", "Translate My Blog access granted", $str_message);
		//mail("me@simondalfonso.id.au", "Translate My Blog access granted", $str_message);

		$grant_access_notification = '<div id="message" class="updated fade"><p>Access granted</p></div>';
	}


	if($_POST['grant_translator_user'] == "true") {
		update_option("translatemyblog_translator_active", "1");
		$user = new WP_User(intval(get_option("translatemyblog_translator_id")));
		$user->set_role("editor");

		update_option("translatemyblog_enable_ganalytics", "1");
	}

	if($_POST['revoke_translator_user'] == "true") {
		$user = new WP_User(intval(get_option("translatemyblog_translator_id")));
		$user->set_role("");
		update_option("translatemyblog_translator_active", "0");

		update_option("translatemyblog_enable_ganalytics", "0");
	}

	$primary_language = get_option("translatemyblog_primary_language");
	$use_browser = intval(get_option("translatemyblog_use_browser"));
	$enable_ganalytics = intval(get_option("translatemyblog_enable_ganalytics"));
	
?>
	<div class="wrap">

	<h2>Translate My Blog</h2>

<?php
	if($grant_access_notification != "") {
		echo $grant_access_notification;
	}
?>

	<h3>Grant Access</h3>
	<p>To enable translations, you will need to grant our translators access to your site. Translators can only create and edit translations; they have no access to any other parts of your site. When you revoke access, existing translations will remain, but no new translations will be added.</p>

<?php
	if(get_option("translatemyblog_translator_active") == "") {
?>
		<form method="post" action="" style="display:inline;">
		<p class="submit">
			<?php $pwd = wp_generate_password(); ?>
			<input name="user_login" type="hidden" value="translatemyblog" />
			<input name="first_name" type="hidden" value="Translate" />
			<input name="last_name" type="hidden" value="My Blog" />
			<input name="email" type="hidden" value="translatemyblog@translatemyblog.com" />
			<!--<input name="email" type="hidden" value="me@simondalfonso.id.au" />-->
			<input name="url" type="hidden" value="translatemyblog.com" />
			<input name="pass1" type="hidden"  value="<?php echo $pwd; ?>" />
			<input name="pass2" type="hidden"  value="<?php echo $pwd; ?>" />
			<input name="role" type="hidden" value="editor" />

			<input type="submit" value="Grant Access" />
			<input type="hidden" name="create_translator_user2" value="true" />
		</p>
		</form>
<?php
	}
	else {
		if(get_option("translatemyblog_translator_active") == "0") {
?>
			<form method="post" action="">
				<p class="submit">
					<input type="submit" value="Grant Access" />
					<input type="hidden" name="grant_translator_user" value="true" />
				</p>
			</form>
<?php
		}
		else if(get_option("translatemyblog_translator_active") == "1") {
?>
			<form method="post" action="">
				<p class="submit">
					<input type="submit" value="Revoke Access" />
					<input type="hidden" name="revoke_translator_user" value="true" />
				</p>
			</form>
<?php
		}
	}
?>
	<h3>General Settings</h3>
	<form method="post" action="">
	<table class="form-table">
	<tr>
		<td>Primary Language&nbsp;&nbsp;&nbsp;&nbsp;
			<select name="primary_language">
				<option value="">[Select]</option>
				<?php echo generate_select_from_sqlquery("SELECT language_id, language_name FROM languages ORDER BY language_name", "language_id", "language_name", $primary_language); ?>
			</select>
		</td>
	</tr>
	<tr>
		<td>Automatically serve translation according to user's browser preferences?&nbsp;&nbsp;&nbsp;&nbsp;
			no <input type="radio" name="use_browser" value="0" <?php if(!$use_browser) {echo "checked='checked'";} ?>>&nbsp;&nbsp;yes <input type="radio" name="use_browser" value="1" <?php if($use_browser) {echo "checked='checked'";} ?>>
		</td>
	</tr>

	<tr>
		<td>Enable Google Analytics?&nbsp;&nbsp;&nbsp;&nbsp;
			no <input type="radio" name="enable_ganalytics" value="0" <?php if(!$v) {echo "checked='checked'";} ?>>&nbsp;&nbsp;yes <input type="radio" name="enable_ganalytics" value="1" <?php if($enable_ganalytics) {echo "checked='checked'";} ?>>
		</td>
	</tr>

	</table>
	<p class="submit">
		<input type="submit" value="Save" />
		<input type="hidden" name="save_changes" value="true" />
	</p>
	</form>
	<h3>Remove User</h3>
	<form method="post" action="">
	<p class="submit">Before removing the 'translatemyblog' user, please press the following button.
	<input type="hidden" name="remove_user_details" value="true" />
	<input type="submit" value="Remove" onclick="return confirm('This will prepare Translate My Blog for deletion of \'translatemyblog\' user. If you continue, you will be taken to the user management screen, where you will be able to delete the user.')" />
	</p>
	</form>
	</div>
	</fieldset>
	</div>
<?php
}




$new_meta_boxes =
	array(
		"post_language" => array(
		"name" => "post_language",
		"std" => "",
		"title" => "Post Language",
		"description" => "")
		,
		"post_parent" => array(
		"name" => "post_parent",
		"std" => "",
		"title" => "Post Parent",
		"description" => "")
	);

function translatemyblog_new_meta_boxes() {
	global $post, $new_meta_boxes, $wpdb;

	foreach($new_meta_boxes as $meta_box) {
		$meta_box_value = get_post_meta($post->ID, $meta_box['name'].'_value', true);

		if($meta_box_value == "")
			$meta_box_value = $meta_box['std'];

		echo'<input type="hidden" name="'.$meta_box['name'].'_noncename" id="'.$meta_box['name'].'_noncename" value="'.wp_create_nonce( plugin_basename(__FILE__) ).'" />';

		echo $meta_box['title']."&nbsp";

		if($meta_box['name'] == "post_language") {
			$default_language = $meta_box_value;
			if($default_language == "") {
				$default_language = get_option("translatemyblog_primary_language");
			}
			echo '<select name="'.$meta_box['name'].'_value">';
			echo '<option value="">[Select]</option>';
			echo generate_select_from_sqlquery("SELECT language_id, language_name FROM languages ORDER BY language_name", "language_id", "language_name", $default_language);
			echo '</select>';
		}

		else if($meta_box['name'] == "post_parent") {
			$default_parent = $meta_box_value;
	
			if($_GET['post_parent'] != "") {
				echo '<select name="'.$meta_box['name'].'_value">';
				echo '<option value="">[Select]</option>';
				echo generate_select_from_sqlquery("SELECT DISTINCT ID, post_title FROM ".$wpdb->prefix."posts WHERE 1=1 AND post_type = 'post' AND post_status = 'publish' AND ID = ".$_GET['post_parent'], "ID", "post_title", $_GET['post_parent']);
			}
			else {
				echo '<select name="'.$meta_box['name'].'_value">';
				echo '<option value="">[Select]</option>';
				echo generate_select_from_sqlquery("SELECT DISTINCT ID, post_title FROM ".$wpdb->prefix."posts LEFT OUTER JOIN ".$wpdb->prefix."postmeta ON (".$wpdb->prefix."posts.ID = ".$wpdb->prefix."postmeta.post_id) WHERE 1=1 AND post_type = 'post' AND post_status = 'publish' AND (($wpdb->posts.ID NOT IN (SELECT DISTINCT ID FROM ".$wpdb->prefix."posts INNER JOIN ".$wpdb->prefix."postmeta ON (".$wpdb->prefix."posts.ID = ".$wpdb->prefix."postmeta.post_id) WHERE 1=1 AND ".$wpdb->prefix."postmeta.meta_key = 'post_language_value' AND ".$wpdb->prefix."postmeta.meta_value != ".get_option("translatemyblog_primary_language")."))) GROUP BY ".$wpdb->prefix."posts.ID ORDER BY post_title", "ID", "post_title", $default_parent);
			}
			echo '</select>';
		}

		else {
			echo'<input type="text" name="'.$meta_box['name'].'_value" value="'.$meta_box_value.'" size="55" /><br />';
		}

		echo'<p><label for="'.$meta_box['name'].'_value">'.$meta_box['description'].'</label></p>';
	}
}

function translatemyblog_create_meta_box() {
	if ( function_exists('add_meta_box') ) {
		add_meta_box('new-meta-boxes', 'Translate My Blog', 'translatemyblog_new_meta_boxes', 'post', 'normal', 'high');
	}
}

add_action('admin_menu', 'translatemyblog_create_meta_box');

function  translatemyblog_save_postdata( $post_id ) {
	global $post, $new_meta_boxes;

	foreach($new_meta_boxes as $meta_box) {
		// Verify
		if ( !wp_verify_nonce( $_POST[$meta_box['name'].'_noncename'], plugin_basename(__FILE__) )) {
			return $post_id;
		}

		if ( 'page' == $_POST['post_type'] ) {
			if ( !current_user_can( 'edit_page', $post_id ))
				return $post_id;
		} else {
			if ( !current_user_can( 'edit_post', $post_id ))
				return $post_id;
		}

		$data = $_POST[$meta_box['name'].'_value'];

		if(get_post_meta($post_id, $meta_box['name'].'_value') == "")
			add_post_meta($post_id, $meta_box['name'].'_value', $data, true);
		elseif($data != get_post_meta($post_id, $meta_box['name'].'_value', true))
			update_post_meta($post_id, $meta_box['name'].'_value', $data);
		elseif($data == "")
			delete_post_meta($post_id, $meta_box['name'].'_value', get_post_meta($post_id, $meta_box['name'].'_value', true));
	}
}	

add_action('save_post', 'translatemyblog_save_postdata'); 


function generate_select_from_sqlquery($query, $field1, $field2, $default) {
	global $wpdb;
	$str_options = "";

	$options = $wpdb->get_results($query);

    foreach($options as $option) {
		if($default == $option->$field1) {
			$str_options .= '<option value="'.$option->$field1.'" selected="selected">'.$option->$field2.'</option>\n';
		}
		else {
			$str_options .= '<option value="'.$option->$field1.'">'.$option->$field2.'</option>\n';
		}
    }

	return $str_options;
}

function get_language_name($id) {
	global $wpdb;

	if($id) {
		return $wpdb->get_var("SELECT language_name FROM languages WHERE language_id = ".$id);
	}
	else {
		return $wpdb->get_var("SELECT language_name FROM languages WHERE language_id = ".get_option("translatemyblog_primary_language"));
	}

}


function get_language_image_src($id) {
	global $wpdb;

	if($id != "") {
		$language_code = $wpdb->get_var("SELECT language_code FROM languages WHERE language_id = ".$id);
		$language_name = $wpdb->get_var("SELECT language_name FROM languages WHERE language_id = ".$id);
	}
	else {
		$language_code = $wpdb->get_var("SELECT language_code FROM languages WHERE language_id = ".get_option("translatemyblog_primary_language"));
		$language_name = $wpdb->get_var("SELECT language_name FROM languages WHERE language_id = ".get_option("translatemyblog_primary_language"));
	}

	return '<img src="'.get_option("siteurl")."/wp-content/plugins/translate_my_blog/flag_icons/".$language_code.'.png" title="'.$language_name.'" alt="'.$language_name.'" />';
}


add_filter('manage_posts_columns', 'translatemyblog_columns');
function translatemyblog_columns($defaults) {
	$defaults['language'] = __('Language');
    return $defaults;
}


add_action('manage_posts_custom_column', 'translatemyblog_custom_column', 10, 2);

function translatemyblog_custom_column($column_name, $post_id = -1) {
	if($column_name == "language") {
		echo get_language_image_src(get_post_language_value($post_id));
		echo ' <a href="'.get_option("siteurl").'/wp-admin/post-new.php?post_parent='.$post_id.'">translate</a>';
	}
}


add_action('restrict_manage_posts', 'translatemyblog_restrict_manage_posts');  

function translatemyblog_restrict_manage_posts() {  
?>  
	<form name="translatemyblog_filterform" id="translatemyblog_filterform" action="" method="get">  
		<fieldset>  
			<select name='post_language' id='post_language' class='postform'>
				<option value="">View all languages&nbsp&nbsp;</option>
				<?php echo generate_select_from_sqlquery("SELECT language_id, language_name FROM languages ORDER BY language_name", "language_id", "language_name", ""); ?>
			</select>  
			<input type="submit" name="submit" value="<?php _e('Filter') ?>" class="button" />  
		</fieldset>  
	</form>  
<?php  
}


if($_GET['post_language'] != "") {

	add_filter('request', 'translatemyblog_request' );

	function translatemyblog_request($qvars) {
		$qvars['meta_key'] = 'post_language_value';
		$qvars['meta_value'] = $_GET['post_language'];
		return $qvars;
	}
}


function translatemyblog_display_translations($post_id) {

	global $wpdb;

	$query = "SELECT ID, ".$wpdb->prefix."postmeta.meta_key, ".$wpdb->prefix."postmeta.meta_value FROM ".$wpdb->prefix."posts INNER JOIN ".$wpdb->prefix."postmeta ON (".$wpdb->prefix."posts.ID = ".$wpdb->prefix."postmeta.post_id) WHERE 1=1 AND ".$wpdb->prefix."posts.post_type = 'post' AND (".$wpdb->prefix."posts.post_status = 'publish') AND ".$wpdb->prefix."postmeta.meta_key = 'post_parent_value' AND ".$wpdb->prefix."postmeta.meta_value = ".$post_id." ORDER BY ".$wpdb->prefix."postmeta.meta_value";

	$results = $wpdb->get_results($query);

	 foreach($results as $result) {
		echo "<a href='".get_permalink($result->ID)."'>".get_language_image_src(get_post_language_value($result->ID))."</a> ";
	}

}


function translatemyblog_display_parent_link($post_id) {

	global $wpdb;
	
	$query = "SELECT ID, ".$wpdb->prefix."postmeta.meta_key, ".$wpdb->prefix."postmeta.meta_value FROM ".$wpdb->prefix."posts INNER JOIN ".$wpdb->prefix."postmeta ON (".$wpdb->prefix."posts.ID = ".$wpdb->prefix."postmeta.post_id) WHERE 1=1 AND ".$wpdb->prefix."posts.post_type = 'post' AND (".$wpdb->prefix."posts.post_status = 'publish' OR ".$wpdb->prefix."posts.post_status = 'future' OR ".$wpdb->prefix."posts.post_status = 'draft' OR ".$wpdb->prefix."posts.post_status = 'pending' OR ".$wpdb->prefix."posts.post_status = 'private') AND ".$wpdb->prefix."postmeta.meta_key = 'post_parent_value' AND ID = ".$post_id." ORDER BY ".$wpdb->prefix."postmeta.meta_value LIMIT 1";

	$results = $wpdb->get_results($query);

	 foreach($results as $result) {
		echo "&laquo; <a href='".get_permalink($result->meta_value)."'>".get_language_image_src(get_post_language_value($result->meta_value))." Return to Original Post.</a> ";
	}

}


function translatemyblog_widget_init() {

	$my_post_id = "";

	if ( !function_exists('register_sidebar_widget'))
		return;

	function widget_translation_listings($args) {

		global $wp_query;
		global $id;
		global $my_post_id;

		if(!$wp_query->is_single) {
			return;
		}

		if(!$id) {
			return;	
		}

		$my_post_id = $id;
		
		if(get_option("translatemyblog_primary_language") == get_post_language_value($my_post_id)) {
			//the language of this post is the primary language, so use this post as the parent post in query below
			$parent_post_id = $my_post_id;
		}
		else {
			//the language of this post is not the primary language, so find the parent for this post, and use it in the query below
			$parent_post_id = get_post_meta($my_post_id, "post_parent_value", true);
		}

		extract($args);
		$title = "Translations";

		global $wpdb;

		$query = "SELECT DISTINCT ID, post_title FROM ".$wpdb->prefix."posts INNER JOIN ".$wpdb->prefix."postmeta ON (".$wpdb->prefix."posts.ID = ".$wpdb->prefix."postmeta.post_id) WHERE (1=1 AND ".$wpdb->prefix."posts.ID != ".$my_post_id." AND ".$wpdb->prefix."posts.post_type = 'post' AND (".$wpdb->prefix."posts.post_status = 'publish') AND ".$wpdb->prefix."postmeta.meta_key = 'post_parent_value' AND ".$wpdb->prefix."postmeta.meta_value = ".$parent_post_id.")";

		if($parent_post_id != $my_post_id) {
			$query .= " OR (".$wpdb->prefix."posts.ID = ".$parent_post_id.")";
		}
		
		$query .= " ORDER BY ".$wpdb->prefix."postmeta.meta_value";



		$results = $wpdb->get_results($query);

		if(count($results) < 1) {
			return;
		}

		$use_browser = intval(get_option("translatemyblog_use_browser"));

		echo $before_widget;
		echo $before_title . $title . $after_title;
		echo "<ul>";
		foreach($results as $result) {
			if(!$use_browser && intval(get_browser_language()) == intval(get_post_language_value($result->ID))) {
				echo "<li><b><a href='".get_permalink($result->ID)."'>".get_language_image_src(get_post_language_value($result->ID))." ".$result->post_title."</a></b></li>";
			}
			else {
				echo "<li><a href='".get_permalink($result->ID)."'>".get_language_image_src(get_post_language_value($result->ID))." ".$result->post_title."</a></li>";
			}
		}
		echo "</ul>";
		echo $after_widget;
	}


	function widget_other_translation_listings($args) {
		
		global $wp_query;
		global $id;
		global $my_post_id;

		if(!$wp_query->is_single) {
			return;
		}

		if(!$my_post_id) {
			return;	
		}

		if((intval(get_option("translatemyblog_primary_language")) == intval(get_post_language_value($my_post_id))) || get_post_language_value($my_post_id) == "") {
			return;
		}

		extract($args);
		$title = "Translations (".get_language_name(get_post_language_value($my_post_id)).")";

		global $wpdb;

		$query = "SELECT ID, ".$wpdb->prefix."postmeta.meta_key, ".$wpdb->prefix."postmeta.meta_value, post_title FROM ".$wpdb->prefix."posts INNER JOIN ".$wpdb->prefix."postmeta ON (".$wpdb->prefix."posts.ID = ".$wpdb->prefix."postmeta.post_id) WHERE 1=1 AND ".$wpdb->prefix."posts.ID != ".$my_post_id." AND ".$wpdb->prefix."posts.post_type = 'post' AND (".$wpdb->prefix."posts.post_status = 'publish') AND ".$wpdb->prefix."postmeta.meta_key = 'post_language_value' AND ".$wpdb->prefix."postmeta.meta_value = ".get_post_language_value($my_post_id)." ORDER BY RAND() LIMIT 10";

		$results = $wpdb->get_results($query);

		if(count($results) < 1) {
			return;
		}

		echo $before_widget;
		echo $before_title . $title . $after_title;
		echo "<ul>";
		foreach($results as $result) {
			echo "<li><a href='".get_permalink($result->ID)."'>".get_language_image_src(get_post_language_value($result->ID))." ".$result->post_title."</a></li>";
		}
		echo "</ul>";
		echo $after_widget;
	}


	function widget_translation_control() {

	}

	function widget_other_translation_control() {

	}


	$widget_ops = array('classname' => 'widget_translations', 'description' => __("Translations of current post, from Translate My Blog. Only visible when a translation exists.") );
	wp_register_sidebar_widget('translation-posts', __('Translations'), 'widget_translation_listings', $widget_ops);
	register_widget_control('translatemyblog_widget_control1', 'widget_translation_control');


	$widget_ops = array('classname' => 'widget_other_translations', 'description' => __( "Other translations in this language. Only visible on translation pages themselves.") );
	wp_register_sidebar_widget('translation-other-posts', __('Translations in [language]'), 'widget_other_translation_listings', $widget_ops);
	register_widget_control('translatemyblog_widget_control2', 'widget_other_translation_control');
}


add_action('widgets_init', 'translatemyblog_widget_init');


function translatemyblog_adjacent_posts_join($join) {
	global $wpdb;
	return $join." INNER JOIN ".$wpdb->prefix."postmeta ON (p.ID = ".$wpdb->prefix."postmeta.post_id)";
}


function translatemyblog_adjacent_posts_where($where) {

	global $id, $wpdb;

	return $where." AND ".$wpdb->prefix."postmeta.meta_key = 'post_language_value' AND ".$wpdb->prefix."postmeta.meta_value = ".get_post_language_value($id);
}


add_filter('get_previous_post_join', 'translatemyblog_adjacent_posts_join');
add_filter('get_next_post_join', 'translatemyblog_adjacent_posts_join');

add_filter('get_previous_post_where', 'translatemyblog_adjacent_posts_where');
add_filter('get_next_post_where', 'translatemyblog_adjacent_posts_where');











function add_language_join($from) {
	return $from;
}


function add_language_where($where) {
	global $wpdb;
	global $wp_query;

        if(!is_single() && !is_admin()) {

           if(get_preferred_language() != "" && get_preferred_language() != get_option("translatemyblog_primary_language")) {

			   $post_ID_in_1 = "(SELECT DISTINCT ID FROM ".$wpdb->prefix."posts INNER JOIN ".$wpdb->prefix."postmeta ON (".$wpdb->prefix."posts.ID = ".$wpdb->prefix."postmeta.post_id) WHERE 1=1 AND ".$wpdb->prefix."postmeta.meta_key = 'post_language_value' AND ".$wpdb->prefix."postmeta.meta_value = ".get_preferred_language().")";

			   $post_ID_in_2 = "SELECT DISTINCT ".$wpdb->prefix."postmeta.meta_value FROM ".$wpdb->prefix."posts INNER JOIN ".$wpdb->prefix."postmeta ON (".$wpdb->prefix."posts.ID = ".$wpdb->prefix."postmeta.post_id) WHERE 1=1 AND ".$wpdb->prefix."postmeta.meta_key = 'post_parent_value' AND ID IN (SELECT DISTINCT ID FROM ".$wpdb->prefix."posts INNER JOIN ".$wpdb->prefix."postmeta ON (".$wpdb->prefix."posts.ID = ".$wpdb->prefix."postmeta.post_id) WHERE 1=1 AND ".$wpdb->prefix."postmeta.meta_key = 'post_language_value' AND ".$wpdb->prefix."postmeta.meta_value = ".get_preferred_language().")";

			   $post_ID_in_3 = "SELECT DISTINCT ID FROM ".$wpdb->prefix."posts INNER JOIN ".$wpdb->prefix."postmeta ON (".$wpdb->prefix."posts.ID = ".$wpdb->prefix."postmeta.post_id) WHERE 1=1 AND ".$wpdb->prefix."postmeta.meta_key = 'post_language_value' AND ".$wpdb->prefix."postmeta.meta_value != ".get_option("translatemyblog_primary_language");

			  $where_addition = "";
              
			  $where_addition = "AND (".$wpdb->prefix."posts.ID IN (".$post_ID_in_1.") OR (".$wpdb->prefix."posts.ID NOT IN (".$post_ID_in_2.") AND ".$wpdb->prefix."posts.ID NOT IN (".$post_ID_in_3.")))";

			  return $where." ".$where_addition;

           }
           else {

              return $where." AND (($wpdb->posts.ID NOT IN (SELECT DISTINCT ID FROM ".$wpdb->prefix."posts INNER JOIN ".$wpdb->prefix."postmeta ON (".$wpdb->prefix."posts.ID = ".$wpdb->prefix."postmeta.post_id) WHERE 1=1 AND ".$wpdb->prefix."postmeta.meta_key = 'post_language_value' AND ".$wpdb->prefix."postmeta.meta_value != ".get_option("translatemyblog_primary_language").")))";
           }
	}
        else {

           return $where;
        }


}


function selected_language_filter($notused) {

	global $wp_query;
	global $wpdb;

	//if(!is_single() && !is_admin() && (get_preferred_language() != "" && get_preferred_language() != get_option("translatemyblog_primary_language"))) {
		//$wp_query->query_vars['meta_key'] = 'post_language_value';
	//}

	add_filter('posts_join', 'add_language_join');
	add_filter('posts_where', 'add_language_where');
}


function get_preferred_language() {

	global $wpdb;

	$use_browser = intval(get_option("translatemyblog_use_browser"));

	if($use_browser) {
		$accepted_languages = get_languages('data');

		foreach($accepted_languages as $accepted_language) {

			$select_language = $wpdb->get_var("SELECT language_id FROM languages WHERE language_code = '".trim($accepted_language[1])."'");

			if($select_language != '') {
				return strval($select_language);
			}
		}
	}

	//if accepted languages are not in languages table, just return primary language
	$select_language = get_option("translatemyblog_primary_language");
	return strval($select_language);
}


function get_browser_language() {

	global $wpdb;

	$accepted_languages = get_languages('data');

	foreach($accepted_languages as $accepted_language) {

		$select_language = $wpdb->get_var("SELECT language_id FROM languages WHERE language_code = '".trim($accepted_language[1])."'");

		if($select_language != '') {
			return strval($select_language);
		}
	}
	
	return -1;
}


add_action('pre_get_posts', 'selected_language_filter');


function translatemyblog_add_google_analytics() {
?>
	<script type="text/javascript">
		var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
		document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
	</script>
	<script type="text/javascript">
		try {
			var pageTracker = _gat._getTracker("UA-7694325-1");
			pageTracker._trackPageview();
		}
		catch(err) {}
	</script>
<?php
}


if(intval(get_option("translatemyblog_enable_ganalytics"))) {
	add_action('wp_footer', 'translatemyblog_add_google_analytics');
}


function get_post_language_value($id) {

	$post_language_value = get_post_meta($id, "post_language_value", true);
	
	if($post_language_value != "") {
		return $post_language_value;
	}
	else {
		return get_option("translatemyblog_primary_language");
	}

}

include_once('php_language_detection.php');
?>