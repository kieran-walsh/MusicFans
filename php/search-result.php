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
                <h3>Search for posts</h3>
            </div>     
        </div>
        <div class="row">
            <div class="col">
                <p>Search for existing posts by phrase or keyword.</p>
            </div>
            <div class="col">
                <form class="form-inline float-right" method="GET" action="search-result.php">
                    <input class="form-control mr-sm-2" type="search" placeholder="Search" name="search" required>
                    <button class="btn btn-dark my-2 my-sm-0" type="submit">Search</button>
                  </form>
            </div>
        </div>
        <hr/>
        <div class="row">
            <div class="col">
                <h3>Search results</h3>
                <br/>
                <!-- <p>Your search returned the following result(s):</p> -->
            </div>
        </div>
        <?php
            include "db_connect.php";

            //Common words array - found on Wikipedia
            // https://en.wikipedia.org/wiki/Most_common_words_in_English

            $common_words = array(
                "the", "be", "to", "of", "and", "a", "in", "that", "have",
                "i", "it", "for", "not", "on", "with", "he", "as", "you",
                "do", "at", "this", "but", "his", "by", "from", "they", "we",
                "say", "her", "she", "or", "an", "will", "my", "one", "all",
                "would", "there", "their", "what", "so", "up", "out", "if",
                "about", "who", "get", "which", "go", "me", "when", "make",
                "can", "like", "time", "no", "just", "him", "know", "take",
                "people", "into", "year", "your", "good", "some", "could",
                "them", "see", "other", "than", "then", "now", "look", "only",
                "come", "its", "over", "think", "also", "back", "after", "use",
                "two", "how", "our", "work", "first", "well", "even", "new", 
                "want", "because", "any", "these", "give", "day", "most", "us", 
                "is", "really"  
            );
            
            session_start();

            if(isset($_GET['search'])) {
                $search_full = $_GET['search'];
            }
            else {
                $search_full = $_SESSION['last_search'];
            }
            $_SESSION['last_search'] = $search_full;

            //Split search into array
            //Consluted PHP documentation on how to use explode()
            $search_arr = explode(" ", $search_full);

            //Filter out noise
            $new_words = array();
            //Consluted PHP documentation on how to use count(),
            //strtolower(), in_array(), and array_push()
            for ($i = 0; $i < count($search_arr); $i++) {
                $current = strtolower($search_arr[$i]);

                //Clean symbols from keywords
                //PHP substring function: https://www.php.net/manual/en/function.substr.php

                $current_last = substr($current, -1, 1);
                $symbol_arr = array(".", ",", ";", ":", ")", "_");

                if (in_array($current_last, $symbol_arr)) {
                    $current = substr($current, 0, -1);
                    //echo "<p>".$current_last." symbol? yes</p>";
                }

                //Add word to keywords array if not a common word
                if (in_array($current, $common_words) == false) {
                    array_push($new_words, $current);
                }
            }

            function getAll($arr, $and = true) {
                $result = "";
                if (count($arr) == 0) {
                    return $result;
                }

                $op = 'AND';
                if ($and == false) {
                    $op = "OR";
                }

                for ($i = 0; $i < count($arr); $i++) {
                    $current = $arr[$i];
                    if ($i == count($arr) - 1) {
                        $result = $result."(content LIKE '%".$current."%' OR title LIKE '%".$current."%')";                   
                    }
                    else {
                        $result = $result."(content LIKE '%".$current."%' OR title LIKE '%".$current."%') ".$op." ";
                    }
                }

                $result = $result." ORDER BY time_posted DESC";

                return $result;
            }
            
            //Search for posts with the given "WHERE" clause
            function search($db, $where) {

                global $searched;

                if (isset($_SESSION['username'])) {
                    $sql = "SELECT * 
                            FROM Posts
                            WHERE posted_by != '".$_SESSION['username']."' AND ".$where;
                }
                else {
                    $sql = "SELECT * 
                    FROM Posts
                    WHERE $where";
                }

                $result = mysqli_query($db, $sql);
                if ($result == false || $result == null) {
                    return false;
                }
            
                if (mysqli_num_rows($result) > 0) {                    
                    while ($row = mysqli_fetch_assoc($result)) {
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
                                $form_action = 'remove_bookmark.php';
                            }
                            else {
                                $bookmark_btn = '<input type="submit" class="btn btn-dark" value="Bookmark"/>';
                                $form_action = 'add_bookmark.php';
                            }
                        }
                        else {
                            $bookmark_btn = '<input type="submit" class="btn btn-dark" value="Bookmark"/>';
                            $form_action = '../no-bookmark.html';
                        }
    
                        if (in_array($row['post_id'], $searched) == false) {
                            echo '<div id="'.$row['post_id'].'"class="row">
                                    <div class="col">
                                        <p class="bg-dark text-white" style="padding: 5px">Post #'.$row['post_id'].'</p>
                                        <h3>'.$row['title'].'</h3>
                                        <p>'.$row['content'].'</p>
                                        <p class="bg-light text-right">'.$row['time_posted'].'</p>
                                        <p class="float-left"> Posted by: '.$row['posted_by'].'</p>
                                        <form class="form-inline float-right" method="GET" action="'.$form_action.'">
                                            '.$bookmark_btn.'
                                            <input type="hidden" name="page-from" value="search-result.php#'.$row['post_id'].'"/>
                                            <input type="hidden" name="post-id" value="'.$row['post_id'].'"/>
                                            <input type="hidden" name="post-content" value="'.$row['title'].' '.$row['content'].'"/>
                                        </form>                          
                                    </div>
                                </div>
                                <hr/>';
                            array_push($searched, $row['post_id']);
                        }
                    }
                    return true;
                } 
                else {
                    //No recent posts
                    return false;
                }
                return;
            }

            $vectors_arr = array();
            $not_in_vector = array();
            $num_results = 0;
            $searched = array();

            if (isset($_SESSION['username'])) {
                //SELECT matrix keywords and sort by score
                $sql_vectors = "SELECT word
                                FROM VectorKeywords
                                WHERE user = '".$_SESSION['username']."'
                                ORDER BY score DESC, word ASC
                                LIMIT 5";
                $vectors_result = mysqli_query($db, $sql_vectors);

                while ($row = mysqli_fetch_assoc($vectors_result)) {
                    array_push($vectors_arr, $row['word']);
                }

                foreach ($new_words as $word) {
                    if (in_array($word, $vectors_arr) == false) {
                        array_push($not_in_vector, $word);
                    }
                }

                /*
                echo "<p>Vector keywords:</p>";
                print_r($vectors_arr);
                echo "<p>Not in vector:</p>";
                print_r($not_in_vector);
                echo "<p>New words:</p>";
                print_r($new_words);
                */

                //Iterate over the matrix and check for each term
                //If found, print it out
                foreach ($vectors_arr as $current) {
                    foreach($not_in_vector as $word) {
                        //echo "<p>Current vector: ".$current."; other word: ".$word."</p>";
                        $where = "(title LIKE '%$current%' OR content LIKE '%$current%') AND (title LIKE '%$word%' OR content LIKE '%$word%')";
                        $current_result = search($db, $where);
                        if ($current_result == true) {
                            $num_results = $num_results + 1;
                        }
                    }
                }
            }
            else {
                $not_in_vector = $new_words;
            }

            if (count($not_in_vector) > 0 || $num_results == 0) {
                //After the loop
                //Go to the end of the list - if some terms weren't searched, search them
                //If none, search for "or terms"
                $all_where = getAll($new_words);
                $or_where = getAll($new_words, false);

                $all_result = search($db, $all_where);
                if ($all_result == false) {
                    $or_result = search($db, $or_where);
                    if ($or_result == false) {
                        echo "<p>Your search didn't return any results. Try searching for something else.</p><br/>";
                    }
                }
            }
        ?>
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