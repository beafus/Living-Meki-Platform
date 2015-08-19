<?php
require 'PHPMailerAutoload.php';

$msg = null;
if(isset($_POST["phpmailer"])){
	$nombre = htmlspecialchars($_POST["nombre"]);
	$email = htmlspecialchars($_POST["email"]);
	$asunto = htmlspecialchars($_POST["asunto"]);
	$mensaje = $_POST["mensaje"];
	$adjunto = $_FILES["adjunto"]; 

	

	$mail= new PHPMailer;
	$mail->isSMTP();
	$mail->SMTPAuth =true;
	$mail->Host = "smtp.gmail.com";
	$mail->Username= "beatrizfusterg@gmail.com";
	$mail->Password = "narizdeculo";
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
		$msg = "Enhorabuena email enviado con éxito a $email";
	}else{

		$msg = "Ha ocurrido un error al enviar el email a $email".$mail->ErrorInfo;
	}
}

?>

<!DOCTYPE HTML>

<html>
	<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" type="text/css" href="style.css">
	</head>
	<body>

	<div class="mycontainer">
		<h1>Enviar Email con PHPMailer</h1>
		<strong><?php echo $msg; ?></strong>
		<form method="POST" enctype="multipart/form-data" action="<?php echo $_SERVER["PHP SELF"] ?>">
			<table>
				<tr>
					<td>Nombre del destinatario:</td>
					<td><input type="text" name="nombre"></td>
				</tr>
				<tr>
					<td>Email del destinatario:</td>
					<td><input type="text" name="email"></td>
				</tr>
				<tr>
					<td>Asunto:</td>
					<td><input type="text" name="asunto"></td>
				</tr>
				<tr>
					<td>Adjuntar Archivo:</td>
					<td><input type="file" name="adjunto"></td>
				</tr>
				<tr>
					<td>Mensaje:</td>
					<td><textarea name="mensaje" cols="30" rows="10"></textarea></td>
				</tr>
			</table>
			<input type="hidden" name="phpmailer">
			<input type="submit" value="Enviar Email">
		</form>
	</div>
		
	</body>
</html>