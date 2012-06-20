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

function addAfterMany($elementToAdd, $options, $class, $element, $afterClass, $page){
    /*  Searches for an element that matches the specified characteristics
    **  and adds a div with the specified id and classes after it.  If
    **  multiple elements match the div is added after each one.
    **  TODO: Add matching of self-closing tags
    */
    $matches1 = Array();
    $matches2 = Array();
    $matches3 = Array();
    $matches4 = Array();
    if($class == "" || $class == NULL){
        $class = "";
    }
    else{
        $class = "class='" . $class . "'";
    }
    if($afterClass == "" || $afterClass == NULL){
        $afterClass = "";
    }
    else{
        $afterClass = "class='" . $afterClass . "'";
    }
    //match an opening tag of the search element with the class identifiers
    $openWithClass = "%(<\s*" . $element . "\s*" . $afterClass . "\s*>)%";
    //match an opening tag of the search element
    $open = "%(<\s*" . $element . "[^>]*>)%";
    //match a closing tag of the search element
    $close = "%(</\s*" . $element . "\s*>)%";
    //match the next opening tag of the search element if there is no closing tag
    //of the search element first
    $nextOpen = "%(<[^/]*" . $element . "[^>]*>)%";
    //non-greedy match of a whole search element
    $whole = "%(<\s*" . $element . "[^>]*>).*?</\s*" . $element . "\s*>%";

    /* So basically what's going on here is we do a non greedy search for any element
    ** of the specified type. As long as the HTML is well formed (and if it comes from
    ** us it should be) this will always go for the deepest node first.  After we match
    ** that we yank out of the tree and put it in an array to await further processing.
    ** This way we can loop through the tree indefinitely.
    */
    $nodes = Array();
    $data = $page;
    while(preg_match($whole, $data, $matches, PREG_OFFSET_CAPTURE) > 0){
        $data = substr_replace($data, "", $matches[0][1], strlen($matches[0][0]));

        $sibling = False;
        if(preg_match($nextOpen, $data, $matches3) > 0){
            $sibling = True;
        }
        //we define the new div if the element matches our specifications
        $add = "";
        if(preg_match($openWithClass, $matches[0][0], $matches2) > 0){
            $add = "<" . $elementToAdd . " " . $class . " " . $options . "></div>";
        }

        $nodes[] = Array('data' => $matches[0][0], 'add' => $add, 'pos' => $matches[0][1], 'end' => $matches[0][1] + strlen($matches[0][0]), 'sibling' => $sibling);
    }
    //pop the stuff off in reverse order
    while(($node = array_pop($nodes)) != NULL) {
        //if the node is a sibling we have to make sure it gets placed in the right order
        //we popped the siblings off in reverse
        $node2 = array_pop($nodes);
        while($node2 != NULL && $node2['sibling'] == true){
            $add = $node2['data'] . $node2['add'];
            $node['data'] = $add . $node['data'];
            $node2 = array_pop($nodes);
        }
        $data = substr_replace($data, $node['data'] . $node['add'], $node['pos'], 0);
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