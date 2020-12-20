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
    <?php
        //Referenced PHP documentation (articles "header", "session_start", 
        //"isset", and "Sessions") for this code
        session_start(); 
        if (isset($_SESSION['username'])) {
            header("Location: logged-in.html");
            exit();
        }
    ?>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="index.php">MusicFans</a>      
        <ul class="navbar-nav mr-auto"></ul>
        <ul class="navbar-nav justify-content-end">
            <li class="nav-item">
                <a class="nav-link" href="index.php">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="dashboard.php">Profile</a>
            </li>
            <li class="nav-item active">
                <a class="nav-link disabled" href="login.php">Login</a>
            </li>
        </ul>
    </nav>
    <div class="container bg-light">
        <br/>
        <div class="row">
            <div class="col">
                <h2>Login/Sign Up</h2>
            </div>
        </div>
        <hr/>
        <div class="row">
            <br/>
            <div class="col-4">
                <h4>Already a member? Log in.</h4>
                <br/>
                <form method="POST" action="php/login-result.php">
                    <label for="username">Username</label>
                    <input class="form-control" type="text" placeholder="Username" aria-label="Username" name="user-login" required/>
                    <label for="password">Password</label>
                    <input class="form-control" type="text" placeholder="Password" aria-label="Password" name="pass-login" required/>
                    <br/>
                    <button class="btn btn-dark my-2 my-sm-0" type="submit">Log In</button>
                </form>
            </div>
            <div class="col-2 border-right"></div>
            <div class="col-1"></div>
            <div class="col-4">
                <h4>New to MusicFans? Sign Up.</h4>
                <br/>
                <form method="POST" action="php/signup-result.php">
                    <label for="fname">First name</label>
                    <input class="form-control" type="text" placeholder="First name" aria-label="First Name" name="fname" required/>
                    <label for="username">Username (15 characters max)</label>
                    <input class="form-control" type="text" placeholder="Username" aria-label="Username" name="user-signup" required/>
                    <label for="password">Password</label>
                    <input class="form-control" type="text" placeholder="Password" aria-label="Password" name="pass-signup" required/>
                    <br/>
                    <button class="btn btn-dark my-2 my-sm-0" type="submit">Sign Up</button>
                </form>            
            </div>
        </div>
        <br/><br/>
    </div>
    
    <footer class="footer bg-light fixed-bottom">
        <hr/>
        <div class="container text-muted">
            <p>This website is powered by Bootstrap!</p>
        </div>
        <br/>
    </footer>

  </body>
</html>