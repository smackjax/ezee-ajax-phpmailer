// Bear in mind all of these files can be consolidated and minified. This is just for ease-of-use to see what's happening.

var testOutput = document.getElementById('output-test');
var url = '../ezee-ajax-emailer/send-email.php'

function handleContactForm(formVals, formElement) {
    // Set loading spinner
    // Get formats for each data name
    var dataWithFormats = prepJsonWithDefaultFormat(formVals);
    emailAjaxData(dataWithFormats)
    .then(resData=>{
        console.log(JSON.parse(resData));
        document.getElementById('output-test').innerHTML = resData;
    })
    // Network failure  
    .catch(e=>{
        // Find out if this includes 500/400 codes
    })
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
    .then( res=>res.json() )
}