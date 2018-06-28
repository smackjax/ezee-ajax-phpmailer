// This function handles verifying JSON request shape, 
// and setting default format values from the front-end.
// NOTE: There doesn't have to be a default value from here. 

var defaultEmailValFormats = {
    'name' : 'text',
    'email' : 'email',
    'phone' : 'phone',
    'message' : 'text',
};

function prepJsonWithDefaultFormat(formVals) {
    var inputNames = Object.keys(formVals);
    var dataWithFormats = {};
    for(var i = 0; i < inputNames.length; i++ ){
        var iName = inputNames[i];
        if(defaultEmailValFormats[iName]){
            dataWithFormats[iName] = {
                'value': formVals[iName],
                'format' : defaultEmailValFormats[iName]
            }
        } else {
            dataWithFormats[iName] = formVals[iName];
        }
    }
    return dataWithFormats;
}

