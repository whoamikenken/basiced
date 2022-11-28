$(document).ready(function(){
	$("#seminar_table").dataTable({
	    "sPaginationType": "full_numbers",
	    "oLanguage": {
	                     "sEmptyTable":     "No Data Available.."
	                 },
	    "aLengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]]
	});
});