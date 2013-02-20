<?php 
function safeurl($url) {
    if ($pos = strpos($url,"?APIKey=")) {
        return substr($url,0,$pos+17)."...".substr($url,$pos+43);
    }
    else return $url;
}

function checho($f) {
                if ($_SESSION[$f]) echo checked; 
}

function print_list($id, $activity, $prefix,$level=0) {
  global $i;
  $j=0;
    $id = trim($id); //the id is used in the javascript toggle and won't like additonal spaces in it
    $cid = $activity->attributes()->id;
    $cid = $activity->{'iati-identifier'};
    //For organisation files. If iati-identifier is missing use name instead
    if (!$cid) {
      $cid = $activity->{'name'};
    }
    if ( (isset($_SESSION["actvis"]) && $level==0) || (isset($_SESSION["cvis"]) && $level>0) ) {
      $plus_sign = "- ";
    } else {
      $plus_sign = "+";
    }
    echo "<li>";
        if ($level > 0) {
            echo "<a id = \"$i\" href=\"javascript:toggle2('$prefix-$id-$i','$i')\" class=\"exp\">" . $plus_sign . "</a>";
            echo "<a href=\"javascript:toggle2('$prefix-$id-$i','$i')\" class=\"exp\">".ucwords($activity->getName())."</a>: ";
            //Display any attributes that the top level element in this list may have. Organisation files only I think
            if (count($activity->attributes())>0) {
              echo "<span class=\"attribute\"> [";
                foreach($activity->attributes() as $a => $b) {
                    echo $a . '="' . $b . '"&nbsp;';
                }
              echo "]</span>";
            }
        }
        if ($cid) {
          echo "<a id = \"$i\" href=\"javascript:toggle2('$prefix-$id-$i','$i')\" class=\"exp\">" . $plus_sign . "</a>";
          echo "<span class=\"num\">".$cid."</span> ";
          echo "<a href=\"javascript:toggle2('$prefix-$id-$i','$i')\" class=\"exp\">".$activity->title."</a>";
        }
        //if ($level==0 && $_SESSION["times"] !== false) {
        //    if ($_SESSION["sort_group"] === FALSE || $_SESSION["sort"]) $format = "d/m/Y H:i";
        //    else $format = "H:i";
        //    echo " <span class=\"time\">(".date($format, strtotime((string)$activity->Starts))." - ".date($format, strtotime((string)$activity->Ends)).")</span> ";
        //}
        if ( (isset($_SESSION["actvis"]) && $level==0) || (isset($_SESSION["cvis"]) && $level>0) ) {
          echo "<ul id=\"$prefix-$id-$i\" class=\"actinfo\">";
        } else {
          echo "<ul id=\"$prefix-$id-$i\" class=\"actinfo\" style=\"display: none;\">";
        }
        foreach ($activity->children() as $child) {
          
            if (count($child->children())>0) {
                $i++;$j++;
                print_list($id, $child, $prefix."-".$child->getName(), $level+1);
            }
            else {
                if ($_SESSION["showblank"] || $child != "") {
                  if($j&1) { $oddeven = "odd"; } else { $oddeven="even"; } 
                    echo "<li class=\"" . $oddeven ."\"><div><span class=\"label\">".ucwords($child->getName())."</span>: ";
                      if (count($child->attributes())>0) {
                        echo "<span class=\"attribute\"> [";
                          foreach($child->attributes() as $a => $b) {
                              echo $a . '="' . $b . '"&nbsp;';
                          }
                        echo "]</span>";
                      }
                    //echo "<br/>"; 
                    echo "&nbsp;";
                    echo (string)$child[0];
                    echo "</div></li>";
                    $j++;
                }  
                   
            }
        }
        echo "</ul>";
    echo "</li>";
     $i++;
}

function make_xml_into_array ($xurl, $cacheFile,$freshness){
 
//depends upon having 'clsParseXML.php' included

//***This is all the collecting the feed, and saving it to a cache file. Uses clsParseXML***//
//Checks the cached file if less than 'freshness' mins old then check the feed and re-populate the cache with fresh data
if ($freshness == FALSE) {
  $freshness = 720;
} 
//echo $freshness;
$seconds = (60*$freshness);

//if ( filemtime( "cache/cache_plingstoday.txt" ) < (time()-$seconds) ) {
if ( filemtime( $cacheFile ) < (time()-$seconds) || filemtime($cacheFile) == FALSE) {

    //Overide xml variable for now
    //$xml_plings = 'http://feeds.plings.net/xml.activity.php/1/la/00BS';
    //$xml_url = $xml_plings;
    $xml_url = $xurl;

    //check url exists with curl. This is the first step in checking for a valid feed...
    // create a new curl resource
    $ch = curl_init();
    //$url = "http://www.google.com/does/not/exist";


    // set URL and other appropriate options
    curl_setopt($ch, CURLOPT_URL, $xml_url);
    curl_setopt($ch,CURLOPT_TIMEOUT,30);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //Follow re-directs:
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    // grab URL and pass it to the browser - as CURLOPT_RETURNTRANSFER is set, it returns the page
    //if true. Returns false if not valid
    $output = curl_exec($ch);

    // Get response code
    $response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch); // close cURL handler

      if (empty($output)) {
      $last_modified = filemtime($cacheFile);

        $errormsg = 'Sorry, we can\'t grab the most up to date Activities (next 24hrs) feed from Plings.<br/>This data was last updated: ';
        $errormsg .= date("m/d/y H:i:s", $last_modified);
      } else {
      //echo $response_code;
      // If the response is 200 - i.e. ok, then we proceed to parse the feed
          if ($response_code == '200') {

           //Write the data to a file
	         $cache = fopen ( $cacheFile, "w" );
		       fwrite( $cache, $output );
		       fclose( $cache );
            
        } //end 'if response code =200 - ie. we've refreshed the data in the cache
      
        else {
          //print ('could not refresh the cache');
        } //end 'if response code =200 else
      } //end if output empty else..
  } //end of 'if cache file is too old then refresh it'

  
//Start buliding the output
//Always bulid the page from the cache - fresh data is written to the cache first.

//$cache = file_get_contents( $cacheFile );
//print_r ($xml);
//if (filesize($cacheFile)< 20000000000000 ) {
    
    $xml = file_get_contents($cacheFile);
    //echo $xml;
    /* Some safety against XML Injection attack
     * see: http://phpsecurity.readthedocs.org/en/latest/Injection-Attacks.html
     * 
     * Attempt a quickie detection of DOCTYPE - discard if it is present (cos it shouldn't be!)
    */
      
    $collapsedXML = preg_replace("/[[:space:]]/", '', $xml);
    //echo $collapsedXML;
    if(preg_match("/<!DOCTYPE/i", $collapsedXML)) {
        //throw new InvalidArgumentException(
       //     'Invalid XML: Detected use of illegal DOCTYPE'
       // );
        //echo "fail";
      return FALSE;
    } else {
      $oldValue = libxml_disable_entity_loader(true);
      $dom = new DOMDocument();
      $dom->loadXML($xml);
      $xml = simplexml_import_dom($dom);
      //$xml = simplexml_load_file($cacheFile);
      libxml_disable_entity_loader($oldValue);
      //***Now we have an array with all the XML data in it.
      return $xml;
    }

//} else {
  //$xml = FALSE;
//}
//print_r ($xml);
//echo $xml;

}

function nice_file_name($url) {
$url = preg_replace("/http:\/\//"," ",$url);
$url = preg_replace("/\//","_",$url);
return $url;
}
?>
