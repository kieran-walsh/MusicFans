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
            <li class="nav-item active">
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
            <?php
                session_start();
                if (isset($_SESSION['username'])) {
                    echo '<div class="col-10">
                    <p>Currently signed in: '.$_SESSION['username'].'</p>
                    </div>
                    <div class="col-2">
                        <a class="btn btn-dark justify-content-end" href="dashboard.php">Profile</a>
                        <a class="btn btn-dark float-right justify-content-end" href="php/signout.php">Sign out</a>
                    </div>';
                }
                else {
                    echo '<div class="col-10">
                            <p>Already a member or wanna sign up?</p>
                        </div>
                        <div class="col-2">
                            <a class="btn btn-dark float-right justify-content-end" href="login.php">Go to Login</a>
                        </div>';
                }
            ?>
        </div>
        <hr/>
        <div class="row">   
            <div class="col">
                <h3>Search for posts</h3>
            </div>     
        </div>
        <div class="row">
            <div class="col">
                <p>Search for existing posts by phrase or keyword.</p>
            </div>
            <div class="col">
                <form class="form-inline float-right" method="GET" action="php/search-result.php">
                    <input class="form-control mr-sm-2" type="search" placeholder="Search" name="search" required>
                    <button class="btn btn-dark my-2 my-sm-0" type="submit">Search</button>
                  </form>
            </div>
        </div>
        <hr/>
        <div class="row">
            <div class="col">
                <h3>Recent Activity on MusicFans</h3>
                <br/>
            </div>
        </div>
        <?php
            include "php/db_connect.php";
            
            $sql = "SELECT * 
                        FROM Posts
                        ORDER BY time_posted DESC
                        LIMIT 5 ";

            $result = mysqli_query($db, $sql);
        
            if (mysqli_num_rows($result) > 0) {
                //Has recent posts
                while($row = mysqli_fetch_assoc($result)) {
                    // Print columns from the row - got this from:
                    // https://stackoverflow.com/questions/2970936/how-to-echo-out-table-rows-from-the-db-php

                    if (isset($_SESSION['username'])) {
                        //Check to see if the user has already bookmarked the current post
                        $sql_post = "SELECT * 
                        FROM Favorites 
                        WHERE post_id = ".$row['post_id']." AND by_user = '".$_SESSION['username']."' ";

                        $result_post = mysqli_query($db, $sql_post);

                        //If there are results, it's already bookmarked - show the "already bookmarked" button
                        //If none, it hasn't beeen bookmarked yet
                        if (mysqli_num_rows($result_post) > 0) {
                            $bookmark_btn = '<input type="submit" class="btn btn-dark disabled" value="Bookmarked"/>';
                            $form_action = 'php/remove_bookmark.php';
                        }
                        else {
                            $bookmark_btn = '<input type="submit" class="btn btn-dark" value="Bookmark"/>';
                            $form_action = 'php/add_bookmark.php';
                        }
                    }
                    else {
                        $bookmark_btn = '<input type="submit" class="btn btn-dark" value="Bookmark"/>';
                        $form_action = 'no-bookmark.html';
                    }

                    echo '<div id="'.$row['post_id'].'"class="row">
                            <div class="col">
                                <p class="bg-dark text-white" style="padding: 5px">Post #'.$row['post_id'].'</p>
                                <h3>'.$row['title'].'</h3>
                                <p>'.$row['content'].'</p>
                                <p class="bg-light text-right">'.$row['time_posted'].'</p>
                                <p class="float-left"> Posted by: '.$row['posted_by'].'</p>
                                <form class="form-inline float-right" method="GET" action="'.$form_action.'">
                                    '.$bookmark_btn.'
                                    <input type="hidden" name="page-from" value="../index.php#'.$row['post_id'].'"/>
                                    <input type="hidden" name="post-id" value="'.$row['post_id'].'"/>
                                    <input type="hidden" name="post-content" value="'.$row['title'].' '.$row['content'].'"/>
                                </form>                          
                            </div>
                          </div>
                          <hr/>';
                }
            } 
            else {
                //No recent posts
                echo '<p>No recent activity to show.</p>';
            }
        ?>
        <br/>
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