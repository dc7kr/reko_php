function parseTime(timeStr) {

	if ( timeStr == null || timeStr.length==0) {
		return null;
	}
	var parts = timeStr.split(':');

	var hour=parts[0];
	var min =parts[1];

	var date = new Date();
	date.setHours(hour);
	date.setMinutes(min);

	return date;
}

function adjustTgRows(begDate,endDate) {
	var begFull = new Date(begDate.getTime());
	var endFull = new Date(endDate.getTime());
	begFull.setHours(0);
	begFull.setMinutes(0);
	begFull.setSeconds(0);
	endFull.setHours(23);
	endFull.setMinutes(59);
	endFull.setSeconds(59);

	var days= Math.ceil((endFull.getTime()-begFull.getTime())/86400000);

	var curDate = new Date(begDate.getTime());
	curDate.setHours(0);
	curDate.setMinutes(0);
	curDate.setSeconds(0);

	$('.tgrow').remove();

	for (var num=1;num < days-1;num++ ) {
		newNum = num+1;
		var newElem = $('#tgrow' + num).clone().attr('id', 'tgrow' + newNum).attr('class','tgrow');
		var subChilds = newElem.children('.tg_data');
		subChilds.children('.tg_tag').attr('id', 'tg_tag' + newNum).attr('name','tg_tag'+newNum);
		subChilds.children('.tg_f').attr('id', 'tg_f' + newNum).attr('name','tg_f'+newNum);
		subChilds.children('.tg_m').attr('id', 'tg_m' + newNum).attr('name','tg_m'+newNum);
		subChilds.children('.tg_a').attr('id', 'tg_a' + newNum).attr('name','tg_a'+newNum);
		subChilds.children('.tg_tariff').attr('id', 'tg_tariff' + newNum).attr('name','tg_tariff'+newNum);
		subChilds.children('.tg_sum').attr('id', 'tg_sum' + newNum).attr('name','tg_sum'+newNum);
		$('#tgrow' + num).after(newElem);
		$('#tg_f'+newNum).change(updateTagegelder);
		$('#tg_m'+newNum).change(updateTagegelder);
		$('#tg_a'+newNum).change(updateTagegelder);
	} 

	for (var i=1;i< days ;i++ ) {
		$('#tg_tag'+i).val($.datepicker.formatDate('dd.mm.yy',curDate));
		var secondDayBegin = new Date(curDate.getTime()+86400*1000);

		var hours=0;
		if ( i == 1 ) {	
			hours = secondDayBegin.getTime()-begDate.getTime();
			hours = hours / 3600000; 
		} else if ( i == days ) {
			hours = (endDate.getHours()*60+endDate.getMinutes())/60;
		} else {
			hours=25;
		}

		var tariff=0;
		if ( hours == 25 ) { 
			tariff = tg25_rate; 
		} else if ( hours >= 14 ) { 
			tariff = tg14_rate; 
		} else if ( hours >= 8) { 
			tariff =tg8_rate; 
		}
		$('#tg_tariff'+i).val(tariff);
		curDate.setTime(curDate.getTime()+86400*1000);
	}
}

function updateMasterSum() {
	var sum=0;
	$('.summand').each(function(index) {
		sum+= 1*$(this).val();
	});

	$('#master_sum').val(sum.toFixed(2));
}

