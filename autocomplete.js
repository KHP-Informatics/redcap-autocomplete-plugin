function add_autocomplete(ac_field_name, ac_dict_name, ac_target){

     alert(ac_field_name+' '+ac_dict_name+' '+ac_target);
     var selector = '[name="'+ac_field_name+'"]';
     var ac_fields =  $(selector);
     // this should only return one field
     if(ac_fields.length > 1){ alert('Autocorrect will not work if there are multiple fields with the same name '); }
     if(ac_fields.length < 1){ alert('Field "'+ac_field_name+'" not found for Autocorrect: '); }
   
     ac_field = ac_fields[0];
     console.log(ac_field);  // it's getting to here fine and I have an element. 

  
    // Why am I still getting string is not a function errors?
    ac_field.autocomplete({ source: ac_target,
                             minLength: 2                    
                           });
}

/*
function trigger_autocomplete(ac_field_name, ac_dict_name, ac_target){
  
  // do this on load, don't wipe out other onload events. 
  // is there a redcap hook function for this?
  if(window.attachEvent) {
    window.attachEvent('onload', add_autocomplete(ac_field_name, ac_dict_name, ac_target));
  } else {
    if(window.onload) {
      var curronload = window.onload;
      var newonload = function() {
          curronload();
          add_autocomplete(ac_field_name, ac_dict_name, ac_target);
      };
      window.onload = newonload;
    } else {
      window.onload = add_autocomplete(ac_field_name, ac_dict_name, ac_target);
    }
  }

}
*/
