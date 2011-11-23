<?php
# Copyright (c) 2009 Ben Webb <bjwebb67@googlemail.com>
# Released as free software under the MIT license,
# see the LICENSE file for details.
# Adapted by (c) 2011 David Carpenter <caprenter@gmail.com>
# All changes released as free software under the MIT license,
# see the LICENSE file for details.
error_reporting(0);
$i =0;

include "functions/sort.php";
include "functions/all.php";

$sort_terms = array("title", "iati-identifier");
$sortopts = array(" "=>"Default","title"=>"Title","iati-identifier"=>"IATI ID");

session_start();
?>
<html>
<head>
  <title>Preview IATI Data</title>
  <link rel="stylesheet" href="style.css" type="text/css" />
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />

  <!--Thanks to http://www.randomsnippets.com/2008/02/12/how-to-hide-and-show-your-div/-->
  <script language="javascript">
    function toggle2(showHide, switchText) {
	    var ele = document.getElementById(showHide);
	    var text = document.getElementById(switchText);
	    if(ele.style.display == "") {
        		ele.style.display = "none";
		    text.innerHTML = "+";
      	}
	    else {
		    ele.style.display = "";
		    text.innerHTML = "- ";
	    }
    }
  </script>
  
  <style><!-- 
    SPAN.searchword { background-color:yellow; }
    // -->
  </style>
  <!--<script src="http://links.tedpavlic.com/js/searchhi_slim.js" type="text/javascript" language="JavaScript"></script>-->
   <script src="javascript/searchhi_slim.js" type="text/javascript" language="JavaScript"></script>
