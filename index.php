<?php
/**
 * PLUGIN NAME: Autocomplete
 * DESCRIPTION: A plugin to faciliate generation of Auto-complete fields
 * VERSION: 0.1
 * AUTHOR: Cass Johnston <cassjohnston@gmail.com>
 */

// Call the REDCap Connect file in the main "redcap" directory
require_once "../../redcap_connect.php";

// Bail if we don't have a project ID
if(empty($project_id)){
  // no idea how you're supposed to do this gracefully in Redcap.

}

// OPTIONAL: Display the project header
require_once APP_PATH_DOCROOT . 'ProjectGeneral/header.php';


/* get any instrument provided as a URI param*/
$this_i = $_GET["instrument"];

/* Fetch all other instruments for this project */
$instruments = REDCap::getInstrumentNames();

/* we really only want text fields */
if($this_i){
  $fields = REDCap::getFieldNames($this_i);
  foreach ($fields as $i =>$field){
    if(REDCap::getFieldType($field) != 'text'){
      unset($fields[$i]);
    }
  }
}else{
  $fields='';
}

// Your HTML page content goes here
?>

<!-- Load the javascript code for this app -->
<script src="<?php echo APP_PATH_WEBROOT_FULL?>plugins/autocomplete/functions.js" type="text/javascript"> </script> 

<h3 style="color:#800000;">
  Auto-Complete Fields
</h3>

<div id="results" name="results" style="display:none"> </div>

<div>
  <h4>Upload a new completion dictionary</h4>
  <form id="newAutoCompleteDictionary" target="results" enctype="multipart/form-data" method="post" 
                        action="<?php echo APP_PATH_WEBROOT_FULL;?>plugins/autocomplete/create_dictionary.php"
                        style="padding:10px 10px 20px 10px;">
    <p><input type="file" id="dictionary_file" name="dictionary_file"/></p>
    <input type="submit" value="Upload" />
  </form>
  
</div>




<div>
<h4>Create a new Autocomplete Field</h4>

<form id="newAutoCompleteForm" target="results" enctype="multipart/form-data" method="post" 
                        action="<?php echo APP_PATH_WEBROOT_FULL;?>plugins/autocomplete/create_autocomplete.php"
                        style="padding:10px 10px 20px 10px;">

   <table>
   <tr>
   <td><label for='ac_instrument_name'>Instrument</label></td>
   <td><select id='ac_instrument_name' name='ac_instrument_name' onChange="window.location='<?php echo APP_PATH_WEBROOT_FULL ?>plugins/autocomplete/?pid=<?php echo $project_id ?>&instrument=' + this.value; ">
     <option value=''>Choose Instrument...</option>
     <?php
       foreach ($instruments as $name => $display_name){
          echo "<option value='$name' ";
          if ($name == $this_i){ echo " SELECTED "; }
          echo ">$display_name</option>";
       }

      ?>
   </select></td>
   </tr>
   <tr>
   <td><label for='ac_field_name'>Field Name:</label></td>
   <td><select id='ac_field_name' name='ac_field_name'>
     <option value=''>Choose Field...</option>
     <?php
       foreach($fields as $name => $display_name){
         echo "<option value=$name>$display_name</option>";
       }
     ?>
   </select></td>
   </tr>
   <tr><td colspan=2><input type="submit"></td></tr>
   </div>
   </table>



</form>
</div>


<div>
<h4>Manage Autocomplete Fields</h4>



</div>

<?php

// OPTIONAL: Display the project footer
require_once APP_PATH_DOCROOT . 'ProjectGeneral/footer.php';
