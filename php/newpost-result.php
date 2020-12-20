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
        <a class="navbar-brand" href="index.php">MusicFans</a>      
        <ul class="navbar-nav mr-auto"></ul>
        <ul class="navbar-nav justify-content-end">
            <li class="nav-item">
                <a class="nav-link" href="index.php">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="dashboard.php">Profile</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="login.php">Login</a>
            </li>
        </ul>
    </nav>
    <div class="container bg-light">
        <br/>
        <div class="row">
            <div class="col">
                <h2>New Post</h2>
            </div>
        </div>
        <br/>
        <hr/>
        <div class="row">
            <div class="col">
            <?php
                include "db_connect.php";
                session_start();

                // Consulted W3Schools for this code:
                // https://www.w3schools.com/php/php_mysql_insert.asp

                $title = $_POST['title'];
                $content = $_POST['content'];
                $posted_by = $_SESSION['username'];

                $q = "INSERT INTO Posts (title, content, posted_by)
                        VALUES ('$title', '$content', '$posted_by')";

                // Insert
                if (mysqli_query($db, $q)) {
                    echo "<p>New post created successfully!<br/><hr/>";
                    echo "<p>Title: $title </p>";
                    echo "<p>Posted By: $posted_by</p>";
                    echo "<p>Content: $content </p>";
                } 
                else {
                    echo "<p>New post not created. Error: ".$q."<br>". mysqli_error($db)."</p>";
                    echo "<p>Please go back to your acount page and try again.</p>";
                }

                echo '<hr/><a class="btn btn-dark" href="../dashboard.php">Go to Profile</a>';

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