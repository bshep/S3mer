var oldClassName = 'box-positive';

function doSelect(obj) { 
  oldObj = document.getElementsByClassName('box-selected')[0];
  newObj = $(obj);

  if (newObj.className != 'box-selected') {
    tmpName = newObj.className;
    newObj.className = 'box-selected';
    
    if (tmpName == 'box-negative') {
      $(newObj.getElementsByClassName('player-disable')[0]).hide();
      $(newObj.getElementsByClassName('player-enable')[0]).show();
    } else {
      $(newObj.getElementsByClassName('player-disable')[0]).show();
      $(newObj.getElementsByClassName('player-enable')[0]).hide();
    } 

    
    if( oldObj != null ) { oldObj.className = oldClassName; }
    
    oldClassName = tmpName;
  } else {
    newObj.className = oldClassName;
  }
}

function doEnable(obj, id, type) { 
	
	try{
		
		

		if(type=='player'){
			new Ajax.Request('process_command.php?command=en&playerid=' + id);	
		}
		else if(type='show'){
			new Ajax.Request('process_command.php?command=en&showid=' + id);	
		}
		
	 	
	}
	catch(e){
		alert(e.toString());
	}
	
	 
  newObj = $(obj);

  $(newObj.getElementsByClassName('player-disable')[0]).show();
  $(newObj.getElementsByClassName('player-enable')[0]).hide();
  
  oldClassName = 'box-positive';
  
 
  
}

function doDisable(obj, id, type) { 
	
	
	
	try{

			if(type=='player'){
				new Ajax.Request('process_command.php?command=ds&playerid=' + id);
			}
			else if(type=='show'){
				new Ajax.Request('process_command.php?command=ds&showid=' + id);
			}
		 
	}
	catch(e){
		alert(e.toString());
	}
	
	
	
  newObj = $(obj);

  $(newObj.getElementsByClassName('player-disable')[0]).hide();
  $(newObj.getElementsByClassName('player-enable')[0]).show();
  
  oldClassName = 'box-negative';
  
  
}

function onPageLoadShows() {
	

}

var idnum = 1;

function addDragBox(e,ed) {
  
  newNode = e.cloneNode(e);
  
  newNode.id = 'box1' + idnum;
  idnum = idnum + 1;

  newNode.setStyle('opacity: 1.0')
  newNode.style.top = '';
  newNode.style.left = '';
  newNode.style.zIndex = '';
  
  newNode.setStyle('position: relative');
  newNode.setAttribute('onclick','doSelect(\'' + newNode.id +'\')');

  
  $('content-boxes').appendChild(newNode);
  new Draggable(newNode.id,{revert: true, onStart: beginDragBox, onEnd: endDragBox});
}

function beginDragBox(e,ed) {
  $('box-add').className = 'box-neutral-drop';
  $('box-add').getElementsBySelector('.title')[0].innerHTML = 'Clone Show'
  $('box-add').getElementsBySelector('.right-side-details p')[0].innerHTML = 'Drop here to copy or clone a show'
}

function endDragBox(e,ed) {
  $('box-add').className = 'box-neutral';
  $('box-add').getElementsBySelector('.title')[0].innerHTML = 'Create Show'
  $('box-add').getElementsBySelector('.right-side-details p')[0].innerHTML = 'Click here to create a new show'
}

function deleteShow(showid){
	try{
		var lang=$('lang').innerHTML;
		var showInUse = "This show is in use. Do you want to delete this show?";
		
		
		if(lang=='es'){
			showInUse="Este espectáculo está en uso. Realmente desa eliminar este espectáculo?"
		}
		
		if(lang=='pt'){
			showInUse="Confirme excluir"
		}
		
		
		new Ajax.Request("process_command.php?infocmd=showuse&showid="+showid,{
			onComplete:function(transport){
				var quse=parseInt(transport.responseText);
				var confirmed=true;
				var delshow=false;
				if(quse!=0){
					var answer = confirm(showInUse);
					if(answer){
						delshow=true;
					}
				}
				else{
					delshow=true;
				}
				
				
				if(delshow){
					new Ajax.Request("process_command.php?command=del&showid="+showid,{
						onComplete:function(){
							window.location.reload();
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

function deletePlayer(playerid){
	try{
		var lang=$('lang').innerHTML;
		var confirmDelPlayer = "Are you sure you want to delete this player?";
		
		
		if(lang=='es'){
			confirmDelPlayer="Está seguro que desea eliminar este reproductor?"
		}
		
		if(lang=='pt'){
			confirmDelPlayer="Está certo que deseja excluir este reprodutor?"
		}
		
		var answer=confirm(confirmDelPlayer);
		
		if(answer){
			new Ajax.Request("process_command.php?command=del&playerid=" + playerid,{
				onComplete:function(){
					window.location.reload();
				}
			});
		}
		
	}
	catch(e){
		alert(e.toString());
	}
}
