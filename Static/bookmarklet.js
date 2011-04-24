(function(bias, d, createElement, div, ie){
	var i, divs = [
		d[createElement](div), //top
		d[createElement](div), //left
		d[createElement](div), //right
		d[createElement](div) //middle
	], bgiURL="http://fordi.org/sz/"+(['left', 'center', 'right'][bias])+'.png';
	for (i=0; i<divs.length; i++) {
		divs[i].style.position='absolute';
		divs[i].style.background='url('+bgiURL+')';
		divs[i].style.zIndex=9001;
		d.body.appendChild(divs[i]);
	}
	divs[3].style.bottom=divs[3].style.left=divs[3].style.right=divs[0].style.top=divs[0].style.left=divs[0].style.right=0;
	
	(new Image()).src=bgiURL;
	var pageX=0, pageY=0, updated=true, cancel=false,ttime=null;
	divs[0].style.backgroundPosition=['0 0', '50% 0', '100% 0'][bias];
	(function () {
		if (!updated) return setTimeout(arguments.callee, 33);

		divs[0].style.height = (pageY-10)+'px';
		divs[3].style.top= (pageY+10)+'px';
		divs[3].style.backgroundPosition=['0 ', '50% ', '100% '][bias]+'-'+pageY+'px';
		
		updated=false;
		return setTimeout(arguments.callee, 33);
	})(); 
	ie?attachEvent:addEventListener((ie?'on':'')+'mousemove', function (e) {
		if (e.pageX==pageX && e.pageY==pageY) return;
		if (cancel) {
			cancel=false;
			return;
		}
		e = e || Event;
		
		pageX=e.pageX; pageY=e.pageY; updated=true;
		//divs[0].style.display=divs[1].style.display=divs[2].style.display=divs[3].style.display='none';
		if (!!ttime) clearTimeout(ttime);
		ttime = setTimeout(function () {
			cancel=true;
			///divs[0].style.display=divs[1].style.display=divs[2].style.display=divs[3].style.display='block';
		}, 250);
	}, false);
	
	
})(1, document, 'createElement', 'div', !!window.attachEvent);