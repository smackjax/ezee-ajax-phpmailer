// Bear in mind all of these files can be consolidated and minified. This is just for ease-of-use to see what's happening.

var testOutput = document.getElementById('output-test');
var url = '../ezee-ajax-emailer/send-email.php'

function handleContactForm(formVals, formElement) {
    // Set loading spinner
    // Get formats for each data name
    var dataWithFormats = prepJsonWithDefaultFormat(formVals);
    emailAjaxData(dataWithFormats)
    .then(resData=>{
        console.log("----- RESPONSE ----- ");
        console.log(resData);
        console.log("---------- ");
        resData.toString();
        if(resData['status'] && resData['status'] === 'success'){
            document.getElementById('output-test').innerHTML = 'Success!(code 200) Check console to see response.';
        } else if(resData['status'] && resData['status'] === 'fail'){
            document.getElementById('output-test').innerHTML = 'Fail.(code 400) Check console to see response.';
        }else if(resData['status'] && resData['status'] === 'error'){
            document.getElementById('output-test').innerHTML = 'Server Error.(code 500) Check console to see response.';
        }
    })
    // Network failure/bad JSON
    .catch(e=>{
        console.log(e);
        document.getElementById('output-test').innerHTML = 'Catastrophic error';
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