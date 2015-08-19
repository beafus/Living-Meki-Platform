<?php 
get_header();
 ?>

<!-- your custom php code goes here -->




<?php





function applicationForm(){

	global $wpdb;


?>

<div class="wrap">
<h2>We are retrieving the data of our database</h2>
<h3>This plugin will search...</h3>

<form id="applicantsForm" action="" method="POST">
<p>Nombre:</p>
	<input type="text" name="name" aria-required="true">
<p>Formacion:</p>
	<input type="text" name="formacion" aria-required="true">
<p>Edad:</p>
	<input type="text" name="age" aria-required="true">
<p>Teléfono de contacto:</p>
	<input type="text" name="telf" aria-required="true" placeholder="666 777 888">
<p>Correo electrónico:</p>
	<input type="text" name="email" aria-required="true" placeholder="example@gmail.com">
<p>Profesión:</p>
	<input type="text" name="profesion" aria-required="true" >
<p>He ido antes a Meki:</p>
<input type="radio" name="enmeki" value="Si" aria-required="true" >Si<br>
<input type="radio" name="enmeki" value="No" aria-required="true" >No


<p>Indica año: </p>	
	<input type="text" name="year" >

<p>Podría ir a Meki:</p>
	Primera de julio <input type="checkbox" name="julio_primera"  value="Si" >
	Segunda de julio <input type="checkbox" name="julio_segunda"  value="Si"  >
	Primera de agosto <input type="checkbox" name="agosto_primera"  value="Si">
	Segunda de agosto <input type="checkbox" name="agosto_segunda"  value="Si" >
	Cualquiera <input type="checkbox" name="cualquiera"  value="Si" >

	

<p>Me he enterado del proyecto Living Meki por:</p>
	<input type="textarea" cols= "40" rows="10" name="enterado" aria-required="true">
<p>Mi experiencia en voluntariado es:</p>
	<input type="textarea" cols= "40" rows="10" name="experiencia" aria-required="true">
<p>Lo que pienso que puedo aportar a Living Meki es:</p>
	<input type="textarea" cols= "40" rows="10"  name="aportar" aria-required="true">
<p>Lo que espero obtener de esta experiencia es:</p>
	<input type="textarea" cols= "40" rows="10" name="obtener" aria-required="true">
<br/>
	<input type="submit" name="guardardatos" value="Submit" class="button-primary">

</form>


<?php


$inputname = $_POST['name'];
$inputedad = $_POST['age'];
$inputemail = $_POST['email'];
$inputtelf = $_POST['telf'];
$inputformacion = $_POST['formacion'];
$inputprofesion = $_POST['profesion'];
$inputenmeki = $_POST['enmeki'];
$inputyear = $_POST['year'];


if(isset($_POST['julio_primera'])){
$quincena1 = $_POST['julio_primera'];
}else{
	$quincena1 = "No";

}
if(isset($_POST['julio_segunda'])){
 $quincena2 = $_POST['julio_segunda'];
}else{
	$quincena2 = "No";

}
if(isset($_POST['agosto_primera'])){
 $quincena3 = $_POST['agosto_primera'];
}else{
	$quincena3 = "No";

}
if(isset($_POST['agosto_segunda'])){
 $quincena4 = $_POST['agosto_segunda'];
}else{
	$quincena4 = "No";
}
if(isset($_POST['cualquiera'])){
 
$cualquiera = $_POST['cualquiera'];
}else{
	$cualquiera = "No";
}




$inputexperiencia = $_POST['experiencia'];
$inputaportar = $_POST['aportar'];
$inputobtener = $_POST['obtener'];
$inputenterado = $_POST['enterado'];



if(isset($_POST['guardardatos'])){

		

		$wpdb->insert( 
	'applicants_db', 
	array( 
		'name' => $inputname, 
		'age' => $inputedad,
		'email' => $inputemail,
		'telf' => $inputtelf, 
		'formacion' => $inputformacion,
		'profesion' => $inputprofesion, 
		'enmeki' => $inputenmeki,
		'year' => $inputyear, 
		'julio_primera' => $quincena1,
		'julio_segunda' => $quincena2,
		'agosto_primera' => $quincena3,
		'agosto_segunda' => $quincena4,
		'cualquiera' => $cualquiera,
		'experiencia' => $inputexperiencia, 
		'aportar' => $inputaportar,
		'obtener' => $inputobtener, 
		'enterado' => $inputenterado
	), 
	array( 
		'%s', 
		'%d',
		'%s', 
		'%s', 
		'%s', 
		'%s', 
		'%s', 
		'%s', 
		'%s',
		'%s', 
		'%s', 
		'%s',
		'%s',
		'%s', 
		'%s', 
		'%s',
		'%s'
	) 
);

	}

	

	


	
	echo "<p>".$inputname."</p>";
	echo "<p>".$inputedad."</p>";
	echo "<p>".$inputemail."</p>";
	echo "<p>".$inputtelf."</p>";
	echo "<p>".$inputformacion."</p>";
	echo "<p>".$inputprofesion."</p>";
	echo "<p>".$inputenmeki."</p>";
	echo "<p>".$inputyear."</p>";
	
	echo "<p>".$quincena1."</p>";
	echo "<p>".$quincena2."</p>";
	echo "<p>".$quincena3."</p>";
	echo "<p>".$quincena4."</p>";
	echo "<p>".$cualquiera."</p>";
	
	echo "<p>".$inputexperiencia."</p>";
	echo "<p>".$inputaportar."</p>";
	echo "<p>".$inputobtener."</p>";
	echo "<p>".$inputenterado."</p>";



	

?>



</div>


<?php

}



?>



<?php get_footer(); ?>


