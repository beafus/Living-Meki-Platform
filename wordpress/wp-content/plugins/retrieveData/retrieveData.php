
<?php
/*
Plugin Name: retrieve data
Plugin URI: http://www.example.com
Description: I want to retrieve the data I want from my database
Author: Beatriz Fuster
Author URI: http://www.example.com
Text Domain: contact-form-7
Version: 1.0
*/

add_action('admin_menu','retrieveData_admin_actions');

function retrieveData_admin_actions(){
	add_options_page('retrieveData','retrieveData','manage_options',_FILE_,'retrieveData_admin');
	//first argument title og the page, second name of submenu, third capability(who can view submenu), four constant, display menu page.
}

/*
todo  esto hace que aparezca en el setting menu

esto es lo antiguo de prubea
<p>user emails: <?php $user_emails = $wpdb->get_col('SELECT user_email FROM ' . $wpdb->users); ?> 
<pre><?php echo var_dump($user_emails)?>  </pre></p>
*/

function retrieveData_admin(){

	global $wpdb;
	//$myresults = array();

?>

<div class="wrap">
<h2>We are retrieving the data of our database</h2>
<h3>This plugin will search...</h3>

<form id="formPrueba" action="" method="POST">
<p>Nombre:</p>
	<input type="text" name="name" aria-required="true">
<p>Email:</p>
	<input type="text" name="email" aria-required="true">
<br/>
	<input type="submit" name="prueba" value="Submit" class="button-primary">

</form>

<?php

$inputname = $_POST['name'];
$inputemail = $_POST['email'];

if(isset($_POST['prueba'])){

		

		$wpdb->insert( 
	'prueba', 
	array( 
		'name' => $inputname, 
		'email' => $inputemail
	), 
	array( 
		'%s', 
		'%s' 
	) 
);

	}


	echo "<p>".$inputname."</p>";
	echo "<p>".$inputemail."</p>";

	

?>




<p>Click button below to begin the search</p>
<br/>
<!--<form action="" method="POST">
	<input type="submit" name="search_applicants" value="Search" class="button-primary">
</form>
<br/>-->
<table class="widefat">
<thead>
<tr>
<th>Post Title</th>
<th>Post ID</th>
<th>Button</th>
</tr>
</thead>
<tfoot>
<tr>
<th>Post Title</th>
<th>Post ID</th>
<th>Button</th>
</tr>
</tfoot>
<tbody>


<?php



 //$user_emails = $wpdb->get_col('SELECT user_email FROM ' . $wpdb->users); 
//$myresults = $wpdb->get_results( 'SELECT name, formacion * FROM $wpdb->applicants_db');
//$myresults = $wpdb->get_results( 'SELECT ID, post_title  FROM ' . $wpdb->posts );
//$myresults = $wpdb->get_results( 'SELECT ID, post_title  FROM ' . $wpdb->posts. 'WHERE post_status = inherit ');
//if(isset($_POST['search_applicants'])){


	$myresults = $wpdb->get_results( 'SELECT id, name, email  FROM ' . applicants_db );

	//update_option('retrieveData_applicants', $myresults); //store the results in WP options table

//}
//else if (get_option('retrieveData_applicants')) {

	//$myresults = get_option('retrieveData_applicants');
//}



foreach ($myresults as $myresult ) {

	$selectedID = $myresult->id;

?>
	<tr>
	<?php
	echo "<td>".$myresult->name."</td>";
	echo "<td>".$myresult->email."</td>";
	echo "<td><form id='selectAsVolunteer' action='' method='POST'><input type='submit'  name='{$selectedID}' value='select volunteer' class='button-primary'></form></td></tr>";


//echo "<td>".$myresult->post_title."</td>";
//echo "<td>".$myresult->ID."</td>";
	?>
	<!--<td><form id="selectAsVolunteer" action="" method="POST"><input type="submit"  name="<?php $selectedID;?>" value="select volunteer" class="button-primary"></td>

-->
	</tr>


<?php


$selectedname = $myresult->name;
$selectedemail = $myresult->email;

if(isset($_POST[$selectedID])){

		

		$wpdb->insert( 
	'prueba', 
	array( 
		'name' => $selectedname, 
		'email' => $selectedemail
	), 
	array( 
		'%s', 
		'%s' 
	) 
);

	}

}













?>


</tbody>
</table>




</div>


<?php

}

/*
global $wpdb;
$results = $wpdb->get_results( 'SELECT * FROM wp_options WHERE option_id = 1', OBJECT );


$myrows = $wpdb->get_results( "SELECT id, name FROM mytable" );


$mylink = $wpdb->get_row("SELECT * FROM $wpdb->links WHERE link_id = 10");



INSERT INTO `wordpress`.`applicants_db` (`id`, `name`, `formacion`, `age`, `telf`, `email`, `profesion`, `enmeki`, `year`, `quincena`, `experiencia`, `aportar`, `obtener`, `enterado`) VALUES ('101', 'Beatriz', 'Ingeniera de Telecomunicaciones', '23', '606 514 574', 'beatrizfusterg@gmail.com', 'Application Developer', 'Si', '2013', 'Primera de Julio', 'Hospital y Meki', 'Todo mi conocimiento y energía', 'Aprender de los demas y a valorar', 'Me entere por mi amiga Paloma');


*/



?>




