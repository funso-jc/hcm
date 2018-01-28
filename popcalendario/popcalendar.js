	var	fixedX = -1			// x position (-1 if to appear below control)
	var	fixedY = -1			// y position (-1 if to appear below control)
	var startAt = 1			// 0 - sunday ; 1 - monday
	var showWeekNumber = 1	// 0 - don't show; 1 - show
	var showToday = 1		// 0 - don't show; 1 - show
	var imgDir = "images/"			

	var gotoString = "Go To Current Month"
	var todayString = "Hoy es"
	var weekString = "Sem"
	var scrollLeftMessage = "Click to scroll to previous mes. Hold mouse button to scroll automatically."
	var scrollRightMessage = "Click to scroll to next mes. Hold mouse button to scroll automatically."
	var selectMonthMessage = "Click to select a mes."
	var selectYearMessage = "Click to select a ano."
	var selectDateMessage = "Select [date] as date." // No sustituya [date], ser� substituido por la fecha.

	var	crossobj, crossMonthObj, crossYearObj, mesSeleccionado, anoSeleccionado, diaSeleccionado, omonthSelected, oyearSelected, odateSelected, monthConstructed, yearConstructed, intervalID1, intervalID2, timeoutID1, timeoutID2, ctlToPlaceValue, ctlNow, dateFormat, nStartingYear, fx, fy, limi, Cano

	var	bPageLoaded=false
	var	ie=document.all
	var	dom=document.getElementById

	var	ns4=document.layers
	var	hoy =	new	Date()
	var	diaActual = hoy.getDate()
	var	mesActual = hoy.getMonth()
	var	anoActual = hoy.getYear()  
	var	imgsrc = new Array("drop1.gif","drop2.gif","left1.gif","left2.gif","right1.gif","right2.gif")
	var	img	= new Array()

	var bShow = false;

    /* hides <select> and <applet> objects (for IE only) */
    function hideElement( elmID, overDiv )
    {
      if( ie )
      {
        for( i = 0; i < document.all.tags( elmID ).length; i++ )
        {
          obj = document.all.tags( elmID )[i];
          if( !obj || !obj.offsetParent )
          {
            continue;
          }
      
          // Find the element's offsetTop and offsetLeft relative to the BODY tag.
          objLeft   = obj.offsetLeft;
          objTop    = obj.offsetTop;
          objParent = obj.offsetParent;
          
          while( objParent.tagName.toUpperCase() != "BODY" )
          {
            objLeft  += objParent.offsetLeft;
            objTop   += objParent.offsetTop;
            objParent = objParent.offsetParent;
          }
      
          objHeight = obj.offsetHeight;
          objWidth = obj.offsetWidth;
      
          if(( overDiv.offsetLeft + overDiv.offsetWidth ) <= objLeft );
          else if(( overDiv.offsetTop + overDiv.offsetHeight ) <= objTop );
          else if( overDiv.offsetTop >= ( objTop + objHeight ));
          else if( overDiv.offsetLeft >= ( objLeft + objWidth ));
          else
          {
            obj.style.visibility = "hidden";
          }
        }
      }
    }
     
    /*
    * unhides <select> and <applet> objects (for IE only)
    */
    function showElement( elmID )
    {
      if( ie )
      {
        for( i = 0; i < document.all.tags( elmID ).length; i++ )
        {
          obj = document.all.tags( elmID )[i];
          
          if( !obj || !obj.offsetParent )
          {
            continue;
          }
        
          obj.style.visibility = "";
        }
      }
    }

	function HolidayRec (d, m, y, desc)
	{
		this.d = d
		this.m = m
		this.y = y
		this.desc = desc
	}

	var HolidaysCounter = 0
	var Vacaciones = new Array()

	function addHoliday (d, m, y, desc)
	{
		Vacaciones[HolidaysCounter++] = new HolidayRec ( d, m, y, desc )
	}

	if (dom)
	{
		for	(i=0;i<imgsrc.length;i++)
		{
			img[i] = new Image
			img[i].src = imgDir + imgsrc[i]
		}
		document.write ("<div onclick='bShow=true' id='calendar'	style='z-index:+999;position:absolute;visibility:hidden;'><table	width="+((showWeekNumber==1)?250:220)+" style='font-family:arial;font-size:11px;border-width:1;border-style:solid;border-color:#a0a0a0;font-family:arial; font-size:11px}' bgcolor='#ffffff'><tr background='images/calback.jpg'><td background='images/calback.jpg'><table width='"+((showWeekNumber==1)?248:218)+"'><tr><td style='padding:2px;font-family:arial; font-size:11px;'><font color='#FFFFFF'><B><span id='caption'></span></B></font></td><td align=right><a href='javascript:hideCalendar()'><IMG SRC='"+imgDir+"close.gif' BORDER='0' ALT='Close the Calendar'></a></td></tr></table></td></tr><tr><td style='padding:5px' bgcolor=#ffffff><span id='content'></span></td></tr>")
			
		if (showToday==1)
		{
			document.write ("<tr bgcolor=#f0f0f0><td style='padding:5px' align=center><span id='lblToday'></span></td></tr>")
		}
			
		document.write ("</table></div><div id='selectMonth' style='z-index:+999;position:absolute;visibility:hidden;'></div><div id='selectYear' style='z-index:+999;position:absolute;visibility:hidden;'></div>");
	}
	
