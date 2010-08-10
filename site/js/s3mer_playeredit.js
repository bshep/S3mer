
var unsaved=true;

function onReposition(force) {
 
}


function onUpdateShowList() {	
	
	var csvOrder=serializedToComma(Sortable.serialize('show-playlist'),'show-playlist');
	new Ajax.Request('process_command.php?commandnr=setserialized&csvOrder=' + csvOrder,{});
	
}



function onPageLoad() {

  	
  	Sortable.create('show-playlist',{tag: 'div', onUpdate: onUpdateShowList, only: ['row_container']});
    
	if($('np').value==1){

	    $('playername').observe('focus', onFieldEnter);
	    $('playerdesc').observe('focus', onFieldEnter);
    
	    $('playername').observe('blur', onFieldLeave);
	    $('playerdesc').observe('blur', onFieldLeave);

	}

    $('playername').observe('blur', SaveSessionVariable);
    $('playerdesc').observe('blur', SaveSessionVariable);
    
    
}


function toggleDetail(obj) {
  
  	
  	if ($('row_detail'+ obj).style.display == 'none'){
		Effect.BlindDown('row_detail'+ obj, {duration:0.2});
		$('row'+ obj).className = "sh-playlist-row sh-playlist-row-no-padding";
		
  	}	
	else {
		Effect.BlindUp('row_detail'+ obj, {duration:0.2});
    // $('row_detail'+ obj).className = "sh-playlist-row-uselected-even";
		$('row'+ obj).className = "sh-playlist-row-uselected-even";
		
	}
  
}


function addShow(myform,overlayObject){

	try{		
			$('spinner').show();	
		var i=0;
		for (i=0;i<myform.selectedaddshows.options.length;i++) 
		{
			if(myform.selectedaddshows.options[i].selected){	   
		    	new Ajax.Request("process_command.php?commandnr=ashtp&playerid=" + $F('thisplayerid') + "&showid=" + myform.selectedaddshows.options[i].value,{
		    		onComplete: function(){
					
						var url = 'edit-player-schedule.php?playerid=' + $F('thisplayerid');
		    			new Ajax.Updater('show-playlist', url, {
							onComplete: function(){
								
								Sortable.create('show-playlist',{tag: 'div', onUpdate: onUpdateShowList, only: ['row_container']});
									$('spinner').hide();
							},
							evalScripts: 'true'
  						});

						

		    		}
		    	});
			}
			
		}
		try{
			savePlayerData(myform.playerid.value);		
			
		}
		catch(e){
			alert(e.toString());
		}
		
		overlayObject.close();
  			
	}
	catch(e){
		alert(e.toString());
	}
}



function deleteShow(schedid,playerid){
	try{
		$('spinner').show();
		new Ajax.Request("process_command.php?commandnr=del&scheduleid=" + schedid,
			{method:'get',
			onComplete: function(){
				
				new Ajax.Updater('show-playlist', 'edit-player-schedule.php?playerid=' + playerid, {
					method: 'post',
					onComplete: function(){
						
						Sortable.create('show-playlist',{tag: 'div', onUpdate: onUpdateShowList, only: ['row_container']});
						$('spinner').hide();
						
					},
					evalScripts: true
		  		});
			}
		});
		
  		
	}
	catch(e){
		alert(e.toString());	
	}
}


function getUserLanguage(){
	
	new Ajax.Request('process_command.php?infocmd=getlang',{
		method:'get',
		asynchronous: false,
		onSuccess: function(transport){
			userlang=transport.responseText;	
		}
	});
}

function savePlayerData(playerid){
	try{
		
		unsaved=false;
		new Ajax.Request('saveplayerdata.php?playerid=' + playerid)
	}
	catch(e){
		alert(e.toString());
	}
	
}


function exitPlayerEditSave(playerid){
	try{
		
		unsaved=false;
		var url='saveplayerdata.php?playerid=' + playerid;
		new Ajax.Request(url,{
			onComplete:function(){
				window.location='player-tiles.php';
			}
		})
	}
	catch(e){
		alert(e.toString());
	}
}

function saveScheduleData(myform,lang,contentDiv){
	try{
		
		var errorcount=0;
		
		var transition = myform.transition.value;
		var scheduleid = myform.scheduleid.value;
		var sunday=0;
		var monday=0;
		var tuesday=0;
		var wednesday=0;
		var thursday=0;
		var friday=0;
		var saturday=0;	
		var am=0;
		var pm=0;
		
		if(myform.sunday.checked){
			sunday=1;
		}
		if(myform.monday.checked){
			monday=1;
		}	
		if(myform.tuesday.checked){
			tuesday=1;
		}
		if(myform.wednesday.checked){
			wednesday=1;
		}
		if(myform.thursday.checked){
			thursday=1;
		}
		if(myform.friday.checked){
			friday=1;
		}
		if(myform.saturday.checked){
			saturday=1;	
		}
		if(myform.am.checked){
			am=1;
		}
		if(myform.pm.checked){
			pm=1;
		}
		
		var frommonth=0;
		frommonth = $('frommonth' + scheduleid).value;
		
		var fromdate=0;
		fromdate = $('fromday' + scheduleid).value;
		var fromyear=0;
		fromyear = $('fromyear' + scheduleid).value;
	
		var tomonth=0;
		tomonth = $('tomonth' + scheduleid).value;
		
		var todate=0;
		todate = $('today' + scheduleid).value;
		var toyear=0;
		toyear = $('toyear' + scheduleid).value;
		
		
		var fromtime;
		var totime;
		
		if(!IsValidTime(myform.fromtime.value,lang)){
			errorcount++;
			myform.fromtime.className='form-items-error-time';
		}
		else{
			fromtime=MySQLTime(myform.fromtime.value,lang);
		}
	
		if(!IsValidTime(myform.totime.value,lang)){
			errorcount++;
			myform.totime.className='form-items-error-time';
		}
		else{
			totime=MySQLTime(myform.totime.value,lang);
		}
		
		var csvData;
		
		csvData= scheduleid + ',' + transition + ',' + sunday + ',' + monday + ',' + tuesday + ',' + wednesday + ',' + thursday + ',' + friday + ',' + saturday + ',' + am + ',' + pm + ',' + frommonth + ',' + fromdate + ',' + fromyear + ',' + tomonth + ',' + todate + ',' + toyear + ',' + fromtime + ',' + totime;
		
		if(errorcount==0){
			new Ajax.Request('process_command.php?commandnr=saveschedule&csvData=' + csvData,{
				onComplete:function(){
					
					Effect.BlindUp($('row_detail' + contentDiv), {duration:0.2});
					
					var cmbeffect = myform.transition;
					var effect_text = cmbeffect.options[cmbeffect.options.selectedIndex].text;
					
					$('effect_' + contentDiv).innerHTML = effect_text;
					$('row' + contentDiv).className = 'sh-playlist-row-uselected-even';
				
				}
			});		
			
		}
		
		
		
	}
	catch(e){
		alert(e.toString());
	}
	
}




