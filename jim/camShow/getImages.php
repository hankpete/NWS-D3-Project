<?php

    //The directory (relative to this file) that holds the images
    $dir = "../data/cams/";


    //This array will hold all the image addresses
    $result = array();

    //Get all the files in the specified directory
    $files = scandir($dir);


    foreach($files as $line) {

        switch(ltrim(strstr($line, '.'), '.')) {

            //If the line contains an image suffix, add it to the array
            case "jpg": case "jpeg":case "png":case "gif":

                $result[] = array("file"=>$dir.$line,"lat"=>"","lon"=>"","facing"=>"");

        }
    }

    //Convert the array into JSON
    $resultJson = json_encode($result);

    //Output the JSON object
    //This is what the AJAX request will see
    echo($resultJson);

?>
