<?php
# Copyright (c) 2009 Ben Webb <bjwebb67@googlemail.com>
# Released as free software under the MIT license,
# see the LICENSE file for details.
# Adapted by (c) 2011 David Carpenter <caprenter@gmail.com>
# All changes released as free software under the MIT license,
# see the LICENSE file for details.
error_reporting(0);
$i =0;
$newurl = ("data/WB/WB_AR.xml");
include "sort.php";
//$sort_terms = array("title", "VenueName", "ProviderName", "cost", "la", "ward");
//$sortopts = array(" "=>"Default","title"=>"Title","VenueName"=>"Venue Name","ProviderName"=>"Provider Name","cost"=>"Cost","la"=>"LA","ward"=>"Ward");
$sort_terms = array("title", "iati-identifier");
$sortopts = array(" "=>"Default","title"=>"Title","iati-identifier"=>"IATI ID");
//$paramform = array("Max Results"=>"MaxResults","Page"=>"Page","Start Date (yyyy-mm-dd)"=>"searchDate","Day"=>"onday");

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
   
    $cid = $activity->attributes()->id;
    $cid = $activity->{'iati-identifier'};
    echo "<li>";
        if ($level > 0)
            echo "<a href=\"javascript:toggle('$prefix-$id-$i')\" class=\"exp\">".ucwords($activity->getName())."</a>: ";
        if ($cid) {
          echo "<span class=\"num\">".$cid."</span> ";
          echo "<a href=\"javascript:toggle('$prefix-$id-$i')\" class=\"exp\">".$activity->title."</a>";
        }
        //if ($level==0 && $_SESSION["times"] !== false) {
        //    if ($_SESSION["sort_group"] === FALSE || $_SESSION["sort"]) $format = "d/m/Y H:i";
        //    else $format = "H:i";
        //    echo " <span class=\"time\">(".date($format, strtotime((string)$activity->Starts))." - ".date($format, strtotime((string)$activity->Ends)).")</span> ";
        //}
        if ( ($_SESSION["actvis"] && $level==0) || ($_SESSION["cvis"] && $level>0) ) {
          echo "<ul id=\"$prefix-$id-$i\" class=\"actinfo\">";
        } else {
          echo "<ul id=\"$prefix-$id-$i\" class=\"actinfo\" style=\"display: none;\">";
        }
        foreach ($activity->children() as $child) {
            if (count($child->children())>0) {
                print_list($id, $child, $prefix."-".$child->getName(), $level+1);
            }
            else {
                if ($_SESSION["showblank"] || $child != "") {
                    echo "<li><span class=\"label\">".ucwords($child->getName())."</span>: ";
                      if (count($child->attributes())>0) {
                        echo " [";
                          foreach($child->attributes() as $a => $b) {
                              echo $a . '="' . $b . "\"\n";
                          }
                        echo "]";
                      }
                    echo "<br/>" .(string)$child[0];
                    echo "</li>";
                }  
                   
            }
        }
        echo "</ul>";
    echo "</li>";
     $i++;
}

session_start();
?>
<html>
<head>
  <title>Show My IATI Data</title>
  <link rel="stylesheet" href="style.css" type="text/css" />

	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
  <script language="javascript">
        function toggle(elem) {
            if (document.getElementById(elem).style.display == 'none') document.getElementById(elem).style.display = '';
            else document.getElementById(elem).style.display = 'none';
        }
  </script>
  <style><!-- 
    SPAN.searchword { background-color:yellow; }
    // -->
