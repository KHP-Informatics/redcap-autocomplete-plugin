<?php
/*

void redcap_data_entry_form ( int $project_id, string $record = NULL, string $instrument, int $event_id, int $group_id = NULL )

project_id
The project ID number of the REDCap project to which the data entry form belongs.

record
The name of the record, assuming the record has been created. If the record does not exist yet (e.g., if says "Adding new record" in green at top of page), its value will be NULL.

instrument
The name of the current data collection instrument (i.e., the unique name, not the instrument label). This corresponds to the value of Column B in the Data Dictionary.

event_id
The event ID number of the current data entry form, in which the event_id corresponds to a defined event in a longitudinal project. For classic projects, there will only ever be one event_id for the project.

group_id 
The group ID number corresponding to the data access group to which this record has been assigned. If no DAGs exist or if the record has not been assigned to a DAG, its value will be NULL.



*/
function redcap_data_entry_form($project_id, $record, $instrument, $event_id, $group_id){

  /* scope the global MySQLi Redcap DB connection */
  global $conn;

/* Does this form have any registered auto-complete fields? If so, add the required class
   This hook works with the auto-complete plugin to enable users to define an auto-complete field on a form.
 */

}

?>
