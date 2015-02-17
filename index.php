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

// Dictionary class
require_once('../dictionaries/Dictionary.php');


/* Function to register a new autocomplete field */
function register_autocomplete_field($project_id, $ac_instrument, $ac_field, $ac_dictionary){
  // scope the global database connection
  global $conn;

  // make a note in the redcap database that this text field uses this dictionary. 
  // the hook will then check this database and add in the necessary javascript when the page is loaded. 
  // We'll use the element_enum field and prefix it with DICT: to indicate that the enum values are available in a separate dictionary
  $ac_dictionary = 'DICT:'.$ac_dictionary;
  
  if($project_id && $ac_instrument && $ac_field && $ac_dictionary){
    $stmt = $conn->prepare("UPDATE redcap_metadata set element_enum=? where project_id=? and form_name=? and field_name=?") or trigger_error($conn->error);;
    $stmt->bind_param('siss', $ac_dictionary, $project_id, $ac_instrument, $ac_field) or trigger_error($stmt->error);
    $result = $stmt->execute();
    if ($result){
      echo "Autocomplete field successfully registered";
    }
    else{
      "Error registering autocomplete field: ".$conn->error;
    }
   
  }  

  //select * from redcap_metadata where project_id=$project_id and form_name=$ac_instrument and field_name=$ac_field;
 
  // do we need to do any checks to ensure the user has permissions on this project / form / field?
  
  //update redcap_metadata set element_note="AUTOCOMPLETE:$ac_dictionary" where project_id=$project_id and form_name=$ac_instrument and field_name=$ac_field;

  
}





/* fetch forms and fields */
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

/* Fetch data_dictionary location */
$query = "select * from redcap_config where field_name = 'dictionary_directory'";
$result = mysqli_query($conn, $query);
if($result->num_rows == 0){
  // default to the plugin directory
  $dict_dir = APP_PATH_DOCROOT.'../plugins/dictionaries';
  $query = "insert into redcap_config (field_name, value) values ('dictionary_directory', '$dict_dir')";
  $result = mysqli_query($conn, $query);
} else {
  $query = 'select value from redcap_config where field_name="dictionary_directory"';
  $result = mysqli_query($conn, $query);
  $row = mysqli_fetch_assoc($result);
  $dict_dir = $row['value'];
}


/* Fetch existing dictionaries */ 
$dictionaries = array_map(function($a) { return pathinfo($a, PATHINFO_FILENAME); }, glob("$dict_dir/*.sqlite"));

/*
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
*/


/* Process submitted request */
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
     foreach($dictionaries as $name){
      echo "<option value=$name>$name</option>";
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
