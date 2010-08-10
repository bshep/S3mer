var lastSelected=0;
var step_2hidden=true;
var step_3hidden=true;

var showregion=0;

var spinner = $('spinner');


var selectedshow;
var selectedtemplate;



function changeIcon() {
	
	var image = 'images/icons/show.png';
	
	if($F('showtype') == 1){
		image = 'images/icons/proshow.png';
	}
	
	$('iconimage').writeAttribute('src',image);
	
}

function hideAll() {
	
    var configRegions = document.getElementsByClassName("configuration-region");

    for (var index = 0; index < configRegions.length; ++index) {
      $(configRegions[index]).hide();
    }
}


function onPageLoad(show_id,current_template) {
	
	
	hideAll();
	
	new UI.Carousel("horizontal_carousel", { scrollInc: 4 });


	if($('ns').value==1){
  
	    $('showname').observe('focus', onFieldEnter);
	    $('showdesc').observe('focus', onFieldEnter);
    
	    $('showname').observe('blur', onFieldLeave);
	    $('showdesc').observe('blur', onFieldLeave);
		
	}


    $('showname').observe('blur', SaveSessionVariable);
    $('showdesc').observe('blur', SaveSessionVariable);

	try{
		$('showtype').observe('blur', SaveSessionVariable);
		
	}
	catch(e){
		
	}

	selectedshow=show_id;
	selectedtemplate=current_template;
    
    if(current_template!=0){
    	
    	clickSmallTemplate(current_template,show_id,true);
    
    }
    
}


function clickSmallTemplate(template_id,show_id,first_load){


	$('spinner').show();

	if(lastSelected!=0){
		$('layout' + lastSelected).className='small-template-container';	
	}
	
	if(!first_load){
		if(lastSelected!=template_id){
			new Ajax.Request('process_command.php?commandnr=crrein&showid=' + show_id + '&templateid=' + template_id);
		}	
	}
	
	lastSelected = template_id;
	
	$('layout' + template_id).className='small-template-container-selected';
	
	new Ajax.Updater('layout_regions','edit-show-template-region.php?layoutid=' + template_id + '&showid=' + show_id,{
		onComplete:function(){
			$('spinner').hide();
		}
	});
	
	if(step_2hidden){
		Effect.BlindDown($('step_2'), {duration:0.2});
		step_2hidden=false;
	}
}

function clickLayoutRegion(showid,rtype,trid){
	try{
		
		$('spinner').show();
		
		
		
		new Ajax.Request('process_command.php?commandnr=getshowregionid&showid=' + showid + '&rid=' + trid,{
			asynchronous:false,
			onSuccess: function(transport){
				showregion=transport.responseText;
				if(showregion!=0){
					$('selectedshowregion').value=showregion;
					$('selectedtemplateregion').value=trid;
					$('selectedregiontype').value=rtype;
					new Ajax.Updater('region_configuration','edit-show-region-config.php?templateregion=' + trid + '&showregion=' + showregion + '&regiontype=' + rtype,{
						onComplete:function(){
							if(step_3hidden){
								Effect.BlindDown($('region_configuration'), {duration:0.2});
								step_3hidden=false;
								
							}
							if(rtype==1 || rtype==3){
								$('mainregionbuttons').show();
							}
							else{
								$('mainregionbuttons').hide();
							}
							Sortable.create('region_configuration',{tag: 'div', onUpdate: onUpdateList, only: ['row_container']});
							$('spinner').hide();
						
						}
					});

				}
			}
		});
			
	}
	catch(e){
		alert(e.toString());
	}
}

function onUpdateList(){

	$('spinner').show();
	var csvOrder=serializedToComma(Sortable.serialize('region_configuration'),'region_configuration');

	new Ajax.Request('process_command.php?commandnr=setplaylistorder&csvOrder=' + csvOrder,{
		onComplete:function(){
			$('spinner').hide();
		}
	});


}

function deleteplaylistitem(itemid,trid,showregion,rtype){
	try{
		
		
		$('spinner').show();
		var templateregion = 0;
		var regiontype=0;
		
		new Ajax.Request('process_command.php?commandnr=delplitem&plitid=' + itemid,{
			onComplete:function(){
				new Ajax.Request('process_command.php?infocmd=get-region-template&regionid=' + showregion,{
					onComplete:function(transport){
						templateregion=parseInt(transport.responseText);
						new Ajax.Request('process_command.php?infocmd=get-region-type&templateregion=' + templateregion,{
								onComplete:function(transport){
									regiontype=parseInt(transport.responseText);
									new Ajax.Updater('region_configuration','edit-show-region-config.php?templateregion=' + templateregion + '&showregion=' + showregion + '&regiontype=' + regiontype,{
										onComplete:function(){
											Sortable.create('region_configuration',{tag: 'div', onUpdate: onUpdateList, only: ['row_container']});
											$('spinner').hide();
										}
									});											
								}
						});	
					}
				});
			}
		});
		
		
		
	}
	catch(e){
		alert(e.toString());
	}
}

function renderURL(myform){
	
	
	try{
	
	
		if(myform.rssFeeds.value!=0){
			
			var urlValue;
			new Ajax.Request('process_command.php?commandnr=getrssurl&urlid=' +myform.rssFeeds.value,{
				asynchronous:false,
				onSuccess:function(transport){
					urlValue=transport.responseText;
				}
			});

			myform.SourceURL.value=urlValue;


			if(urlValue.length==0){
				myform.SourceURL.type='text';

			}
			else{
				myform.SourceURL.type='hidden';

			}
			
			
		}
		else{
			myform.SourceURL.type='text';
			myform.SourceURL.value='';
		}
	
	

		
	}
	catch(e){
		alert(e.toString());
	}

}


