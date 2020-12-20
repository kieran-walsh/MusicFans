<?php
    session_start();
    unset($_SESSION['username']);
    unset($_SESSION['password']);
    unset($_SESSION['last_search']);
    //Consluted PHP documentation on how to use session_destroy(),
    //session_abort(), and header()
    session_destroy();
    session_abort();
    header("Location: ../login.php");
?>