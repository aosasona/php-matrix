<?php

define("__CONNECT", "VALID");

include $_SERVER["DOCUMENT_ROOT"]."/php-matrix/connect-hidden.php";

//When data is POSTed from the site
if(isset($_POST["submit_data"])){

//Create a block mapping table
$block_map = "CREATE TABLE IF NOT EXISTS block_map (
    id INT(10) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(600) UNIQUE KEY NOT NULL,
    user_block VARCHAR(30) NOT NULL,
    alpha_block VARCHAR(30) NOT NULL)";

    mysqli_query($conn, $block_map);

    $username = mysqli_real_escape_string($conn, strtolower($_POST["user"])); //THE USERNAME IS COLLECTED, MADE TO LOWER CASE AND SANITIZED

    $max = 4; //THIS SHOULD BE EQUAL TO THE VALUE IN YOUR 'set_matrix.php' SCRIPT BEOFORE RUNNING EITHER

    $check_exists = "SELECT * FROM `block_map` WHERE username='$username'"; 

    $check_exists_query = mysqli_query($conn, $check_exists); //QUERY THE SQL 

    $count_check_exists = mysqli_num_rows($check_exists_query); //COUNT THE NUMBER OF ROWS WHERE THE USER EXISTS

    //CHECK IF THE USER ALREADY EXISTS
    if($count_check_exists != NULL){
        echo '<script type="text/javascript">alert("User Already Exists!")</script>'; //ECHO JS SCRIPT
        echo '<meta http-equiv="refresh", content="0, url=/php-matrix/index.html">'; //GO BACK TO THE HOME PAGE
    }

    else {

    //IF USER HAS NEVER BEEN IN THE SYSTEM, DO THIS:

    $compare = mysqli_fetch_array($check_exists_query);
        //RANDOMLY SELECT A BLOCK FROM THE LIST OF BLOCKS

        for($i = 1; $i <= $max; $i++){
            $block = 'block'.$i; //LIST ALL BLOCKS

            $block_array[]= $block; //CREATE AN ARRAY FROM THE LIST
            
        }

        $r = array_rand($block_array, 3); //RANDOMLY SELECT THREE BLOCKS WHICH ARE NOT THE SAME 
        
        shuffle($r); //SHUFFLE RESULT
        
        $selected_block = $block_array[$r[0]]; //FIRST SELECTED BLOCK

        $alpha_block = $block_array[$r[1]]; //SECOND SELECTED BLOCK


        //INSERT INTO THE BLOCK MAP TABLE
        $query_insert = "INSERT INTO `block_map` (username, user_block, alpha_block) VALUES('$username', '$selected_block', '$alpha_block')";
        mysqli_query($conn, $query_insert); 

//CHECK IF THE USER IS THE FIRST ALPHA IN THE SELECTED BLOCK

$get = "SELECT * FROM `table_map` ORDER BY id DESC LIMIT 1";
$get_result = mysqli_query($conn, $get);
$get_value = mysqli_fetch_array($get_result);
$matrix_scale = $get_value["matrix_scale"];

//THIS SECTION CREATES THE ROWS ARRAY USING THE MATRIX SCALE AS THE REFERENCE
for($refs = 1; $refs <= $matrix_scale; $refs++){

    $ref_no = "ref".$refs;

    $ref[] = $ref_no;
}
$r = $ref;

$ref_rand =  array_rand($r, 3);

shuffle($ref_rand); //SHUFFLE THE RESULT

//THE VARIABLES FOR ONLY 3 ROWS WHICH IS THE MINIMUM FOR THIS MATRIX SYSTEM
$ref_0 = $ref[$ref_rand[0]];
$ref_1 = $ref[$ref_rand[1]];
$ref_2 = $ref[$ref_rand[2]];


//THIS PART CHECKS IF A TABLE HAS NO USERS
$connect = $GLOBALS['conn'];

$select = $GLOBALS['selected_block'];

$check_alpha = "SELECT COUNT(*) AS `count` FROM `$select`"; //COUNT THE NUMBER OF ROWS

$sub = mysqli_query($connect, $check_alpha);

$query_check_alpha = mysqli_fetch_assoc($sub);

$count_check_alpha = $query_check_alpha["count"];

if($count_check_alpha > 0){

    $my_block = "INSERT INTO `$alpha_block` (alpha) VALUES('$username')";
    mysqli_query($conn, $my_block); //INSERT USER AS AN ALPHA IN A BLOCK

$ver_active = "FALSE";

while($ver_active == "FALSE"){

$val_empty = "SELECT * FROM `$selected_block` ORDER BY rand() LIMIT 1";

$val_empty_q = mysqli_query($conn, $val_empty)  or die(mysqli_error($conn));

while($fetch = mysqli_fetch_array($val_empty_q)){

        if($fetch[$ref_0] = NULL || $fetch[$ref_0] == "" || $fetch[$ref_0] == " "){
            $ver_active = "TRUE";

            define("ALPHA", $fetch["alpha"]); //define a constant ALPHA variable

            define("FREE_ROW", $ref_0); //define a constant FREE_ROW variable to represent the current row that's available

            $ref = FREE_ROW;
            
            $alpha_obj = ALPHA;

            $ins = "UPDATE `$selected_block` SET $ref = '$username' WHERE alpha = '$alpha_obj'";

            mysqli_query($conn, $ins) or die(mysqli_error($conn)); //INSERT USER AS REFERRAL INTO ANY OF THE EMPTY ROWS
            
            break;

            exit;

        } else {

            if($fetch[$ref_1] = NULL || $fetch[$ref_1] == "" || $fetch[$ref_1] == " "){

                $ver_active = "TRUE";

                define("ALPHA", $fetch["alpha"]); //define a constant ALPHA variable

                define("FREE_ROW", $ref_1); //define a constant FREE_ROW variable to represent the current row that's available

                $ref = FREE_ROW;
            
                $alpha_obj = ALPHA;
    
                $ins = "UPDATE `$selected_block` SET $ref = '$username' WHERE alpha = '$alpha_obj'";

                mysqli_query($conn, $ins) or die(mysqli_error($conn)); //INSERT USER AS REFERRAL INTO ANY OF THE EMPTY ROWS

                break;

                exit;

                } else {

                    if($fetch[$ref_2] = NULL || $fetch[$ref_2] == "" || $fetch[$ref_2] == " "){

                    $ver_active = "TRUE";

                    define("ALPHA", $fetch["alpha"]); //define a constant ALPHA variable

                    define("FREE_ROW", $ref_2); //define a constant FREE_ROW variable to represent the current row that's available

                    $ref = FREE_ROW;
            
                    $alpha_obj = ALPHA;
        
                    $ins = "UPDATE `$selected_block` SET $ref = '$username' WHERE alpha = '$alpha_obj'";

                    mysqli_query($conn, $ins) or die(mysqli_error($conn)); //INSERT USER AS REFERRAL INTO ANY OF THE EMPTY ROWS

                    break;

                    exit;

                }
            }
        }

    }

}

} else {

            $my_block = "INSERT INTO `$alpha_block` (alpha) VALUES('$username')";
            mysqli_query($conn, $my_block); //INSERT USER AS AN ALPHA TO BE THE FIRST GENERATION IN A BLOCK

        }

    }

    echo '<meta http-equiv="refresh", content="0, url=/php-matrix/index.html">'; //GO BACK TO THE HOME PAGE
}
?>