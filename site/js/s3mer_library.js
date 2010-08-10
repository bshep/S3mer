var swfu;
var currentpage=1;
var pages;
var keypressed=false;
var firsttime=true;
var FileSearchOrigValue;
var searching;
var delay=1000;

var currentfreespace;

 
function clearFileSearch(){
	
	if ($('filesearch').value==FileSearchOrigValue){
		$('filesearch').value='';
		$('filesearch').className = 'form-items';
	}
	currentpage=1;
	keypressed=false;
	if(firsttime){
		firsttime=false;
	}
}

function onFileSearchLeave() {
	if ($('filesearch').value==''){
		$('filesearch').value = FileSearchOrigValue;
		$('filesearch').className = 'form-items-disabled';
		firsttime=true;
		keypressed=true;
	}
}
function onPageLoad() {
	
	
	FileSearchOrigValue = $('filesearch').value;
	
	//$('filesearch').observe('change',searchImages);
	$('filesearch').observe('focus', clearFileSearch);
	$('filesearch').observe('blur', onFileSearchLeave);
	
	
	var settings_object = { 
		upload_url : "http://media1.s3mer.com.s3.amazonaws.com/",
		flash_url : "js/swfuploader/swfupload.swf", 
		post_params: {"PHPSESSID" : "<?php echo session_id(); ?>"},
		file_size_limit : "200 MB",
		button_width: "105",
		button_height: "29",
		button_placeholder_id: "spanSWFUploadButton",
		file_types : "*.jpg;*.gif;*.mov;*.m4v;*.swf;*.png;*.mp4;*.flv",
		file_types_description : "All Files",
		file_upload_limit : 0,
		file_queue_limit : 0,
		file_post_name: "file",
		custom_settings : {
			progressTarget : "fsUploadProgress",
			upload_target : "divFileProgressContainer"
		},
		debug: false,
		button_cursor : SWFUpload.CURSOR.HAND,
		button_image_url:"http://www.s3mer.com/images/upload_btn.png",
		button_width: 109,
    button_height: 30,
		file_queue_error_handler : fileQueueError,
	  file_dialog_complete_handler : fileDialogComplete,
	  upload_progress_handler : uploadProgress,
		upload_start_handler : uploadStart,
	  upload_error_handler : uploadError,
	  upload_success_handler : uploadSuccess,
	  upload_complete_handler : uploadComplete,
	  swfupload_loaded_handler: doPostLoadCommands
		

		
	};
	
	swfu = new SWFUpload(settings_object);
	

  	//Calc Spaces
	
	calcSpace();
	searchImages();
	
}


function calcSpace(){
	try{
		
		var lang ='en';

		new Ajax.Request('process_command.php?infocmd=getlang',{
			onComplete:function(transport){
				lang=transport.responseText;


				if(lang=='en'){
					diskspace='Disk Space: ';
					availableof=' available of ';
				}
				else if(lang='es'){
					diskspace='Espacio en disco: ';
					availableof=' disponible de ';
				}
				else if(lang='pt'){
					diskspace='Espaço no disco: ';
					availableof=' disponível de '; 
				}

			}
		});


		var totalspace=0;

		new Ajax.Request('process_command.php?infocmd=user-storage-total',{
			onComplete:function(transport){
				totalspace=parseInt(transport.responseText);

				new Ajax.Request('process_command.php?infocmd=user-storage',{
					onComplete:function(transport){
						var occupied = transport.responseText;
						var free = roundNumber(totalspace - occupied, 2);
						currentfreespace = free;
						$('user_storage').innerHTML= diskspace +  free + 'MB ' + availableof + totalspace + 'MB Total' ;
					}
				})
			}
		});
		
	}
	catch(e){
		alert(e.toString());
	}
}


function doPostLoadCommands() { 
	
    setUploaderPostParams();

}




function searchImages(){
	try{
		
		
		if(searching!=true){
			searching=true;
			setTimeout(function(){
		      
		
				$('spinner').show();
				var selectedFolder='';
				

				new Ajax.Request('process_command.php?infocmd=library-selected-folder',{
					onComplete:function(transport){
						selectedFolder=transport.responseText;

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


						new Ajax.Request('process_command.php?infocmd=library-pages&sw=' + $('filesearch').value,{
							asynchronous:false,
							onComplete: function(transport){
								pages=transport.responseText;

								var searchword = '';

								if(firsttime==true){
									searchword='%';
								}
								else{
									searchword=$('filesearch').value;
								}


								new Ajax.Updater('tile_contents','library-tiles-contents.php?sw=' + searchword + '&page=' + currentpage + '&folder=' + selectedFolder + '&mediatype=' + mediatype + '&shareditem=' + shareditem,{
									method:'get',
									onComplete: function(){
										if(currentpage==1){
											$('previous').hide();
										}
										else{
											$('previous').show();
										}
										if(currentpage==pages){
											$('next').hide();
										}
										else{
											$('next').show();
										}
										$('spinner').hide();

										if($F('shownext')==1){
											$('next').show();
										}
										else{
											$('next').hide();
										}

									}
								})
							}
						});				
					}
				})
		
		
		      searching=false;
		      }, delay);
			
		}
		

		
	}
	catch(e){
		alert(e.toString());
	}
}

