<?php


/***************
*
* Dictionary Plugin
* 
****************/

/* Add in a link to the Dictionary plugin to the control centre*/
function redcap_control_center()
{

    print  "<div id='ac_cc_link' style='clear:both;margin:0 -6px 3px;border-top:1px solid #ddd;'>
              <b style='padding:5px'>Dictionaries Plugin</b>
                <div style='padding-top:5px;'></div>
                <span style='padding-left:10px'>
                  <a href='/plugins/dictionaries/'>Dictionaries</a><br/>
                </span>
            </div>";

    // Use JavaScript/jQuery to append our link to the bottom of the left-hand menu
    print  "<script type='text/javascript'>
            $(document).ready(function(){
                // Append link to left-hand menu
                $( 'div#ac_cc_link' ).appendTo( 'div#control_center_menu' );
            });
            </script>";

}


/***************
*
* Autocomplete Plugin
* 
****************/


/* Modify data entry forms to add javascript for any registered autocomplete fields */

function autocomplete($field_name, $dict_name){
  $source = APP_PATH_WEBROOT_FULL.'plugins/autocomplete/autocomplete.php?dictionary='.$dict_name;
  echo "<script type='text/javascript'>
          $(function(){add_autocomplete('$field_name', '$dict_name', '$source'); });
        </script>";

}


function load_autocomplete(){

  // current version of Redcap is using jquery 1.7.1. 
  // it's still not recognising the autocomplete function and I can't see why. it's defined in the redcap jquery base.js
  // which is loaded as far as I can see. 
  echo "<script type='text/javascript' src='".APP_PATH_WEBROOT_FULL."plugins/autocomplete/autocomplete.js'></script>";

 // try to override it with this?
 //echo '<script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>';
}

function redcap_data_entry_form($project_id, $record, $instrument, $event_id, $group_id){
 
  // scope the global MySQLi Redcap DB connection 
  global $rc_connection;

  // pull in the necessary javascript and css files 
  load_autocomplete();
 
  // Check all the text fields in this form to find any with DICT: element_enum definitions 
  $stmt = $rc_connection->prepare("select field_name, element_enum from redcap_metadata where project_id=? and form_name=? and element_type='text'") or trigger_error($conn->error);
  $stmt->bind_param('is', $project_id, $instrument);
  $stmt->execute();
  $stmt->bind_result($field_name, $element_enum);

  // Add autocomplete functionality to those fields that need it 
  while($stmt->fetch()){
    if(preg_match('/^DICT:(.*)$/', $element_enum, $dict_name)){
       autocomplete($field_name, $dict_name[1] );
    }
  }
  $stmt->close();
  
}


?>
