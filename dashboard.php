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
        //Referenced PHP documentation ("header", "session_start", 
        //"isset", and "Sessions") for this code
        session_start(); 
        if (isset($_SESSION['username']) == false) {
            header("Location: dashboard.html");
            exit();
        }
    ?>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#">MusicFans</a>      
        <ul class="navbar-nav mr-auto"></ul>
        <ul class="navbar-nav justify-content-end">
            <li class="nav-item">
                <a class="nav-link" href="index.php">Home</a>
            </li>
            <li class="nav-item active">
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
            <div class="col-10">
                <h2>Dashboard for <?php echo $_SESSION['username'];?></h2>
            </div>
            <?php
                if (isset($_SESSION['username'])) {
                    echo '
                    <div class="col-2">
                        <a class="btn btn-dark float-right justify-content-end" href="php/signout.php">Sign out</a>
                    </div>';
                }
            ?>
        </div>
        <hr/>

        <div class="row">   
            <div class="col-4">
                <h3>Add a new post</h3>
                <p>Tell everyone what you're thinking!</p>
                <form class="form" method="POST" action="php/newpost-result.php">
                    <input class="form-control mr-sm-2" type="text" placeholder="Title" name="title"/>
                    <br/>
                    <textarea class="form-control mr-sm-2" rows="5" placeholder="What you wanna say..." name="content"></textarea>
                    <br/>
                    <button class="btn btn-dark my-2 my-sm-0" type="submit">Post</button>
                  </form>
            </div>
            <div class="col-2"></div>
            <div class="col-6 border-left">
                <h3>Stats for <?php echo $_SESSION['username'];?></h3>
                <br/>
                <?php
                    include "php/db_connect.php";

                    function get_value($db, $col) {
                        $sql = "SELECT $col 
                        FROM Users
                        WHERE username = '".$_SESSION['username']."'";

                        $result = mysqli_query($db, $sql);
                        $row = mysqli_fetch_assoc($result);
                        $time_joined = $row[$col];
                        return $time_joined;
                    }
                
                    function get_post_count($db ) {
                        $sql = "SELECT count(posted_by) AS cnt
                        FROM Posts
                        WHERE posted_by = '".$_SESSION['username']."'";

                        $result = mysqli_query($db, $sql);
                        $row = mysqli_fetch_assoc($result);
                        $cnt = $row['cnt'];
                        return $cnt;
                    }

                    function get_bookmark_count($db ) {
                        $sql = "SELECT count(by_user) AS cnt
                        FROM Favorites
                        WHERE by_user = '".$_SESSION['username']."'";

                        $result = mysqli_query($db, $sql);
                        $row = mysqli_fetch_assoc($result);
                        $cnt = $row['cnt'];
                        return $cnt;
                    }

                    $time_joined = get_value($db, 'time_joined');
                    $post_count = get_post_count($db);
                    $bookmark_count = get_bookmark_count($db);

                    echo "<p><b>When you joined</b>: ".$time_joined."</p>";
                    echo "<p><b>How many times you've posted</b>: ".$post_count."</p>";
                    echo "<p><b>How many posts you've bookmarked</b>: ".$bookmark_count."</p>";
                    if (isset($_SESSION['last_search'])) {
                        echo "<p><b>Most recent search</b>: '".$_SESSION['last_search']."'</p>";
                    }
                    else {
                        echo "<p><b>Most recent search</b>: No searches during this session.</p>";
                    }

                    //*********** Showing vector matrix
                    echo "<p><b>Your vector matrix: </b> (scroll)</p>";

                    $sql_matrix = "SELECT word, score
                                     FROM VectorKeywords
                                    WHERE user = '".$_SESSION['username']."'";
                    $result_matrix = mysqli_query($db, $sql_matrix);
                    if (mysqli_num_rows($result_matrix) > 0) {
                        echo '<div style="height: 200px; padding: 5px; overflow-y: scroll; border: 1px solid lightgray;">';
                        while ($row = mysqli_fetch_assoc($result_matrix)) {
                            echo "<span>[".$row['word'].": ".$row['score']."]</span></br> ";
                        }
                        echo "</div>";
                    }
                    else {
                        echo "no matrix";
                    }
                    //*************

                ?>
            </div>
        </div>
        <hr/>
        <div>
            <button id="bookmarks-btn" class="btn btn-dark">Bookmarks</button>
            <button id="posts-btn" class="btn btn-dark">Your Posts</button>
            <button id="content-btn" class="btn btn-dark">Related Content</button>
        </div>
        <hr/>
        <div id="bookmarks" class="row">
            <div class="col">
                <h3>Posts You've Bookmarked</h3>
                <br/>

                <?php
                    include "php/db_connect.php";

                    $sql = "SELECT * 
                              FROM Posts as p
                                   JOIN Favorites as f ON p.post_id = f.post_id
                             WHERE f.by_user = '".$_SESSION['username']."'
                             ORDER BY time_posted DESC";

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
                                            <input type="hidden" name="page-from" value="../dashboard.php#'.$row['post_id'].'"/>
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
                        echo '<p>No bookmarked posts to show.</p>';
                    }
                ?>
                <br/>
            </div>
        </div>
        
        <div id="recent-activity" class="row" style="display: none;">
            <div class="col">
                <h3>Recent Activity for <?php echo $_SESSION['username'];?></h3>
                <br/>
                <?php
                    include "php/db_connect.php";

                    $sql = "SELECT * 
                              FROM Posts
                             WHERE posted_by = '".$_SESSION['username']."'
                             ORDER BY time_posted DESC";

                    $result = mysqli_query($db, $sql);
                
                    if (mysqli_num_rows($result) > 0) {
                        //Has recent posts
                        while($row = mysqli_fetch_assoc($result)) {
                            // Print columns from the row - got this from:
                            // https://stackoverflow.com/questions/2970936/how-to-echo-out-table-rows-from-the-db-php
                            echo '<div class="row">
                                    <div class="col">
                                        <p class="bg-dark text-white" style="padding: 5px">Post #'.$row['post_id'].'</p>
                                        <h3>'.$row['title'].'</h3>
                                        <p>'.$row['content'].'</p>
                                        <p class="float-right">'.$row['time_posted'].'</p>
                                        <p> Posted by: '.$row['posted_by'].'</p>
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
        </div>

        <div id="related-content" class="row" style="display: none;">
            <div class="col">
                <h3>Related Content Based on Your Bookmarks</h3>
                <br/>
                <?php
                    
                    include "php/db_connect.php";

                    $sql = "SELECT * 
                            FROM Posts as p
                                JOIN Related_Content as c ON p.post_id = c.post_id
                            WHERE c.for_user = '".$_SESSION['username']."'";

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
                                            <input type="hidden" name="page-from" value="../dashboard.php#'.$row['post_id'].'"/>
                                            <input type="hidden" name="post-id" value="'.$row['post_id'].'"/>
                                        </form>                          
                                    </div>
                                </div>
                                <hr/>';
                        }
                } 
                else {
                    //No recent posts
                    echo '<p>No related content to show.</p>';
                }
            ?>
            <br/>
            </div>
        </div>

    </div>

    <footer class="footer bg-light">
        <hr/>
        <div class="container text-muted">
            <p>This website is powered by Bootstrap!</p>
        </div>
        <br/>
    </footer>

    <script src="scripts.js"></script>
    
  </body>
</html>