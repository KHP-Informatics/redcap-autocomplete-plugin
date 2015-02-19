<?php
/**
 * PLUGIN NAME: Autocomplete
 * DESCRIPTION: A plugin to faciliate generation of Auto-complete fields
 * VERSION: 0.1
 * AUTHOR: Cass Johnston <cassjohnston@gmail.com>
 */


// Call the REDCap Connect file in the main "redcap" directory
require_once "../../redcap_connect.php";

// load up the Dictionary class.
require_once "../dictionaries/Dictionary.php";


/* Redcap checks? 

No permissions on dictionaries (at least for now) so nay authenticated user should be able to 
access this function. Does redcap_connect check this for us?

*/


function dictionary_filename($dictionary){

  //scope the database connection
  global $conn;

  // check the redcap database for the location of the dictionary files
  $query = "select value from redcap_config where field_name = 'dictionary_directory'";
  $result = mysqli_query($conn, $query);
  if($result->num_rows == 0){
    error_log("Redcap Autocomplete: No dictionary_directory configured. Have you installed the Dictionary plugin? ");
    return '';
  }
  $row = mysqli_fetch_assoc($result);
  $dict_dir = $row['value'];
  if (!$dict_dir){
    error_log("Redcap Autocomplete: Dictionary directory is defined as ''. Something is wrong");
    return '';
  }

  // don't let users break out of the dictionary directory with ../../dict name
  if(pathinfo($dictionary, PATHINFO_BASENAME) != $dictionary){
    error_log("Redcap Autocomplete: Invalid dictionary name: $dictionary");
    return '';
  }

  // generate full path to dictionary file
  $dictionary_file = $dict_dir.'/'.$dictionary.'.sqlite';

  // check we have a $dictionary.sqlite file in that location and we can read it
  if (!is_readable($dictionary_file)){
    error_log("Redcap Autocomplete: Dictionary file not readable: $dictionary_file");
    return '';
  }

  return $dictionary_file;  

}

function make_json($result){

  while($row = $result->fetchArray()){
    $terms[] = $row['term'];
  }
  
  return json_encode($terms);
}


function autocomplete($dictionary, $string){
 
  // if the parameters aren't correct, don't bother returning anything
  if(! ($dictionary && $string) ){ return '';}

  // generate full path to dictionary file
  $dictionary_file = dictionary_filename($dictionary);
  if (!$dictionary_file){return '';}

  // create a Dictionary object (a subclass of SQLite3)
  $dict = new Dictionary( $dictionary_file, $dictionary );
  if (! $dict){
    error_log("Failed to create Dictionary object from $dictionary_file");
    return '';
  }

  // Check there aren't a crazy number of matches
  $stmt = $dict->prepare("select count(*) as count from dictionary where lower(term) like lower(:string)");
  $stmt->bindValue(':string', "$string%"); // just return stuff starting with what you typed. maybe implement something for contains (with popularity ranking?) later.
  $result = $stmt->execute(); 
  $count = $result->fetchArray();
  if($count['count'] > 500){
    // stupid number, wait until we get more string to match
    return '';
  }


  // get the matching terms
  $stmt = $dict->prepare("select term from dictionary where lower(term) like lower(:string)");
  $stmt->bindValue(':string', "$string%"); // just return stuff starting with what you typed. maybe implement something for contains (with popularity ranking?) later.
  $result = $stmt->execute(); 

  // TODO: get c14n
  $c14n = '';

  // convert result to json
  $json = make_json($result, $c14n);

  return $json;

}


$dictionary = $_GET['dictionary'];
$string = $_GET['term'];

$json = autocomplete($dictionary, $string);
echo $json;

?>
