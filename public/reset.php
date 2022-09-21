<?php
session_start();

require '../vendor/autoload.php';

use CognitoApp\CognitoClient;

$client = new CognitoClient();
$client->initialize();


$entercode = false;

if(isset($_POST['action'])) {

    if($_POST['action'] === 'code') {
        $username = $_POST['username'] ?? '';

        $error = $client->sendPasswordResetMail($username);

        if(empty($error)) {
            header('Location: reset.php?username=' . $username);
        }
    }

    if($_POST['action'] == 'reset') {

        $code = $_POST['code'] ?? '';
        $password = $_POST['password'] ?? '';
        $username = $_GET['username'] ?? '';

        $error = $client->resetPassword($code, $password, $username);

        if(empty($error)) {
            header('Location: index.php?reset');
        }
    }
}

if(isset($_GET['username'])) {
    $entercode = true;
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
                                <a href="forgotpassword.php" class="text-white">Forgot Password</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="" method="post" autocomplete="off">
                            <?php if(isset($entercode) && $entercode) { ?>

                                <h2>Reset password</h2>
                                <p>If your account was found, an e-mail has been sent to the associated e-mailadres. Enter the code and your new password.</p>
                                <hr>

                                <?php echo isset($error) ? '<p class="text-danger">'.$error.'</p>' : ''; ?>
                                <?php echo isset($message) ? '<p class="text-success">'.$message.'</p>' : ''; ?>
                                
                                <div class="form-group">
                                    <input type="text" class="form-control" name="code" placeholder="Code" required="required" autocomplete="nope">
                                </div>
                                <div class="input-group mb-3" id="show_hide_password">
                                    <input type="password" class="form-control" name="password" placeholder="Password" required="required" autocomplete="new-password">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><a href="" class=""><i class="fa fa-eye-slash" aria-hidden="true"></i></a></span>
                                    </div>
                                </div>      
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-lg btn-block">Reset password</button>
                                </div>
                                <input type='hidden' name='action' value='reset' />

                            <?php } else { ?>

                                <h2>Forgotten password</h2>
                                <p>Enter your username and we will sent you a reset code to your e-mailadres.</p>
                                <hr>

                                <?php echo isset($error) ? '<p class="text-danger">'.$error.'</p>' : ''; ?>
                                <?php echo isset($message) ? '<p class="text-success">'.$message.'</p>' : ''; ?>

                                <div class="form-group">
                                    <input type="text" class="form-control" name="username" placeholder="Username" autocomplete="nope">
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-lg btn-block">Send code</button>
                                </div>
                                <input type='hidden' name='action' value='register' />
                                <input type='hidden' name='action' value='code' />
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