//---------------------------------------------TRADUCCION EN ESPA�OL-----------------------------------------------------------------------------
	var	monthName =	new	Array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre")
	var	monthName2 = new Array("ENE","FEB","MAR","ABR","MAY","JUN","JUL","AGO","SEP","OCT","NOV","DIC")
	if (startAt==0)
	{
		dayName = new Array	("Lun","Mar","Mie","Jue","Vie","Sab","Dom")
	}
	else
	{
		dayName = new Array	("Lun","Mar","Mie","Jue","Vie","Sab","Dom")
	}
	var	styleAnchor="text-decoration:none;color:black;"
	var	styleLightBorder="border-style:solid;border-width:1px;border-color:#a0a0a0;"

	function swapImage(srcImg, destImg){
		if (ie)	{ document.getElementById(srcImg).setAttribute("src",imgDir + destImg) }
	}

	function init()	{
		if (!ns4)
		{ 
			if (!ie) { anoActual +=1900 }

			crossobj=(dom)?document.getElementById("calendar").style : ie? document.all.calendar : document.calendar
			hideCalendar()

			crossMonthObj=(dom)?document.getElementById("selectMonth").style : ie? document.all.selectMonth	: document.selectMonth

			crossYearObj=(dom)?document.getElementById("selectYear").style : ie? document.all.selectYear : document.selectYear

			monthConstructed=false;
			yearConstructed=false;

			if (showToday==1)
			{
				document.getElementById("lblToday").innerHTML =	todayString + " <a onmousemove='window.status=\""+gotoString+"\"' onmouseout='window.status=\"\"' title='"+gotoString+"' style='"+styleAnchor+"' href='javascript:mesSeleccionado=mesActual;anoSeleccionado=anoActual;constructCalendar();'>"+dayName[(hoy.getDay()-startAt==-1)?6:(hoy.getDay()-startAt)]+", " + diaActual + " " + monthName[mesActual].substring(0,15)	+ "	" +	anoActual	+ "</a>"
			}

			sHTML1="<span id='spanLeft'	style='border-style:solid;border-width:1;border-color:#ffffff;cursor:pointer' onmouseover='swapImage(\"changeLeft\",\"left2.gif\");this.style.borderColor=\"#88AAFF\";window.status=\""+scrollLeftMessage+"\"' onclick='javascript:decMonth()' onmouseout='clearInterval(intervalID1);swapImage(\"changeLeft\",\"left1.gif\");this.style.borderColor=\"#ffffff\";window.status=\"\"' onmousedown='clearTimeout(timeoutID1);timeoutID1=setTimeout(\"StartDecMonth()\",500)'	onmouseup='clearTimeout(timeoutID1);clearInterval(intervalID1)'>&nbsp<IMG id='changeLeft' SRC='"+imgDir+"left1.gif' width=10 height=11 BORDER=0>&nbsp</span>&nbsp;"
			sHTML1+="<span id='spanRight' style='border-style:solid;border-width:1;border-color:#ffffff;cursor:pointer'	onmouseover='swapImage(\"changeRight\",\"right2.gif\");this.style.borderColor=\"#88AAFF\";window.status=\""+scrollRightMessage+"\"' onmouseout='clearInterval(intervalID1);swapImage(\"changeRight\",\"right1.gif\");this.style.borderColor=\"#ffffff\";window.status=\"\"' onclick='incMonth()' onmousedown='clearTimeout(timeoutID1);timeoutID1=setTimeout(\"StartIncMonth()\",500)'	onmouseup='clearTimeout(timeoutID1);clearInterval(intervalID1)'>&nbsp<IMG id='changeRight' SRC='"+imgDir+"right1.gif'	width=10 height=11 BORDER=0>&nbsp</span>&nbsp"
			sHTML1+="<span id='spanMonth' style='border-style:solid;border-width:1;border-color:#ffffff;cursor:pointer'	onmouseover='swapImage(\"changeMonth\",\"drop2.gif\");this.style.borderColor=\"#88AAFF\";window.status=\""+selectMonthMessage+"\"' onmouseout='swapImage(\"changeMonth\",\"drop1.gif\");this.style.borderColor=\"#ffffff\";window.status=\"\"' onclick='popUpMonth()'></span>&nbsp;"
			sHTML1+="<span id='spanYear' style='border-style:solid;border-width:1;border-color:#fffffff;cursor:pointer' onmouseover='swapImage(\"changeYear\",\"drop2.gif\");this.style.borderColor=\"#88AAFF\";window.status=\""+selectYearMessage+"\"'	onmouseout='swapImage(\"changeYear\",\"drop1.gif\");this.style.borderColor=\"#ffffff\";window.status=\"\"'	onclick='popUpYear()'></span>&nbsp;"
			
			document.getElementById("caption").innerHTML  =	sHTML1

			bPageLoaded=true
		}
	}

	function hideCalendar()	{
		crossobj.visibility="hidden"
		if (crossMonthObj != null){crossMonthObj.visibility="hidden"}
		if (crossYearObj !=	null){crossYearObj.visibility="hidden"}

	    showElement( 'SELECT' );
		showElement( 'APPLET' );
	}

	function padZero(num) {
		return (num	< 10)? '0' + num : num ;
	}

	function constructDate(d,m,y)
	{
		sTmp = dateFormat
		sTmp = sTmp.replace	("dd","<e>")
		sTmp = sTmp.replace	("d","<d>")
		sTmp = sTmp.replace	("<e>",padZero(d))
		sTmp = sTmp.replace	("<d>",d)
		sTmp = sTmp.replace	("mmmm","<p>")
		sTmp = sTmp.replace	("mmm","<o>")
		sTmp = sTmp.replace	("mm","<n>")
		sTmp = sTmp.replace	("m","<m>")
		sTmp = sTmp.replace	("<m>",m+1)
		sTmp = sTmp.replace	("<n>",padZero(m+1))
		sTmp = sTmp.replace	("<o>",monthName[m])
		sTmp = sTmp.replace	("<p>",monthName2[m])
		sTmp = sTmp.replace	("yyyy",y)
		return sTmp.replace ("yy",padZero(y%100))
	}

	function closeCalendar() {
		var	sTmp

		hideCalendar();
		ctlToPlaceValue.value =	constructDate(diaSeleccionado,mesSeleccionado,anoSeleccionado)
	}

	/*** Mes Plegadizo ***/

	function StartDecMonth()
	{
		intervalID1=setInterval("decMonth()",80)
	}

	function StartIncMonth()
	{
		intervalID1=setInterval("incMonth()",80)
	}

	function incMonth () {
		mesSeleccionado++
		if (mesSeleccionado>11) {
			mesSeleccionado=0
			anoSeleccionado++
		}
		constructCalendar()
	}

	function decMonth () {
		mesSeleccionado--
		if (mesSeleccionado<0) {
			mesSeleccionado=11
			anoSeleccionado--
		}
		constructCalendar()
	}

	function constructMonth() {
		popDownYear()
		if (!monthConstructed) {
			sHTML =	""
			for	(i=0; i<12;	i++) {
				sName =	monthName[i];
				if (i==mesSeleccionado){
					sName =	"<B>" +	sName +	"</B>"
				}
				sHTML += "<tr><td id='m" + i + "' onmouseover='this.style.backgroundColor=\"#FFCC99\"' onmouseout='this.style.backgroundColor=\"\"' style='cursor:pointer' onclick='monthConstructed=false;mesSeleccionado=" + i + ";constructCalendar();popDownMonth();event.cancelBubble=true'>&nbsp;" + sName + "&nbsp;</td></tr>"
			}

			document.getElementById("selectMonth").innerHTML = "<table width=70	style='font-family:arial; font-size:11px; border-width:1; border-style:solid; border-color:#a0a0a0;' bgcolor='#FFFFDD' cellspacing=0 onmouseover='clearTimeout(timeoutID1)'	onmouseout='clearTimeout(timeoutID1);timeoutID1=setTimeout(\"popDownMonth()\",100);event.cancelBubble=true'>" +	sHTML +	"</table>"

			monthConstructed=true
		}
	}

	function popUpMonth() {
		constructMonth()
		crossMonthObj.visibility = (dom||ie)? "visible"	: "show"
		crossMonthObj.left = parseInt(crossobj.left) + 50
		crossMonthObj.top =	parseInt(crossobj.top) + 26

		hideElement( 'SELECT', document.getElementById("selectMonth") );
		hideElement( 'APPLET', document.getElementById("selectMonth") );			
	}

	function popDownMonth()	{
		crossMonthObj.visibility= "hidden"
	}

	/*** A�o Plegadizo ***/

	function incYear() {
	}

	function decYear() {
	}

	function selectYear(nYear) {
		anoSeleccionado=parseInt(nYear);
		yearConstructed=false;
		constructCalendar();
		popDownYear();
	}

	function constructYear() {
		//----------------------------------------FUNCION PARA DETERMINAR LOS A�OS---------------------------------------//
		popDownMonth()
		sHTML =	""
		if (Cano <= 0) {
			var limAno = 30;}
		else {
			var limAno = Cano;}
		if (!yearConstructed) {

			sHTML =	""

			nStartingYear =	anoActual;
			
//Dentro de el siguiente ciclo se generan los a�os, en el caso de que sea introcir fecha de nacimiento restale 30---- i>=(anoActual-30)-----
//CAMBIA el siguiente for por este " for	(i=anoActual; i<=(anoActual+4); i++) { " si quieres que los a�os empizen en el actual en adelante	
        	for	(i=anoActual; i>=(anoActual-limAno); i--) {		
				sName =	i;
				if (i==anoSeleccionado){
					sName =	"<B>" +	sName +	"</B>"
				}

				sHTML += "<tr><td id='y" + sName + "' onmouseover='this.style.backgroundColor=\"#FFCC99\"' onmouseout='this.style.backgroundColor=\"\"' style='cursor:pointer' onclick='selectYear("+i+");event.cancelBubble=true'>&nbsp;" + sName + "&nbsp;</td></tr>"
			}

			sHTML += ""

			document.getElementById("selectYear").innerHTML	= "<table width=44 style='font-family:arial; font-size:11px; border-width:1; border-style:solid; border-color:#a0a0a0;'	bgcolor='#FFFFDD' onmouseover='clearTimeout(timeoutID2)' onmouseout='clearTimeout(timeoutID2);timeoutID2=setTimeout(\"popDownYear()\",100)' cellspacing=0>"	+ sHTML	+ "</table>"

			yearConstructed	= true
		}
	}

	function popDownYear() {
		clearInterval(intervalID1)
		clearTimeout(timeoutID1)
		clearInterval(intervalID2)
		clearTimeout(timeoutID2)
		crossYearObj.visibility= "hidden"
	}

	function popUpYear() {
		var	leftOffset

		constructYear()
		crossYearObj.visibility	= (dom||ie)? "visible" : "show"
		leftOffset = parseInt(crossobj.left) + document.getElementById("spanYear").offsetLeft
		if (ie)
		{
			leftOffset += 6
		}
		crossYearObj.left =	leftOffset
		crossYearObj.top = parseInt(crossobj.top) +	26
	}

	/*** calendar ***/
   function WeekNbr(n) {
      // Algorithm used:
      // From Klaus Tondering's Calendar document (The Authority/Guru)
      // hhtp://www.tondering.dk/claus/calendar.html
      // a = (14-mes) / 12
      // y = ano + 4800 - a
      // m = mes + 12a - 3
      // J = dia + (153m + 2) / 5 + 365y + y / 4 - y / 100 + y / 400 - 32045
      // d4 = (J + 31741 - (J mod 7)) mod 146097 mod 36524 mod 1461
      // L = d4 / 1460
      // d1 = ((d4 - L) mod 365) + L
      // WeekNumber = d1 / 7 + 1
 
      ano = n.getFullYear();
      mes = n.getMonth() + 1;
      if (startAt == 0) {
         dia = n.getDate() + 1;
      }
      else {
         dia = n.getDate();
      }
 
      a = Math.floor((14-mes) / 12);
      y = ano + 4800 - a;
      m = mes + 12 * a - 3;
      b = Math.floor(y/4) - Math.floor(y/100) + Math.floor(y/400);
      J = dia + Math.floor((153 * m + 2) / 5) + 365 * y + b - 32045;
      d4 = (((J + 31741 - (J % 7)) % 146097) % 36524) % 1461;
      L = Math.floor(d4 / 1460);
      d1 = ((d4 - L) % 365) + L;
      semana = Math.floor(d1/7) + 1;
 
      return semana;
   }

	function constructCalendar () {
		var aNumDays = Array (31,0,31,30,31,30,31,31,30,31,30,31)
		var dateMessage
		var	startDate =	new	Date (anoSeleccionado,mesSeleccionado,1)
		var endDate
		if (mesSeleccionado==1)
		{
			endDate	= new Date (anoSeleccionado,mesSeleccionado+1,1);
			endDate	= new Date (endDate	- (24*60*60*1000));
			numDaysInMonth = endDate.getDate()
		}
		else
		{
			numDaysInMonth = aNumDays[mesSeleccionado];
		}

		datePointer	= 0
		dayPointer = startDate.getDay() - startAt
		
		if (dayPointer<0)
		{
			dayPointer = 6
		}

		sHTML =	"<table	 border=0 style='font-family:verdana;font-size:10px;'><tr>"

		if (showWeekNumber==1)
		{
			sHTML += "<td width=27><b>" + weekString + "</b></td><td width=1 rowspan=7 bgcolor='#d0d0d0' style='padding:0px'><img src='"+imgDir+"divider.gif' width=1></td>"
		}

		for	(i=0; i<7; i++)	{
			sHTML += "<td width='27' align='right'><B>"+ dayName[i]+"</B></td>"
		}
		sHTML +="</tr><tr>"
		
		if (showWeekNumber==1)
		{
			sHTML += "<td align=right>" + WeekNbr(startDate) + "&nbsp;</td>"
		}

		for	( var i=1; i<=dayPointer;i++ )
		{
			sHTML += "<td>&nbsp;</td>"
		}
	
		for	( datePointer=1; datePointer<=numDaysInMonth; datePointer++ )
		{
			dayPointer++;
			sHTML += "<td align=right>"
			sStyle=styleAnchor
			if ((datePointer==odateSelected) &&	(mesSeleccionado==omonthSelected)	&& (anoSeleccionado==oyearSelected))
			{ sStyle+=styleLightBorder }
            //alert ("hola ("+omonthSelected+")");
			sHint = ""
			for (k=0;k<HolidaysCounter;k++)
			{
				if ((parseInt(Vacaciones[k].d)==datePointer)&&(parseInt(Vacaciones[k].m)==(mesSeleccionado+1)))
				{
					if ((parseInt(Vacaciones[k].y)==0)||((parseInt(Vacaciones[k].y)==anoSeleccionado)&&(parseInt(Vacaciones[k].y)!=0)))
					{
						sStyle+="background-color:#FFDDDD;"
						sHint+=sHint==""?Vacaciones[k].desc:"\n"+Vacaciones[k].desc
					}
				}
			}

			var regexp= /\"/g
			sHint=sHint.replace(regexp,"&quot;")

			dateMessage = "onmousemove='window.status=\""+selectDateMessage.replace("[date]",constructDate(datePointer,mesSeleccionado,anoSeleccionado))+"\"' onmouseout='window.status=\"\"' "
          	var f1= new Date(anoSeleccionado,mesSeleccionado,datePointer);
			//alert ("hola ("+mesSeleccionado+")");
//------------------------------------TERMINAR LA FUNCION EN 0 PARA QUE INICIE EN EL DIA ACTUAL EN LA SIGUIENTE LINEA----------------------------------------------
			var temp = addToDate(hacerDateFormat(diaActual, mesActual, anoActual),0);
			var nDia = Number(temp.substr(0, 2)); 
   			var nMes = Number(temp.substr(3, 2)); 
   			var nAno = Number(temp.substr(6, 4)); 
			var yDia = Number(fy.substr(0, 2)); 
   			var yMes = Number(fy.substr(3, 2))-1; 
   			var yAno = Number(fy.substr(6, 4)); 
			var xDia = Number(fx.substr(0, 2)); 
   			var xMes = Number(fx.substr(3, 2))-1; 
   			var xAno = Number(fx.substr(6, 4)); 
			var fechai = new Date(yAno,yMes,yDia);
			var fechaf = new Date(xAno,xMes,xDia);
			//alert ("hola ("+mesSeleccionado+")");
			if ((datePointer==nDia)&&(mesSeleccionado==nMes)&&(anoSeleccionado==nAno))
			//{ sHTML += "<b style='"+sStyle+"'><font color=#ff0000>&nbsp;" + datePointer + "</font>&nbsp;</b>"}
			{ sHTML += "<b><a "+dateMessage+" title=\"" + sHint + "\" style='"+sStyle+"' href='javascript:diaSeleccionado="+datePointer+";closeCalendar();'><font color=#ff0000>&nbsp;" + datePointer + "</font>&nbsp;</a></b>"}
			//----------------------------RETRINGIR LOS DOMINGOS -------------------------------------------/ 
			else if ((dayPointer % 7 == (startAt * -1)+7)&&(limi==1))
			{ sHTML += "&nbsp;<font color=#909090>" + datePointer + "</font>&nbsp;" }
			//--------------------------- RESTRINGIR LOS SABADOS -------------------------------------------/
			else if ((dayPointer % 7 == (startAt * -1)+1)&&(limi==1))
			{ sHTML += "&nbsp;<font color=#909090>" + datePointer + "</font>&nbsp;" }
			///////////////////////////// LA FECHA MENOR //////////////////////////////////////////////////
			else if (FechaMenor(fechai,f1)) //((datePointer<diaActual)&&(mesSeleccionado<=mesActual)&&(anoSeleccionado=anoActual))
			{ sHTML += "&nbsp;<font color=#909090>" + datePointer + "</font>&nbsp;" }
			/////////////////////////////LA FECHA MAYOR//////////////////////////////////////////////////
			else if (fechaf<f1)
			{ sHTML += "&nbsp;<font color=#909090>" + datePointer + "</font>&nbsp;" }
			else
			{ sHTML += "<a "+dateMessage+" title=\"" + sHint + "\" style='"+sStyle+"' href='javascript:diaSeleccionado="+datePointer + ";closeCalendar();'>&nbsp;" + datePointer + "&nbsp;</a>" }

			sHTML += ""
			if ((dayPointer+startAt) % 7 == startAt) { 
				sHTML += "</tr><tr>" 
				if ((showWeekNumber==1)&&(datePointer<numDaysInMonth))
				{
					sHTML += "<td align=right>" + (WeekNbr(new Date(anoSeleccionado,mesSeleccionado,datePointer+1))) + "&nbsp;</td>"
				}
			}
		}

		document.getElementById("content").innerHTML   = sHTML
		document.getElementById("spanMonth").innerHTML = "&nbsp;" +	monthName[mesSeleccionado] + "&nbsp;<IMG id='changeMonth' SRC='"+imgDir+"drop1.gif' WIDTH='12' HEIGHT='10' BORDER=0>"
		document.getElementById("spanYear").innerHTML =	"&nbsp;" + anoSeleccionado	+ "&nbsp;<IMG id='changeYear' SRC='"+imgDir+"drop1.gif' WIDTH='12' HEIGHT='10' BORDER=0>"
	}

	function popUpCalendar(ctl,	ctl2, format, fi, ff, lim, cAno) {
		//LAS VARIABLES LEFTPOS Y TOPPOS SON PARA DETERMINAR EL TAMA�O Y LA POSICI�N DE LA VENTANA DEL CALENDARIO
		 // alert (" ("+ctl+")("+ctl2+") ("+format+") ("+fi+") ("+ff+") ("+lim+") ("+cAno+")"); 
		var	leftpos=-190
		var	toppos=-10
	fy = fi;
	fx = ff; 
	limi =lim;
	Cano = cAno;
  
	//	alert ("1"); 
		if (bPageLoaded)
		{
			//alert ("2"); 
			if ( crossobj.visibility ==	"hidden" ) {
				ctlToPlaceValue	= ctl2
				dateFormat=format;
//alert ("3"); 
				formatChar = " "
				aFormat	= dateFormat.split(formatChar)
				if (aFormat.length<3)
				{
					//alert ("4"); 
					formatChar = "/"
					aFormat	= dateFormat.split(formatChar)
					if (aFormat.length<3)
					{
						//alert ("5"); 
						formatChar = "."
						aFormat	= dateFormat.split(formatChar)
						if (aFormat.length<3)
						{
							//alert ("6"); 
							formatChar = "-"
							aFormat	= dateFormat.split(formatChar)
							if (aFormat.length<3)
							{
								//alert ("7"); 
								// invalid date	format
								formatChar=""
							}
						}
					}
				}

				tokensChanged =	0
				if ( formatChar	!= "" )
				{
					//alert ("8"); 
					// use user's date
					aData =	ctl2.value.split(formatChar)

					for	(i=0;i<3;i++)
					{
						//alert ("9");
						if ((aFormat[i]=="d") || (aFormat[i]=="dd"))
						{ 
						//alert ("10");
							diaSeleccionado = parseInt(aData[i], 10)
							tokensChanged ++
						}
						else if	((aFormat[i]=="m") || (aFormat[i]=="mm"))
						{
							//alert ("11");
							mesSeleccionado =	parseInt(aData[i], 10)-1
							tokensChanged ++
						}
						else if	(aFormat[i]=="yyyy")
						{
							//alert ("12");
							anoSeleccionado = parseInt(aData[i], 10)
							tokensChanged ++
						}
						else if	(aFormat[i]=="mmm")
						{
							for	(j=0; j<12;	j++)
							{
								if (aData[i]==monthName[j])
								{
									mesSeleccionado=j
									tokensChanged ++
								}
							}
						}
						else if	(aFormat[i]=="mmmm")
						{
							for	(j=0; j<12;	j++)
							{
								if (aData[i]==monthName2[j])
								{
									mesSeleccionado=j
									tokensChanged ++
								}
							}
						}
					}
				}

				if ((tokensChanged!=3)||isNaN(diaSeleccionado)||isNaN(mesSeleccionado)||isNaN(anoSeleccionado))
				{//alert ("13");
//------------------------------------TERMINAR LA FUNCION EN 0 PARA QUE INICIE EN EL DIA ACTUAL EN LA SIGUIENTE LINEA----------------------------------------------
					var temp = addToDate(hacerDateFormat(diaActual, mesActual, anoActual),0);
					var nDia = Number(temp.substr(0, 2)); 
   					var nMes = Number(temp.substr(3, 2)); 
   					var nAno = Number(temp.substr(6, 4)); 
			//alert ("14");
					diaSeleccionado = nDia
					mesSeleccionado = nMes
					anoSeleccionado = nAno
				}

				odateSelected=diaSeleccionado
				omonthSelected=mesSeleccionado
				oyearSelected=anoSeleccionado
				aTag = ctl
				do {
					aTag = aTag.offsetParent;
					leftpos	+= aTag.offsetLeft;
					toppos += aTag.offsetTop;
				} while(aTag.tagName!="BODY");

				crossobj.left =	fixedX==-1 ? ctl.offsetLeft	+ leftpos :	fixedX
				crossobj.top = fixedY==-1 ?	ctl.offsetTop +	toppos + ctl.offsetHeight +	2 :	fixedY
				constructCalendar (1, mesSeleccionado, anoSeleccionado);
				crossobj.visibility=(dom||ie)? "visible" : "show"
			//	alert ("15");
				
				hideElement( 'SELECT', document.getElementById("calendar") );
				hideElement( 'APPLET', document.getElementById("calendar") );			

				bShow = true;
			}
			else
			{
				hideCalendar()
				if (ctlNow!=ctl) {popUpCalendar(ctl, ctl2, format, fi, ff, div)}
			//	alert ("16");
			}
			ctlNow = ctl
		}
		
	}

	document.onkeypress = function hidecal1 () { 
		if (event.keyCode==27) 
		{
			hideCalendar()
		}
	}
	document.onclick = function hidecal2 () { 		
		if (!bShow)
		{
			hideCalendar()
		}
		bShow = false
	}

	if(ie)
	{
		init()
	}
	else
	{
		window.onload=init
	}
	
