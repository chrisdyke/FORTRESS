<?php
function filter($data)
{
	$conn = mysqli_connect("localhost", "root", "", "afia") or die ("Please, check the server connection.");
    // remove whitespaces from begining and end
    $data = trim($data);
    
    // apply stripslashes to pevent double escape if magic_quotes_gpc is enabled
    if(get_magic_quotes_gpc())
    {
        $data = stripslashes($data);
    }
    // connection is required before using this function
    $data = mysqli_real_escape_string($conn,$data);
    return $data;
}


function generate_string($input, $strength = 5) 
{
    $input_length = strlen($input);
    $random_string = '';
    for($i = 0; $i < $strength; $i++)
	 {
        $random_character = $input[mt_rand(0, $input_length - 1)];
        $random_string .= $random_character;
    }
  
    return $random_string;
}
 
//$string_length = 6;
//$captcha_string = generate_string($permitted_chars, $string_length);
 
 
?>