<?php
session_start();

require '../vendor/autoload.php';

use CognitoApp\CognitoClient;

$client = new CognitoClient();
$client->initialize();


if(!$client->isAuthenticated()) {
    header('Location: /');
    exit;
}

$user  = $client->getUser();
$pool  = $client->getPoolMetadata();
$users = $client->getPoolUsers();
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

<nav class="navbar navbar-expand-md navbar-dark bg-dark mb-4">
  <a class="navbar-brand" href="/home.php">Cognito App</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarCollapse">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item active">
        <a class="nav-link" href="/home.php">Home</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="/logout.php">Logout</a>
      </li>
    </ul>
    <form class="form-inline mt-2 mt-md-0">
      <input class="form-control mr-sm-2" type="text" placeholder="Search" aria-label="Search">
      <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
    </form>
  </div>
</nav>

<main role="main" class="container">
    <div class="jumbotron">
        <h1>Welcome <strong><?php echo $user->get('Username');?></strong>!</h1>
        <h2>You are succesfully authenticated.</h2>
        <hr>
        <p class="lead">
            <h4>User data</h4>
            <p><code><?php var_dump($user); ?></code></p>

            <h2>Users</h2>
            <ul>
            <?php
            foreach($users as $user) {
                $email_attribute_index = array_search('email', array_column($user['Attributes'], 'Name'));
                $email = $user['Attributes'][$email_attribute_index]['Value'];
                echo "<li>{$user['Username']} ({$email})</li>";
            }
            ?>
            </ul>
        </p>
    </div>
</main>

    
    <script src="/js/jquery-3.6.0.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
  </body>
</html>