</head>
<body onload="highlightSearchTerms('search');">
<div id="sitewrapper">
  
  <div id="topbar">
    <ul><li><a href="http://www.iatiregistry.org/">IATI Data</a></li>
      <li><a href="http://www.iatistandard.org/">IATI Standard</a></li>
      <li><a href="http://www.aidtransparency.net/">Aid Transparency</a></li>
      <li><a href="http://www.aidtransparency.net/iati-websites">IATI Sites:</a></li>
    </ul>
  </div>
 
  <div id="header">
		<div class="header-wrapper">
      <a class="logo dc_title" href="/">Preview IATI Data</a>
			
      <div class="nav">
        <ul id="menu-primary-navigation" class="menu-main">
          <li id="menu-item-3" class="menu-item menu-item-type-taxonomy menu-item-object-category menu-item-1482">
            <a href="?action=new" title="New Url">New Data</a>
          </li>
          <li id="menu-item-2" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1478">
            <a href="index.php" title="Refresh">Refresh</a>
          </li>
        </ul>
      </div>
      
      <form id="searchform" name="searchhi" action="" onSubmit="localSearchHighlight(document.searchhi.h.value); 
                                                        document.searchhi.reset(); document.searchhi.h.focus(); return false;">
          <div id="search">
            <p>Search: <input name="h" value="" /></p>
            <input type="button" value="Remove" onclick="unhighlight(document.getElementsByTagName('body')[0]); 
                                                                    document.searchhi.reset(); document.searchhi.h.focus();" />
            <input type="button" value="Highlight" onclick="localSearchHighlight(document.searchhi.h.value); 
                                                             document.searchhi.reset(); document.searchhi.h.focus();" />
          </div>
      </form><!-- #searchform -->
			
			<div class="fix"></div>

		</div><!-- .header-wrapper -->
	</div><!-- #header -->
  
  <div id="page-content">
    <div id="wrapper">
    <div id="main">
    <?php
      if (isset($_GET["action"]) && $_GET["action"] == "new") {
          $_SESSION["url"] = "";
          $_SESSION["apikey"] = "";
          $_SESSION["showblank"] = TRUE;
      }
      elseif (isset($_GET["action"]) && $_GET["action"] == "update") {
          
         if (in_array($_REQUEST["sort"], $sort_terms)) {
              $_SESSION["sort"] = $_REQUEST["sort"];
              if (isset($_REQUEST["sort_order"]) && $_REQUEST["sort_order"] == "on") {
                  $_SESSION["sort_order"] = "desc";
              } else {
                 $_SESSION["sort_order"] = "asc";
              }
          } else {
            $_SESSION["sort"] = "";
          }
          foreach (array("actvis","cvis","showblank","sort_group","times") as $f) {
            if (isset($_REQUEST[$f]) && $_REQUEST[$f] == "on") $_SESSION[$f] = true;
              else $_SESSION[$f] = false;
          }
      }
      if (isset($_REQUEST["url"]) && $_REQUEST["url"]) {
        $url = htmlentities($_REQUEST["url"]);
      } else {
        //if (isset($_SESSION["url"])) {
         $url = htmlentities($_SESSION["url"]);
        //} else {
         // $url = "";
         // $_SESSION["url"] = "";
        //}
      }
      if ($url) {
        $newurl = $url;
        $_SESSION["url"] = $newurl;
              
    ?>
      <div class="content-column-1">

      </div>
      <div id="sidebar">
      <h3>Options</h3>
      <div class="options">
        <form method="post" action="?action=update">            
            <div class="sort">
            Sort by:
            <select name="sort">
                <?php
                    foreach ($sortopts as $key=>$field) {
                        echo "<option value=\"$key\"";
                        if ($_SESSION["sort"] == $key) echo " selected ";
                        echo ">$field</option>";
                    }
                ?>
            </select>
            <br/>Reverse Sort <input type="checkbox" name="sort_order" <?php if ($_SESSION["sort_order"] == "desc") echo "checked"; ?>>
            </div>
            
            <div class="show">
              <!--Show times: <input type="checkbox" name="times" <?php if ($_SESSION["times"] !== false) echo "checked"; ?>> |-->
              Show blank fields: <input type="checkbox" name="showblank" <?php checho("showblank"); ?> /> <br/><br/>
              Show expanded: 
              <ul>
                <li>Activities<input type="checkbox" name="actvis" <?php checho("actvis"); ?> /> </li>
                <li>Children (transactions etc.)<input type="checkbox" name="cvis" <?php checho("cvis"); ?> /></li>
              </ul>
            </div>
            <div class="submit"><input type="submit" value="update"/></div>
        </form>
        </div>
        <div class="options">
          <p>This is a preview of a single raw data file published using the <a href="http://iatistandard.org/">IATI XML standard</a>.<br>
          The preview function allows you to view the data in the way it was published, without analysing or changing the raw data.</p>
          <p>You can find out about other tools for accessing IATI-compliant data from a range of sources on the <a href="http://iatiregistry.org/using-iati-data">using IATI data page of the IATI Registry</a>.</p>
          <p>Note: This tool cannot open very large files.</p>
        </div>
      </div><!--end Form div-->
      <div class="content-column-1">
  <?php 
          libxml_use_internal_errors(true); //suppress and save errors
          $activities = make_xml_into_array ($newurl, "cache/".nice_file_name($newurl));
          if (!$activities) {
            //echo '<div class="content-column-1">';
              echo 'Sorry, could not get IATA compliant data from the supplied file!<br/>Please <a href="?action=new" title="New Url">try again</a>.<br/>';
              //echo "Failed loading XML\n";        
              //foreach(libxml_get_errors() as $error) {
                //echo "\t", $error->message;
              //}
            //echo '</div>';
            //print('<script language="javascript">
            //        toggle(\'urledit\');
            //        toggle(\'edit_link\');
             //   </script>');
            //$_SESSION["url"] = "";
  
          } else {
           //$count = $xml->count(); //php >5.3
           $count = count($activities->children()); //php < 5.3
            if ($_SESSION["sort"]) {
                $activities = sort_plings_xml($activities,$_SESSION["sort"],$_SESSION["sort_order"]);
            }
            if ($activities) {
                echo "<ul>";
                   $header = safeurl($newurl);
                   $header .= "<br/>This file has " .$count. " activities";
                   echo "<li><h3>".$header."</h3>";
                   echo "<p>Use the expand (+) and collapse (-) buttons to view and hide the details </p>";
                   echo "</li><ul class=\"actinfo\">";
                $i = 0;
                foreach($activities as $activity) {
                  //print_r($activity);
                    $id = $activity->{'iati-identifier'};
                    print_list($id, $activity, "actinfo");
                    $i++;
                }
                echo "</ul></ul>";
                #echo $i;
            }
            else {
                echo 'Sorry, no activities were found in this file!<br/>Please <a href="?action=new" title="New Url">try again</a>';
            }
        } 
      echo "</div>";
  }
  else {
  ?>
      <div class="content-column-1">
        <h2>Fetch IATI Data</h2>
        <form method="post" action="index.php">
            Please enter an address of an IATI compliant XML file.<br />
            <input type="text" name="url" size="80" /> <input type="submit" value="Submit" />
        </form>
        <h3>Examples</h3>
        World Bank<br/>
        <ul>

        <li><a href="index.php?url=http://siteresources.worldbank.org/IATI/WB-AF.xml">http://siteresources.worldbank.org/IATI/WB-AF.xml</a></li>
        <li><a href="index.php?url=http://siteresources.worldbank.org/IATI/WB-AL.xml">http://siteresources.worldbank.org/IATI/WB-AL.xml</a></li>
        </ul>
        Department for International Development, UK<br/>
        <ul>

        <li><a href="http://projects.dfid.gov.uk/iati/Region/380">http://projects.dfid.gov.uk/iati/Region/380</a></li>
        <li><a href="http://projects.dfid.gov.uk/iati/Country/BD">http://projects.dfid.gov.uk/iati/Country/BD</a></li>
        </ul>
      </div>
          

          <!--<div class="footer"><img src="logo.png" /></div>-->
  <?php } ?>
          </div>
      </div>
  </div><!--wrapper-->
  </div>
  
    <div id="footer-wrapper">
      <div id="footer">
        Preview IATI Data is free software based on ShowMyPlings code by Ben Webb, you can download the source <a href="">here</a>.
      </div>
    </div>
          
  </div><!--site wrapper-->
</body>
</html>
