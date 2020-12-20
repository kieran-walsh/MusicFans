<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <title>Music App</title>
  </head>
  <body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="../index.php">MusicFans</a>      
        <ul class="navbar-nav mr-auto"></ul>
        <ul class="navbar-nav justify-content-end">
            <li class="nav-item">
                <a class="nav-link" href="../index.php">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../dashboard.php">Profile</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../login.php">Login</a>
            </li>
        </ul>
    </nav>
    <div class="container bg-light">
        <br/>
        <div class="row">
            <div class="col">
                <h2>Sign Up</h2>
            </div>
        </div>
        <br/>
        <hr/>
        <div class="row">
            <div class="col">
                <?php
                    include "db_connect.php";

                    $new_user = $_POST['user-signup'];
                    $new_pass = $_POST['pass-signup'];
                    $fname = $_POST['fname'];

                    $sql = "INSERT INTO Users (username, password, fname)
                                VALUES ('$new_user', '$new_pass', '$fname')";

                    session_start();
                    $_SESSION['username'] = $new_user;
                    $_SESSION['password'] = $new_pass;

                    // Insert
                    if (mysqli_query($db, $sql)) {
                        echo '<p>New account created successfully!</p><hr/>
                            <p>Hey '.$fname.'! Welcome to MusicFans.</p><br/>
                            <a class="btn btn-dark" href="../dashboard.php">Go to Your Account</a>';
                    } 
                    else {
                        echo '<p> Account creation unsuccessful: '.mysqli_error($db).'</p>
                                <p>Please go back to the login page to try again.</p><br/>
                                <a class="btn btn-dark" href="../login.php">Go to Login</a> ';
                    }
                ?>
            </div>
        </div>
        <br/><br/>
    </div>
    
    <footer class="footer fixed-bottom">
        <hr/>
        <div class="container text-muted">
            <p>This website is powered by Bootstrap!</p>
        </div>
        <br/>
    </footer>
  </body>
</html>