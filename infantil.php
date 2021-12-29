<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<style type="text/css"> 
	html {
		overflow: hidden;
	}
	body {
		background: #000;
		left: 0px;
		top: 0px;
		width: 100%;
		height: 100%;
		margin: 0px;
		padding: 0px;
		color: #fff;
		font-family: verdana, arial, helvetica, sans-serif;
		filter: alpha(opacity=100);
	}
	#screen {
		position:absolute;
		left: 0;
		top: 0;
		width: 100%;
		height: 100%;
		overflow: hidden;
		background: #000;
	}
	#grid {
		position:absolute;
	}
	#grid img {
		position: absolute;
		cursor: pointer;
		left: -10000px;
		-ms-interpolation-mode:nearest-neighbor;
	}
	#grid .over {
		border: #f00 solid;
	}
	#notice {
		position: absolute;
		font-size: 0.7em;
		left: 1em;
		top: 1em;
		width: 15em;
		background: #000;
		filter: alpha(opacity=70);
		opacity: 0.7;
		cursor: help;
		padding: 0.2em;
	}
	#notice span {
		color: #f80;
	}
	#caption {
		position: absolute;
		font-size: 1em;
		left: 0px;
		top: 90%;
		width: 100%;
		font-weight: bold;
		text-align: center;
		color: #fff;
	}
</style>
 
<script type="text/javascript"> 
// ===============================================================
//                     --- images grid ---
// script written by Gerard Ferrandez - Ge-1-doot - November 2006
// http://www.dhteumeuleu.com
// ===============================================================
//
 
var O = [];
var iL;
var scr;
var grd;
var grs;
var tit;
var cap;
var xm = 0;
var ym = 0;
var nx = 0;
var ny = 0;
var nw = 0;
var nh = 0;
var cx = 0;
var cy = 0;
var cz = .2;
var Tw = 0;
var Th = 0;
var X  = 0;
var Y  = 0;
var F  = false;
var Fo = false;
var NK = 0;
 
 
///////////////////////////////////////////////////////////////////////////////
var ZM = .8;      // init zoom
var NX = 4;    // grid X
var NY = 2;      // grid Y
var NB = 1.2;    // border size
var Mz = NX * 2; // max zoom
var mz = .1;     // min zoom
 
var path   = "infantil/"; // path
 
var images = [
//    source, title, caption
	["1.jpg","","Bloomsunglasses"],
	["2.jpg","","Bloomsunglasses"],
	["3.jpg","","Bloomsunglasses"],
	["4.jpg","","Bloomsunglasses"],
	["5.jpg","","Bloomsunglasses"],
	["6.jpg","","Bloomsunglasses"],
	["7.jpg","","Bloomsunglasses"],
	["00.jpg","","Bloomsunglasses"]			
];
///////////////////////////////////////////////////////////////////////////////
 
/* ================== mouse move ====================*/
document.onmousemove = function(e)
{
	if (!F)
	{
		if (window.event)
		{
			e = window.event;
		}
		xm = Math.min(nw, Math.max(0, (e.x || e.clientX) - nx));
		ym = Math.min(nh, Math.max(0, (e.y || e.clientY) - ny));
	}
}
 
/* ==================== resize ===================== */
function resize()
{
	nx = scr.offsetLeft;
	ny = scr.offsetTop;
	nw = scr.offsetWidth;
	nh = scr.offsetHeight;
	Tw = nw * cz;
	Th = nh * cz;
	for (var i in O)
	{
		O[i].resize();
	}
}
 
onresize = resize;
 
/* ============= mouseWheel - IE/Mozilla ============ */
function scroll(e)
{
	if (!F)
	{
		var z = 0;
		if (e)
		{
			/* Moz */
			if (e.preventDefault) e.preventDefault();
			z = (e.detail > 0) ? 1 :  - 1;
		}
		else
		{
			/* IE */
			z = (event.wheelDelta < 0) ? 1 :  - 1;
		}
		if (z > 0)
		{
			if (ZM < Mz)
			{
				/* zoom in */
				ZM *= 1.2;
			}
		}
		else
		{
			if (ZM > mz)
			{
				/* zoom out */
				ZM *= .8;
			}
		}
	}
	else
	{
		/* unlock image */
		F = false;
	}
	return false;
}
 
if (window.addEventListener)
{
	/* Moz */
	window.addEventListener('DOMMouseScroll', scroll, false);
}
else
{
	/* IE */
	document.onmousewheel = scroll;
}
 
