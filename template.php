<?php
/* template.php defines the template function, which takes an array of tags and 
*  a string of data and substitutes the tags in the string for what is defined
*  in the array of tags.
*/
function template($tags, $data){
    /* Takes an array $tags and a string $data.  Performs a regexp pattern
    *  match and replaces all occurences of a tag with it's substitution. $tags
    *  is a two-dimensional array defined like so: 
    *  $tags = Array( 'separator' => <seperator>, 'tags' => 
    *            Array( <tag> => <substition> ) ).
    *  The separator can be any string that is unique in the $data string. It is
    *  used as the front and end of the tag match. The tag and substitution
    *  should both be strings, and the tag should not contain the separator in
    *  it.
    */
    $sep = $tags['separator'];
    $matches = Array();
    $pattern = "/" . $sep . "(.*)" . $sep . "/";

    $num_matches = preg_match_all($pattern, $data, $matches);

    if($num_matches != 0 && $num_matches != False){
        foreach($matches[1] as $key => $val){
            if(array_key_exists($val, $tags['tags']) == True){
                $data = str_replace($matches[0][$key], $tags['tags'][$val], $data);
            }
        }
    }

    return $data;
}

function addScript($script, $page){
    /*  Specific version of the templating function which adds the specified
    **  script to the bottom of the <head> section.
    */

    $matches = Array();
    $pattern = "/(<\/head>)/i";

    $num_matches = preg_match($pattern, $page, $matches, PREG_OFFSET_CAPTURE);

    if($num_matches > 0){
        $script = "<script src='" . $script . "' type='text/javascript'></script>\n";
        $page = substr_replace($page, $script, $matches[1][1], 0);
    }

    return $page;
}

function addStyle($script, $page){
    /*  Specific version of the templating function which adds the specified
    **  style to the bottom of the <head> section.
    */

    $matches = Array();
    $pattern = "/(<\/head>)/i";

    $num_matches = preg_match($pattern, $page, $matches, PREG_OFFSET_CAPTURE);

    if($num_matches > 0){
        $script = "<link href='" . $script . "' rel='stylesheet' type='text/css' />\n";
        $page = substr_replace($page, $script, $matches[1][1], 0);
    }

    return $page;
}

function setTitle($title, $page){
    /*  Specific version of the templating function which looks for
    **  the title of an html page.  If no title tag is found it returns
    **  the unaltered page.
    */

    $matches = Array();
    $pattern = "/<title>([^<]*)<\/title>/i";

    $num_matches = preg_match($pattern, $page, $matches, PREG_OFFSET_CAPTURE);

    if($num_matches > 0){
        $page = substr_replace($page, htmlentities($title), $matches[1][1], 0);
    }
    return $page;
}

function addTitle($page){
    //inserts an empty <title> tag in the <head> section
    $matches = Array();
    $pattern = "%(</head>)%i";

    $num_matches = preg_match($pattern, $page, $matches, PREG_OFFSET_CAPTURE);

    if($num_matches > 0){
        $page = substr_replace($page, "<title></title>", $matches[1][1], 0);
    }
    return $page;
}

function getSkeleton(){
    //simply returns an HTML page skeleton
    $skeleton = "<!DOCTYPE html><html><head></head><body></body></html>";
    return $skeleton;
}
?>