<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE); ?>
<!doctype html>
<html lang="de">
	<head>
		<meta charset="utf-8">
		<title>Projekt - SudokuGame</title>
		<link rel="stylesheet" href="style/bootstrap.min.css?ver=3.3.1" type="text/css" media="all">
		<link rel="stylesheet" href="style/style.css?ver=1.0" type="text/css" media="all">
		<script src="js/jquery.min.js?ver=1.11.1"></script>
		<script src="js/jquery-migrate.min.js?ver=1.2.1"></script>
		<script src="js/jquery.mobile.js?ver=1.4.5"></script>
		<script src="js/bootstrap.min.js?ver=3.3.1"></script>
		<script src="js/modernizr.custom.js?ver=2.8.3"></script>
	</head>
	<body>
		<?php @$action1 = $_GET['do'];
		
		switch($action1):
		
			case ("login"):
				$errors = array();
				require_once('inc/functions.php');

				if(isset($_POST['login']) && $_POST['login'] == "Login"):
					$user = mysqli_real_escape_string($db, $_POST['username']);
					$getpwd = mysqli_real_escape_string($db, $_POST['password']);
					$pwd = md5($salt.$getpwd);
				
					$query = $db->query("SELECT * FROM sdk_user WHERE username = '$user' AND password = '$pwd'");
					
					// Check Username Feld leer
					if(empty($_POST["username"])): $errors["user_text"] = '<span class="texterror">Bitte ein Username eingeben!</span>'; $errors["user_style"] = 'class="formerror"'; endif;
					// Check Passwort Feld leer
					if(empty($_POST["password"])): $errors["password_text"] = '<span class="texterror">Bitte geben Sie ein Passwort ein!</span>'; $errors["password_style"] = 'class="formerror"'; endif;
					// Check ob Username und Passwort richtig
					if($query->num_rows == 0): $errors["incorrect_text"] = '<span class="texterror">Der Benutzername und/oder das Passwort sind falsch!</span>'; $errors["user_style"] = 'class="formerror"'; $errors["password_style"] = 'class="formerror"'; endif;
				endif;
				
				if(isset($_POST['login']) && $_POST['login'] == "Login" && count($errors) == 0):
						$user = mysqli_real_escape_string($db, $_POST['username']);
						$getpwd = mysqli_real_escape_string($db, $_POST['password']);
						$pwd = md5($salt.$getpwd);
						$query = $db->query("SELECT * FROM sdk_user WHERE username = '$user' AND password = '$pwd'");
						$row = $query->fetch_object();
						$_SESSION['username'] = $row->username;
						$_SESSION['token'] = md5($hash.$row->id);
						echo '<meta http-equiv="refresh" content="0; URL=index.php?do=include">';
				else:
					echo isset($errors["user_text"]) ? $errors["user_text"]:"";
					echo isset($errors["password_text"]) ? $errors["password_text"]:""; 
					echo isset($errors["incorrect_text"]) ? $errors["incorrect_text"]:""; ?>
					
					<form action="index.php?do=login" method="post">
						<p>Username:<br />
						<input type="text" name="username" value="<?php echo isset($_POST["username"])?$_POST["username"]:""; ?>"></p>
						<p>Passwort:<br />
						<input type="password" name="password" value="<?php echo isset($_POST["password"])?$_POST["password"]:""; ?>"></p>
						<p><input type="submit" name="login" value="Login"></p>
					</form>
					<p><a href="index.php?do=register">Registrieren</a> || <a href="index.php?do=forgot">Passwort vergessen?</a></p>
				<?php endif;
			break;
				
			case 'logout':
				session_unset();
				session_destroy();
				redirect('index.php');
				exit;
			break;
			
			case 'register':
				require_once('inc/functions.php');
				$errors = array();
				
				if(isset($_POST['register']) && $_POST['register'] == "Registrieren"):
					$user = mysqli_real_escape_string($db, $_POST['username']);
					$email = mysqli_real_escape_string($db, $_POST['email']);
					
					$query1 = $db->query("SELECT * FROM sdk_user WHERE username = '$user'");
					$query2 = $db->query("SELECT * FROM sdk_user WHERE email = '$email'");
					
					// Check Username Feld leer
					if(empty($_POST["username"])): $errors["user_text"] = '<span class="texterror">Bitte ein Username eingeben!</span>'; $errors["user_style"] = 'class="formerror"'; endif;
					// Check Username existiert
					if($query1->num_rows > 0): $errors["user_text"] = '<span class="texterror">Der Benutzername existiert bereits, bitte wählen Sie einen anderen!</span>'; $errors["user_style"] = 'class="formerror"'; endif;
					// Check E-Mail Feld leer
					if(empty($_POST["email"])): $errors["email_text"] = '<span class="texterror">Bitte geben Sie eine E-Mail Adresse ein!</span>'; $errors["email_style"] = 'class="formerror"'; endif;
					// Check E-Mail-Addresse existiert
					if($query2->num_rows > 0): $errors["email_text"] = '<span class="texterror">Diese E-Mail Adresse wurde bereits für eine Registrierung genutzt. Falls Sie Ihr Passwort vergessen haben, können Sie es <a href="index.php?do=forgot">HIER</a> zurücksetzen lassen.</span>'; $errors["email_style"] = 'class="formerror"'; endif;
					// Check E-Mail-Adresse ist E-Mail-Adresse
					if(checkMail($_POST["email"]) == false): $errors["email_text"] = '<span class="texterror">Bitte geben Sie eine gültige E-Mail Adresse ein!</span>'; $errors["email_style"] = 'class="formerror"'; endif;
					// Check Passwort Feld leer
					if(empty($_POST["password"])): $errors["password_text"] = '<span class="texterror">Bitte geben Sie ein Passwort ein!</span>'; $errors["password_style"] = 'class="formerror"'; endif;
					// Check Passwortwiederholung Feld leer
					if(empty($_POST["repassword"])): $errors["repassword_text"] = '<span class="texterror">Bitte geben Sie das gleiche Passwort zweimal ein!</span>'; $errors["repassword_style"] = 'class="formerror"'; endif;
					// Check Username = Passwort
					if($_POST["username"] == $_POST["password"]): $errors["user_pwd_text"] = '<span class="texterror">Der Benutzername darf nicht mit dem Passwort übereinstimmen!</span>'; $errors["user_style"] = 'class="formerror"'; $errors["password_style"] = 'class="formerror"'; $errors["repassword_style"] = 'class="formerror"'; endif;
					// Check Passwortlänge min. 6 Zeichen
					if(strlen($_POST["password"]) <= 5): $errors["password_text"] = '<span class="texterror">Das Passwort ist zu kurz! Das Passwort muss aus mind. 6 Zeichen bestehen!</span>'; $errors["password_style"] = 'class="formerror"'; $errors["repassword_style"] = 'class="formerror"'; endif;
					// Check Passwort = Passwortwiederholung
					if($_POST["password"] != $_POST["repassword"]): $errors["password_invalid_text"] = '<span class="texterror">Die eingegebenen Passwörter stimmen nicht überein!</span>'; $errors["password_style"] = 'class="formerror"'; $errors["repassword_style"] = 'class="formerror"'; endif;
					// Check Nutzungsbedingungen und AGB gelesen
					if(!$_POST["termsofuse"] == 'confirmed'): $errors["termsofuse_text"] = '<span class="texterror">Die Nutzungsbedingungen und die Allgemeinen Geschäftsbedingungen müssen akzeptiert werden!</span>'; $errors["termsofuse_style_start"] = '<span class="texterror">'; $errors["termsofuse_style_end"] = '</span>'; endif;
				endif;
				
				if(isset($_POST['register']) && $_POST['register'] == "Registrieren" && count($errors) == 0):
					$user = mysqli_real_escape_string($db, $_POST['username']);
					$passwd = mysqli_real_escape_string($db, $_POST['password']);
					$pwd = md5($salt.$passwd);
					$mail = mysqli_real_escape_string($db, $_POST['email']);
				
					$query = 'INSERT INTO sdk_user (username, password, email) VALUES (?, ?, ?)';
					$doit = $db->prepare($query);
					$doit->bind_param('sss', $user, $pwd, $mail);
					$doit->execute();
					
					if ($doit->affected_rows == 1):
						$query = $db->query("SELECT id FROM sdk_user WHERE username = '$user' AND password = '$pwd' AND email = '$mail'");
						$row = $query->fetch_object();

						$token = md5($hash.$row->id);
					
						$query = 'INSERT INTO sdk_user (hash) VALUES (?)';
						$doit = $db->prepare($query);
						$doit->bind_param('s', $token);
						$doit->execute();
						
						$receiver 	= $mail;
						$sender		= "noreply@phoenixgames.de";
						$subject 	= "SudokuGame - Registrierung";
						
						$mail_head	= "MIME-Version: 1.0\r\n";
						$mail_head .= "Content-type: text/plain; charset=UTF-8\r\n";
						$mail_head .= "FROM: SudokuGame <".$sender.">\r\n";
						
						$mail_body	= "Hallo $user,\r\n\r\n";
						$mail_body .= "Sie können sich nun mit folgenden Daten einloggen:\r\n\r\n";
						$mail_body .= "Benutzername: $user\r\n";
						$mail_body .= "Passwort: $passwd\r\n\r\n\r\n";
						$mail_body .= "Liebe Grüße\r\n";
						$mail_body .= "Ihr SudokuGame Team";
						
						mail($receiver, $subject, $mail_body, $mail_head);
						
						echo 'Registrierung erfolgreich!';
					else:
						echo 'Die Registrierung ist fehlgeschlagen!';
					endif;
				else:
					echo isset($errors["user_text"]) ? $errors["user_text"]:"";
					echo isset($errors["email_text"]) ? $errors["email_text"]:"";
					echo isset($errors["password_short"]) ? $errors["password_short"]:"";
					echo isset($errors["password_text"]) ? $errors["password_text"]:"";
					echo isset($errors["repassword_text"]) ? $errors["repassword_text"]:"";
					echo isset($errors["user_pwd_text"]) ? $errors["user_pwd_text"]:"";
					echo isset($errors["password_invalid_text"]) ? $errors["password_invalid_text"]:"";
					echo isset($errors["termsofuse_text"]) ? $errors["termsofuse_text"]:""; ?>
					
					<form action="index.php?do=register" method="post">
						<span class="reg">Username:</span><br> 
						<input type="text" name="username" <?php echo isset($errors["user_style"])?$errors["user_style"]:""; ?> value="<?php echo isset($_POST["username"])?$_POST["username"]:""; ?>"><br> 
						<br> 
						<span class="reg">E-Mail:</span><br> 
						<input type="text" name="email" <?php echo isset($errors["email_style"])?$errors["email_style"]:""; ?> value="<?php echo isset($_POST["email"])?$_POST["email"]:""; ?>"><br>
						<br> 
						<span class="reg">Passwort:</span><br> 
						<input type="password" name="password" <?php echo isset($errors["password_style"])?$errors["password_invalid_style"]:""; ?> value="<?php echo isset($_POST["password"])?$_POST["password"]:""; ?>"><br>
						<br> 
						<span class="reg">Passwort wiederholen:</span><br> 
						<input type="password" name="repassword" <?php echo isset($errors["repassword_style"])?$errors["password_invalid_style"]:""; ?> value="<?php echo isset($_POST["repassword"])?$_POST["repassword"]:""; ?>"><br>
						<br>
						<input type="checkbox" name="termsofuse" value="confirmed"> <?php echo isset($errors["termsofuse_style_start"])?$errors["termsofuse_style_start"]:""; ?>Ich habe die Nutzungsbedingungen und die Allgemeinen Geschäftsbedingungen gelesen und akzeptiere diese.<?php echo isset($errors["termsofuse_style_end"])?$errors["termsofuse_style_end"]:""; ?></input><br>
						<br>
						<input type="submit" name="register" value="Registrieren">
					</form>
					<p><a href="index.php">Zurück</a> || <a href="index.php?do=forgot">Passwort vergessen?</a></p>
				<?php endif;
			break;
			
			case 'forgot':
				require_once('inc/functions.php');
				$errors = array();
				
				if(isset($_POST['resetpw']) && $_POST['resetpw'] == "Passwort zurücksetzen"):
					$email = mysqli_real_escape_string($db, $_POST['email']);
				
					$query = $db->query("SELECT * FROM sdk_user WHERE email = '$email'");
					
					// Check E-Mail Feld leer
					if(empty($_POST["email"])): $errors["email_text"] = '<span class="texterror">'.$reg_mail_empty.'</span>'; $errors["email_style"] = 'class="formerror"'; endif;
					// Check E-Mail-Addresse existiert nicht
					if($query->num_rows == 0): $errors["email_text"] = '<span class="texterror">'.$reg_mail_exist.'</span>'; $errors["email_style"] = 'class="formerror"'; endif;
					// Check E-Mail-Adresse ist E-Mail-Adresse
					if(checkMail($_POST["email"]) == false): $errors["email_text"] = '<span class="texterror">'.$reg_mail_fail.'</span>'; $errors["email_style"] = 'class="formerror"'; endif;
				endif;
				
				if(isset($_POST['resetpw']) && $_POST['resetpw'] == "Passwort zurücksetzen"  && count($errors) == 0):
					$email = mysqli_real_escape_string($db, $_POST['email']);
					$query = $db->query("SELECT * FROM sdk_user WHERE email = '$email'");
					$row = $query->fetch_object();

					if($email == $row->email):
						$getpw = genPW();
						$newpw = md5($salt.$getpw);
						$db->query("UPDATE sdk_user SET password = '$newpw' WHERE email = '$row->email'");
						if($db->affected_rows == 1):
							$receiver 	= $row->email;
							$sender		= "noreply@phoenixgames.de";
							$subject 	= "SudokuGame - Ihr neues Passwort";
							
							$mail_head	= "MIME-Version: 1.0\r\n";
							$mail_head .= "Content-type: text/plain; charset=UTF-8\r\n";
							$mail_head .= "FROM: SudokuGame <".$sender.">\r\n";
							
							$mail_body	= "Hallo $row->username,\r\n\r\n";
							$mail_body .= "Ihr neues Passwort lautet: $getpw\r\n\r\n";
							$mail_body .= "Sie können sich nun wie gewohnt einloggen.\r\n\r\n\r\n";
							$mail_body .= "Liebe Grüße\r\n";
							$mail_body .= "Ihr SudokuGame Team";
							
							mail($receiver, $subject, $mail_body, $mail_head);
							
							echo 'Das neue Passwort wurde Ihnen per Mail zugesendet.';
						else:
							echo 'Es konnte leider kein neues Passwort zugeschickt werden!';
						endif;
					endif;
				else:
					echo isset($errors["email_text"]) ? $errors["email_text"]:""; ?>
					<form action="index.php?do=forgot" method="post">
						<p>E-Mail Adresse:<br />
						<input type="text" name="email" <?php echo isset($errors["email_style"])?$errors["email_style"]:""; ?> value="<?php echo isset($_POST["email"])?$_POST["email"]:""; ?>"></p>
						<p><input type="submit" name="resetpw" value="Passwort zurücksetzen"></p>
					</form>
					<p><a href="index.php?do=register">Registrieren</a> || <a href="index.php">Zurück</a></p>
				<?php endif;
			break;
			
			case 'include':
				if(!isset($_SESSION['username']) && !isset($_SESSION['token'])):
					redirect('index.php');
				endif;
				require_once('inc/functions.php'); ?>
				
				<p><a href="index.php?do=include&path=show_profil">Profil</a> || <a href="index.php?do=include&path=edit_profil">Profil bearbeiten</a></p>
				
				<?php @$action2 = $_GET['path'];
				switch($action2):
					case 'show_profil':
						$token = mysqli_real_escape_string($db, $_SESSION['token']);
						$dir = 'http://'.$_SERVER['HTTP_HOST'].'/sudoku/upload/';
						$query = $db->query("SELECT * FROM sdk_user WHERE hash = '$token'");
						$row = $query->fetch_object(); ?>
						
						<table border="0" width="100%" cellspacing="0" cellpadding="0">
							<tbody>
								<tr>
									<td width="25%">
										<img src="<?php echo $dir.$row->profil_img; ?>" border="0" width="250" height="250" alt="Profilbild von <?php echo $row->username; ?>" title="Profilbild von <?php echo $row->username; ?>" />
									</td>
									<td width="75%">
										<b>Benutzername:</b> <?php echo $row->username; ?><br />
										<br />
										<h2>Highscores</h2><br />
										<b>Novice:</b> <?php echo $row->highscore_novice; ?><br />
										<b>Easy:</b> <?php echo $row->highscore_easy; ?><br />
										<b>Medium:</b> <?php echo $row->highscore_medium; ?><br />
										<b>Hard:</b> <?php echo $row->highscore_hard; ?>
									</td>
								</tr>
							</tbody>
						</table>
					<?php break;
					
					case 'edit_profil';
						if(isset($_POST['upload']) && $_POST['upload'] == "Hochladen"):
							$target_dir = "upload/";
							$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
							$uploadOk = 1;
							$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
							// Check if image file is a actual image or fake image
							$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
							if($check !== false):
								echo "File is an image - " . $check["mime"] . ".";
								$uploadOk = 1;
							else:
								echo "File is not an image.";
								$uploadOk = 0;
							endif;
							// Check if file already exists
							if (file_exists($target_file)):
								echo "Sorry, file already exists.";
								$uploadOk = 0;
							endif;
							// Check file size
							if ($_FILES["fileToUpload"]["size"] > 500000):
								echo "Sorry, your file is too large.";
								$uploadOk = 0;
							endif;
							// Allow certain file formats
							if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif"):
								echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
								$uploadOk = 0;
							endif;
							// Check if $uploadOk is set to 0 by an error
							if ($uploadOk == 0):
								echo "Sorry, your file was not uploaded.";
							// if everything is ok, try to upload file
							else:
								if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)):
									$token = mysqli_real_escape_string($db, $_SESSION['token']);
									$file = $_FILES["fileToUpload"]["name"];
									$db->query("UPDATE sdk_user SET profil_img = '$file' WHERE hash = '$token'");
									
									echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
								else:
									echo "Sorry, there was an error uploading your file.";
								endif;
							endif;
						endif; ?>
							<form action="index.php?do=include&path=edit_profil" method="post" enctype="multipart/form-data">
								Select image to upload:
								<input type="file" name="fileToUpload" id="fileToUpload">
								<input type="submit" value="Hochladen" name="upload">
							</form>
						<?php
					break;
					
				endswitch;
			break;
			
			default: ?>
				<form action="index.php?do=login" method="post">
					<p>Username:<br />
					<input type="text" name="username" value="<?php echo isset($_POST["username"])?$_POST["username"]:""; ?>"></p>
					<p>Passwort:<br />
					<input type="password" name="password" value="<?php echo isset($_POST["password"])?$_POST["password"]:""; ?>"></p>
					<p><input type="submit" name="login" value="Login"></p>
				</form>
				<p><a href="index.php?do=register">Registrieren</a> || <a href="index.php?do=forgot">Passwort vergessen?</a></p>
			<?php break;
			
		endswitch; ?>
	</body>
</html>
