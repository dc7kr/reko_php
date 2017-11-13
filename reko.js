$(document).ready(function() {
	datepicker_german();
    $('#pkwkm').change(function() {
    if (! pkw_nolimit ) {
		    updateSumMax('pkwkm',pkw_rate,pkw_maxSum);
      } else {
		    updateSumMax('pkwkm',pkw_rate,99999);
      }
    });

	$('#sonst').change(updateMasterSum);
	$('#bahn').change(updateMasterSum);
	$('#hotel').change(updateMasterSum);
	$('#oeff').change(updateMasterSum);
	$('#taxi').change(updateMasterSum);

	$('#tg_f1').change(updateTagegelder);
	$('#tg_m1').change(updateTagegelder);
	$('#tg_a1').change(updateTagegelder);

	$( ".datepicker" ).datepicker();
	$( "#tabs" ).tabs();
	//$('#btnDel').attr('disabled','disabled');
	$('#beginn').change(updateTagegelder);
	$('#ende').change(updateTagegelder);
	$('#beginnZeit').change(updateTagegelder);
	$('#endeZeit').change(updateTagegelder);


	var timepickerVars = {
		stepMinute :5,
		timeOnlyTitle: 'Uhrzeit wählen',
		timeText: 'Uhrzeit',
		hourText: 'Stunden',
		minuteText: 'Minuten',
		secondText: 'Sekunden',
		currentText: 'Jetzt',
		closeText: 'Schließen'};
		
	$('#beginnZeit').timepicker(timepickerVars);
	$('#endeZeit').timepicker(timepickerVars);


});

