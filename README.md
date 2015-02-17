#First attempt at a Redcap plugin for auto-completion of text fields

**THIS PLUGIN HAS NOT BEEN PROPERLY TESTED YET. DO NOT USE IN PRODUCTION**

## Prerequisites

This plugin requires the Dictionaries plugin which can be found here:
§

##Installation:

Put the functions in the hooks.php file in your hooks file. 
Put everything else in redcap/plugins/autocomplete


## Administrator Documentation:

The hooks will add a link to the bottom of the left hand menu on the control centre page to the 'Auto-Complete Dictionaries' page. 
On this page, administrators can upload a text file containing terms to be used for auto-completion.

The text file should be formatted as a single column of terms to be used for auto-completion. 

On the back-end these files are converted to sqlite databases. 

If you
If you have a particularly large dictionary which cannot be uploaded via http you can do the conversion yourself and manually transfer the sqlite file onto your redcap server in your dictionaries folder.

The sqlite database should be name <dictionary_name>.sqlite3
It should contain a single table, called 'dictionary' which should contain a numeric primary key, an indexed text column 'term' and a 

The schema of the database is defined as:

```
CREATE TABLE dictionary (
  id INT NOT NULL PRIMARY KEY
  term TEXT NOT NULL,
  same_as INT NOT NULL DEFAULT 0);

CREATE INDEX ON dictionary(term);
```


## User Documentation


 
