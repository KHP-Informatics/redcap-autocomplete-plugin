First attempt at a Redcap plugin for auto-completion of text fields

Install:

Put the functions in the hooks.php file in your hooks file. 
Put everything else in redcap/plugins/autocomplete


As admin:
Control Centre > Miscellaneous Modules > Custom Application Links

Add a link with an appropriate label (Autocomplete Plugin, or similar) and ensure that the Append project ID to URL box is checked. 
URL should be something like https://<redcap.host>/plugins/autocomplete

As user:

In your project, create an instrument with a text box in it. Remember the variable name you gave it.

Click the Autocomplete Plugin (or whatever you called it) link in the Applications menu on the left hand side of the page.


You can either upload a new dictionary file or use an existing one. Dictionary files should be plain text files containing all possible values of the field, one per line.

If you have a particularly large dictionary, for which uploading via http would be unfeasible,  you can convert it to a sqlite database and upload it to the dictionaries directory in the plugin. The database file should be called <DictionaryName>.sqlite and should contain a single table called 'dictionary' with a single indexed text column called 'terms'.

 
