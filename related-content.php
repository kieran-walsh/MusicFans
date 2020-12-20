<?php
    //Program to compute related content for a user
    include 'php/db_connect.php';

    /* Related content - based on content
       1. For every content item, get a list of the people who like it
       2. For every person in this list, get a list of content they like
       3. Remove the current user from person/content list (either)
       4. Then return the content list
    */

    //Get all other users who bookmarked a particular post
    function getOtherUsers($db, $post_id, $user) {
        $sql = "SELECT by_user
                  FROM Favorites
                 WHERE post_id = $post_id AND by_user != '$user'";
        $result = mysqli_query($db, $sql);

        $arr = array();
        while ($row = mysqli_fetch_assoc($result)) {
            array_push($arr, $row['by_user']);
        }

        return $arr;
    }

    //Get a list of bookmarked posts by a particular user
    function getUserBookmarks($db, $user, $noPostsFrom) {
        $sql = "SELECT f.post_id
                  FROM Favorites as f
                       JOIN Posts as p ON f.post_id = p.post_id
                 WHERE f.by_user = '$user' AND p.posted_by != '$noPostsFrom'";
        $result = mysqli_query($db, $sql);

        $arr = array();
        while ($row = mysqli_fetch_assoc($result)) {
            array_push($arr, $row['post_id']);
        }

        return $arr;
    }

    //Overall function to get related content
    function getRelatedContent($db, $user) {
        $result_arr = array();

        //Gets all the posts bookmarked by the user and stores in an array
        $sql_first = "SELECT post_id
                        FROM Favorites
                       WHERE by_user = '$user'";
        $first_result = mysqli_query($db, $sql_first);

        $bookmarked_posts = array();
        while ($row = mysqli_fetch_assoc($first_result)) {
            array_push($bookmarked_posts, $row['post_id']);
        }

        echo "<h3>Posts bookmarked by ".$user." (by post_id)</h3>";
        print_r($bookmarked_posts);
        echo "<hr/><br/><h3>Posts bookmarked by other users</h3>";

        //For each post, get a list of people who also bookmarked it
        foreach ($bookmarked_posts as $post) {
            $other_users = getOtherUsers($db, $post, $user);

            if (count($other_users) > 0) {
                echo "<p>Post: ".$post."</p>";
                echo "<span>Other users:</span> ";
                print_r($other_users);
                echo "<hr/>";
            }

            //For each other, get a list of posts they liked
            foreach ($other_users as $other) {
                $bookmarked_by_other = getUserBookmarks($db, $other, $user);

                echo "&nbsp;&nbsp;&nbsp;&nbsp; <span>Other: ".$other."</span><br/>";
                echo "&nbsp;&nbsp;&nbsp;&nbsp; <span>Posts bookmarked by other: </span>";
                print_r($bookmarked_by_other);
                echo "<hr/>";
                
                //Check that they aren't in posts bookmarked by the user or already added
                foreach ($bookmarked_by_other as $other_post) {
                    if (in_array($other_post, $bookmarked_posts) == false
                        && in_array($other_post, $result_arr) == false) {
                        array_push($result_arr, $other_post);
                    }
                }
            }
        }
        return $result_arr;

    }

    function addToDatabase($db, $content, $user) {
        foreach ($content as $rec) {
            $sql_insert = "INSERT INTO Related_Content (post_id, for_user)
                                VALUES ($rec, '$user') ";
            $insert_result = mysqli_query($db, $sql_insert);
            if ($insert_result) {
                echo "<p>Recommendation (Post #".$rec.") added to Related_Content for ".$user.".</p>";
            }
            else {
                echo "<p>Recommendation not added. ".mysqli_error($db)."</p>";
            }
        }
        echo "<br/>";
    }

    $user = $_GET['user'];

    $content = getRelatedContent($db, $user);
    echo "<br/><h3>Results (by post_id)</h3>";
    print_r($content);
    echo "<br/>";

    addToDatabase($db, $content, $user)

?>