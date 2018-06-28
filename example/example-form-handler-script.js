var testOutput = document.getElementById('output-test');
var url = '../ajax-emailer/send.php'

function handleContactForm(formVals, formElement) {
    // Set loading spinner
    // Get formats for each data name
    var dataWithFormats = getEmailDataFormats(formVals);
    emailAjaxData(dataWithFormats)
    .then(resData=>{
        console.log(resData);
    })
    // Network failure  
    .catch(e=>{
        // Find out if this includes 500/400 codes
    })
}

var defaultEmailValFormats = {
    'name' : 'text',
    'email' : 'email',
    'phone' : 'phone',
    'message' : 'text',
};

function getEmailDataFormats(formVals) {
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

function emailAjaxData(data){
    return fetch(url, {
        method: 'POST', // or 'PUT'
        body: JSON.stringify(data), // data can be `string` or {object}!
        headers:{
          'Content-Type': 'application/json'
        }
    })
    // Return json server response
    .then( res=>{ 
        console.log(res.status);
        return res.text();
        
    })
    .then(textRes=>{
        
        document.getElementById('output-test').innerHTML = textRes;
        // res.json() 
        return textRes;
    })
}