function selectFolder(selectedFolder){
	try{
		
		var lastSelectedFolder;
		
		new Ajax.Request('process_command.php?infocmd=library-selected-folder',{
			onComplete: function(transport){
				lastSelectedFolder=transport.responseText;
				$(lastSelectedFolder).className='folder-tile';
				$(selectedFolder).className='folder-tile-selected';
				new Ajax.Request("putsessionvariable.php?vname=library_selected_folder&vvalue=" + selectedFolder);
				searchImages();
			}
		});
		
		
		
	}
	catch(e){
		alert(e.toString());
	}
}

function createNewFolder(){
	
}

function uploadFile(myObject,lang){
	
	var limitreached;
	var lang ='en';
	
	if(currentfreespace<0){
		new Ajax.Request('process_command.php?infocmd=getlang',{
			onComplete:function(transport){
				lang=transport.responseText;


				if(lang=='en'){
					limitreached="You have reached your storage space limit";
				}
				else if(lang='es'){
					limitreached="Usted ha utilizado su espacio de almacenamiento en su totalidad";
				}
				else if(lang='pt'){
					limitreached="Você não tem mais espaço disponível na sua conta";
				}
				
				alert(limitreached);

			}
		});
	}
	else{
		
		new Ajax.Request('process_command.php?infocmd=library-selected-folder',{
			onComplete: function(transport){
				lastSelectedFolder=transport.responseText;
				swfu.selectFiles(); 
				myObject.blur();	
			}
		});
		
		
	}

}

function selectAll(){
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

function selectNone(){
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

function nextPage(){
	currentpage++;
	searchImages();
}

function previousPage(){
	currentpage--;
	searchImages();
}

function deleteSelected(){
	try{
		var lang=$('lang').innerHTML;
		var mediaInUse="Media in use,confirm Delete : ";
		if(lang=='es'){
			mediaInUse="Medio en uso, confirme su eliminación: ";
		}
		if(lang=='pt'){
			mediaInUse="Artigo em uso, confirme seu exclução: ";
		}
		var elements = $$('.checkitem');
		var i=0;
		
		var elid;
		
		for(i=0;i<elements.length;i++){
			if(elements[i].checked==true){
				elid=elements[i].id;
				new Ajax.Request('process_command.php?infocmd=mediause&mediaid=' + elements[i].id,{
					asynchronous:false,
					onComplete:function(transport){
						var useCount = parseInt(transport.responseText);
					
						var delelement=false;
						if(useCount==0){
							delelement=true;
						}
						else{
							nmediause=mediaInUse + $('fn' + elid).innerHTML;
							var answer = confirm(nmediause);
							if(answer){
								delelement=true;
							}
						}
						
						
						if(delelement){
							
							new Ajax.Request('process_command.php?commandnr=delmedia&id=' + elid,{
								asynchronous:false
							});
							
						}
						
					}
				});
				
				
			}
		}
		calcSpace();
		searchImages();
	}
	catch(e){
		alert(e.toString());
	}
}

function addFolder(){
	try{
		
		var foldername = $F('newFolderName');
		new Ajax.Request('process_command.php?commandnr=createfolder&foldername=' + foldername,{
			onComplete: function(){
				refreshFolders();
			}
		});

	}
	catch(e){
		alert(e.toString());
	}
}

function refreshFolders(){
	try{
		new Ajax.Updater('horizontal_carousel','library-tiles-folders.php',{
			onComplete: function(){
				new UI.Carousel("horizontal_carousel");
				var newFolderNameContainer = new Control.Modal('modal_link_one',{
			        opacity: 0.8,
			        fade: true,
			        width: 320,
			        height: 100
		    	});
			}
		});
	}
	catch(e){
		alert(e.toString());
	}
}

function deleteFolder(lang){
	try{
		
		var cannot_del;
		var not_empty;

		if(lang=='en'){
			cannot_del='You cannot delete the selected folder';
			not_empty='Folder must be empty in order to proceed to delete';
		}
		else if(lang=='es'){
			cannot_del='No puede borrar la carpeta seleccionada';	
			not_empty='La carpeta seleccionada no está vacía';
		}
		else if(lang=='pt'){
			cannot_del='Não pode excluir a pasta selecionada';
			not_empty='A pasta selecionada ha arquivos'
		}
		
		//check if folder is empty
		//proceed to delete folder if already empty, else display message to user
		
		var lastSelectedFolder = '';
		new Ajax.Request('process_command.php?infocmd=library-selected-folder',{
			onComplete: function(transport){
				lastSelectedFolder=transport.responseText;
				if(lastSelectedFolder=='All' || lastSelectedFolder=='Videos' || lastSelectedFolder =='Images' || lastSelectedFolder=='Public' || lastSelectedFolder=='Main'){
					alert(cannot_del);
				}
				else{
					new Ajax.Request('process_command.php?infocmd=files-in-folder&folder=' + lastSelectedFolder,{
						onComplete: function(transport){
							var filefolder = parseInt(transport.responseText);
							if(filefolder==0){
								new Ajax.Request('process_command.php?commandnr=delfolder&folder=' + lastSelectedFolder,{
									onComplete:function(){
										refreshFolders();
									}
								});
							}else{
								alert(not_empty);
							}
							
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
