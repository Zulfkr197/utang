<?php
    /*
     * Thanks to : 
     * https://www.a2hosting.com/kb/developer-corner/postgresql/connect-to-postgresql-using-php
     */

    $host       =  "127.0.0.1";
    $sqluser    =  "root";
    $sqlpass    =  "";
    $sqldbname  =  "zulfkr_iPOS5";

    $sqlconn    = mysqli_connect($host, $sqluser, $sqlpass, $sqldbname);