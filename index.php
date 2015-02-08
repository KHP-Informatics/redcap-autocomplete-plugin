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

// header
require_once APP_PATH_DOCROOT . 'ProjectGeneral/header.php';



function register_autocomplete_field($project_id, $ac_instrument, $ac_field, $ac_dictionary){
  // make a note in the redcap database that this text field uses this dictionary. 
  // the hook will then check this database and add in the necessary javascript when the page is loaded. 
  var_dump($project_id);
  var_dump($ac_instrument);
  var_dump($ac_field);
  var_dump($ac_dictionary);
}




// fetch forms and fields:
$this_i = $_GET["instrument"];
$instruments = REDCap::getInstrumentNames();
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

// fetch available dictionary information
$dict_dir = APP_PATH_DOCROOT.'../plugins/autocomplete/dictionaries';
$dictionaries = array();
if  (! file_exists($dict_dir)){
  echo "<p>No dictionary directory found. Please contact your redcap administrator</p>";
}else {
  $handle = opendir($dict_dir);
  if ($handle){
    while($file = readdir($handle)){
      $path = pathinfo($file);
      if($path['extension'] == 'sqlite'){ //only look at files with the right file extension
         $dictionaries[$file] = $path['filename'];
      }
    }
    closedir($handle);
  } else {
    echo "<p>Couldn't open dictionary directory for reading. Please contact your Redcap administrator</p>";
  }
}

// has the form been submitted? If so, process the request
if ($_SERVER["REQUEST_METHOD"] == "POST"){
  $ac_instrument = $_POST['ac_instrument_name'];
  $ac_field = $_POST['ac_field_name'];
  $ac_dictionary = $_POST['ac_dictionary'];
  register_autocomplete_field($project_id, $ac_instrument, $ac_field, $ac_dictionary);
}


//START HTML CONTENT 
?>


<!-- Load the javascript code for this app -->
<script src="<?php echo APP_PATH_WEBROOT_FULL?>plugins/autocomplete/functions.js" type="text/javascript"> </script> 

<h3 style="color:#800000;">Auto-Complete Fields</h3>

<div id="results" name="results" style="display:none"> </div>


<p>The Auto-complete plugin uses dictionaries of terms which must be available on your Redcap server. If you need to upload a new dictionary, please speak to your Redcap administrator</p>

<hr/>
<!-- Create a new Auto-complete Field  -->
<div>
<h4>Create a new Autocomplete Field</h4>

<form id="newAutoCompleteForm"  enctype="multipart/form-data" method="post" 
                        action="<?php echo APP_PATH_WEBROOT_FULL;?>plugins/autocomplete/?pid=<?php echo $project_id ?>&instrument=<?php echo $this_i ?>"
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
       foreach($fields as $name){
         echo "<option value=$name>$name</option>";
       }
     ?>
   </select></td>
   </tr>
   <tr>
   <td><label for="ac_dictionary">Auto-completion Dictionary</td>
   <td><select id="ac_dictionary" name="ac_dictionary">
   <option value=''>Choose Dictionary...</option>
   <?php
     foreach($dictionaries as $file_name => $display_name){
      echo "<option value=$file_name>$display_name</option>";
     }
   ?>
   </select></td>
   </tr>
   <tr><td colspan=2><input type="submit" value="submit"></td></tr>
   </div>
   </table>



</form>
</div>

<hr/>
<!-- Manage Existing Auto-complete fields -->
<div>
<h4>Manage Autocomplete Fields</h4>



</div>


<?php 
// END HTML CONTENT

// Footer
require_once APP_PATH_DOCROOT . 'ProjectGeneral/footer.php';
