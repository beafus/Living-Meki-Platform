<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that other
 * 'pages' on your WordPress site will use a different template.
 *
 * @package WordPress
 * @subpackage Twenty_Thirteen
 * @since Twenty Thirteen 1.0
 */

get_header(); ?>



	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">

			<?php /* The loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<header class="entry-header">
						<?php if ( has_post_thumbnail() && ! post_password_required() ) : ?>
						<div class="entry-thumbnail">
							<?php the_post_thumbnail(); ?>
						</div>
						<?php endif; ?>

						<h1 class="entry-title"><?php the_title(); ?></h1>
					</header><!-- .entry-header -->

					<div class="entry-content">
						<?php the_content();




	global $wpdb;
	require 'PHPMailerAutoload.php';




$secondapplicants = $wpdb->get_results( 
	"
	SELECT *
	FROM applicants_db 
	"
);



?>



<?php

$turno = $_POST["turno"];
$coordinador = $_POST["coordinador"];

$actividad = $_POST["actividad"];

//echo "<p>".$turno."</p>";
//echo "<p>".$coordinador."</p>";
//echo "<p>".$actividad."</p>";

$allturno = $turno;
$allactividad = $actividad;
$allturno = $turno;

if($actividad =='todos'){

$allactividad = "(actividad='Cinema' OR actividad='Girls Club' OR actividad='Library' OR actividad='Scholarships' OR actividad='Classes' OR actividad='Projects' OR actividad='Workshops' OR actividad='Media' OR actividad='Sports' OR actividad='Material' OR actividad='Health')";

}else{

$allactividad = "actividad='{$actividad}'";
	
}

if($coordinador =='todos'){

$allcoordinador = "(responsable='Yes' OR responsable='No')";

}else{

$allcoordinador = "responsable='{$coordinador}'";
	
}

if($turno =='todos'){

$allturno = "(turno='1july' OR turno='2july' OR turno='1august' OR turno='2august')";

}else{

$allturno = "turno='{$turno}'";
	
}


$myemails = $wpdb->get_results( 
	"
	SELECT email
	FROM volunteers_db WHERE {$allactividad} AND  {$allturno} AND {$allcoordinador}
	"
);
	
$emails = array();


foreach ( $myemails as $myemail ) 
{

$selectedemail = $myemail->email;

array_push($emails,$selectedemail);


}


  $valueList = implode( ', ', $emails );
  //echo "<div class='infobox'>";
  //echo "<p><strong>Email: </strong>".$valueList."</p></div><br/>";



$msg = "Select to send an email";
if(isset($_POST["phpmailer"])){
	


foreach ( $myemails as $myemail ) 
{

$selectedemail = $myemail->email;

$email = htmlspecialchars($selectedemail);


$asunto = htmlspecialchars($_POST["asunto"]);
	$mensaje = $_POST["mensaje"];
	$adjunto = $_FILES["adjunto"]; 

	//$nombre = htmlspecialchars($_POST["nombre"]);

	$mail= new PHPMailer;
	$mail->isSMTP();
	$mail->SMTPAuth =true;
	$mail->Host = "smtp.gmail.com";
	$mail->Username= "example@gmail.com";
	
	$mail->Password = "Password";
	// you will have to enter your email and password to make it work
	$mail->SMTPSecure= "tls";
	$mail->Post= 587;



	$mail->From ="beatrizfusterg@gmail.com";
	$mail->FromName = "Living Meki";
	$mail->Subject = $asunto;
	$mail->addAddress($email, $nombre);
	$mail->addReplyTo("beatrizfusterg@gmail.com", "Living Meki");
	$mail->MsgHTML($mensaje);


	if($adjunto["size"]>0){
		$mail-> addAttachment($adjunto["tmp_name"], $adjunto["name"]);

	}

	if($mail->Send()){
		$msg = "Your email has been correctly sent to $valueList";
	}else{

		$msg = "There has been an error sending the email to $valueList .".$mail->ErrorInfo;
	}


}


	
}

?>



	<div class="mycontainer">
		
		<div class='infobox'><strong><?php echo $msg; ?></strong></div><br/>
		<form method="POST" enctype="multipart/form-data" action="<?php echo $_SERVER["PHP SELF"] ?>">

		<?php

echo "<table><tr><td><span><strong>Coordinador:  </strong></span><br/>";
echo "<select name='coordinador'>";
echo "<option  value='todos'>All</option>";
echo "<option  value='Yes'>Yes</option>";
echo "<option  value='No'>No</option></select></td>";

echo "<td><span><strong>Turno:  </strong></span><br/>";
echo "<select name='turno'>";
echo "<option  value='todos'>All</option>";
echo "<option  value='1july'>Primera de julio</option>";
echo "<option  value='2july'>Segunda de julio</option>";
echo "<option  value='1august'>Primera de agosto</option>";
echo "<option  value='2august'>Segunda de agosto</option></select></td>";

echo "<td><span><strong>Actividad:  </strong></span><br/>";
echo "<select name='actividad'>";
echo "<option  value='todos'>All</option>";
echo "<option  value='Girls Club'>Girls Club</option>";
echo "<option  value='Library'>Library</option>";
echo "<option  value='Scholarships'>Scholarships</option>";
echo "<option  value='Cinema'>Cinema</option>";
echo "<option  value='Classes'>Classes</option>";
echo "<option  value='Projects'>Projects</option>";
echo "<option  value='Workshops'>Workshops</option>";
echo "<option  value='Media'>Media</option>";
echo "<option  value='Sports'>Sports</option>";
echo "<option  value='Material'>Material</option>";
echo "<option  value='Health'>Health</option></select></td></tr></table>";


	
	

?>


			<table>
				<tr>
					<td>Email:</td>
					<td><input placeholder="Write here the email or select them with the dropdown menus leaving this is blanck" class="emailinput" type="text" name="email"></td>
				</tr>
				<tr>
					<td>Subject:</td>
					<td><input class="emailinput" type="text" name="asunto"></td>
				</tr>
				<tr>
					<td>Add file:</td>
					<td><input  class="uploadfile" type="file" name="adjunto"></td>
				</tr>
				<tr>
					<td>Message:</td>
					<td><textarea name="mensaje" cols="90" rows="20"></textarea></td>
				</tr>
			</table>
			<input type="hidden" name="phpmailer">
			<input type="submit" value="Send Email">
		</form>
	</div>





					
						<?php wp_link_pages( array( 'before' => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'twentythirteen' ) . '</span>', 'after' => '</div>', 'link_before' => '<span>', 'link_after' => '</span>' ) ); ?>
					</div><!-- .entry-content -->

					<footer class="entry-meta">
						<?php edit_post_link( __( 'Edit', 'twentythirteen' ), '<span class="edit-link">', '</span>' ); ?>
					</footer><!-- .entry-meta -->
				</article><!-- #post -->

				<?php comments_template(); ?>
			<?php endwhile; ?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>