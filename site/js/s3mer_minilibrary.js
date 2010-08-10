
var mlcurrentpage=1;
var showregion = parent.showregion;

var spinner = parent.spinner;
var searching;
var delay=1000;



function clearSearchField(){
	
	if($('mlSearchQuery').className='form-items-disabled'){
	
		$('mlSearchQuery').value='';
		$('mlSearchQuery').className='form-items';
	
	}
}

function processSearch(){
	try{
		mlcurrentpage=1;
		$('mlprevious').hide();
		searchLibraryImages();
	}
	catch(e){
		alert(e.toString());
	}
}



function searchLibraryImages(){
	try{
		
		if(searching!=true){
		    searching=true;
		    setTimeout(function(){
		      	$('mlSearchSpinner').show();
				var searchword='';
				var selectedFolder = $F('ml_folderSelect');
				var shareditem = 0;
				var mediatype = '%';

				if(selectedFolder=='All'){
					selectedFolder='%';
				}
				else if(selectedFolder=='Public'){
					selectedFolder='%';
					shareditem=1;
				}
				else if(selectedFolder=='Videos'){
					selectedFolder='%';
					mediatype='1';
				}
				else if(selectedFolder=='Images'){
					selectedFolder='%';
					mediatype='2';
				}


				if($('mlSearchQuery').className=='form-items'){
					searchword=$F('mlSearchQuery');
				}

				new Ajax.Updater('mlFileContainer','edit-show-library-contents.php?sw=' + searchword + "&folder=" + selectedFolder + "&shareditem=" + shareditem + "&mediatype=" + mediatype + "&page=" + mlcurrentpage + "&buttonselect=" + $F('buttonselect'),{
					onComplete:function(){
						$('mlSearchSpinner').hide();
					}
				});
		      searching=false;
		      }, delay);
		  }
		
		
		
	}
	catch(e){
		alert(e.toString());
	}
}


function mlSelectAll(){

	try{
		var elements = $$('.checkitem');
		var i=0;
		for(i=0;i<elements.length;i++){
			
			elements[i].checked=true;
		}
	}
	catch(e){
		alert(e.toString());
	}
}

function mlSelectNone(){
	try{
		var elements = $$('.checkitem');
		var i=0;
		for(i=0;i<elements.length;i++){
			
			elements[i].checked=false;
		}
	}
	catch(e){
		alert(e.toString());
	}
}

function goNext(){
	try{
		
		mlcurrentpage++;	
		$('mlprevious').show();
		searchLibraryImages();
		
		var totalpages=0;
		
		var searchword='';
		var selectedFolder = $F('ml_folderSelect');
		var shareditem = 0;
		var mediatype = '%';

		if(selectedFolder=='All'){
			selectedFolder='%';
		}
		else if(selectedFolder=='Public'){
			selectedFolder='%';
			shareditem=1;
		}
		else if(selectedFolder=='Videos'){
			selectedFolder='%';
			mediatype='1';
		}
		else if(selectedFolder=='Images'){
			selectedFolder='%';
			mediatype='2';
		}


		if($('mlSearchQuery').className=='form-items'){
			searchword=$F('mlSearchQuery');
		}
		
		new Ajax.Request('process_command.php?infocmd=mini-library-pages&sw=' + searchword + '&folder=' + selectedFolder + '&shareditem=' + shareditem + '&mediatype=' + mediatype,{
			onComplete:function(transport){
				totalpages=parseInt(transport.responseText);
			}
		});
		
		
		// if(totalpages<=mlcurrentpage){
		// 		$('mlnext').hide();
		// 	}
		// 	else{
		// 		$('mlnext').show();
		// 	}
			
	}
	catch(e){
		alert(e.toString());
	}
}

function goPrevious(){
	try{
		mlcurrentpage--;
		if(mlcurrentpage==1){
			$('mlprevious').hide();
		}
		searchLibraryImages();
	}
	catch(e){
		alert(e.toString());
	}
}

function unselectallbut(id){
	try{
		var elements = $$('.checkitem');
		var i=0;
		for(i=0;i<elements.length;i++){
			if(elements[i].id != id){
				elements[i].checked=false;	
			}
			
		}
	}
	catch(e){
		alert(e.toString());
	}
}



function addItemPlaylist(){
	try{
		
		$('mlSearchSpinner').show();
		
		var elements = $$('.checkitem');
		var templateregion = 0;
		var regiontype=0;
		var i=0;
		
		var checkedelements=0;
		
		for(i=0;i<elements.length;i++){
			if(elements[i].checked){
				checkedelements++;
			}
		}
		
		
		for(i=0;i<elements.length;i++){
			if(elements[i].checked){
				new Ajax.Request('process_command.php?commandnr=insert-item-playlist&regionid=' + showregion + '&mediaid=' + elements[i].name,{
					asynchronous:false,
					onComplete:function(){
						new Ajax.Request('process_command.php?infocmd=get-region-template&regionid=' + showregion,{
							asynchronous:false,
							onComplete:function(transport){
								templateregion=parseInt(transport.responseText);
								new Ajax.Request('process_command.php?infocmd=get-region-type&templateregion=' + templateregion,{
										asynchronous:false,
										onComplete:function(transport){
											regiontype=parseInt(transport.responseText);
											checkedelements--;
											if(checkedelements==0){
												$('mlSearchSpinner').hide();
												parent.minilibrary2.close();
												parent.afterInsertPlaylistFiles();
											}									
										}
								});	
							}
						});
					}
				});
			}
		}	
	}
	catch(e){
		alert(e.toString());
	}
}

function setBackgroundImage(){
	try{
		var elements = $$('.checkitem');
		var i=0;
		for(i=0;i<elements.length;i++){	
			if(elements[i].checked){
				new Ajax.Request('process_command.php?commandnr=setbackground&mediaid=' + elements[i].name + '&showid=' + parent.selectedshow,{
					onComplete:function(){
						parent.minilibrary.close();
						parent.afterSettingBackground();
					}
				});
			}
		}
	}
	catch(e){
		alert(e.toString());
	}
}