// ************ ADICIONAR DIAS A UNA FECHA ***********

  var aFinMes = new Array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31); 

  function finMes(nMes, nAno){ 
   return aFinMes[nMes - 1] + (((nMes == 2) && (nAno % 4) == 0)? 1: 0); 
  } 

   function padNmb(nStr, nLen, sChr){ 
    var sRes = String(nStr); 
    for (var i = 0; i < nLen - String(nStr).length; i++) 
     sRes = sChr + sRes; 
    return sRes; 
   } 

   function hacerDateFormat(nDay, nMonth, nYear){ 
    var sRes; 
    sRes = padNmb(nDay, 2, "0") + "/" + padNmb(nMonth, 2, "0") + "/" + padNmb(nYear, 4, "0"); 
    return sRes; 
   } 
    
  function incDate(sFec0){ 
   var nDia = parseInt(sFec0.substr(0, 2), 10); 
   var nMes = parseInt(sFec0.substr(3, 2), 10); 
   var nAno = parseInt(sFec0.substr(6, 4), 10); 
   nDia += 1; 
   if (nDia > finMes(nMes, nAno)){ 
    nDia = 1; 
    nMes += 1; 
    if (nMes == 13){ 
     nMes = 1; 
     nAno += 1; 
    } 
   } 
   return hacerDateFormat(nDia, nMes, nAno); 
  } 

  function decDate(sFec0){ 
   var nDia = Number(sFec0.substr(0, 2)); 
   var nMes = Number(sFec0.substr(3, 2)); 
   var nAno = Number(sFec0.substr(6, 4)); 
   nDia -= 1; 
   if (nDia == 0){ 
    nMes -= 1; 
    if (nMes == 0){ 
     nMes = 12; 
     nAno -= 1; 
    } 
    nDia = finMes(nMes, nAno); 
   } 
   return hacerDateFormat(nDia, nMes, nAno); 
  } 

  function addToDate(sFec0, sInc){ 
   var nInc = Math.abs(parseInt(sInc)); 
   var sRes = sFec0; 
   if (parseInt(sInc) >= 0) 
    for (var i = 0; i < nInc; i++) sRes = incDate(sRes); 
   else 
    for (var i = 0; i < nInc; i++) sRes = decDate(sRes); 
   return sRes; 
  } 

  function recalcF1(){ 
   with (document.formulario){ 
    fecha1.value = addToDate(fecha0.value, increm.value); 
   } 
  } 
 
function ValidarFecha()
{/*
	var temp = new Date()
	fechaActual= (temp.getDate() + 1) + "/";
	fechaActual += temp.getMonth() + "/";
    fechaActual += temp.getFullYear();

	if (( addToDate(fechaActual, 2) < document.form1.date1.value )  && (document.form1.date1.value > fechaActual ) ){
		return true
		}
	else {
		return false;
	} */
	return true;
}



//----------------------------Eliminar las siguientes lineas para activar todos los dias del a�o-----y retornar un false-----------------------------------------
function FechaMenor(fechaActual, fecha1) {
	
			var msegActual = fechaActual.getTime();
			var msegFecha1 = fecha1.getTime();
			var Diferencia = msegActual - msegFecha1;
			Diferencia /= 86400000;
			if (Diferencia < 0) {
				return false;
			}
			else {
				return true;
			} return false;
		}