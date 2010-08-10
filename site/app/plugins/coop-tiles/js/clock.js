var myCurrentTime;

function createTime() {
	var currentTime = new Date();
 	var hours = currentTime.getHours();
  	var minutes = currentTime.getMinutes();
	var seconds = currentTime.getSeconds();
	var day = currentTime.getDay();
	var dayNum = currentTime.getDate();
	var month = currentTime.getMonth();
	var year = currentTime.getFullYear();
	

	switch (month){
		case 1:
			month = "Enero";
			break;
		case 2:
			month = "Febrero";
			break;
		case 3:
			month = "Marzo";
			break;
		case 4:
			month = "Abril";
			break;
		case 5:
			month = "Mayo";
			break;
		case 6:
			month = "Junio";
			break;
		case 7:
			month = "Julio";
			break;
		case 8:
			month = "Agosto";
			break;
		case 9:
			month = "Septiembre";
			break;
		case 10:
			month = "Octubre";
			break;
		case 11:
			month = "Noviembre";
			break;
		case 12:
			month = "Diciembre";
			break;

	}

	switch (day){
		case 1:
			day = "Lunes";
			break;
		case 2:
			day = "Martes";
			break;
		case 3:
			day = "Miercoles";
			break;
		case 4:
			day = "Jueves";
			break;
		case 5:
			day = "Viernes";
			break;
		case 6:
			day = "Sabado";
			break;
		case 7:
			day = "Domingo";
			break;
	}


  	var suffix = "AM";
  	if (hours >= 12) {
  		suffix = "PM";
  		hours = hours - 12;
  	}

  	if (hours == 0) {
  		hours = 12;
  	}

  	if (minutes < 10) {
  		minutes = "0" + minutes
	}
  	
	if (seconds < 10) {
	  		seconds = "0" + seconds
	}

	myCurrentTime = (hours + ":" + minutes + ":" + seconds + " " + suffix + " - " + day + ", " + dayNum + " de " + month + " de " + year);
}

function salonUpdater() {
	createTime();
	$('jsTime').innerHTML = myCurrentTime;
}


new PeriodicalExecuter(salonUpdater, 1);

new Ajax.PeriodicalUpdater('actividades', 'coopPartial.php',{
	frequency: 5
});







































