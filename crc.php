<?php
	$dir = 0;
	$code_correct = true;
	$msg_correct = true;

	if(isset($_POST['btn_submit']))
	{
		$msg_correct = false;
		$code_correct = false;

		$continue_msg = false;
		$continue_code = false;

		if (preg_match("#^[01]+$#", htmlspecialchars($_POST['msg'])))
		{
			$msg_correct = true;
			$continue_msg = true;
		}
		if (preg_match("#^1[01]*$#", htmlspecialchars($_POST['code'])))
		{
			$code_correct = true;
			$continue_code = true;
		}

		if($continue_msg && $continue_code)
		{
			if(isset($_POST['msg_direction']))
			{
				include("class_crc.php");
				$objet_crc = new Crc(htmlspecialchars($_POST['msg']),htmlspecialchars($_POST['code']));
				$reste = $objet_crc->r_x();
				if($_POST['msg_direction'] == 'emission') 
				{
					$dir = 1;
				}
				else if($_POST['msg_direction'] == 'reception')
				{
					$dir = 2;
					if($reste == 0)
						$confirmed = true;
					else
						$confirmed = false;
				}
			}
		}
	}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<link rel="shortcut icon" type="image/x-icon" href="crc_ico.jpg" />
	<title>Code de Redondance Cyclique - CRC</title>
	 <!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
	<!-- jQuery library -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<!-- Popper JS -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
	<!-- Latest compiled JavaScript -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>

		<link href='https://fonts.googleapis.com/css?family=Sofia' rel='stylesheet'>

		<style type="text/css">
			/*body {
			    font-family: 'Sofia';font-size: 22px;
			}*/
			.btn, .form-check-inline {margin-bottom: 10px;}
			.form-control {padding: 20px;}
		</style>
</head>
<body>
	<div class="jumbotron">
  		<h1>Code de Redondance Cyclique - CRC</h1>
  		<p>CRC : code de redondance cyclique ou cyclic redundancy check est un outil logiciel qui permet de garantir une certaine conformité des données entre l'émission et la réception. Lors de l'emission du message le CRC effectue l'ajout d'une combinaison de comparaison et de vérification à l'aide d'une procédure de hachage.A la réception de ce dernier il déclenche le mécanisme de detection de conformité et d'erreurs</p>
	</div>

	<?php if($dir == 2) { if($confirmed) { ?>
	<div class="alert alert-success">
  		<strong>Code Accepté!</strong> Conformité message confirmée.
	</div>
		<?php } else if(!$confirmed) { ?>
	<div class="alert alert-danger">
  		<strong>Code refusé!</strong> Conformité message rejetée.
	</div>
	<?php } } ?>

	<div class="offset-sm-2 col-sm-8 jumbotron">
  		<h1>Contrôle d'erreurs :</h1>
  		 <form action="" method="POST">
  		 	 <div class="form-check-inline">
			  <label class="form-check-label">
			    <input type="radio" class="form-check-input" name="msg_direction" value="emission" <?php if($dir == 0 || $dir == 1) echo 'checked'; ?>>Emission
			  </label>
			</div>
			<div class="form-check-inline">
			  <label class="form-check-label">
			    <input type="radio" class="form-check-input" name="msg_direction" value="reception" <?php if($dir == 2) echo 'checked'; ?>>Reception
			  </label>
			</div>
			<div class="form-check-inline disabled">
			</div> 
		  <div class="form-group">
		    <label for="msg">Message à envoyer : <?php if(!$msg_correct) echo '<span class="text-danger">Veuillez saisir une donnée binaire</span>'; ?></label>
		    <input type="text" class="form-control" name="msg" id="msg" <?php if(isset($_POST['msg'])) echo 'value="'.$_POST['msg'].'"'; ?>>
		  </div>
		  <div class="form-group">
		    <label for="code">Code de calcul : <?php if(!$code_correct) echo '<span class="text-danger">Veuillez saisir une donnée binaire - MIN 1</span>'; ?></label>
		    <input type="text" class="form-control" name="code" id="code" <?php if(isset($_POST['code'])) echo 'value="'.$_POST['code'].'"'; ?>>
		  </div>
		  <button type="submit" class="btn btn-primary" name="btn_submit">Valider</button>

		  <?php if($dir == 1 && isset($reste)) { ?>
		  <div class="form-group">
		    <label for="cle_msg">Clé :</label>
		    <input type="text" class="form-control" id="cle_msg" disabled style='background: #ffffff;' value="<?php echo $reste; ?>">
		  </div>
		  <div class="form-group">
		    <label for="complete_msg">Message complet :</label>
		    <input type="text" class="form-control" id="complete_msg" disabled style='background: #ffffff;' value="<?php echo $_POST['msg'].$reste; ?>">
		  </div>
		 <?php } ?>

		</form> 
	</div>
</body>
</html>