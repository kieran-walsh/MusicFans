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
                    <h2>Remove Bookmark</h2>
                </div>
            </div>
            <br/>
            <hr/>
            <div class="row">
                <div class="col">
                    <?php
                        include 'db_connect.php';
                        session_start();

                        if (isset($_SESSION['username']) == false) {
                            header("Location: ../login.php");
                        }

                        // Get the sent variables
                        $page_from = $_GET['page-from'];
                        $post_id = $_GET['post-id'];
                        $user = $_SESSION['username'];

                        //Add to favorites table
                        $q = "DELETE FROM Favorites
                                WHERE post_id = $post_id AND by_user = '$user'";

                        // Insert
                        if (mysqli_query($db, $q)) {
                            //header("Location: ".$page_from);
                        } 
                        else {
                            echo "<p>Couldn't remove favorite. Error: ".$q."<br>". mysqli_error($db)."</p>";
                            echo '<hr/><a class="btn btn-dark" href="'.$page_from.'">Go Back</a>';
                        }

                    ?>
                </div>
            </div>
            <br/>

            <div class="row">
                <div class="col">
                     <!-- Update the vector matrix with new keywords -->
                    <?php
                        
                        include 'db_connect.php';

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
                            "is"  
                        );

                        $post_content = $_GET['post-content'];

                        //Split search into array
                        //Consluted PHP documentation on how to use explode()
                        $content_arr = explode(" ", $post_content);

                        //Filter out noise
                        $keywords = array();
                        //Consluted PHP documentation on how to use count(),
                        //strtolower(), in_array(), and array_push()
                        for ($i = 0; $i < count($content_arr); $i++) {
                            $current = strtolower($content_arr[$i]);

                            //Clean symbols from keywords
                            //PHP substring function: https://www.php.net/manual/en/function.substr.php

                            $current_last = substr($current, -1, 1);
                            $symbol_arr = array(".", ",", ";", ":", ")", "_");

                            if (in_array($current_last, $symbol_arr)) {
                                $current = substr($current, 0, -1);
                                echo "<p>".$current_last." symbol? yes</p>";
                            }

                            //Add word to keywords array if not a common word
                            if (in_array($current, $common_words) == false) {
                                array_push($keywords, $current);
                            }
                        }

                        print_r($keywords);
                        echo "<p>made it this far... now to update the vectors</p>";

                        //We now have an array of the keywords of the new bookmarked post
                        //Score - number of occurrences over total bookmarks

                        //VectorKeywords - user, word, score
                        //$keywords
                        function updateVectors($db, $keywords) {
                            $user = $_SESSION['username'];
                            $dict = array();

                            //Get the number of bookmarks before the one just added
                            $sql_count = "SELECT count(by_user) as cnt FROM Favorites WHERE by_user = '$user'";
                            $result_count = mysqli_query($db, $sql_count);
                            $fav_count_prev = mysqli_fetch_assoc($result_count)['cnt'] + 1;

                            //Get all the current keywords
                            $sql = "SELECT word, score
                                    FROM VectorKeywords
                                    WHERE user = '$user'";
                            $result = mysqli_query($db, $sql);
                            
                            //Adds all the current keywords and scores to the dict
                            if (mysqli_num_rows($result) > 0) {
                                while($row = mysqli_fetch_assoc($result)) {
                                    $word = $row['word'];
                                    $score = $row['score'];
                                    $dict[$word] = $score;
                                }
                            }

                            //Right now we have $keywords - list of bookmark terms
                            //And $dict - list of words/scores

                            //If there's a match - remove proportionally
                            //If the result is 0, take it out of the table

                            //Iterate over dict and update scores to be accurate
                            foreach ($dict as $word => $score) {
                                if (in_array($word, $keywords)) {
                                    $num_freq = ($fav_count_prev * $score) - 1;
                                    echo "<p>".$word." in keywords. num_freq: ".$num_freq."</p>";
                                }
                                else {
                                    $num_freq = $fav_count_prev * $score;
                                    echo "<p>".$word." NOT in keywords</p>";
                                }
                                
                                if ($fav_count_prev - 1 == 0) {
                                    $new_score = $num_freq / 1;
                                }
                                else {
                                    $new_score = $num_freq / ($fav_count_prev - 1);
                                }

                                $dict[$word] = $new_score;
                            }

                            echo "<p>After the updates: </p>";
                            print_r($dict);

                            //Insert back into the table or add if not there
                            foreach ($dict as $word => $score) {
                                //If there are occurences, just update the scores
                                if ($score > 0.0001) {
                                    $sql_update = "UPDATE VectorKeywords
                                                      SET score = $score
                                                    WHERE user = '$user' AND word = '$word'";
                                    $result_update = mysqli_query($db, $sql_update);
                                    if ($result_update) {
                                        echo $word." updated ";
                                    }
                                    else {
                                        echo $word." not updated ";
                                    }
                                }
                                //If there are no occurences left, delete from table
                                else {
                                    $sql_insert = "DELETE FROM VectorKeywords
                                                         WHERE user = '$user' AND word = '$word'";
                                    $result_insert = mysqli_query($db, $sql_insert);
                                    if ($result_insert) {
                                        echo $word." deleted ";
                                    }
                                    else {
                                        echo $word." deleted ";
                                    }
                                }
                            }
                        }

                        updateVectors($db, $keywords);
                        header("Location: ".$page_from);
                    
                    ?>
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
    </body>
</html>