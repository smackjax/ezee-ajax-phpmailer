// This is from another repo, if you're interested: https://github.com/smackjax/ajax-form-values-to-json
function initAjaxFormHandlers(){for(var e=document.getElementsByClassName("ezee-ajax-form"),t=0;t<e.length;t++){e[t].addEventListener("submit",ezeeAjaxFormSubmit)}}function ezeeAjaxFormSubmit(e){var t;if(e.target)t=e.target;else{if(!e.srcElement)return void console("Couldn't find form element from event.");t=e.srcElement}e.preventDefault(),t.method="post";var n=t.getAttribute("onsubmit"),a=ezeeValsToObj(t);window[n](a,t)}function ezeeValsToObj(e){for(var t=e.elements,n={},a=0;a<t.length;a++){var o=t[a];if(o.value)o.name?o.name:o.id?o.id:("no-name-please-set-name",alert("No 'name' set on input. See console."),console.log("No 'name' attribute set on element with value: ",o.value)),n[o.name]=o.value}return n}window.addEventListener("DOMContentLoaded",initAjaxFormHandlers);