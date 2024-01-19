ciHelper = {
	dias: 'Domingo Lunes Martes Mi&eacute;rcoles Jueves Viernes S&aacute;bado '.split(' '),
	meses: 'Enero Febrero Marzo Abril Mayo Junio Julio Agosto Setiembre Octubre Noviembre Diciembre'.split(' '),
	fillMeses: function($cbo){
		var meses = ciHelper.meses;
		var init_month = 1;
		for(var mes in meses){
			$cbo.append('<option value="'+init_month+'">'+meses[mes]+'</option>');
			init_month++;
		}
		return $cbo;
	},
	titleMessages: {
		infoReq: "Informacion requerida",
		regiGua: "Registro guardado",
		regiAct: "Registro actualizado",
		regiEli: "Registro eliminado",
		dbReq: 'DB_Error: Data incompleta'
	},
	sendingInfo: function(){
		K.notification({text: 'Enviando informaci&oacute;n...<img src="images/ajax-loader_noti.gif" />'});
	},
	noItems: function(){
		K.notification({text: 'No se encontraron registros!'});
	},
	gridButtons: function($grid){
		$grid.find('[name=btnGrid]:last').mouseup(function(){
	    	$(this).trigger('contextmenu');
	    	var $button = $grid.find('.ui-state-highlight [name=btnGrid]');
	    	var top = $button.offset().top + $button.height();
	    	var left = $button.offset().left;
			$('#jqContextMenu').show().css({
				"top": top+'px',
				"left": left+'px'
			});
			$('#jqContextMenu').next('div').show().css({
				"top": (top+2)+'px',
				"left": (left+2)+'px'
			});
		}).button({
	        icons: { primary: "ui-icon-circlesmall-plus" },
	        text: false
	    });
	},
	dateDifference: function(date1,date2){
		date1 = new Date(date1.sec*1000);
		date2 = new Date(date2.sec*1000);
		var diferencia = date1.getTime() - date2.getTime();
		return Math.floor(diferencia / (1000 * 60 * 60 * 24));
	},
	dateDifferenceMin: function(date1,date2){
		date1 = new Date(date1.sec*1000);
		date2 = new Date(date2.sec*1000);
		var diferencia = date1.getTime() - date2.getTime();
		return Math.floor(diferencia / (1000 * 60 * 60));
	},
	dateDiffNow: function(date){
		var date1 = new Date(date.sec*1000);
		var date2 = new Date();
		var diferencia = date1.getTime() - date2.getTime();
		return Math.floor(diferencia / (1000 * 60 * 60 * 24));
	},
	dateDiffNow_regular: function(date1){
		var date2 = new Date();
		date2.setHours(0);
		date2.setMinutes(0);
		date2.setSeconds(0);
		var diferencia = date1.getTime() - date2.getTime();
		//console.info(parseInt(diferencia / (1000 * 60 * 60 * 24)));
		return parseInt(diferencia / (1000 * 60 * 60 * 24));
	},
	dateDiffNowSec: function(date){
		var date1 = new Date(date.sec*1000);
		var date2 = new Date();
		var diferencia = date2.getTime() - date1.getTime();
		return Math.floor(diferencia / (1000));
	},
	dateSumNow: function(anios){
		var date = new Date();
		date.setYear(date.getFullYear()+anios);
		return date;
	},
	dateFormatLongS: function(date){
		var dateFor = ciHelper.dias[date.getDay()] + " " +date.getDate()+" de "+ciHelper.meses[date.getMonth()]+" de "+date.getFullYear()+" - "+(date.getHours()<10?'0'+date.getHours():date.getHours())+':'+(date.getMinutes()<10?'0'+date.getMinutes():date.getMinutes());
		return dateFor;
	},
	/*
	
	Function: dateComp
		Compare two dates and return false if first date is less than the last one.
	
	dateCompOptions:
		fecha - (string) first date to compare on format "yyyy-mm-dd hh:ii:ss".
		fecha2 - (string) second date to compare on format "yyyy-mm-dd hh:ii:ss".

	*/
	dateComp: function(fecha, fecha2){
		var xMes=fecha.substr(5, 2);
		var xDia=fecha.substr(8, 2);
		var xAnio=fecha.substr(0,4);
		var yMes=fecha2.substr(5, 2);
		var yDia=fecha2.substr(8, 2);
		var yAnio=fecha2.substr(0,4);
		if (xAnio > yAnio){
			return(true);
		}else{
			if (xAnio == yAnio){
				if (xMes > yMes){
		      		return(true);
				}
		 		if (xMes == yMes){
					if (xDia > yDia){
						return(true);
					}else{
						return(false);
					}
				}else{
					return(false);
				}
			}else{
				return(false);
			}
		}
	},
	string: {
		replaceAll: function( text, busca, reemplaza ){
			while (text.toString().indexOf(busca) != -1)
			text = text.toString().replace(busca,reemplaza);
			return text;
		}
	},
	date: {
		get: {
			/***************************************************************
			* ESTA FUNCION ADMITE DE PARAMETRO PLUS
			* - plus: cantidad de dias que se desea agregar a la fecha actual
			***************************************************************/
			now_y: function(){
				var date = new Date();
				return date.getFullYear();
			},
			now_m: function(){
				var date = new Date();
				return date.getMonth()+1;
			},
			now_ymd: function(plus){
				var date = new Date();
				if(plus!=null){
					 tiempo = date.getTime();
					 milisegundos = parseInt(plus*24*60*60*1000);
					 date.setTime(tiempo+milisegundos);
				}
				var month = date.getMonth()+1;
				return date.getFullYear()+"-"+(month<10?'0'+month:month)+'-'+(date.getDate()<10?'0'+date.getDate():date.getDate());
			},
			now_ymdhi: function(plus){
				var date = new Date();
				if(plus!=null){
					 tiempo = date.getTime();
					 milisegundos = parseInt(plus*24*60*60*1000);
					 date.setTime(tiempo+milisegundos);
				}
				var month = date.getMonth()+1;
				return date.getFullYear()+"-"+(month<10?'0'+month:month)+'-'+(date.getDate()<10?'0'+date.getDate():date.getDate())+' '+date.getHours()+':'+date.getMinutes();
			},
			now_per: function(plus){
				var date = new Date();
				if(plus!=null){
					 tiempo = date.getTime();
					 milisegundos = parseInt(plus*24*60*60*1000);
					 date.setTime(tiempo+milisegundos);
				}
				var month = date.getMonth()+1;
				return (month<10?'0'+month:month)+'-'+date.getFullYear();
				}
				
			},
		format: {
			dayBDNotHour: function(date){
				var month = Date().getMonth();;
				var dateFor = date.getFullYear()+"-"+(month<10?'0'+month:month)+'-'+(date.getDate()<10?'0'+date.getDate():date.getDate());
				return dateFor;
			},
			dayBD: function(date){
				var month = date.getMonth()+1;
				var dateFor = date.getFullYear()+"-"+(month<10?'0'+month:month)+'-'+(date.getDate()<10?'0'+date.getDate():date.getDate())+' '+(date.getHours()<10?'0'+date.getHours():date.getHours())+':'+(date.getMinutes()<10?'0'+date.getMinutes():date.getMinutes())+':00';
				return dateFor;
			},
			dayLong: function(date){
				return ciHelper.dias[date.getDay()] + " " +date.getDate()+" de "+ciHelper.meses[date.getMonth()]+" de "+date.getFullYear();
			},
			hour: function(date){
				return (date.getHours()<10?'0'+date.getHours():date.getHours())+':'+(date.getMinutes()<10?'0'+date.getMinutes():date.getMinutes());
			},
			db: function(dateOri){
				var date = new Date(dateOri.sec*1000),
				month = date.getMonth()+1,
				dateFor = date.getFullYear()+"-"+(month<10?'0'+month:month)+"-"+(date.getDate()<10?'0'+date.getDate():date.getDate())+" "+(date.getHours()<10?'0'+date.getHours():date.getHours())+':'+(date.getMinutes()<10?'0'+date.getMinutes():date.getMinutes())+':00';
				return dateFor;
			},
			dbNotSec: function(dateOri){
				var date = new Date(dateOri.sec*1000),
				month = date.getMonth()+1,
				dateFor = date.getFullYear()+"-"+(month<10?'0'+month:month)+"-"+(date.getDate()<10?'0'+date.getDate():date.getDate())+" "+(date.getHours()<10?'0'+date.getHours():date.getHours())+':'+(date.getMinutes()<10?'0'+date.getMinutes():date.getMinutes());
				return dateFor;
			},
			dbNotHour: function(dateOri){
				var date = new Date(dateOri.sec*1000),
				month = date.getMonth()+1,
				day = date.getDate();
				if(month<10) month = '0'+month;
				if(day<10) day = '0'+day;
				var dateFor = date.getFullYear()+"-"+month+"-"+day;
				return dateFor;
			},
			y: function(date){
				return date.getFullYear();
			},

			m: function(date){
				var month = date.getMonth()+1;
				month = (month<10?'0'+month:month);
				return month;
			},
			d: function(date){
				var day = date.getDate();
				day = (day<10?'0'+day:day);
				return day;
			},
			h: function(date,hours){
				if(hours==null) hours = 0;
				var hour = date.getUTCHours();
				hour -= hours;
				if(hour>23) hour -= 24;
				if(hour<0) hour += 24;
				hour = (hour<10?'0'+hour:hour);
				return hour;
			},
			i: function(date){
				var min = date.getMinutes();
				min = (min<10?'0'+min:min);
				return min;
			},
			hi: function(date,hours){
				return ciHelper.date.format.h(date,hours)+':'+ciHelper.date.format.i(date);
			},
			string_m: function(date){
				return ciHelper.meses[date.getMonth()];
			},
			ymd: function(date){
				return ciHelper.date.format.y(date)+'-'+ciHelper.date.format.m(date)+'-'+ciHelper.date.format.d(date);
			},
			ymdhi: function(date,hours){
				if(hours==null) hours = 0;
				return ciHelper.date.format.y(date)+'-'+ciHelper.date.format.m(date)+'-'+ciHelper.date.format.d(date)+' '+ciHelper.date.format.hi(date,hours)+':00';
			},
			ymdhis: function(date,hours){
				if(hours==null) hours = 0;
				return ciHelper.date.format.y(date)+'-'+ciHelper.date.format.m(date)+'-'+ciHelper.date.format.d(date)+' '+ciHelper.date.format.hi(date,hours)+':00';
			},
			ym: function(date){
				return ciHelper.date.format.string_m(date)+' del '+ciHelper.date.format.y(date);
			},
			bd_y: function(dateOri){
				if(dateOri==null) return '--';
				date = new Date(dateOri.sec*1000);
				return date.getFullYear();
			},
			bd_m: function(dateOri){
				if(dateOri==null) return '--';
				date = new Date(dateOri.sec*1000);
				var month = date.getMonth()+1;
				month = (month<10?'0'+month:month);
				return month;
			},
			bd_d: function(dateOri){
				if(dateOri==null) return '--';
				date = new Date(dateOri.sec*1000);
				var day = date.getDate();
				day = (day<10?'0'+day:day);
				return day;
			},
			bd_ymd: function(dateOri){
				if(dateOri==null) return '--';
				var date = new Date(dateOri.sec*1000);
				return ciHelper.date.format.y(date)+'-'+ciHelper.date.format.m(date)+'-'+ciHelper.date.format.d(date);
			},
			bd_his: function(dateOri){
				if(dateOri==null) return '--';
				var date = new Date(dateOri.sec*1000);
				return (date.getHours()<10?'0'+date.getHours():date.getHours())+':'+(date.getMinutes()<10?'0'+date.getMinutes():date.getMinutes())+':'+(date.getSeconds()<10?'0'+date.getSeconds():date.getSeconds());
			},
			bd_ymdhi: function(dateOri){
				if(dateOri==null) return '--';
				var date = new Date(dateOri.sec*1000);
				return ciHelper.date.format.y(date)+'-'+ciHelper.date.format.m(date)+'-'+ciHelper.date.format.d(date)+' '+(date.getHours()<10?'0'+date.getHours():date.getHours())+':'+(date.getMinutes()<10?'0'+date.getMinutes():date.getMinutes());
			},
			bd_ymdhis: function(dateOri){
				if(dateOri==null) return '--';
				var date = new Date(dateOri.sec*1000);
				return ciHelper.date.format.y(date)+'-'+ciHelper.date.format.m(date)+'-'+ciHelper.date.format.d(date)+' '+(date.getHours()<10?'0'+date.getHours():date.getHours())+':'+(date.getMinutes()<10?'0'+date.getMinutes():date.getMinutes())+':'+(date.getSeconds()<10?'0'+date.getSeconds():date.getSeconds());
			},
			long: function(dateOri){
				if(dateOri==null){
					return '--';
				}
				if(dateOri.sec==null){
					return '--';
				}
				var date = new Date(dateOri.sec*1000);		
				return ciHelper.dias[date.getDay()] + " " +date.getDate()+" de "+ciHelper.meses[date.getMonth()]+" de "+date.getFullYear()+" - "+(date.getHours()<10?'0'+date.getHours():date.getHours())+':'+(date.getMinutes()<10?'0'+date.getMinutes():date.getMinutes());
			}
		},
		getYear: function(){
			var date = new Date();
			return date.getFullYear();
		},
		getMonth: function(){
			var date = new Date();
			return date.getMonth()+1;
		},
		getMonthi: function(){
			var date = new Date();
			return date.getMonth()+1;
		},
		getDay: function(){
			var date = new Date();
			return date.getDate();
		},
		getStringPer: function(date){
			date = new Date(date.sec*1000);
			return ciHelper.meses[date.getMonth()]+' '+date.getFullYear();
		},
		getStringAge: function(fromdate, todate){
		    if(todate) todate= new Date(todate);
		    else todate= new Date();

		    var age= [], fromdate= new Date(fromdate),
		    y= [todate.getFullYear(), fromdate.getFullYear()],
		    ydiff= y[0]-y[1],
		    m= [todate.getMonth(), fromdate.getMonth()],
		    mdiff= m[0]-m[1],
		    d= [todate.getDate(), fromdate.getDate()],
		    ddiff= d[0]-d[1];

		    if(mdiff < 0 || (mdiff=== 0 && ddiff<0))--ydiff;
		    if(mdiff<0) mdiff+= 11;
		    if(ddiff<0){
		        fromdate.setMonth(m[1]+1, 0);
		        ddiff= fromdate.getDate()-d[1]+d[0];
		        --mdiff;
		    }
		    if(ydiff> 0) age.push(ydiff+ ' a&ntilde;o'+(ydiff> 1? 's ':' '));
		    if(mdiff> 0) age.push(mdiff+ ' mes'+(mdiff> 1? 'es':''));
		    if(ddiff> 0) age.push(ddiff+ ' d&iacute;a'+(ddiff> 1? 's':''));
		    if(age.length>1) age.splice(age.length-1,0,' y ');    
		    return age.join('');
		},
		daysInMonth: function(humanMonth, year) {
   			return new Date(year || new Date().getFullYear(), humanMonth, 0).getDate();
   		},
		convert:function(d) {
	        // Converts the date in d to a date-object. The input can be:
	        //   a date object: returned without modification
	        //  an array      : Interpreted as [year,month,day]. NOTE: month is 0-11.
	        //   a number     : Interpreted as number of milliseconds
	        //                  since 1 Jan 1970 (a timestamp) 
	        //   a string     : Any format supported by the javascript engine, like
	        //                  "YYYY/MM/DD", "MM/DD/YYYY", "Jan 31 2009" etc.
	        //  an object     : Interpreted as an object with year, month and date
	        //                  attributes.  **NOTE** month is 0-11.
	        return (
	            d.constructor === Date ? d :
	            d.constructor === Array ? new Date(d[0],d[1],d[2]) :
	            d.constructor === Number ? new Date(d) :
	            d.constructor === String ? new Date(d) :
	            typeof d === "object" ? new Date(d.year,d.month,d.date) :
	            NaN
	        );
    	},
		compare:function(a,b) {
	        // Compare two dates (could be of any type supported by the convert
	        // function above) and returns:
	        //  -1 : if a < b
	        //   0 : if a = b
	        //   1 : if a > b
	        // NaN : if a or b is an illegal date
	        // NOTE: The code inside isFinite does an assignment (=).
	        return (
	            isFinite(a=ciHelper.date.convert(a).valueOf()) &&
	            isFinite(b=ciHelper.date.convert(b).valueOf()) ?
	            (a>b)-(a<b) :
	            NaN
	        );
	    },
	    inRange:function(d,start,end) {
	        // Checks if date in d is between dates in start and end.
	        // Returns a boolean or NaN:
	        //    true  : if d is between start and end (inclusive)
	        //    false : if d is before start or after end
	        //    NaN   : if one or more of the dates is illegal.
	        // NOTE: The code inside isFinite does an assignment (=).
	       return (
	            isFinite(d=ciHelper.date.convert(d).valueOf()) &&
	            isFinite(start=ciHelper.date.convert(start).valueOf()) &&
	            isFinite(end=ciHelper.date.convert(end).valueOf()) ?
	            start <= d && d <= end :
	            NaN
	        );
	    },
	    diff: function(date1,date2){
			var diferencia = date1.getTime() - date2.getTime();
			return Math.floor(diferencia / 1000);
	    },
	    diffDays: function(date1,date2){
			var diferencia = date1.getTime() - date2.getTime();
			if(ciHelper.date.format.bd_ymd(date1)==ciHelper.date.format.bd_ymd(date2))
				return 0;
			else
				return Math.floor(diferencia / 1000 / 60 / 60 / 24);
	    },
	    getWeekNumber: function(d) {
		    // Copy date so don't modify original
		    d = new Date(+d);
		    d.setHours(0,0,0);
		    // Set to nearest Thursday: current date + 4 - current day number
		    // Make Sunday's day number 7
		    d.setDate(d.getDate() + 4 - (d.getDay()||7));
		    // Get first day of year
		    var yearStart = new Date(d.getFullYear(),0,1);
		    // Calculate full weeks to nearest Thursday
		    var weekNo = Math.ceil(( ( (d - yearStart) / 86400000) + 1)/7);
		    // Return array of year and week number
		    return [d.getFullYear(), weekNo];
		},
		isValidDate: function (value, userFormat) {
			var userFormat = userFormat || 'yyyy-mm-dd', // default format

			delimiter = /[^mdy]/.exec(userFormat)[0],
			theFormat = userFormat.split(delimiter),
			theDate = value.split(delimiter),

			isDate = function (date, format) {
				var m, d, y
				for (var i = 0, len = format.length; i < len; i++) {
					if (/m/.test(format[i])) m = date[i]
					if (/d/.test(format[i])) d = date[i]
					if (/y/.test(format[i])) y = date[i]
				}
				return (
					m > 0 && m < 13 &&
					y && y.length === 4 &&
					d > 0 && d <= (new Date(y, m, 0)).getDate()
				)
			}

			return isDate(theDate, theFormat)
		}
	},
	enti: {
		formatName: function(data){
			if(data==null) return '--';
			var nomb = data.nomb;
			//if(data.tipo_enti=='P'){
			if(data.apmat!=null){
				if(data.apmat==null||data.apmat=='') nomb = data.appat+', '+data.nomb;
				else nomb = data.appat+' '+data.apmat+', '+data.nomb;
			}
			return nomb;
		},
		relDoc: function(data){
			if(data==null) return false;
			var docs = [];
			for(var i=0,j=data.docident.length; i<j; i++){
				switch(data.docident[i].tipo){
					case 'DNI': docs.push({cod:"DNI",nomb:"Documento Nacional de Identidad",key: '1',val: data.docident[i].num}); break;
					case 'RUC': docs.push({cod:"RUC",nomb:"Registro Único de Contribuyentes",key: '6',val: data.docident[i].num}); break;
					case 'CE': docs.push({cod:"CE",nomb:"Carnet de Extranjeria",key: '4',val: data.docident[i].num}); break;
					case 'OT': docs.push({cod:"OT",nomb:"Otros tipos de Documentos",key: '0',val: data.docident[i].num}); break;
					case 'PS': docs.push({cod:"PS",nomb:"Pasaporte",key: '7',val: data.docident[i].num}); break;
					case 'CD': docs.push({cod:"CD",nomb:"Cédula Diplomática de Identidad",key: 'A',val: data.docident[i].num}); break;
				}
			}
			return docs;
		},
		dbRel: function(data){
			if(data==null) return false;
			var enti = {
				_id: data._id.$id,
				nomb: data.nomb,
				tipo_enti: data.tipo_enti,
				n: data.nomb,
				fullname: data.nomb
			};
			if(data.tipo_enti=='P'){
				enti.appat = data.appat;
				enti.apmat = data.apmat;
				enti.n = data.nomb+' '+data.appat+' '+data.apmat;
				enti.fullname = data.nomb+' '+data.appat+' '+data.apmat;
			}
			if(data.docident!=null)
				enti.doc = data.docident[0].num;
			else if(enti.doc!=null)
				enti.doc = enti.doc;
			else
				enti.doc = '';
			return enti;
		},
		dbTrabRel: function(data){
			if(data==null) return false;
			var enti = {
				_id: data._id.$id,
				nomb: data.nomb,
				tipo_enti: data.tipo_enti,
				n: data.nomb,
				fullname: data.nomb
			};
			if(data.tipo_enti=='P'){
				enti.appat = data.appat;
				enti.apmat = data.apmat;
				enti.n = data.nomb+' '+data.appat+' '+data.apmat;
				enti.fullname = data.nomb+' '+data.appat+' '+data.apmat;
			}
			if(data.docident!=null)
				enti.doc = data.docident[0].num;
			else if(enti.doc!=null)
				enti.doc = enti.doc;
			else
				enti.doc = '';
			if(data.cargo!=null){
				enti.cargo = {
					organizacion: {
						_id: data.cargo.organizacion._id.$id,
						nomb: data.cargo.organizacion.nomb
					}
				};
			}
			if(data.roles!=null){
				if(data.roles.trabajador!=null){
					enti.cargo = {};
					if(data.roles.trabajador.programa!=null){
						enti.programa = {
							_id: data.roles.trabajador.programa._id.$id,
							nomb: data.roles.trabajador.programa.nomb
						}
					}
					if(data.roles.trabajador.cargo!=null){
						if(data.roles.trabajador.cargo._id!=null){
							enti.cargo._id = data.roles.trabajador.cargo._id.$id;
							enti.cargo.nomb = data.roles.trabajador.cargo.nomb;
						}
						if(data.roles.trabajador.cargo.funcion!=null){
							enti.cargo.funcion = data.roles.trabajador.cargo.funcion;
						}
						if(data.roles.trabajador.cargo.actividad!=null)
							enti.cargo.actividad = data.roles.trabajador.cargo.actividad;
						if(data.roles.trabajador.cargo.componente!=null)
							enti.cargo.componente = data.roles.trabajador.cargo.componente;
					}
				}
			}
			return enti;
		},
		dbTrabRelAll: function(data){
			if(data==null) return false;
			var enti = {
				_id: data._id.$id,
				nomb: data.nomb,
				tipo_enti: data.tipo_enti,
				fullname: data.nomb
			};
			if(data.tipo_enti=='P'){
				enti.appat = data.appat;
				enti.apmat = data.apmat;
				enti.fullname = data.nomb+' '+data.appat+' '+data.apmat;
			}
			enti.cargo = {
				organizacion: {}
			};
			if(data.roles.trabajador.cargo._id!=null){
				enti.cargo._id = data.roles.trabajador.cargo._id.$id;
				enti.cargo.nomb = data.roles.trabajador.cargo.nomb;
			}else{
				enti.cargo.funcion = data.roles.trabajador.cargo.funcion;
			}
			if(data.roles.trabajador.cargo.actividad!=null)
				enti.cargo.actividad = data.roles.trabajador.cargo.actividad;
			if(data.roles.trabajador.cargo.componente!=null)
				enti.cargo.componente = data.roles.trabajador.cargo.componente;
			if(data.roles.trabajador.nivel!=null){
				enti.nivel = {
					_id: data.roles.trabajador.nivel._id.$id,
					nomb: data.roles.trabajador.nivel.nomb,
					abrev: data.roles.trabajador.nivel.abrev,
					salario: data.roles.trabajador.nivel.salario,
					basica: data.roles.trabajador.nivel.basica,
					reunificada: data.roles.trabajador.nivel.reunificada,
					incentivo: data.roles.trabajador.nivel.incentivo
				};
			}
			if(data.roles.trabajador.contrato!=null){
				enti.contrato = {
					_id: data.roles.trabajador.contrato._id.$id,
					nomb: data.roles.trabajador.contrato.nomb,
					cod: data.roles.trabajador.contrato.cod
				};
			}
			if(data.roles.trabajador.pension!=null){
				enti.pension = {
					_id: data.roles.trabajador.pension._id.$id,
					nomb: data.roles.trabajador.pension.nomb,
					tipo: data.roles.trabajador.pension.tipo,
					porcentajes: data.roles.trabajador.pension.porcentajes
				};
			}
			if(data.roles.trabajador.nivel_carrera_carrera!=null){
				enti.nivel_carrera = {
					_id: data.roles.trabajador.nivel_carrera._id.$id,
					nomb: data.roles.trabajador.nivel_carrera.nomb,
					abrev: data.roles.trabajador.nivel_carrera.abrev,
					salario: data.roles.trabajador.nivel_carrera.salario,
					basica: data.roles.trabajador.nivel_carrera.basica,
					reunificada: data.roles.trabajador.nivel_carrera.reunificada,
					incentivo: data.roles.trabajador.nivel_carrera.incentivo
				};
			}
			if(data.roles.trabajador.cese!=null){
				enti.cese = {
					fec: ciHelper.dateFormatBDNotHour(data.roles.trabajador.cese.fec),
					motivo: data.roles.trabajador.cese.motivo,
					observ: data.roles.trabajador.cese.observ
				};
			}
			if(data.roles.trabajador.fecing!=null) enti.fecing = ciHelper.dateFormatBDNotHour(data.roles.trabajador.fecing);
			return enti;
		}
	},
	formatMon: function(n,mon,digits){
		mon = mon || 'S';
		n = parseFloat(n);
		mon = (mon=='S')?'S/.':'$';
		return mon+K.round(n,(digits!=null)?digits:2);
	},
	monto2string : {
		mod: function(dividendo,divisor){
			resDiv = dividendo / divisor ;  
			parteEnt = Math.floor(resDiv);          
			parteFrac = resDiv - parteEnt ;     
			modulo = Math.round(parteFrac * divisor);  
			return modulo; 
		},
		ObtenerParteEntDiv : function(dividendo , divisor){
			resDiv = dividendo / divisor ;  
			parteEntDiv = Math.floor(resDiv); 
			return parteEntDiv; 
		},
		fraction_part : function(dividendo , divisor){
			resDiv = dividendo / divisor ;  
			f_part = Math.floor(resDiv); 
			return f_part; 
		},
		string_literal_conversion : function(number){
			centenas = ciHelper.monto2string.ObtenerParteEntDiv(number, 100); 

			number = ciHelper.monto2string.mod(number, 100); 

			decenas = ciHelper.monto2string.ObtenerParteEntDiv(number, 10); 
			number = ciHelper.monto2string.mod(number, 10); 

			unidades = ciHelper.monto2string.ObtenerParteEntDiv(number, 1); 
			number = ciHelper.monto2string.mod(number, 1);  
			string_hundreds="";
			string_tens="";
			string_units="";
			if(centenas == 1){
			   string_hundreds = "ciento ";
			} 


			if(centenas == 2){
			   string_hundreds = "doscientos ";
			}
			 
			if(centenas == 3){
			   string_hundreds = "trescientos ";
			} 

			if(centenas == 4){
			   string_hundreds = "cuatrocientos ";
			} 

			if(centenas == 5){
			   string_hundreds = "quinientos ";
			} 

			if(centenas == 6){
			   string_hundreds = "seiscientos ";
			} 

			if(centenas == 7){
			   string_hundreds = "setecientos ";
			} 

			if(centenas == 8){
			   string_hundreds = "ochocientos ";
			} 

			if(centenas == 9){
			   string_hundreds = "novecientos ";
			} 
			if(decenas == 1){
			   if(unidades == 1){
			      string_tens = "once";
			   }
			   
			   if(unidades == 2){
			      string_tens = "doce";
			   }
			   
			   if(unidades == 3){
			      string_tens = "trece";
			   }
			   
			   if(unidades == 4){
			      string_tens = "catorce";
			   }
			   
			   if(unidades == 5){
			      string_tens = "quince";
			   }
			   
			   if(unidades == 6){
			      string_tens = "dieciseis";
			   }
			   
			   if(unidades == 7){
			      string_tens = "diecisiete";
			   }
			   
			   if(unidades == 8){
			      string_tens = "dieciocho";
			   }
			   
			   if(unidades == 9){
			      string_tens = "diecinueve";
			   }
			} 
			if(decenas == 2){
			   string_tens = "veinti";
			}
			if(decenas == 3){
			   string_tens = "treinta";
			}
			if(decenas == 4){
			   string_tens = "cuarenta";
			}
			if(decenas == 5){
			   string_tens = "cincuenta";
			}
			if(decenas == 6){
			   string_tens = "sesenta";
			}
			if(decenas == 7){
			   string_tens = "setenta";
			}
			if(decenas == 8){
			   string_tens = "ochenta";
			}
			if(decenas == 9){
			   string_tens = "noventa";
			}

			if (decenas == 1) 
			{ 
			   string_units=""; 
			} 
			else 
			{ 
			   if(unidades == 1){
			      string_units = "uno";
			   }
			   if(unidades == 2){
			      string_units = "dos";
			   }
			   if(unidades == 3){
			      string_units = "tres";
			   }
			   if(unidades == 4){
			      string_units = "cuatro";
			   }
			   if(unidades == 5){
			      string_units = "cinco";
			   }
			   if(unidades == 6){
			      string_units = "seis";
			   }
			   if(unidades == 7){
			      string_units = "siete";
			   }
			   if(unidades == 8){
			      string_units = "ocho";
			   }
			   if(unidades == 9){
			      string_units = "nueve";
			   }
			}
			if (centenas == 1 && decenas == 0 && unidades == 0) 
			{ 
			string_hundreds = "cien " ; 
			}  
			if (decenas == 1 && unidades ==0) 
			{ 
			string_tens = "diez " ; 
			} 
			if (decenas == 2 && unidades ==0) 
			{ 
			string_tens = "veinte " ; 
			} 
			if (decenas >=3 && unidades >=1) 
			{ 
			string_tens = string_tens+" y "; 
			} 
			final_string = string_hundreds+string_tens+string_units;


			return final_string ; 
		},
		covertirNumLetras : function(number){

			number1=number;
			cent = number1.split('.');   
			centavos = cent[1];

			if (centavos == 0 || centavos == undefined){
			centavos = "00";}

			if (number == 0 || number == "") 
			{ 
			   centenas_final_string=" cero "; 
			  
			} 
			else 
			{ 

			  millions  = ciHelper.monto2string.ObtenerParteEntDiv(number, 1000000); 
			   number = ciHelper.monto2string.mod(number, 1000000);       
			   
			  if (millions != 0)
			   {                      
			   
			      if (millions == 1) 
			      {            
			         descriptor= " millon ";  
			         } 
			      else 
			      {                       
			           descriptor = " millones "; 
			         } 
			   } 
			   else 
			   {    
			      descriptor = " ";              
			   } 
			   millions_final_string = ciHelper.monto2string.string_literal_conversion(millions)+descriptor; 
			       
			   
			   thousands = ciHelper.monto2string.ObtenerParteEntDiv(number, 1000); 
			     number = ciHelper.monto2string.mod(number, 1000);           
			  if (thousands != 1) 
			   {                   
			      thousands_final_string =ciHelper.monto2string.string_literal_conversion(thousands) + " mil "; 
			   } 
			   if (thousands == 1)
			   {
			      thousands_final_string = " mil "; 
			  }
			   if (thousands < 1) 
			   { 
			      thousands_final_string = " "; 
			   } 
			  centenas  = number;                     
			   centenas_final_string = ciHelper.monto2string.string_literal_conversion(centenas) ; 
			   
			}
			cad = millions_final_string+thousands_final_string+centenas_final_string; 
			cad = cad.toUpperCase();       
			if (centavos.length>2)
			{   
			   if(centavos.substring(2,3)>= 5){
			      centavos = centavos.substring(0,1)+(parseInt(centavos.substring(1,2))+1).toString();
			   }   else{
			     centavos = centavos.substring(0,2);
			    }
			}
			if (centavos.length==1)
			{
			   centavos = centavos+"0";
			}
			centavos = centavos+ "/100"; 
			return ""+cad+' CON '+centavos+"";
		}
	},
	codigos :function(n, length){
		n = n.toString();
		   while(n.length < length) n = "0" + n;
		   return n;
	},
	sum : function(arr){
	        var r = 0;
	        $.each(arr,function(i,v){
	            r += parseFloat(v);
	        });
	        return r;
	},
	strlen: function (string) {
		  // http://kevin.vanzonneveld.net
		  // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
		  // +   improved by: Sakimori
		  // +      input by: Kirk Strobeck
		  // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
		  // +   bugfixed by: Onno Marsman
		  // +    revised by: Brett Zamir (http://brett-zamir.me)
		  // %        note 1: May look like overkill, but in order to be truly faithful to handling all Unicode
		  // %        note 1: characters and to this function in PHP which does not count the number of bytes
		  // %        note 1: but counts the number of characters, something like this is really necessary.
		  // *     example 1: strlen('Kevin van Zonneveld');
		  // *     returns 1: 19
		  // *     example 2: strlen('A\ud87e\udc04Z');
		  // *     returns 2: 3
		  var str = string + '';
		  var i = 0,
		    chr = '',
		    lgth = 0;

		  if (!this.php_js || !this.php_js.ini || !this.php_js.ini['unicode.semantics'] || this.php_js.ini['unicode.semantics'].local_value.toLowerCase() !== 'on') {
		    return string.length;
		  }

		  var getWholeChar = function (str, i) {
		    var code = str.charCodeAt(i);
		    var next = '',
		      prev = '';
		    if (0xD800 <= code && code <= 0xDBFF) { // High surrogate (could change last hex to 0xDB7F to treat high private surrogates as single characters)
		      if (str.length <= (i + 1)) {
		        throw 'High surrogate without following low surrogate';
		      }
		      next = str.charCodeAt(i + 1);
		      if (0xDC00 > next || next > 0xDFFF) {
		        throw 'High surrogate without following low surrogate';
		      }
		      return str.charAt(i) + str.charAt(i + 1);
		    } else if (0xDC00 <= code && code <= 0xDFFF) { // Low surrogate
		      if (i === 0) {
		        throw 'Low surrogate without preceding high surrogate';
		      }
		      prev = str.charCodeAt(i - 1);
		      if (0xD800 > prev || prev > 0xDBFF) { //(could change last hex to 0xDB7F to treat high private surrogates as single characters)
		        throw 'Low surrogate without preceding high surrogate';
		      }
		      return false; // We can pass over low surrogates now as the second component in a pair which we have already processed
		    }
		    return str.charAt(i);
		  };

		  for (i = 0, lgth = 0; i < str.length; i++) {
		    if ((chr = getWholeChar(str, i)) === false) {
		      continue;
		    } // Adapt this line at the top of any loop, passing in the whole string and the current iteration and returning a variable to represent the individual character; purpose is to treat the first part of a surrogate pair as the whole character and then ignore the second part
		    lgth++;
		  }
		  return lgth;
	},
	confirm: function (dialogText, okFunc, cancelFunc, dialogTitle) {
		K.Modal({
			id: 'windowconfir',
			title: dialogTitle,
			icon: 'fa fa-pencil',
			content: '<div class="icon warning pulseWarning" style="display: block;text-align: center;">'+
					'<button class="btn btn-warning" style="cursor:default;"><i class="fa fa-info-circle fa-5x"></i></button>'+
				'</div>'+
				'<p class="lead text-muted" style="display: block;">'+dialogText+'</p>'+
				'<p style="text-align: center;"><button name="btnNo" tabindex="2" class="cancel btn btn-danger btn-lg" style="display: inline-block;">No</button>'+
				'<button name="btnSi" tabindex="1" class="confirm btn btn-lg btn-success">Si</button></p>',
			width: 500,
			height: 270,
			noButtons: true,
			onContentLoaded: function(){
				$('#windowconfir [name=btnSi]').click(function(){
					if (typeof (okFunc) == 'function') {
		    			setTimeout(okFunc, 50);
		    		}
		    		$('#windowconfir').modal('hide');
				});
				$('#windowconfir [name=btnNo]').click(function(){
					if (typeof (cancelFunc) == 'function') {
		    			setTimeout(cancelFunc, 50);
		    		}
		    		$('#windowconfir').modal('hide');
				});
			}
		});
	},
	createCuota: function(fec){
		// fec: fecha de vencimiento del alquiler yyyy-mm-dd
		// plazo: plazo en dias para calcular vencimiento de letra 5 dias despues
		fec = moment(fec);
		var addDays = 0;
		switch(fec.day()){
		case 0://Domingo
			addDays = 5;
			break;
		case 1://Lunes
			addDays = 7;
			break;
		case 2://Martes
			addDays = 7;
			break;
		case 3://Miercoles
			addDays = 7;
			break;
		case 4://Jueves
			addDays = 7;
			break;
		case 5://Viernes
			addDays = 7;
			break;
		case 6://Sabado
			addDays = 6;
			break;
		}				
		fec.add('days',addDays);
		return fec.format("YYYY-MM-DD");
	},
	createFecProt: function(fec){
		// fec: fecha de vencimiento del alquiler yyyy-mm-dd
		// plazo: 9 del mes siguiente a la fecha de venc de pago
		var orig = fec;
		fec = moment(fec);	
		fec.add('months',1);
		fec.startOf('month');
		if(parseFloat(moment(orig).format("D"))>20){
			fec.add('days',14);
			//console.log("added");
		}
		//console.log(moment(orig).format("DD/MM/YYYY"));
		fec.add('days',8);
		var addDays = 0;
		switch(fec.day()){
		case 0://Domingo
			addDays = 1;
			break;
		case 6://Sabado
			addDays = 2;
			break;
		}
		fec.add('days',addDays);
		return fec.format("YYYY-MM-DD");
	},
	roundToDec: function(value) {
    var converted = parseFloat(value);
    var decimal = (converted - parseInt(converted, 10));
    decimal = Math.floor(decimal * 10);
    if (decimal >= 5) {
      return (parseInt(converted, 10)+0.5);
    }else{
      return Math.round(converted);
    }
  },
  datepicker :function(p){
		if(p==null) p = {};
		if(p.format==null) p.format = 'YYYY-MM-DD';
		if(p.date==null) p.date = ciHelper.date.get.now_ymd();
		new K.Modal({
			id: 'windowDatepicker',
			title: 'Seleccionar Fecha',
			width: 300,
			height: 60,
			buttons: {
				"Seleccionar": {
					icon: 'fa-check',
					type: 'success',
					f: function(){
						p.callback(p.$w.find('#picker input').val());
						K.closeWindow(p.$w.attr('id'));
					}
				},
				"Cerrar": {
					icon: 'fa-close',
					type: 'danger',
					f: function(){
						K.closeWindow(p.$w.attr('id'));
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowDatepicker');
				p.$c = $('#'+p.$w.attr('id')+'-body');
				//p.$c.append('<input type="text" id="picker" data-date="'+p.date+'" data-date-format="yyyy-mm-dd"/>');
				p.$c.append('<div data-date="'+p.date+'" data-date-format="yyyy-mm-dd" id="picker" class="input-append date">'+
					'<input type="text" readonly="readonly" value="'+p.date+'" size="16" class="span2">'+
					'<span class="add-on"><i class="fa fa-calendar"></i></span>'+
				  '</div>');
				p.$c.find('#picker').datepicker({language:'es'});
			}
		});
	},
  selectIniFin: function(p){
		new K.Modal({
			id: 'windowIniFin',
			title: 'Seleccionar un Inicio y Fin',
			content:'<div class="form-horizontal">'+
					'<div class="input-group">'+
						'<label class="input-group-addon">Inicio</label>'+
						'&nbsp;<span style="color:green;" name="ini" class="form-control"></span>'+
						'<span class="input-group-btn">'+
							'<button class="btn btn-info" type="button" name="btnIni"><i class="fa fa-calendar"></i></button>'+
						'</span>'+
					'</div><br />'+
					'<div class="input-group">'+
						'<label class="input-group-addon">Fin</label>'+
						'&nbsp;<span style="color:green;" name="fin" class="form-control"></span>'+
						'<span class="input-group-btn">'+
							'<button class="btn btn-info" type="button" name="btnFin"><i class="fa fa-calendar"></i></button>'+
						'</span>'+
					'</div>'+
				'</div>',
			width: 300,
			height: 80,
			buttons: {
				"Seleccionar": {
					type: 'success',
					icon: 'fa-save',
					f: function(pa){
						K.clearNoti();
						var inicio = p.$w.find('[name=ini]').html(),
						fin = p.$w.find('[name=fin]').html();
						if(inicio==''){
							K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe elegir una fecha de inicio!',
								type: 'error'
							});
							return p.$w.find('[name=btnIni]').click();
						}
						if(fin==''){
							K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe elegir una fecha de fin!',
								type: 'error'
							});
							return p.$w.find('[name=btnFin]').click();
						}
						p.callback(inicio,fin);
						//pa.close();
						K.closeWindow(p.$w.attr('id'));
					}
				},
				"Cancelar": {
					type: 'danger',
					icon: 'fa-ban',
					f: function(pa){
						//pa.close();
						K.closeWindow(p.$w.attr('id'));
					}
				}
			},
			close: function(){
				K.closeWindow(p.$w.attr('id'));
				p = null;
			},
			onContentLoaded: function(){
				p.$w = $('#windowIniFin');
				p.$w.find('[name=btnIni]').click(function(e){
					e.preventDefault();
					ciHelper.datepicker({
						callback: function(data){
							p.$w.find('[name=ini]').html(data);
						}
					});
				});
				p.$w.find('[name=btnFin]').click(function(e){
					e.preventDefault();
					ciHelper.datepicker({
						callback: function(data){
							p.$w.find('[name=fin]').html(data);
						}
					});
				});
				if(p.ini!=null)
					p.$w.find('[name=ini]').html(p.ini);
				if(p.fin!=null)
					p.$w.find('[name=fin]').html(p.fin);
			}
		});
	},
	windowCumple: function(){
		var lock = false;
		if(K.session.cump!=null){
			if(K.session.cump.length>0){
				lock = true;
			}
		}
		if(lock==false){
			K.notification({
				text: 'No hay ning&uacute;n cumplea&ntilde;ero para hoy.'
			});
		}else{
			new K.Modal({
				id: 'window',
				title: 'Feliz Cumplea&ntilde;os!',
				content: 'feliz cumple',
				allScreen: true,
				onContentLoaded: function(){
					var $w = $('#window');
					K.incomplete();
				}
			});
		}
	},
	validator: function($form, opts, extend){
		/*var controls = p.$w.find('[required]');
		var errors = [];
		if(controls.length>0){
			p.$w.find('.has-error').removeClass('has-error');
			p.$w.find('.help-block').remove();
			for(var i=0;i<controls.length;i++){
				var item_control = controls.eq(i);
				var type_control = item_control.prop('type');
				var name_control = item_control.attr('name');
				var error_message = '';
				var is_error = true;
				var action = '--';
				switch(type_control){
					case "text":
						action = 'ingresar';
						if(item_control.val()==''){
							error_message = 'Este campo es requerido';
							is_error = false;
						}
						break;
					case "textarea":
						action = 'ingresar';
						if(item_control.val()==''){
							error_message = 'Este campo es requerido';
							is_error = false;
						}
						break;
					case "select-one":
						action = 'seleccionar';
						if(item_control.val()==''||item_control.val()==null){
							error_message = 'Este campo es requerido';
							is_error = false;
						}
						break;
				}
				if(!is_error){
					item_control.closest('.form-group').addClass('has-error');
					item_control.after('<p class="help-block">'+error_message+'</p>');
					errors.push({
						name: item_control.attr('name'),
						message: error_message
					});
				}
			}
			p.$w.find('#content_errors').empty();
			if(errors.length>0){
				var $content_errors = $('<div class="alert alert-danger" />');
				$content_errors.append('Se han encontrado omisiones y/o errores en el formulario, resuelvalos para continuar.');
				p.$w.find('#content_errors').append($content_errors);
			}
		}
		return errors;*/
		if($form.data('validator')==null){
			var options = $.extend({
				
			},extend);
			$form.validate(options);
		}
		$form.data("validator").settings.submitHandler = function(){
			if(opts.onSuccess!=null){
				opts.onSuccess();
			}
		};
		return $form;
	}
};
ciHelper.titles = ciHelper.titleMessages;
define(
	function(){
		return ciHelper;
	}
);