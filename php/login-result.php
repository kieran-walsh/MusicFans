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
                <a class="nav-link" href="../index.php">Home<span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../dashboard.php">Profile</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../login.html">Login</a>
            </li>
        </ul>
    </nav>
    <div class="container bg-light">
        <br/>
        <div class="row">
            <div class="col">
                <h2>Login</h2>
            </div>
        </div>
        <hr/>
        <div class="row">
            <div class="col">
                <?php
                    include "db_connect.php";

                    $user = $_POST['user-login'];
                    $pass = $_POST['pass-login'];

                    $sql = "SELECT * 
                            FROM Users
                            WHERE username = '$user' AND password = '$pass'";
        
                    $result = mysqli_query($db, $sql);
                
                    if (mysqli_num_rows($result) > 0) {
                        //success
                        echo '<p>Login successful! </p><hr/>
                              <p>Welcome back to MusicFans.</p><br/>
                              <a class="btn btn-dark" href="../dashboard.php">Go to Your Account</a>';

                        session_start();
                        $_SESSION['username'] = $user;
                        $_SESSION['password'] = $pass;
                    } 
                    else {
                        //failure
                        echo '<p> Login unsuccessful!</p><hr/>
                                <p>Either you are not signed up to MusicFans, or you typed in the wrong credentials. Please go back to the login page and try again. </p><br/>
                                <a class="btn btn-dark" href="../login.php">Go to Login</a> ';
                    }

                ?>
            </div>
        </div>
        <br/><br/>
    </div>
    
    <footer class="footer bg-light">
        <hr/>
        <div class="container text-muted">
            <p>This website is powered by Bootstrap!</p>
        </div>
        <br/>
    </footer>

  </body>
</html>