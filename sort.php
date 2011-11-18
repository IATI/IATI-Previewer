<?php
# Copyright (c) 2009 David Carpenter <david@substance.coop>
# Released as free software under the MIT license,
# see the LICENSE file for details.

/*Takes plings (activity or venue) XML and sorts it by the given parameter*/
/*The results are automatically orderd alphabetically, and then by time*/
/*To reverse the order supply 'desc' as the third parameter in this function*/

function sort_plings_xml($originalactivities,$term,$order='asc') {

  //Set up some arrays that we can sort the data by...
  $start=array();
  $sortby =array();
  $activities=array();

  //Loop through the XML grabbing the info we want and pushing this into new arrays
  foreach($originalactivities as $activity){

         switch ($term) {
            case 'title':
       			  array_push ($sortby, htmlspecialchars(stripslashes($activity->title)));
       			 	break;
       		  case 'iati-identifier':
       			  array_push ($sortby, htmlspecialchars(stripslashes($activity->{'iati-identifier'})));
       			  break;
       		  /*case 'ProviderName':
       			  array_push ($sortby, htmlspecialchars(stripslashes($activity->provider->Name)));
       			  break;
       			case 'cost':
       			  array_push ($sortby, htmlspecialchars(stripslashes($activity->Cost)));
       		  	break;
   		  	
   		  	case 'la';
   		  	    array_push($sortby, htmlspecialchars(stripslashes($activity->venue->LAName)));
   		  	    break;
   		  	case 'ward';
   		  	    array_push($sortby, htmlspecialchars(stripslashes($activity->venue->WardName)));
   		  	    break;*/
            }
            
            //start time
              array_push($start, htmlspecialchars(stripslashes($activity->{'iati-identifier'})));
           // echo $start[$j];
            //Make an $activities array cos I don't understand the $xml array!         
              array_push($activities, $activity);
  }
  //Debug and experiments      
  //print_r($sortby);
  //print_r($start);
  //$start = array_reverse($start);


  //If order is 'desc' then once sorted we reverse the array - but this means we get the data sorted by e.g. Name alphabetically reversed, and 
  //with the start time showing the last instance to the first - so we probably want, first instance to the last...
  //Reverse the order of the start times....
  if ($order == 'desc') {
        $start=array_reverse($start);
  }

  //We've got our arrays now, so lets sort it
  //Sorts $activities by our term, then by time - cool, 
  array_multisort($sortby, $start,$activities);

  //print_r($activities);

  //re-order our acticvities array descending if required...
  if ($order == 'desc') {
        $activities=array_reverse($activities);
  }

  //This is for checking...
  /*
  $sorted=array();   
  foreach ($activities as $activity){
  array_push($sorted, $activity->Name.$activity->Starts);
  }
  print_r($sorted);
  */
  //Done!
  return $activities;

}
?>