function updateTagegelder() {

	var begDate = $.datepicker.parseDate('dd.mm.yy', $('#beginn').val());
	var endDate = $.datepicker.parseDate('dd.mm.yy', $('#ende').val());
	var begTime = parseTime($('#beginnZeit').val());
	var endTime = parseTime($('#endeZeit').val());

	if ( begDate == null || endDate == null || begTime == null || endTime == null ) {
		return;
	}

	begDate.setHours(begTime.getHours());
	begDate.setMinutes(begTime.getMinutes());
	endDate.setHours(endTime.getHours());
	endDate.setMinutes(endTime.getMinutes());


	adjustTgRows(begDate,endDate);
	var days= Math.ceil((endDate.getTime()-begDate.getTime())/86400000);

	for (var i =1;i<=days;i++) {
		var sum = $('#tg_tariff'+i).val()*1;
		if ( sum==null ) { sum =0 };
		if ( $('#tg_f'+i).is(':checked')) { sum -= 0.2*24; } 
		if ( $('#tg_m'+i).is(':checked')) { sum -= 0.4*24; } 
		if ( $('#tg_a'+i).is(':checked')) { sum -= 0.4*24; } 
		if (sum <0) { sum=0; }
		$('#tg_sum'+i).val(sum.toFixed(2));
	}

	updateMasterSum();
}

function updateMitfahrer(nr) {
	var val= new Number($('#mitfkm'+nr).val());
	val *=mitf_rate;

	$('#mitfkm_sum'+nr).val(val.toFixed(2));
	updateMasterSum();
}

function updateSumMax(fieldName,rate,max) {
	var field = $('#'+fieldName);

	var num = new Number(rate);
	num*=field.val();

	if ( max != null && num.toFixed(2) > max ) {
		num = max;
	}

	$('#'+fieldName+'_sum').val( num.toFixed(2));

	updateMasterSum();

}

function updateSum(fieldName,rate) {
  updateSumMax(fieldName,rate,null);
}

function datepicker_german() {
 $.datepicker.regional['de'] = {clearText: 'löschen', clearStatus: 'aktuelles Datum löschen',
                closeText: 'schließen', closeStatus: 'ohne Änderungen schließen',
                prevText: '<zurück', prevStatus: 'letzten Monat zeigen',
                nextText: 'Vor>', nextStatus: 'nächsten Monat zeigen',
                currentText: 'heute', currentStatus: '',
                monthNames: ['Januar','Februar','März','April','Mai','Juni',
                'Juli','August','September','Oktober','November','Dezember'],
                monthNamesShort: ['Jan','Feb','Mär','Apr','Mai','Jun',
                'Jul','Aug','Sep','Okt','Nov','Dez'],
                monthStatus: 'anderen Monat anzeigen', yearStatus: 'anderes Jahr anzeigen',
                weekHeader: 'Wo', weekStatus: 'Woche des Monats',
                dayNames: ['Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag'],
                dayNamesShort: ['So','Mo','Di','Mi','Do','Fr','Sa'],
                dayNamesMin: ['So','Mo','Di','Mi','Do','Fr','Sa'],
                dayStatus: 'Setze DD als ersten Wochentag', dateStatus: 'Wähle D, M d',
                dateFormat: 'dd.mm.yy', firstDay: 1,
                initStatus: 'Wähle ein Datum', isRTL: false};
        $.datepicker.setDefaults($.datepicker.regional['de']);
}

function add_mitfahrer() {
	var num     = $('.mitfkm').length;
	var newNum  = new Number(num + 1);

	var newElem = $('#mitf' + num).clone().attr('id', 'mitf' + newNum);

	newElem.children('.mitfname').attr('id', 'mitfname' + newNum).attr('name', 'mitfname' + newNum);
	newElem.children('.mitfkm').attr('id', 'mitfkm' + newNum).attr('name', 'mitfkm' + newNum);
	newElem.children('.mitfkm_sum').attr('id', 'mitfkm_sum' + newNum).attr('name', 'mitfkm_sum' + newNum);
	$('#mitf' + num).after(newElem);
	$('#mitfkm'+newNum).change(function() {
		updateMitfahrer(newNum);
	});
	$('#btnDel').removeAttr('disabled');
 
	if (newNum == 7) {
	     $('#btnAdd').attr('disabled','disabled');
	}
}


function del_mitfahrer() {
	var num = $('.mitfkm').length;
 
	$('#mitf' + num).remove();
	$('#btnAdd').removeAttr('disabled');
 
	if (num-1 == 1)
		$('#btnDel').attr('disabled','disabled');
}

