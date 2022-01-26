<!DOCTYPE html>
<html>

<head>
    <style>
        @import url('https://fonts.googleapis.com/css?family=Poppins');

        * {
            font-family: Poppins;
            padding: 5px;
        }

        form {
            width: 70vw;
            box-sizing: border-box;
            border: 1.5px solid #222;
            padding: 10px 50px 10px 50px;
            padding-bottom: 20px;
            text-align: left;
            margin-top: 15vh;
        }

        input.scale {
            display: inline;
            text-align: center;
            width: 5%;
            border: 1.8px solid #222;
            padding: 10px 15px 10px 15px;
            font-size: 101%;
        }

        input.scale:focus {
            outline-color: red;
        }

        button {
            margin-top: 15px;
            display: block;
            padding: 10px 25px 10px 25px;
            margin: 15px 0px 30px 0px;
            background: black;
            border: none;
            color: white;
            font-size: 102%;
        }

        button:hover {
            color: black;
            background: orange;
        }

        form span {
            padding: 0px 15px 0px 15px;
            font-size: 18px;
        }

        form small {
            font-size: 70%;
            display: block;
            color: red;
            margin-top: 5px;
        }

        span.message {
            display: block;
            text-align: center;
            width: auto;
            color: #228B22;
            border: 1.8px solid #228B22;
            background: #99FF99;
            padding: 10px 20px 10px 20px;
            font-size: 90%;
            margin: 20px 20vw 20px 20vw;
        }

        span.fail {
            display: block;
            text-align: center;
            width: auto;
            color: #B22222;
            border: 1.8px solid #B22222;
            background: #FFB6C1;
            padding: 10px 20px 10px 20px;
            font-size: 90%;
            margin: 20px 20vw 20px 20vw;
        }

        span.load {
            display: none;
            text-align: center;
            width: auto;
            color: #FF8C00;
            border: 1.8px solid #FF8C00;
            background: #FFEBCD;
            padding: 10px 20px 10px 20px;
            font-size: 90%;
            margin: 40px 20vw 40px 20vw;
        }
    </style>

    <title>Set Matrix Scale</title>
    <base href="/php-matrix/">
</head>

<body>
    <center>
        <form action="set_matrix.php" method="POST" autocomplete="off">
            <h1>Create Blocks</h1>
            <input type="number" name="scale1" class="scale" placeholder="3" min="3" required="required">
            <span> x </span>
            <input type="number" name="scale2" class="scale" placeholder="3" min="3" required="required">

            <small>Number of users per user before matrix terminates</small>
            <button type="submit" name="set_matrix" onclick="showAlert()">Set</button>
            <button type="submit" name="reset_matrix" style="background: red;" onclick="showAlert()">Reset All</button>
            <small>&uarr;&uarr;&uarr; Note: This will cause you to lose all your data!</small>
        </form>

    </center>
    <span class="load" id="load">Please be patient...</span>

    <script type="text/javascript">
        function showAlert() {
                document.getElementById('load').style.display = "block"
                setTimeout(function load() {
                    document.getElementById('load').style.display = "none"
                }, 5000);
        }
    </script>
</body>

</html>

<?php
error_reporting(0);

define("__CONNECT", "VALID");

include $_SERVER["DOCUMENT_ROOT"]."/php-matrix/connect-hidden.php";

$max = 4; //Number of tables to generate


//DROP ALL TABLES OR RESET BLOCKS
if (isset($_POST["reset_matrix"])) {
    $i = 1;
    $a = 1;

    $post_i = $_POST["scale1"];
    $post_a = $_POST["scale2"];



    while ($i <= $max) {

        $table_name = "block" . $i;

        $drop = "DROP TABLE `$table_name`";

        mysqli_query($conn, $drop)  or mysqli_error($conn, $drop);

        $i++;
    }

    $drop2 = "DROP TABLE `table_map`";

    mysqli_query($conn, $drop2)  or mysqli_error($conn, $drop2);

    echo '<span class="fail" id="fail">All tables have been deleted!</span>';
?>
    <script type="text/javascript">
        setTimeout(function fail() {
            document.getElementById('fail').style.display = "none"
        }, 7000);
    </script>
    <?php
    echo '<meta http-equiv="refresh" content="8, url=/php-matrix/set_matrix.php">';
}


//CREATE NEW BLOCKS
if (isset($_POST["set_matrix"])) {
    $i = 1;
    $a = 1;

    $post_i = $_POST["scale1"];
    $post_a = $_POST["scale2"];

    //CREATE TABLE FOR MATRIX SCALE REFERENCE
    $table_map = "CREATE TABLE IF NOT EXISTS `table_map` (
        id INT(5) AUTO_INCREMENT PRIMARY KEY NOT NULL,
        matrix_scale VARCHAR(6) NOT NULL)";

        mysqli_query($conn, $table_map)  or mysqli_error($conn, $table_map);

    $create_table_map = "INSERT INTO `table_map` (matrix_scale) VALUES ('$post_i')";

    mysqli_query($conn, $create_table_map); //CREATE NEW MATRIX CONFIG

    if ($post_i == $post_a) {

        while ($i <= $max) {

            $table_name = "block" . $i;

            $table = "CREATE TABLE IF NOT EXISTS `$table_name` (
                    id INT(100) AUTO_INCREMENT PRIMARY KEY NOT NULL,
                    alpha VARCHAR(600) UNIQUE KEY NOT NULL)";

            mysqli_query($conn, $table)  or mysqli_error($conn, $table);

            for ($a = 1; $a <= $post_a; $a++) {
                $col_name = "ref" . $a;

                $col_add = "ALTER TABLE `$table_name` ADD `$col_name` VARCHAR(600) ";

                mysqli_query($conn, $col_add) or mysqli_error($conn, $col_add);
            }

            $i++;
        }

        echo '<span class="message" id="success">Success: Tables have been generated and matrix has been set!</span>';
    ?>
        <script type="text/javascript">
            setTimeout(function success() {
                document.getElementById('success').style.display = "none"
            }, 4000);
        </script>
    <?php
        echo '<meta http-equiv="refresh" content="8, url=/php-matrix/set_matrix.php">';
    } else {
        echo '<span class="fail" id="fail">Fail: System is configured to a square matrix like 3x3, 4x4 etc, try again using equal digits!</span>';
    ?>
        <script type="text/javascript">
            setTimeout(function fail() {
                document.getElementById('fail').style.display = "none"
            }, 7000);
        </script>
<?php
        echo '<meta http-equiv="refresh" content="8, url=/php-matrix/set_matrix.php">';
    }
}
?>