function toggleDetail(obj){

	try{
		
		  // Get id number from 'obj' var
		  var idNum = obj.replace(/\D/g,'');
			
	  	if ($(obj).style.display == 'none'){
			Effect.BlindDown(obj, {duration:0.2});
			$(obj).className = "sh-playlist-row";
			$('row_'+idNum).className = "sh-playlist-row sh-playlist-row-no-padding";
			
	  	}	
		else {
			Effect.BlindUp(obj, {duration:0.2});
			$(obj).className = "sh-playlist-row-uselected-even sh-playlist-row-uselected-even-add-padd";
			$('row_'+idNum).className = "sh-playlist-row-uselected-even";
			
		}
			
		
		
	}
	catch(e){
		alert(e.toString());
	}

}


function insertPlaylistItem(type){
	try{
		$('spinner').show();
		var playlistid = $F('playlistid');
		var trid = $F('templateregion');
		var showregion = $F('showregion');
		var rtype = $F('regiontype');
		
		new Ajax.Request('process_command.php?commandnr=addplitem&playlistid=' + playlistid + '&type=' + type,{
			onComplete:function(){
				new Ajax.Updater('region_configuration','edit-show-region-config.php?templateregion=' + trid + '&showregion=' + showregion + '&regiontype=' + rtype,{
					onComplete:function(){
						Sortable.create('region_configuration',{tag: 'div', onUpdate: onUpdateList, only: ['row_container']});
						$('spinner').hide();
					}
				});
				
				
			}
		});
		
		
	}
	catch(e){
		alert(e.toString());
	}
}





function saveRSS(){
	try{
		$('spinner').show();
		new Ajax.Request('process_command.php?commandnr=saveRSS&showregion=' + showregion + '&SourceURL=' + $F('SourceURL') + '&rssFeeds=' + $F('rssFeeds'),{
			onComplete:function(){
				$('spinner').hide();
			}
		});
		Effect.BlindUp($('region_configuration'), {duration:0.2});
		step_3hidden=true;
		
	}
	catch(e){
		alert(e.toString());
	}
}



function savePlayListItemDuration(playlist_item){
	try{
		
		$('spinner').show();

		var duration=$F('duration_' + playlist_item);
		new Ajax.Request('process_command.php?commandnr=save-playlist-item-duration&duration=' + duration + "&playlistitem=" + playlist_item,{
			onComplete:function(){

				Effect.BlindUp($('sh-detail_' + playlist_item), {duration:0.2});
				$('row_'+playlist_item).className = 'sh-playlist-row-uselected-even';
				$('spinner').hide();
				$('duration_bar_' + playlist_item).innerHTML=duration;
				
			}
		});
		
	}
	catch(e){
		alert(e.toString());
	}
}

function savePlayListItemURL(playlist_item){
	try{
		$('spinner').show();
		var url = $F('url_' + playlist_item);
		new Ajax.Request('process_command.php?commandnr=save-playlist-item-url&url=' + url + "&playlistitem=" + playlist_item,{
			onComplete:function(){
				$('url_bar_' + playlist_item).innerHTML=url;
				$('row_'+playlist_item).className = 'sh-playlist-row-uselected-even';
				Effect.BlindUp($('sh-detail_' + playlist_item), {duration:0.2});
				$('spinner').hide();
			}
		});
		
	}
	catch(e){
		
	}
}

function saveShow(sid){
	try{
		$('spinner').show();
		
		var clock=0;
		
		if($('clock')  == null){
			clock=1;
		}
		else{
			if($('clock').checked){
				clock=1;
			}
		}
			
		
		new Ajax.Request('saveshowdata.php?showid=' + sid + '&clock=' + clock,{
			onComplete:function(){
				window.location="show-tiles.php";
				$('spinner').hide();
			}
		});
	}
	catch(e){
		alert(e.toString());
	}
}


function buttonselector(id){
	try{
		alert(id);
		$('libraryselector').value=id;		
	}
	catch(e){
		alert(e.toString());
	}
}

function processlibraryaddbutton(){
	try{
		alert($F('libraryselector'));
	}
	catch(e){
		alert(e.toString());
	}
}


function afterInsertPlaylistFiles(){
	try{
		$('spinner').show();
		var templateregion = $F('selectedtemplateregion');
		var showregion = $F('selectedshowregion');
		var regiontype = $F('selectedregiontype');
		
		
		new Ajax.Updater('region_configuration','edit-show-region-config.php?templateregion=' + templateregion + '&showregion=' + showregion + '&regiontype=' + regiontype,{
			onComplete:function(){
				Sortable.create('region_configuration',{tag: 'div', onUpdate: onUpdateList, only: ['row_container']});
				$('spinner').hide();
			}
		});
		

		
	}
	catch(e){
		alert(e.toString());
	}
}

function afterSettingBackground(){
	try{
		$('spinner').show();
		new Ajax.Updater('backgroundimage','edit-show-backgroundimage.php?showid=' + selectedshow,{
			onComplete:function(){
				$('spinner').hide();
			}
		});
	}
	catch(e){
		alert(e.toString());
	}
}


function resetshowbackground(){
	try{
		$('spinner').show();
		new Ajax.Request('process_command.php?commandnr=resetbackground&showid=' + selectedshow,{
			onComplete:function(){
				afterSettingBackground();
			}
		});
	}
	catch(e){
		alert(e.toString());
	}
}