</style>
<script src="http://links.tedpavlic.com/js/searchhi_slim.js" type="text/javascript" language="JavaScript"></script>
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
          <!--<a class="logo ir" href="/">Show My IATI Data</a>-->
          <a class="logo dc_title" href="/">Show My IATI Data</a>
			
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
      </form>

			<!--<form method="get" id="searchform" action="http://iatistandard.org/">

				<div id="search">
					<input value="Enter search keyword" onclick="this.value='';" name="s" id="s" type="text">
					<input name="" src="http://iatistandard.org/wp-content/themes/freshnews3/images/btn-search-trans.png" value="Go" class="btn" type="image">
				</div><!--/search -->  
			<!--</form>--><!-- #searchform -->
			
						
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
         // if ($_REQUEST["days"] !== FALSE) $_SESSION["days"] = $_REQUEST["days"];
         // foreach($paramform as $key=>$field) {
          //    $_SESSION["pparams"][$field] = $_REQUEST[$field];
         // }
          
         if (in_array($_REQUEST["sort"], $sort_terms)) {
              $_SESSION["sort"] = $_REQUEST["sort"];
              if (isset($_REQUEST["sort_order"]) && $_REQUEST["sort_order"] == "on")
                  $_SESSION["sort_order"] = "desc";
             else
                 $_SESSION["sort_order"] = "asc";
         }
          else $_SESSION["sort"] = "";
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
          //$bit = split("http://feeds.plings.net/xml.activity.php/",$url);
          //if ($bit && count($bit)==2 && $bit[0]=="") {

              $newurl = $url;
              $_SESSION["url"] = $newurl;
              
    ?>
      <div class="content-column-1">
        <div class="url">
          <h3>Current URL: <?php if(safeurl($newurl) == "") { echo 'none'; } else { echo safeurl($newurl); } ?></h3> <a id="edit_link" href="javascript:toggle('urledit')">edit</a>
        </div>
        <div id="urledit" style="display: none;">
          <form method="post" action="index.php">
            <input type="text" name="url" size="80" value="<?php echo $newurl; ?>" />
            <input type="submit" value="Submit" />
          </form>
        </div>
      </div>
      <div id="sidebar">
      <h3>Options</h3>
      <div class="options">
        <form method="post" action="?action=update">
            <!--<div class="days">
            Days: 
            <select name="days">
                <?php/* foreach(array(0,1,2,3,4,5,6,7,14,30,60,90,120) as $i) {
                    echo "<option value=\"$i\"";
                    if ($_SESSION["days"] == $i) echo " selected ";
                    echo ">$i</option>";
                } */?>
            </select>
            </div>-->
            
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
            <!--Group<input type="checkbox" name="sort_group" <?php //if ($_SESSION["sort_group"] !== false) echo "checked"; ?>>-->
            </div>
            
            <!--<div class="params">
            <?php /*foreach($paramform as $key=>$field) {
                if ($field=="onday" || $field=="searchDate") $size = 7;
                else $size=4;
                echo $key.": <input type=\"text\" name=\"".$field."\" size=\"".$size."\" value=\"".$_SESSION["pparams"][$field]."\"/> ";
            } */?>
            <br/>
            </div>-->
            
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
      </div><!--end Form div-->
      
  <?php 
          libxml_use_internal_errors(true); //suppress and save errors
          $activities = simplexml_load_file($newurl); 
          if (!$activities) {
            echo '<div class="content-column-1 errors">';
              echo "Sorry, could not get IATA compliant data from the supplied file!<br/>";
              echo "Failed loading XML\n";        
              foreach(libxml_get_errors() as $error) {
                echo "\t", $error->message;
              }
            echo '</div>';
            print('<script language="javascript">
                    toggle(\'urledit\');
                    toggle(\'edit_link\');
                </script>');
            //$_SESSION["url"] = "";
  
          } else {
            //print_r($xml);
           // echo "<div>";
           // foreach ($xml->queryDetails->children() as $child) {
           //     if ((string)$child) echo $child->getName().": ".$child."<br/>";
           // }
           // echo "</div>";
            
            if ($_SESSION["sort"]) {
                $activities = sort_plings_xml($activities,$_SESSION["sort"],$_SESSION["sort_order"]);
            }
            if ($activities) {
             // echo'yes';
                echo "<ul><ul>";
                if ($_SESSION["sort_group"] === false) echo "</ul><li><h3>Results:</h3></li><ul class=\"actinfo\">";
                $oheader = "";
                $i = 0;
                foreach($activities as $activity) {
                  //print_r($activity);
                    $id = $activity->{'iati-identifier'};
                    //echo $id;
                    if ($_SESSION["sort_group"] !== false) {
                        if (!$_SESSION["sort"]) {
                            $header = date("l, jS F Y", strtotime($activity->Starts));
                            $header = $newurl;
                        } elseif ($_SESSION["sort"] == "title") {
                            $header = (string)$activity->title;
                       /* } elseif ($_SESSION["sort"] == "VenueName") {
                            $header = (string)$activity->venue->Name;
                        } elseif ($_SESSION["sort"] == "ProviderName") {
                            $header = (string)$activity->provider->Name;
                        } elseif ($_SESSION["sort"] == "cost") {
                            $header = (string)$activity->Cost;
                        } elseif ($_SESSION["sort"] == "la") {
                            $header = (string)$activity->venue->LAName." (".$activity->venue->LA.")";
                        } elseif ($_SESSION["sort"] == "ward") {
                            $header = (string)$activity->venue->WardName." (".$activity->venue->Ward.")";*/
                        }
                        if ($header != $oheader) {
                            echo "</ul><li><h3>".$header."</h3></li><ul class=\"actinfo\">";
                            $oheader = $header;
                        }
                    }
                    print_list($id, $activity, "actinfo");
                    $i++;
                }
                echo "</ul></ul>";
                #echo $i;
            }
            else {
                echo "Sorry, no activities were found in this file!";
            }
        } 
      
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
        Show My IATI Data is free software based on ShowMyPlings code by Ben Webb, you can download the source <a href="">here</a>.
      </div>
    </div>
          
  </div><!--site wrapper-->
</body>
</html>
