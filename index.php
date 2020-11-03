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

$freshness = FALSE;

session_start();
if (!isset($_REQUEST["showblank"])) {
 $_SESSION["showblank"] = TRUE;
}
?>

<!DOCTYPE html>
<html lang="en">

  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>IATI Previewer</title>
    <link rel="stylesheet" type="text/css" href="//netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css" type="text/css" >
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">

    <!--Thanks to http://www.randomsnippets.com/2008/02/12/how-to-hide-and-show-your-div/-->
    <script type="text/javascript">
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

    <style type="text/css"><!--
      SPAN.searchword { background-color:yellow; }
      // -->
    </style>
    <!--<script src="http://links.tedpavlic.com/js/searchhi_slim.js" type="text/javascript" language="JavaScript"></script>-->
   <script src="javascript/searchhi_slim.js" type="text/javascript"></script>
  </head>

  <body onload="highlightSearchTerms('search');">

    <div id="wrap">

      <div class="navbar navbar-default" role="navigation">
        <div class="container">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            </button>
            <!-- logo -->
            <!--<img src=".png" style="float:left" />-->
            <!--<a class="navbar-brand" href="index.php">Home</a>-->
            <a class="navbar-brand" href="?action=new" title="New Url">Home</a>
            <a class="navbar-brand" href="?action=refresh" title="Refresh">Refresh</a>
          </div>
          <form class="navbar-form navbar-right" role="form" id="searchform" name="searchhi" action="" onsubmit="localSearchHighlight(document.searchhi.h.value);
                                                              document.searchhi.reset(); document.searchhi.h.focus(); return false;">
            <div class="form-group">
              <input name="h" value="" placeholder="Search">
            </div>
              <div class="form-group">
                <input type="button" value="Highlight" onclick="localSearchHighlight(document.searchhi.h.value); document.searchhi.h.focus();">
              </div>
              <div class="form-group">
              <input type="button" value="Remove" onclick="unhighlight(document.getElementsByTagName('body')[0]);
                                                                      document.searchhi.reset(); document.searchhi.h.focus();">
              </div>
            </div>
          </form>

          <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav">

            </ul>
          </div><!--/.nav-collapse -->

        </div><!-- end container -->
      </div><!-- end navigation -->

      <div class="container page-header">
        <div class="row">
          <div class="col-md-2">
            <img id="logo" width="192" height="50" src="http://styles.iatistandard.org/assets/svg/source/logo-colour.svg">
          </div>

          <div class="col-md-10">
            <h1>IATI Previewer</h1>
          </div>
        </div>
      </div><!-- /.container -->

    </div><!-- end wrap -->


    <div id="main">
      <div class="container">
        <div class="row">
          <div class="col-md-7">
          <?php
            /* Process $_GET variables */
            if (isset($_GET["action"]) && $_GET["action"] == "new") {
                $_SESSION["url"] = "";
                $_SESSION["apikey"] = "";
                $_SESSION["showblank"] = TRUE;
            }  elseif (isset($_GET["action"]) && $_GET["action"] == "update") {

               if (in_array($_REQUEST["sort"], $sort_terms)) {
                    $_SESSION["sort"] = $_REQUEST["sort"];
                    if (isset($_REQUEST["sort_order"]) && $_REQUEST["sort_order"] == "on") {
                        $_SESSION["sort_order"] = "desc";
                    } else {
                       $_SESSION["sort_order"] = "asc";
                    }
                } else {
                  $_SESSION["sort"] = "";
                  $_SESSION["sort_order"] = "";
                }
                foreach (array("actvis","cvis","showblank","sort_group","times") as $f) {
                  if (isset($_REQUEST[$f]) && $_REQUEST[$f] == "on") $_SESSION[$f] = true;
                    else $_SESSION[$f] = ($f == "showblank") ? "true" : false;
                }
            } elseif (isset($_GET["action"]) && $_GET["action"] == "refresh") {
              $freshness = 1/60; //we refrech the cache
              //echo $freshness;
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

            /*Parse and display the XML */
            if ($url) {
                $newurl = $url;
                $_SESSION["url"] = $newurl;

                libxml_use_internal_errors(true); //suppress and save errors
                $activities = make_xml_into_array ($newurl, "/tmp/".nice_file_name($newurl),$freshness);
                if (!$activities) {
                  echo 'Sorry, could not get IATI compliant data from the supplied file!<br/>Please <a href="?action=new" title="New Url">try again</a>.<br/>';
                  //echo "Failed loading XML\n";
                  //foreach(libxml_get_errors() as $error) {
                    //echo "\t", $error->message;
                  //}
                } else {
                  //$count = $xml->count(); //php >5.3
                  $count = count($activities->children()); //php < 5.3
                  if ($_SESSION["sort"]) {
                      $activities = sort_plings_xml($activities,$_SESSION["sort"],$_SESSION["sort_order"]);
                  }
                  if ($activities) {
                    //URL header
                    $header = '<strong>' . safeurl($newurl) . '</strong>';
                    $header .= "<br>This file has " .$count. " activit".(($count>1)?"ies":"y");
                    //echo "<li><h3>".$header."</h3>";
                    echo '<div class="bg-info url">' . $header;

                    //Last Refreshed info
                    echo "<div class=\"refreshed\">Data last refreshed: ";
                    $filetime_cache = filemtime( "/tmp/" . nice_file_name($newurl) );
                    if (date("j") == date("j", $filetime_cache)) {
                     $day ="Today at ";
                    } else {
                     $day = "Yesterday at ";
                    }
                    echo $day;
                    echo date("H:i:s",filemtime( "/tmp/" . nice_file_name($newurl) )) . "</div>";

                    echo '<a href="https://iativalidator.iatistandard.org/organisations" target="_blank">Check this data in the IATI Validator</a> [opens in new window]';

                    echo '</div>';

                    echo "<p>Use the expand (+) and collapse (-) buttons to view and hide the details </p>";
                    //echo "</li><ul class=\"actinfo\">";
                    echo "<ul class=\"actinfo\">";

                    $i = 0;
                    foreach($activities as $activity) {
                      //print_r($activity);
                      $id = $activity->{'iati-identifier'};
                      print_list($id, $activity, "actinfo");
                      $i++;
                    }
                    echo "</ul></ul>";
                    #echo $i;
                  } else {
                      echo 'Sorry, no activities were found in this file!<br/>Please <a href="?action=new" title="New Url">try again</a>';
                  }
                }
              } else { //There is no URL to use so diplay the default home page
              ?>
              <div class="content-column-1">
                <h2>Fetch IATI Data</h2>
                <form method="get" action="index.php">
                  <div class="form-group">
                    <label for="url">Please enter the web address of an IATI compliant XML file.</label>
                    <div class="input-group">
                      <input type="text" class="form-control" id="url" name="url" aria-describedby="helpBlock" placeholder="http://">
                      <span class="input-group-btn">
                        <button class="btn btn-default" type="submit">Submit</button>
                      </span>
                    </div><!-- /input-group -->
                  </div>
                </form>
                <div class="examples">
                  <h3>...or click on the example below</h3>

                  <ul>
                    <li><a href="index.php?url=https://raw.githubusercontent.com/IATI/IATI-Extra-Documentation/version-1.05/en/activity-standard/activity-standard-example-annotated.xml
">IATI Activity Standard (version 1.05) Example XML</a>
                    </li>
                  </ul>
                </div>
              </div>
            <?php //End if (url) first time
              }
            ?>
            </div><!-- col-md-7 -->


            <div class="col-md-4 col-md-offset-1">
              <div id="sidebar">
                <?php
                 if ($url) {
                ?>
                <div class="panel panel-default">
                  <div class="panel-heading">Options</div>
                  <div class="panel-body">
                    <form method="post" action="?action=update">
                      <div class="sort">
                        Sort by:
                        <select name="sort">
                        <?php
                          foreach ($sortopts as $key=>$field) {
                            echo "<option value=\"$key\"";
                            if ($_SESSION["sort"] == $key) {
                              echo " selected ";
                            }
                            echo ">$field</option>";
                          }
                        ?>
                        </select>
                        <br>
                        Reverse Sort <input type="checkbox" name="sort_order" <?php if ($_SESSION["sort_order"] == "desc") echo "checked"; ?>>
                      </div><!-- sort -->
                      <div class="show">
                        <!--Show times: <input type="checkbox" name="times" <?php if ($_SESSION["times"] !== false) echo "checked"; ?>> |-->
                        Show blank fields: <input type="checkbox" name="showblank" <?php checho("showblank"); ?> /> <br/><br/>
                        Show expanded:
                        <ul>
                          <li>Activities <input type="checkbox" name="actvis" <?php checho("actvis"); ?> /> </li>
                          <li>Children (transactions etc.) <input type="checkbox" name="cvis" <?php checho("cvis"); ?> /></li>
                        </ul>
                      </div>
                      <div class="submit"><input type="submit" value="update"/></div>
                    </form>
                  </div>
                </div>

                <form method="get" action="index.php">
                  <div class="form-group">
                    <label for="url">Preview a different IATI file</label>
                    <div class="input-group">
                      <input type="text" class="form-control" id="url" name="url" aria-describedby="helpBlock" placeholder="http://">
                      <span class="input-group-btn">
                        <button class="btn btn-default" type="submit">Submit</button>
                      </span>
                     </div><!-- /input-group -->
                      <span id="helpBlock" class="help-block">Enter the web address of an IATI compliant XML file.</span>
                  </div>
                </form>

                  <?php //End if (url) second time
                    }
                  ?>
                <div class="panel panel-default">
                  <div class="panel-body">
                    <p>IATI Previewer allows you to browse a single raw data file published using the <a href="http://iatistandard.org/">IATI XML standard</a>.</p>
                    <p>The preview function allows you to view the data in the way it was published, without analysing or changing the raw data.</p>
                    <p>You can find out about other tools for accessing IATI-compliant data from a range of sources on the <a href="http://iatiregistry.org/using-iati-data">using IATI data page of the IATI Registry</a>.</p>
                    <p>Note: This tool cannot open very large files.</p>
                  </div>
                </div>

              </div><!--endSidebar-->
            </div><!-- col-md-4 -->

        </div><!-- row -->
      </div><!--container-->
    </div><!--main-->


  <footer class="footer">
    <div class="container">
      <p class="pull-right"><a href="#">Back to top</a></p>
      <p>IATI Previewer is free software. <br/>Source on <a href="https://github.com/IATI/IATI-Previewer/">GitHub</a>. <a href="https://github.com/IATI/IATI-Previewer/issues?state=open">Submit issues</a>.</p>
      <p>
        Built with <a href="http://twitter.github.com/bootstrap">Bootstrap</a> Bootstrap is licensed under the <a href="http://www.apache.org/licenses/LICENSE-2.0">Apache License v2.0</a>.<br/>
        Icons from <a href="http://glyphicons.com">Glyphicons Free</a>, licensed under <a href="http://creativecommons.org/licenses/by/3.0/">CC BY 3.0</a>.
      </p>
      <div id="iati-links">
        <h4>IATI Links</h4>
        <ul>
          <li><a href="http://www.aidtransparency.net/">Aid Transparency</a></li>
          <li><a href="http://www.iatistandard.org/">IATI Standard</a></li>
          <li><a href="http://www.iatiregistry.org/">IATI Data</a></li>
          <li><a href="http://support.iatistandard.org/">IATI Community</a></li>
          <li><a href="https://www.aidtransparency.net/privacy-policy">Privacy policy</a></li>
        </ul>
      </div>
    </div>
    <div id="footer-cookie">
      <div class="container">
        <aside id="text-21" class="widget-1 widget-first widget-last widget-odd widget widget_text">
          <h3 class="widget-title">Cookie Disclaimer</h3>
          <div class="textwidget">
            <p>Cookies are small text files that are stored by the browser (e.g. Internet Explorer, Chrome, Firefox) on your computer or mobile phone. This site uses anonymous Analytics cookies which allow us to track how many unique individual users we have, and how often they visit the site. Unless a user signs in, these cookies cannot be used to identify an individual; they are used for statistical purposes only. If you are logged in, we will also know the details you gave to us for this, such as username and email address. By continuing to use this site, you are agreeing to the use of cookies.</p>
          </div>
        </aside>
      </div>
    </div>

  </footer>

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-110230511-6"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-110230511-6', {'anonymize_ip': true});
</script>

</body>
</html>
