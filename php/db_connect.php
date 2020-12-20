<?php

    // Consulted W3Schools for this code: 
    // https://www.w3schools.com/php/php_mysql_connect.asp

    $user = "admin";
    $pass = "123Florence!";
    $host = "localhost";
    $db_name = "ENT_project";

    $db = mysqli_connect($host, $user, $pass, $db_name);
    mysqli_set_charset($db, "utf-8");
?>