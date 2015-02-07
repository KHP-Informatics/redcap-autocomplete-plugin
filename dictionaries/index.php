<?php
/*
* A ControlCentre page to allow administrators to manage dictionaries for the auto-complete plugin
*/

//load up the plugin stuff
require_once "../../../redcap_connect.php";

// load the ControlCentre header
include(APP_PATH_DOCROOT . 'ControlCenter/header.php');


// Your HTML page content goes here
?>
<h3 style="color:#800000;">
	Dictionaries
</h3>

<?php
   



  /* Function to process an uploaded dictionary file into a sqlite database */
  function process_dictionary($name, $dictionary){
     // generate filename for dictionary file
     $dbpath = APP_PATH_DOCROOT. "../plugins/autocomplete/dictionaries/$name.sqlite";
     if(file_exists($dbpath)){
       echo "Can't create dictionary - a dictionary with that name already exists";
     } else {
       //create dictionary
       $db = new SQLite3($dbpath);
       // create table 
       $create_q = 'CREATE TABLE dictionary (id INT NOT NULL PRIMARY KEY, term TEXT NOT NULL);';
       $db->query($create_q);
       // enter values into table, one line at a time.
       $stmt = $db->prepare('INSERT INTO dictionary VALUES (:id, :term)');
       $id=1;
       foreach ($dictionary as $term){
         $term = rtrim($term);
         $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
         $stmt->bindValue(':term', $term, SQLITE3_TEXT);
         $result = $stmt->execute();
         $id += 1; 
       }
       echo "Dictionary $name completed";
    }       
  }


  if(isset($_FILES['dictionary_file'])){
    if ($_FILES['dictionary_file']['error'] == UPLOAD_ERR_OK          // check the upload went ok (includes size under that defined in php.ini)
      && is_uploaded_file($_FILES['dictionary_file']['tmp_name'])) {  // check the file we're working on really is teh one uploaded in the POST
        $finfo = new finfo();
        $fileMimeType = $finfo->file($_FILES['dictionary_file']['tmp_name'], FILEINFO_MIME_TYPE);       
        if(preg_match(  "/^text\/plain/", $fileMimeType )){
          $dictionary = file($_FILES['dictionary_file']['tmp_name']); 
          $name = $_POST['dictionary_name'];
          process_dictionary($name, $dictionary);
        }else{
          echo "File not a text file. Upload ignored";
        }  
    } else {
      echo "Problem with upload ".$_FILES['dictionary_file']['error'];
    }
  }

?>

<!-- Upload Form -->
<div>
  <hr/>
  <h4>Upload a new completion dictionary</h4>
  <p>If your dictionary file is too large to upload, please contact your Redcap administrator</a>

  <form id="newAutoCompleteDictionary" enctype="multipart/form-data" method="post" 
        action="<?php echo APP_PATH_WEBROOT_FULL;?>plugins/autocomplete/dictionaries/"
        style="padding:0px 10px 20px 10px;">

    <p><label for="dictionary_name">Dictionary Name</label>
    <input type="text" id="dictionary_name" name="dictionary_name" /></p>
    <p><input type="file" id="dictionary_file" name="dictionary_file"/></p>
    <input type="submit" value="upload" />
  </form>
</div>





<!-- Delete Form -->
<div>
  <hr/>
  <h4>Manage existing dictionaries</h4>
</div>


<?php

include('../../../redcap_v6.2.0/ControlCenter/footer.php');

