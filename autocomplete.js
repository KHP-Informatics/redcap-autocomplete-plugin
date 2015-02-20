function add_autocomplete(ac_field_name, ac_dict_name, ac_target){

     alert(ac_field_name);

     var selector = '[name="'+ac_field_name+'"]';
     var ac_fields =  $(selector);
     // this should only return one field
     if(ac_fields.length > 1){ alert('Autocorrect will not work if there are multiple fields with the same name '); }
     if(ac_fields.length < 1){ alert('Field "'+ac_field_name+'" not found for Autocorrect: '); }
   
     ac_field = $(ac_fields[0]);
     console.log(ac_field);  

     ac_field.autocomplete({ source: ac_target,
                             minLength: 2                    
                           });
}

