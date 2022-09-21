<?php
session_start();

require '../vendor/autoload.php';

use CognitoApp\CognitoClient;

$client = new CognitoClient();
$client->initialize();


if(isset($_POST['action'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if($_POST['action'] === 'register') {
        $email = $_POST['email'] ?? '';
        $error = $client->signup($username, $email, $password);

        if(empty($error)) {
            $confirm = true;
        }
    }

    if($_POST['action'] === 'confirm') {
        $username = $_POST['username'] ?? '';
        $confirmation = $_POST['confirmation'] ?? '';
        $confirm = true;
        $error = $client->confirmSignup($username, $confirmation);
        
        if(empty($error)) {
            header('Location: home.php');
            exit;
        }        
    }
}

$message = '';
if(isset($_GET['reset'])) {
    $message = 'Your password has been reset. You can now login with your new password';
}


?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.101.0">
    <title>Blog Template · Bootstrap v5.2</title>
    <!-- Bootstrap -->
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"/>
    <!-- Google Fonts Roboto -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet"/>
    <!-- Custom styles for this template -->
    <link href="/css/style.css" rel="stylesheet">
  </head>
  <body>
    
    <main role="main" class="container">

        <div class="row justify-content-center">
            <div class="col-lg-5">
                <div class="card shadow-lg mt-5 border-0">
                    <div class="card-header p-0">
                        <div class="row no-gutters">
                            <div class="col-6 bg-white p-3 text-center">
                                <a href="index.php" class="text-dark">Log in</a>
                            </div>
                            <div class="col-6 bg-primary p-3 text-center">
                                <a href="register.php" class="text-white">Register</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="" method="post" autocomplete="off">
                            <?php if(!isset($confirm)) { ?>
                                <h2>Register</h2>
                                <p>Please enter your information below to register</p>
                                <hr>
                                <?php echo isset($error) ? '<p class="text-danger">'.$error.'</p>' : ''; ?>
                                <?php echo isset($message) ? '<p class="text-success">'.$message.'</p>' : ''; ?>
                                <div class="form-group">
                                    <input type="text" class="form-control" name="username" placeholder="Username" required="required" autocomplete="nope">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" name="email" placeholder="E-Mail" required="required" autocomplete="nope">
                                </div>
                                <div class="input-group mb-3" id="show_hide_password">
                                    <input type="password" class="form-control" name="password" placeholder="Password" required="required" autocomplete="new-password">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><a href="" class=""><i class="fa fa-eye-slash" aria-hidden="true"></i></a></span>
                                    </div>
                                </div>      
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-lg btn-block">Register</button>
                                </div>
                                <input type='hidden' name='action' value='register' />
                            <?php } else { ?>
                                <h2>Confirm</h2>
                                <p>Please confirm your E-Mail address!</p>
                                <hr>
                                <?php echo isset($error) ? '<p class="text-danger">'.$error.'</p>' : ''; ?>
                                <?php echo isset($message) ? '<p class="text-success">'.$message.'</p>' : ''; ?>
                                <div class="form-group">
                                    <input type="text" class="form-control" name="username" placeholder="Username" value='<?php echo $username;?>' autocomplete="nope">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" name="confirmation" placeholder="Confirmation code" required="required" autocomplete="nope">
                                </div>    
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-lg btn-block">Confirm</button>
                                </div>
                                <input type='hidden' name='action' value='confirm' />
                            <?php } ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </main>
    
    <script src="/js/jquery-3.6.0.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
	<script>	
	$(document).ready(function() {
		$("#show_hide_password a").on('click', function(event) {
			event.preventDefault();
			if($('#show_hide_password input').attr("type") == "text"){
				$('#show_hide_password input').attr('type', 'password');
				$('#show_hide_password i').addClass( "fa-eye-slash" );
				$('#show_hide_password i').removeClass( "fa-eye" );
			}else if($('#show_hide_password input').attr("type") == "password"){
				$('#show_hide_password input').attr('type', 'text');
				$('#show_hide_password i').removeClass( "fa-eye-slash" );
				$('#show_hide_password i').addClass( "fa-eye" );
			}
		});
	});
	</script>
  </body>
</html>