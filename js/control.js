$.ajaxSetup ({
    // Disable caching of AJAX responses
    cache: false
});


var requested_url;

$(document).ready(function() {
        $.ajaxSetup({ cache: false });
        registerListeners();
});

function registerListeners() {
	$( "#submit" ).click(function() {
		if (requested_url != url) {
			requested_url = url;
	                url = $( "#url").val();
	                changes = $( "#changes").val();
	                getDataForURL(url,changes);
		}
        });
}

function getDataForURL(url,changes) {
	$.ajax({
            type : 'POST',
            url : 'http://odinprac.theodi.org/refine-autobot/getClean.php',           
            data: {
                url: url,
                changes: changes
            },
            success:function (data) {
		var filename = url.substring(url.lastIndexOf('/')+1);
		download(filename,data);
            }          
        }); 
}

function download(filename, text) {
  var element = document.createElement('a');
  element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
  element.setAttribute('download', filename);

  element.style.display = 'none';
  document.body.appendChild(element);

  element.click();

  document.body.removeChild(element);
}

function fcoSpend() {
	$.getJSON( "fco.json", function( data ) {
		$('#changes').val(JSON.stringify(data, null, 2));
	});
	$('#url').val('https://www.gov.uk/government/uploads/system/uploads/attachment_data/file/368071/Publishable_September_2014_Spend.csv');
}
