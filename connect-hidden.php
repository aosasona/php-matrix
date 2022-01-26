<?php
//THIS PORTION SHOULD BE IN ANOTHER FILE WHICH SHOULD BE IN A .GITIGNORE CONFIG DURING LIVE DEPLOYMENT OR USAGE

if(defined("__CONNECT")){

$dbhost = "localhost";

$dbuser = "root";

$dbpassword = "";

$dbname = "matrix";


$conn = mysqli_connect($dbhost, $dbuser, $dbpassword, $dbname);


if (mysqli_connect_errno()) {
    ?>
        <script>
            alert('Database connection could not be established!')
        </script>
    <?php
    }


} else {
    exit('UNAUTHORIZED ACCESS!!!');
}

?>