<?php 

/*
Plugin Name: User Post Fetch API
Description: Api Fetch User Data and Post From Api
Version: 1.0.2
Author: Dayalu
Text Domain: DayaluBajpai
*/

//custom post type article start
function paradisoarticle(){ 
  $labels = array(
  'name' => 'Paradiso Articles',
        'singular_name' => 'Paradiso Article',
        'search_items' => 'Search Paradiso Article',
        'not_found' =>  'No Paradiso Article Found',
        'not_found_in_trash' => 'No Paradiso Article found in Trash', 
        'parent_item_colon' => '',
        'menu_name' => 'Paradiso Article',
);
 $args = array(
  'labels' => $labels,
  'public' => true,
  'has_archive' => true,
  'show_ui' => true,
  'capability_type' => 'post',
        'hierarchical' => false,
        'rewrite' => array('slug' => 'paradisoarticle'),
        'query_var' => true,
        'menu_icon' => 'dashicons-randomize',
        'supports' => array(
            'title',
            'editor',
            'excerpt',
            'trackbacks',
            'custom-fields',
            'comments',
            'revisions',
            'thumbnail',
            'author',
            'page-attributes'
 )
        );
        register_post_type( 'paradisoarticle', $args );
}

add_action( 'init', 'paradisoarticle' );

//class to bind function 

class paradisoApi{
  function __construct(){
    add_action('admin_menu',array($this,'adminSetting'));
    add_action('admin_init', array($this, 'settings'));
  }

  function settings(){
    add_settings_section('paradiso-users-api', null, null, 'paradiso-api');
    add_settings_field('paradisousers','Users API',array($this, 'usersHtmlApi'),'paradiso-api','paradiso-users-api');
register_setting('paradisoapi','paradisousers',array('sanitize_callback' => 'sanitize_text_field', 'default' => '0'));

add_settings_field('paradisopost','Posts API',array($this, 'postsHtmlApi'),'paradiso-api','paradiso-users-api');
register_setting('paradisoapi','paradisopost',array('sanitize_callback' => 'sanitize_text_field', 'default' => '0'));
  }

  function postsHtmlApi(){
    ?>
<input type="checkbox" name="paradisopost" value="1" <?php checked(get_option('paradisopost'),'1')?>>
    <?php
  }


  function usersHtmlApi(){
    ?>
<input type="checkbox" name="paradisousers" value="1" <?php checked(get_option('paradisousers'),'1')?>>
    <?php
  }

function adminSetting(){
  add_options_page('Paradiso Api Setting','Paradiso Settings','manage_options','paradiso-api',array($this,'HtmlApi'));
}

function HtmlApi(){
  ?>

  <div class="wrap">
    <h1>Paradiso API Settings</h1>

    <form action="options.php" method="POST">
<?php 
settings_fields('paradisoapi');
do_settings_sections('paradiso-api');
submit_button();
?>

    </form>
  </div>
  <?php
}

}

$Paradisoapi = new paradisoApi();

if(get_option('paradisousers') == 1){

add_action('init','addusersfff');

function addusersfff(){

		
$result = wp_remote_retrieve_body(wp_remote_get('https://jsonplaceholder.typicode.com/users'));

$results = json_decode($result);

 foreach($results as $results){

	$username = explode(" ", $results->name);
	
 wp_insert_user( array(
		'user_login' => $results->username,
		'user_pass' => 'password',
		'user_email' => $results->email,
		'first_name' => $username[0],
		'last_name' => $username[1],
		'display_name' => $results->name,
		'role' => 'author'
	  ));
	}
}
}


if(get_option('paradisousers') != 1){
  
//delete user

add_action('init','deleteuserfff');

function deleteuserfff(){

  $result = wp_remote_retrieve_body(wp_remote_get('https://jsonplaceholder.typicode.com/users'));

$results = json_decode($result);

 foreach($results as $results){
  global $wpdb;
   $wpdb->query( "DELETE FROM wp_users WHERE user_email =  '$results->email' " );
 }
}

//delete user end

}




$paradisoapidaya = 0;

//post from api to post data



add_action('init','postfromapi');

function postfromapi(){

    
  $post_args=array(
    'post_type'=>'paradisoarticle'
    );
    $postTypes = new WP_Query($post_args);
    $numberOfPosts=$postTypes->found_posts;
    
  if(get_option('paradisopost') == 1 && $numberOfPosts < 105){

  $resultpost = wp_remote_retrieve_body(wp_remote_get('https://jsonplaceholder.typicode.com/posts'));

  $resultspost = json_decode($resultpost);

		
$resultx = wp_remote_retrieve_body(wp_remote_get('https://jsonplaceholder.typicode.com/users'));

$resultsx = json_decode($resultx);
// foreach($resultsx as $resultsxz){

  // global $wpdb;
  // $ID = $wpdb->get_row( "SELECT ID FROM `wp_users` " );

  // var_dump($ID);



// }


$users = get_users( array( 'fields' => array( 'ID' ) ) );
foreach($users as $user){


  $ID[] = $user->ID;

  
 
}

// echo'<pre>';
// var_dump($ID);
// echo'</pre>';
// $i = 0;
// if($i <3){

//   echo $ID[$i];
// $i++;
// }

// exit();
$i = 0;
   foreach($resultspost as $resultspost){
   
  
if($i < 11 || $i<=0){

  $post_id = wp_insert_post(array (
    'post_type' => 'paradisoarticle',
    'post_author' => $ID[$i],
    'post_title' => $resultspost->title,
    'post_content' => $resultspost->body,
    'post_status' => 'publish',
 
    
  ));
  $i++;
} 
else if($i<=11){
  $i = 0;
  // echo "<script>alert('This is Message')</script>";
}
   }

  }


//post form api to post data end


if(get_option('paradisopost') !=1){
  global $wpdb;
   $wpdb->query("DELETE FROM `wp_posts` WHERE `post_type` = 'paradisoarticle'");
}

}