/* =============== images constructor ================= */
function Img(n, x, y)
{
	this.x      = x;
	this.y      = y;
	this.n      = n;
	this.loaded = false;
	
	/* create image */
	this.img        = document.createElement("img");
	this.img.obj    = this;
	this.img.src    = document.getElementById('loading').src;
	grd.appendChild(this.img);
	this.ims = this.img.style;
	
	/* preload */
	this.pre        = new Image();
	this.pre.obj    = this;
	this.pre.onload = function()
	{
		this.obj.loaded     = true;
		this.obj.img.src    = this.src;
	}
	this.pre.src    = path + images[n % iL][0];
		
	/* mouse events */
	this.img.ondrag        = function() { return false; }
	this.img.onselectstart = function() { return false; }
	this.img.ondblclick    = this.img.onmousedown;
	
	/* onclick image */
	this.img.onmousedown = function()
	{
		if (this.obj.loaded)
		{
			if (F)
			{
				/* zoom out */
				ZM = NK;
				F  = false;
			}
			else
			{
				/* zoom in */
				F  = this;
				NK = ZM;
				ZM = NX + .1;
				xm = (((nw / NB) * .5) / NX) + F.obj.x * nw / NX;
				ym = (((nh / NB) * .5) / NY) + F.obj.y * nh / NY;
			}
		}
		return false;
	}
	
	/* update text image */
	this.img.onmouseover = function()
	{
		if(Fo)
		{
			Fo.obj.border(false);
		}
		if (this.obj.loaded)
		{
			var i = images[this.obj.n % iL];
			tit.innerHTML = i[1];
			cap.innerHTML = i[2];
			Fo = this;
			Fo.obj.border(true);
		}
		else
		{
			tit.innerHTML = "";
			cap.innerHTML = "";
		}
	}
	
	/* resize image */
	this.resize = function()
	{
		this.ims.left   = Math.round(NB * this.x * Tw / NX) + 'px';
		this.ims.top    = Math.round(NB * this.y * Th / NY) + 'px';
		this.ims.width  = Math.round(Tw / NX) + 'px';
		this.ims.height = Math.round(Th / NY) + 'px';
		if(this == Fo.obj)
		{
			this.border(true);
		}
	}
	/* borderSize */
	this.border = function(over)
	{
		var b = 0;
		var m = 0;
		if (over)
		{
			b = Math.round(((NB * Th / NY) - (Th / NY)) * .5);
			m = 2 - b;
			this.img.className = "over";
		}
		else
		{
			this.img.className = "";
		}
		this.ims.borderWidth = b + 'px';
		this.ims.marginLeft  = m + 'px';
		this.ims.marginTop   = m + 'px';
	}
}
 
/* =============== main loop ==================== */
function run()
{
	/* zoom speed */
	var vz = (ZM - cz) * .05;
	if (Math.abs(vz) > .002)
	{
		resize();
	}
	else
	{
		vz = 0;
	}
	
	/* soften move */
	cz += vz;
	cx += (xm - cx) * .08;
	cy += (ym - cy) * .08;
	
	/* move grid */
	grs.left = Math.round((nw * .5) - (NB * cx * Tw / nw)) + 'px';
	grs.top  = Math.round((nh * .5) - (NB * cy * Th / nh)) + 'px';
	
	setTimeout(run, 16);
}
 
 
/* =================== start ==================== */
onload = function()
{
	grd = document.getElementById("grid");
	grs = grd.style;
	scr = document.getElementById("screen");
	tit = document.getElementById("title");
	cap = document.getElementById("caption");
	iL  = images.length;
	
	/* create grid */
	var k = 0;
	for (var y = 0; y < NY; y++)
	{
		for (var x = 0; x < NX; x++)
		{
			O.push(new Img(k++, x, y));
		}
	}
	
	/* run */
	resize();
	cx = xm = nw / 2;
	cy = ym = nh / 2;
	run();
	/* hide notice */
	setTimeout( function () {
		document.getElementById('notice').style.visibility = 'hidden';
	}, 30000);
}
</script>
</head>
 
<body bgcolor="#000000">
	<div id="screen">
		<div id="grid"></div>
		<div id="notice" onclick="this.style.visibility='hidden';">
			
			
		</div>
	</div>
	<div id="title"></div>
	<div id="caption"></div>
	<img id="loading" alt="" src="imagens/carregando_jpg.gif" style="visibility: hidden">
</body>
</html>
 

