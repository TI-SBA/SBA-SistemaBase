navg = {
	init: function(){
		var $left = $('#sidebar-left');
		/*
		 * ACCIONES GENERALES
		 */
		$left.find('[name=menPerfil]').click(function(){
			require(['ac/perf'],function(menPerfil){
				menPerfil.init();
			});
		});
		$left.find('[name=menPass]').click(function(){
			require(['ac/perf'],function(menPerfil){
				menPerfil.newPass();
			});
		});
		$left.find('[name=da]').click(function(){
			require(['dashboard'],function(dashboard){
				dashboard.init();
			});
		});
		/*
		 * MAESTROS GENERALES
		 */
		$left.find('[name=mgTitu]').click(function(){
			require(['mg/titu'],function(mgTitu){
				mgTitu.init();
			});
		});
		$left.find('[name=mgOrga]').click(function(){
			require(['mg/orga'],function(mgOrga){
				mgOrga.init();
			});
		});
		$left.find('[name=mgOfic]').click(function(){
			require(['mg/ofic'],function(mgOfic){
				mgOfic.init();
			});
		});
		$left.find('[name=mgProg]').click(function(){
			require(['mg/prog'],function(mgProg){
				mgProg.init();
			});
		});
		$left.find('[name=mgVari]').click(function(){
			require(['mg/vari'],function(mgVari){
				mgVari.init();
			});
		});
		$left.find('[name=mgServ]').click(function(){
			require(['mg/serv'],function(mgServ){
				mgServ.init();
			});
		});
		$left.find('[name=mgEnti]').click(function(){
			require(['mg/enti'],function(mgEnti){
				mgEnti.init();
			});
		});
		$left.find('[name=mgMult]').click(function(){
			require(['mg/mult'],function(mgMult){
				mgMult.init();
			});
		});
		/*
		*	PORTERIA
		*/
		$left.find('[name=poVisi]').click(function(){
			require(['po/visi'],function(poVisi){
				poVisi.init();
			});
		});
		/*
		 * TRAMITE DOCUMENTARIO
		 */
		$left.find('[name=tdTdocs]').click(function(){
			require(['td/tdocs'],function(tdTdocs){
				tdTdocs.init();
			});
		});
		$left.find('[name=tdOrga]').click(function(){
			require(['td/orga'],function(tdOrga){
				tdOrga.init();
			});
		});
		$left.find('[name=tdComi]').click(function(){
			require(['td/comi'],function(tdComi){
				tdComi.init();
			});
		});
		$left.find('[name=tdTupa]').click(function(){
			require(['td/tupa'],function(tdTupa){
				tdTupa.init();
			});
		});
		$left.find('[name=tdExpd]').click(function(){
			$.cookie('mode','td');
			$.cookie('action','tdExpdReci');
			window.location.replace('?old=1');
		});
		/*
		 * CEMENTERIOS
		 */
		$left.find('[name=cmOper]').click(function(){
			$.cookie('mode','cm');
			$.cookie('action','cmOper');
			window.location.replace('?old=1');
		});
		$left.find('[name=cmHope]').click(function(){
			require(['cm/hope'],function(cmHope){
				cmHope.init();
			});
		});
		$left.find('[name=cmEspa]').click(function(){
			$.cookie('mode','cm');
			$.cookie('action','cmEspa');
			window.location.replace('?old=1');
		});

		$left.find('[name=cmOcup]').click(function(){
			$.cookie('action','cmOcup');
			window.location.replace('?old=1');
		});
		$left.find('[name=cmProp]').click(function(){
			$.cookie('action','cmProp');
			window.location.replace('?old=1');
		});

		$left.find('[name=cmAcce]').click(function(){
			require(['cm/acce'],function(cmAcce){
				cmAcce.init();
			});
		});
		$left.find('[name=cmPabe]').click(function(){
			require(['cm/pabe'],function(cmPabe){
				cmPabe.init();
			});
		});
		$left.find('[name=cmConf]').click(function(){
			require(['cm/conf'],function(cmConf){
				cmConf.init();
			});
		});
		$left.find('[name=cmRehi]').click(function(){
			require(['cm/rehi'],function(cmRehi){
				cmRehi.init();
			});
		});
		$left.find('[name=cmTerr]').click(function(){
			require(['cm/terr'],function(cmTerr){
				cmTerr.init();
			});
		});
		/*
		 * INMUEBLES
		 */
		 $left.find('[name=inAler]').click(function(){
 			require(['in/aler'],function(inAler){
 				inAler.init();
 			});
 		});
		$left.find('[name=inTipo]').click(function(){
			require(['in/tipo'],function(inTipo){
				inTipo.init();
			});
		});
		$left.find('[name=inSubl]').click(function(){
			require(['in/subl'],function(inSubl){
				inSubl.init();
			});
		});
		$left.find('[name=inInmu]').click(function(){
			require(['in/inmu'],function(inSubl){
				inSubl.init();
			});
		});
		$left.find('[name=inMoti]').click(function(){
			require(['in/moti'],function(inMoti){
				inMoti.init();
			});
		});
		$left.find('[name=inCalp]').click(function(){
			require(['in/calp'],function(inCalp){
				inCalp.init();
			});
		});
		$left.find('[name=inCalv]').click(function(){
			require(['in/calv'],function(inCalv){
				inCalv.init();
			});
		});
		$left.find('[name=inConf]').click(function(){
			require(['in/conf'],function(inConf){
				inConf.init();
			});
		});
		$left.find('[name=inMovi]').click(function(){
			require(['in/movi'],function(inMovi){
				inMovi.init();
			});
		});
		$left.find('[name=inCotr]').click(function(){
			require(['in/cotr'],function(inCotr){
				inCotr.init();
			});
		});
		$left.find('[name=inMarq]').click(function(){
			require(['in/marq'],function(inMarq){
				inMarq.init();
			});
		});
		$left.find('[name=inActa]').click(function(){
			require(['in/acta'],function(inActa){
				inActa.init();
			});
		});
		$left.find('[name=inPlay]').click(function(){
			require(['in/play'],function(inPlay){
				inPlay.init();
			});
		});
		$left.find('[name=inImpl]').click(function(){
			require(['in/impl'],function(inImpl){
				inImpl.init();
			});
		});
		$left.find('[name=inComp]').click(function(){
			require(['in/comp'],function(inComp){
				inComp.init();
			});
		});
		$left.find('[name=inRein]').click(function(){
			require(['in/rein'],function(inRein){
				inRein.init();
			});
		});
		$left.find('[name=inRepo]').click(function(){
			require(['in/repo'],function(inRepo){
				inRepo.init();
			});
		});
		/*
		 * LOGISTICA
		 */
		$left.find('[name=lgConf]').click(function(){
			require(['lg/conf'],function(lgConf){
				lgConf.init();
			});
		});
		$left.find('[name=lgAlma]').click(function(){
			require(['lg/alma'],function(lgAlma){
				lgAlma.init();
			});
		});
		$left.find('[name=lgProd]').click(function(){
			require(['lg/prod'],function(lgProd){
				lgProd.init();
			});
		});
		$left.find('[name=lgUnid]').click(function(){
			require(['lg/unid'],function(lgUnid){
				lgUnid.init();
			});
		});
		$left.find('[name=lgBien]').click(function(){
			require(['lg/bien'],function(lgBien){
				lgBien.init();
			});
		});
		$left.find('[name=lgCuen]').click(function(){
			require(['lg/cuen'],function(lgCuen){
				lgCuen.init();
			});
		});
		$left.find('[name=lgList]').click(function(){
			require(['lg/list'],function(lgList){
				lgList.init();
			});
		});
		$left.find('[name=lgAjus]').click(function(){
			require(['lg/ajus'],function(lgAjus){
				lgAjus.init();
			});
		});
		$left.find('[name=lgCuadPord]').click(function(){
			require(['lg/cuad','lg/cuadpord'],function(lgCuad,lgCuadPord){
				lgCuadPord.init();
			});
		});
		$left.find('[name=lgCuadToda]').click(function(){
			require(['lg/cuad','lg/cuadtoda'],function(lgCuad,lgCuadToda){
				lgCuadToda.init();
			});
		});
		$left.find('[name=lgPedi_nuev]').click(function(){
			require(['lg/pedi'],function(lgPedi){
				lgPedi.init_nuev();
			});
		});
		$left.find('[name=lgPedi_bien]').click(function(){
			require(['lg/pedi'],function(lgPedi){
				lgPedi.init_bien();
			});
		});
		$left.find('[name=lgPedi_serv]').click(function(){
			require(['lg/pedi'],function(lgPedi){
				lgPedi.init_serv();
			});
		});
		$left.find('[name=lgPedi_loca]').click(function(){
			require(['lg/pedi'],function(lgPedi){
				lgPedi.init_loca();
			});
		});
		$left.find('[name=lgPedi_todo]').click(function(){
			require(['lg/pedi'],function(lgPedi){
				lgPedi.init_todo();
			});
		});
		$left.find('[name=lgCoti]').click(function(){
			require(['lg/coti'],function(lgCoti){
				lgCoti.init();
			});
		});
		$left.find('[name=lgCert_nue]').click(function(){
			require(['lg/cert'],function(lgCert){
				lgCert.init_nue();
			});
		});
		$left.find('[name=lgCert_apr]').click(function(){
			require(['lg/cert'],function(lgCert){
				lgCert.init_apr();
			});
		});
		$left.find('[name=lgCert_env]').click(function(){
			require(['lg/cert'],function(lgCert){
				lgCert.init_env();
			});
		});
		$left.find('[name=lgCert_rec]').click(function(){
			require(['lg/cert'],function(lgCert){
				lgCert.init_rec();
			});
		});

		$left.find('[name=lgSoli_nue]').click(function(){
			require(['lg/soli'],function(lgCert){
				lgSoli.init_nue();
			});
		});

		$left.find('[name=lgSoli_env]').click(function(){
			require(['lg/soli'],function(lgCert){
				lgSoli.init_env();
			});
		});

		$left.find('[name=lgSoli_rec]').click(function(){
			require(['lg/soli'],function(lgCert){
				lgSoli.init_rec();
			});
		});
		$left.find('[name=lgSoli_apr]').click(function(){
			require(['lg/soli'],function(lgCert){
				lgSoli.init_apr();
			});
		});

		$left.find('[name=lgOrde_nue]').click(function(){
			require(['lg/orde'],function(lgOrde){
				lgOrde.init_nue();
			});
		});
		$left.find('[name=lgOrde_env]').click(function(){
			require(['lg/orde'],function(lgOrde){
				lgOrde.init_env();
			});
		});
		$left.find('[name=lgOrde_rec]').click(function(){
			require(['lg/orde'],function(lgOrde){
				lgOrde.init_rec();
			});
		});
		$left.find('[name=lgOrde_apr]').click(function(){
			require(['lg/orde'],function(lgOrde){
				lgOrde.init_apr();
			});
		});

		$left.find('[name=lgOrse_nue]').click(function(){
			require(['lg/orse'],function(lgOrse){
				lgOrse.init_nue();
			});
		});
		$left.find('[name=lgOrse_env]').click(function(){
			require(['lg/orse'],function(lgOrse){
				lgOrse.init_env();
			});
		});
		$left.find('[name=lgOrse_rec]').click(function(){
			require(['lg/orse'],function(lgOrse){
				lgOrse.init_rec();
			});
		});
		$left.find('[name=lgOrse_apr]').click(function(){
			require(['lg/orse'],function(lgOrse){
				lgOrse.init_apr();
			});
		});

		/*$left.find('[name=lgOrdn]').click(function(){
			require(['lg/orde'],function(lgOrde){
				lgOrde.init();
			});
		});
		$left.find('[name=lgOrdp]').click(function(){
			require(['lg/orde'],function(lgOrde){
				lgOrde.initPend();
			});
		});
		$left.find('[name=lgOrda]').click(function(){
			require(['lg/orde'],function(lgOrde){
				lgOrde.initApro();
			});
		});
		$left.find('[name=lgOrdt]').click(function(){
			require(['lg/orde'],function(lgOrde){
				lgOrde.initTodo();
			});
		});*/

		$left.find('[name=lgOrsn]').click(function(){
			require(['lg/orse'],function(lgOrse){
				lgOrse.init();
			});
		});
		$left.find('[name=lgOrsp]').click(function(){
			require(['lg/orse'],function(lgOrse){
				lgOrse.initPend();
			});
		});
		$left.find('[name=lgOrst]').click(function(){
			require(['lg/orse'],function(lgOrse){
				lgOrse.initTodo();
			});
		});



		$left.find('[name=lgOrse]').click(function(){
			require(['lg/orse'],function(lgOrse){
				lgOrse.init();
			});
		});

		$left.find('[name=lgNotn]').click(function(){
			require(['lg/nota'],function(lgNota){
				lgNota.init();
			});
		});
		$left.find('[name=lgNotp]').click(function(){
			require(['lg/nota'],function(lgNota){
				lgNota.initPend();
			});
		});
		$left.find('[name=lgNott]').click(function(){
			require(['lg/nota'],function(lgNota){
				lgNota.initTodo();
			});
		});

		$left.find('[name=lgPeca]').click(function(){
			require(['lg/peco'],function(lgPeco){
				lgPeco.init();
			});
		});
		$left.find('[name=lgPecl]').click(function(){
			require(['lg/peco'],function(lgPeco){
				lgPeco.initAlma();
			});
		});
		$left.find('[name=lgRepo]').click(function(){
			require(['lg/repo'],function(lgRepo){
				lgRepo.init();
			});
		});
		/*
		 * USA
		 */
		$left.find('[name=usCoci]').click(function(){
			require(['us/coci'],function(usCoci){
				usCoci.init();
			});
		});
		$left.find('[name=usUnid]').click(function(){
			require(['us/unid'],function(usUnid){
				usUnid.init();
			});
		});
		$left.find('[name=usIngr]').click(function(){
			require(['us/ingr'],function(usIngr){
				usIngr.init();
			});
		});
		$left.find('[name=usRece]').click(function(){
			require(['us/rece','us/ingr'],function(usRece,usIngr){
				usRece.init();
			});
		});
		$left.find('[name=usPedi]').click(function(){
			require(['us/pedi'],function(usPedi){
				usPedi.init();
			});
		});
		$left.find('[name=usRepe]').click(function(){
			require(['us/repe'],function(usRepe){
				usRepe.init();
			});
		});
		$left.find('[name=usProg]').click(function(){
			require(['us/prog'],function(usProg){
				usProg.init();
			});
		});
		$left.find('[name=usCons]').click(function(){
			require(['us/cons'],function(usCons){
				usCons.init();
			});
		});
		$left.find('[name=usRepo]').click(function(){
			require(['us/repo'],function(usRepo){
				usRepo.init();
			});
		});
		/*
		 * PERSONAL
		 */
		$left.find('[name=peGrup]').click(function(){
			require(['pe/grup'],function(peGrup){
				peGrup.init();
			});
		});
		$left.find('[name=peClas]').click(function(){
			require(['pe/clas'],function(peClas){
				peClas.init();
			});
		});
		$left.find('[name=peCarg]').click(function(){
			require(['pe/carg'],function(peCarg){
				peCarg.init();
			});
		});
		$left.find('[name=peNive]').click(function(){
			require(['pe/nive'],function(peNive){
				peNive.init();
			});
		});
		$left.find('[name=peTipo]').click(function(){
			require(['pe/tipo'],function(peTipo){
				peTipo.init();
			});
		});
		$left.find('[name=peSist]').click(function(){
			require(['pe/sist'],function(peSist){
				peSist.init();
			});
		});
		$left.find('[name=peCont]').click(function(){
			require(['pe/cont'],function(peCont){
				peCont.init();
			});
		});
		$left.find('[name=peConc]').click(function(){
			require(['pe/conc'],function(peConc){
				peConc.init();
			});
		});
		$left.find('[name=peTrab]').click(function(){
			require(['pe/trab'],function(peTrab){
				peTrab.init();
			});
		});
		$left.find('[name=peEqui]').click(function(){
			require(['pe/equi'],function(peEqui){
				peEqui.init();
			});
		});
		$left.find('[name=peTurn]').click(function(){
			require(['pe/turn'],function(peTurn){
				peTurn.init();
			});
		});
		$left.find('[name=peFeri]').click(function(){
			require(['pe/feri'],function(peFeri){
				peFeri.init();
			});
		});
		$left.find('[name=pePlan]').click(function(){
			require(['pe/plan'],function(pePlan){
				pePlan.init();
			});
		});
		$left.find('[name=peBole]').click(function(){
			require(['pe/bole'],function(peBole){
				peBole.init();
			});
		});
		$left.find('[name=peImas]').click(function(){
			require(['pe/imas'],function(peImas){
				peImas.init();
			});
		});
		$left.find('[name=peAsis]').click(function(){
			require(['pe/asis'],function(peAsis){
				peAsis.init();
			});
		});
		$left.find('[name=peHora]').click(function(){
			console.log("peCoasAsis");
			require(['pe/asis'],function(peAsis){
				peAsis.init();
			});
		});
		$left.find('[name=peConf]').click(function(){
			require(['pe/conf'],function(peConf){
				peConf.init();
			});
		});
		$left.find('[name=peRepo]').click(function(){
			//$.cookie('mode','pe');
			//$.cookie('action','peRepo');
			//window.location.replace('?old=1');
			require(['pe/repo'],function(peRepo){
				peRepo.init();
			});
		});
		/*
		 * TESORERIA
		 */
		$left.find('[name=tsCheq]').click(function(){
			require(['ts/cheq2'],function(tsCheq2){
				tsCheq2.init();
			});
		});
		$left.find('[name=tsRede]').click(function(){
			require(['ts/rede2'],function(tsRede2){
				tsRede2.init();
			});
		});
		$left.find('[name=tsReca]').click(function(){
			require(['ts/reca'],function(tsReca){
				tsReca.init();
			});
		});
		$left.find('[name=tsCjdo]').click(function(){
			require(['ts/cjdo'],function(tsCjdo){
				tsCjdo.init();
			});
		});
		$left.find('[name=tsCjse]').click(function(){
			require(['ts/cjse'],function(tsCjse){
				tsCjse.init();
			});
		});
		$left.find('[name=tsCupa]').click(function(){
			$.cookie('action','tsCtppPen');
			window.location.replace('?old=1');
		});
		$left.find('[name=tsComp]').click(function(){
			require(['ts/comp'],function(tsComp){
				tsComp.init();
			});
		});
		$left.find('[name=tsTipo]').click(function(){
			$.cookie('action','tsTipo');
			window.location.replace('?old=1');
		});
		$left.find('[name=tsRein]').click(function(){
			require(['ts/rein2'],function(tsRein2){
				tsRein2.init();
			});
		});
		$left.find('[name=tsLiba]').click(function(){
			require(['ts/liba'],function(tsLiba){
				tsLiba.init();
			});
		});
		$left.find('[name=tsRepo]').click(function(){
			require(['ts/repo'],function(tsRepo){
				tsRepo.init();
			});
		});
		$left.find('[name=tsLibo]').click(function(){
			require(['ts/libo'],function(tsLibo){
				tsLibo.init();
			});
		});
		$left.find('[name=tsCjch]').click(function(){
			$.cookie('action','tsCjch');
			window.location.replace('?old=1');
		});
		$left.find('[name=tsConc]').click(function(){
			$.cookie('action','tsConc');
			window.location.replace('?old=1');
		});
		$left.find('[name=tsCtban]').click(function(){
			$.cookie('action','tsCtban');
			window.location.replace('?old=1');
		});
		/*
		 * ASESORIA LEGAL
		 */
		$left.find('[name=alExpdActi]').click(function(){
			$.cookie('action','alExpdActi');
			window.location.replace('?old=1');
		});
		$left.find('[name=alExpdArch]').click(function(){
			$.cookie('action','alExpdArch');
			window.location.replace('?old=1');
		});
		$left.find('[name=alContFav]').click(function(){
			$.cookie('action','alContFav');
			window.location.replace('?old=1');
		});
		$left.find('[name=alContCont]').click(function(){
			$.cookie('action','alContCont');
			window.location.replace('?old=1');
		});
		$left.find('[name=alDiliProg]').click(function(){
			$.cookie('action','alDiliProg');
			window.location.replace('?old=1');
		});
		$left.find('[name=alDiliEjec]').click(function(){
			$.cookie('action','alDiliEjec');
			window.location.replace('?old=1');
		});
		$left.find('[name=alDiliSusp]').click(function(){
			$.cookie('action','alDiliSusp');
			window.location.replace('?old=1');
		});
		$left.find('[name=alConv]').click(function(){
			//$.cookie('action','alConv');
			//window.location.replace('?old=1');
			require(['al/conv'],function(alConv){
				alConv.init();
			});
		});
		$left.find('[name=alRepo]').click(function(){
			$.cookie('action','alRepo');
			window.location.replace('?old=1');
		});
		/*HOSPITALIZACION ADICCIONES*/
		$left.find('[name=haConf]').click(function(){
			require(['ha/conf'],function(haConf){
				haConf.init();
			});
		});
		$left.find('[name=haTari]').click(function(){
			require(['ha/tari'],function(haTari){
				haTari.init();
			});
		});
		$left.find('[name=haTara]').click(function(){
			require(['ha/tara'],function(haTara){
				haTara.init();
			});
		});
		$left.find('[name=haTarg]').click(function(){
			require(['ha/targ'],function(haTarg){
				haTarg.init();
			});
		});
		$left.find('[name=haCont]').click(function(){
			require(['ha/cont'],function(haCont){
				haCont.init();
			});
		});
		$left.find('[name=haPend]').click(function(){
			require(['ha/pend'],function(haPend){
				haPend.init();
			});
		});
		$left.find('[name=haAlta]').click(function(){
			require(['ha/alta'],function(haAlta){
				haAlta.init();
			});
		});
		$left.find('[name=haHosp]').click(function(){
			require(['ha/hosp'],function(haHosp){
				haHosp.init();
			});
		});
		$left.find('[name=haReci]').click(function(){
			require(['ha/reci'],function(haReci){
				haReci.init();
			});
		});
		$left.find('[name=haRein]').click(function(){
			require(['ha/rein'],function(haRein){
				haRein.init();
			});
		});
		$left.find('[name=haRepo]').click(function(){
			require(['ha/repo'],function(haRepo){
				haRepo.init();
			});
		});
		/*
		 * HOSPITALIZACION
		 */
		$left.find('[name=hoConf]').click(function(){
			require(['ho/conf'],function(hoConf){
				hoConf.init();
			});
		});
		$left.find('[name=hoTari]').click(function(){
			require(['ho/tari'],function(hoTari){
				hoTari.init();
			});
		});
		$left.find('[name=hoTara]').click(function(){
			require(['ho/tara'],function(hoTara){
				hoTara.init();
			});
		});
		$left.find('[name=hoTarg]').click(function(){
			require(['ho/targ'],function(hoTarg){
				hoTarg.init();
			});
		});
		$left.find('[name=hoCont]').click(function(){
			require(['ho/cont'],function(hoCont){
				hoCont.init();
			});
		});
		$left.find('[name=hoPend]').click(function(){
			require(['ho/pend'],function(hoPend){
				hoPend.init();
			});
		});
		$left.find('[name=hoAlta]').click(function(){
			require(['ho/alta'],function(hoAlta){
				hoAlta.init();
			});
		});
		$left.find('[name=hoHosp]').click(function(){
			require(['ho/hosp'],function(hoHosp){
				hoHosp.init();
			});
		});
		$left.find('[name=hoReci]').click(function(){
			require(['ho/reci'],function(hoReci){
				hoReci.init();
			});
		});
		$left.find('[name=hoRein]').click(function(){
			require(['ho/rein'],function(hoRein){
				hoRein.init();
			});
		});
		$left.find('[name=hoRepo]').click(function(){
			require(['ho/repo'],function(hoRepo){
				hoRepo.init();
			});
		});
		/*HOSPITALIZACION ADICCIONES*/
		$left.find('[name=haConf]').click(function(){
			require(['ha/conf'],function(haConf){
				haConf.init();
			});
		});
		$left.find('[name=haTari]').click(function(){
			require(['ha/tari'],function(haTari){
				haTari.init();
			});
		});
		$left.find('[name=haTara]').click(function(){
			require(['ha/tara'],function(haTara){
				haTara.init();
			});
		});
		$left.find('[name=haTarg]').click(function(){
			require(['ha/targ'],function(haTarg){
				haTarg.init();
			});
		});
		$left.find('[name=haCont]').click(function(){
			require(['ha/cont'],function(haCont){
				haCont.init();
			});
		});
		$left.find('[name=haPend]').click(function(){
			require(['ha/pend'],function(haPend){
				haPend.init();
			});
		});
		$left.find('[name=haAlta]').click(function(){
			require(['ha/alta'],function(haAlta){
				haAlta.init();
			});
		});
		$left.find('[name=haHosp]').click(function(){
			require(['ha/hasp'],function(haHosp){
				haHosp.init();
			});
		});
		$left.find('[name=haReci]').click(function(){
			require(['ha/reci'],function(haReci){
				haReci.init();
			});
		});
		$left.find('[name=haRein]').click(function(){
			require(['ha/rein'],function(haRein){
				haRein.init();
			});
		});
		$left.find('[name=haRepo]').click(function(){
			require(['ha/repo'],function(haRepo){
				haRepo.init();
			});
		});
		/*--------------------------*/
		/*
		 * FARMACIA
		 */
		$left.find('[name=faConf]').click(function(){
			require(['fa/conf'],function(faConf){
				faConf.init();
			});
		});
		$left.find('[name=faProd]').click(function(){
			require(['fa/prod'],function(faProd){
				faProd.init();
			});
		});
		$left.find('[name=faGuia]').click(function(){
			require(['fa/guia'],function(faGuia){
				faGuia.init();
			});
		});
		$left.find('[name=faLote]').click(function(){
			require(['fa/lote'],function(faLote){
				faLote.init();
			});
		});
		$left.find('[name=faVent]').click(function(){
			require(['fa/vent'],function(faVent){
				faVent.init();
			});
		});
		$left.find('[name=faComp]').click(function(){
			require(['fa/comp'],function(faComp){
				faComp.init();
			});
		});
		$left.find('[name=faRein]').click(function(){
			require(['fa/rein'],function(faRein){
				faRein.init();
			});
		});
		$left.find('[name=faRepo]').click(function(){
			require(['fa/repo'],function(faRepo){
				faRepo.init();
			});
		});
		/*
		 * AGUA CHAPI
		 */
		$left.find('[name=agConf]').click(function(){
			require(['ag/conf'],function(agConf){
				agConf.init();
			});
		});
		$left.find('[name=agProd]').click(function(){
			require(['ag/prod'],function(agProd){
				agProd.init();
			});
		});
		$left.find('[name=agGuia]').click(function(){
			require(['ag/guia'],function(agGuia){
				agGuia.init();
			});
		});
		$left.find('[name=agLote]').click(function(){
			require(['ag/lote'],function(agLote){
				agLote.init();
			});
		});
		$left.find('[name=agVent]').click(function(){
			require(['ag/vent'],function(agVent){
				agVent.init();
			});
		});
		$left.find('[name=agComp]').click(function(){
			require(['ag/comp'],function(agComp){
				agComp.init();
			});
		});
		$left.find('[name=agRein]').click(function(){
			require(['ag/rein'],function(agRein){
				agRein.init();
			});
		});
		$left.find('[name=agRepo]').click(function(){
			require(['ag/repo'],function(agRepo){
				agRepo.init();
			});
		});
		/**
		 * RECURSOS ECONOMICOS
		 */
		$left.find('[name=reRepo]').click(function(){
			require(['re/repo'],function(reRepo){
				reRepo.init();
			});
		});
		$left.find('[name=rePosc]').click(function(){
			require(['re/posc'],function(rePosc){
				rePosc.init();
			});
		});
		$left.find('[name=reDash]').click(function(){
			require(['re/dash'],function(reDash){
				reDash.init();
			});
		});
		/**
		 * INFORMATICA
		 */
		$left.find('[name=tiComp]').click(function(){
			require(['ti/comp'],function(tiComp){
				tiComp.init();
			});
		});
		$left.find('[name=tiErro]').click(function(){
			require(['ti/erro'],function(tiErro){
				tiErro.init();
			});
		});
		$left.find('[name=tiBack]').click(function(){
			require(['ti/back'],function(tiBack){
				tiBack.init();
			});
		});
		$left.find('[name=tiEdit]').click(function(){
			require(['ti/edit'],function(tiEdit){
				tiEdit.init();
			});
		});
		$left.find('[name=tiDash]').click(function(){
			require(['ti/dash'],function(tiDash){
				tiDash.init();
			});
		});
		/*
		 * GESTION DE PROYECTOS
		 */
		$left.find('[name=ge]').click(function(){
			require(['ge/proy'],function(geProy){
				geProy.init();
			});
		});
		/*
		 * ARCHIVO DIGITAL
		 */
		$left.find('[name=arDocu]').click(function(){
			require(['ar/docu'],function(arDocu){
				arDocu.init();
			});
		});
		/*
		 * SEGURIDAD
		 */
		$left.find('[name=acLogs]').click(function(){
			require(['ac/logs'],function(acLogs){
				acLogs.init();
			});
		});
		$left.find('[name=acLogp]').click(function(){
			require(['ac/logp'],function(acLogp){
				acLogp.init();
			});
		});
		$left.find('[name=acUser]').click(function(){
			require(['ac/user'],function(acUser){
				acUser.init();
			});
		});
		$left.find('[name=acGrup]').click(function(){
			require(['ac/grup'],function(acGrup){
				acGrup.init();
			});
		});
		$left.find('[name=acRepo]').click(function(){
			require(['ac/repo'],function(acRepo){
				acRepo.init();
			});
		});

		/*Moises Heresi*/
		$left.find('[name=mhPaci]').click(function(){
			require(['mh/paci'],function(mhPaci){
				mhPaci.init();
			});

		});
		/*
		Por ejemplo estos son los campos de todo moises heresi, aca supuetsamente se enlaza con el NavgInspinia
		*/
		$left.find('[name=mhHist]').click(function(){
			require(['mh/hist'],function(mhHist){
				mhHist.init();
			});

		});
		$left.find('[name=mhDini]').click(function(){
			require(['mh/dini'],function(mhDini){
				mhDini.init();
			});

		});
		$left.find('[name=mhSocial]').click(function(){
			require(['mh/social'],function(mhSocial){
				mhSocial.init();
			});

		});
		$left.find('[name=mhMedi]').click(function(){
			require(['mh/medi'],function(mhMedi){
				mhMedi.init();
			});

		});
		$left.find('[name=mhPsic]').click(function(){
			require(['mh/psic'],function(mhPsic){
				mhPsic.init();
			});

		});
		$left.find('[name=mhPsiq]').click(function(){
			require(['mh/psiq'],function(mhPsiq){
				mhPsiq.init();
			});

		});
		$left.find('[name=mhHospi]').click(function(){
			require(['mh/hospi'],function(mhHospi){
				mhHospi.init();
			});

		});
		$left.find('[name=mhPadi]').click(function(){
			require(['mh/padi'],function(mhPadi){
				mhPadi.init();
			});

		});
		$left.find('[name=mhPasi]').click(function(){
			require(['mh/pasi'],function(mhPasi){
				mhPasi.init();
			});

		});
		$left.find('[name=mhCons]').click(function(){
			require(['mh/cons'],function(mhCons){
				mhCons.init();
			});

		});
			$left.find('[name=mhEvol]').click(function(){
			require(['mh/evol'],function(mhEvol){
				mhEvol.init();
			});

		});
		$left.find('[name=mhChar]').click(function(){
			require(['mh/char'],function(mhChar){
				mhChar.init();
			});

		});

		$left.find('[name=mhDoct]').click(function(){
			require(['mh/doct'],function(mhDoct){
				mhDoct.init();
			});
		});
		
		$left.find('[name=mhCama]').click(function(){
			require(['mh/cama'],function(mhCama){
				mhCama.init();
			});
		});
		$left.find('[name=mhCmmo]').click(function(){
			require(['mh/cmmo'],function(mhCmmo){
				mhCmmo.init();
			});
		});
		
		$left.find('[name=mhRepo]').click(function(){
			require(['mh/repo'],function(mhRepo){
				mhRepo.init();
			});
		});
		$left.find('[name=mhDash]').click(function(){
			require(['mh/dash'],function(mhDash){
				mhDash.init();
			});
		});
		$left.find('[name=mhPajo]').click(function(){
			require(['mh/pajo'],function(mhPajo){
				mhPajo.init();
			});
		});
    $left.find('[name=mhPaho]').click(function(){
			require(['mh/paho'],function(mhPaho){
				mhPaho.init();
			});
		});
		
		$left.find('[name=mhCapu]').click(function(){
			require(['mh/capu'],function(mhCapu){
				mhCapu.init();
			});
		});
		/*-----------ADICCIONES-----------*/
		/*Moises Heresi*/
		$left.find('[name=adPaci]').click(function(){
			require(['ad/paci'],function(adPaci){
				adPaci.init();
			});

		});
		$left.find('[name=adHist]').click(function(){
			require(['ad/hist'],function(adHist){
				adHist.init();
			});

		});
		$left.find('[name=adDini]').click(function(){
			require(['ad/dini'],function(adDini){
				adDini.init();
			});

		});
		$left.find('[name=adSocial]').click(function(){
			require(['ad/social'],function(adSocial){
				adSocial.init();
			});

		});
		$left.find('[name=adMedi]').click(function(){
			require(['ad/medi'],function(adMedi){
				adMedi.init();
			});

		});
		$left.find('[name=adPsic]').click(function(){
			require(['ad/psic'],function(adPsic){
				adPsic.init();
			});

		});
		$left.find('[name=adPsiq]').click(function(){
			require(['ad/psiq'],function(adPsiq){
				adPsiq.init();
			});

		});
		$left.find('[name=adHospi]').click(function(){
			require(['ad/hospi'],function(adHospi){
				adHospi.init();
			});

		});
		$left.find('[name=adPadi]').click(function(){
			require(['ad/padi'],function(adPadi){
				adPadi.init();
			});

		});
		$left.find('[name=adPasi]').click(function(){
			require(['ad/pasi'],function(adPasi){
				adPasi.init();
			});

		});
		$left.find('[name=adCons]').click(function(){
			require(['ad/cons'],function(adCons){
				adCons.init();
			});

		});
			$left.find('[name=adEvol]').click(function(){
			require(['ad/evol'],function(adEvol){
				adEvol.init();
			});

		});
		$left.find('[name=adChar]').click(function(){
			require(['ad/char'],function(adChar){
				adChar.init();
			});

		});

		$left.find('[name=adDoct]').click(function(){
			require(['ad/doct'],function(adDoct){
				adDoct.init();
			});
		});
		
		$left.find('[name=adCama]').click(function(){
			require(['ad/cama'],function(adCama){
				adCama.init();
			});
		});
		$left.find('[name=adCmmo]').click(function(){
			require(['ad/cmmo'],function(adCmmo){
				adCmmo.init();
			});
		});
		
		$left.find('[name=adRepo]').click(function(){
			require(['ad/repo'],function(adRepo){
				adRepo.init();
			});
		});
		$left.find('[name=adPaho]').click(function(){
			require(['ad/paho'],function(adPaho){
				adPaho.init();
			});
		});
		/*-----------CHILPINILLA--------*/
		/*Moises Heresi*/
		$left.find('[name=chPaci]').click(function(){
			require(['ch/paci'],function(chPaci){
				chPaci.init();
			});

		});
		$left.find('[name=chHist]').click(function(){
			require(['ch/hist'],function(chHist){
				chHist.init();
			});

		});
		$left.find('[name=chDini]').click(function(){
			require(['ch/dini'],function(chDini){
				chDini.init();
			});

		});
		$left.find('[name=chSocial]').click(function(){
			require(['ch/social'],function(chSocial){
				chSocial.init();
			});

		});
		$left.find('[name=chMedi]').click(function(){
			require(['ch/medi'],function(chMedi){
				chMedi.init();
			});

		});
		$left.find('[name=chPsic]').click(function(){
			require(['ch/psic'],function(chPsic){
				chPsic.init();
			});

		});
		$left.find('[name=chPsiq]').click(function(){
			require(['ch/psiq'],function(chPsiq){
				chPsiq.init();
			});

		});
		$left.find('[name=chHospi]').click(function(){
			require(['ch/hospi'],function(chHospi){
				chHospi.init();
			});

		});
		$left.find('[name=chPadi]').click(function(){
			require(['ch/padi'],function(chPadi){
				chPadi.init();
			});

		});
		$left.find('[name=chPasi]').click(function(){
			require(['ch/pasi'],function(chPasi){
				chPasi.init();
			});

		});
		$left.find('[name=chCons]').click(function(){
			require(['ch/cons'],function(chCons){
				chCons.init();
			});

		});
			$left.find('[name=chEvol]').click(function(){
			require(['ch/evol'],function(chEvol){
				chEvol.init();
			});

		});
		$left.find('[name=chChar]').click(function(){
			require(['ch/char'],function(chChar){
				chChar.init();
			});

		});

		$left.find('[name=chDoct]').click(function(){
			require(['ch/doct'],function(chDoct){
				chDoct.init();
			});
		});
		
		$left.find('[name=chCama]').click(function(){
			require(['ch/cama'],function(chCama){
				chCama.init();
			});
		});
		$left.find('[name=chCmmo]').click(function(){
			require(['ch/cmmo'],function(chCmmo){
				chCmmo.init();
			});
		});
		
		$left.find('[name=chRepo]').click(function(){
			require(['ch/repo'],function(chRepo){
				chRepo.init();
			});
		});
		$left.find('[name=chPajo]').click(function(){
			require(['ch/pajo'],function(chPajo){
				chPajo.init();
			});
		});
    	$left.find('[name=chPaho]').click(function(){
			require(['ch/paho'],function(chPaho){
				chPaho.init();
			});
		});
		
		$left.find('[name=chCapu]').click(function(){
			require(['ch/capu'],function(chCapu){
				chCapu.init();
			});
		});

		$left.find('[name=chCont]').click(function(){
			require(['ch/cont'],function(chCont){
				chCont.init();
			});
		});



		/* ------------------------------ */ 

		/*ARCHIVO DIGITAL*/
		$left.find('[name=ddPear]').click(function(){
			require(['dd/pear'],function(ddPear){
				ddPear.init();
			});
		});
		
		$left.find('[name=ddOfic]').click(function(){
			require(['dd/ofic'],function(ddOfic){
				ddOfic.init();
			});
		});
		$left.find('[name=ddDire]').click(function(){
			require(['dd/dire'],function(ddDire){
				ddDire.init();
			});
		});
		$left.find('[name=ddTipo]').click(function(){
			require(['dd/tipo'],function(ddTipo){
				ddTipo.init();
			});
		});
		$left.find('[name=ddTido]').click(function(){
			require(['dd/tido'],function(ddTido){
				ddTido.init();
			});
		});
		$left.find('[name=ddTise]').click(function(){
			require(['dd/tise'],function(ddTise){
				ddTise.init();
			});
		});
		$left.find('[name=ddRegi]').click(function(){
			require(['dd/regi'],function(ddRegi){
				ddRegi.init();
			});
		});
		$left.find('[name=ddDepu]').click(function(){
			require(['dd/depu'],function(ddDepu){
				ddDepu.init();
			});
		});
		$left.find('[name=ddRedo]').click(function(){
			require(['dd/redo'],function(ddRedo){
				ddRedo.init();
			});
		});
		$left.find('[name=ddForm]').click(function(){
			require(['dd/form'],function(ddForm){
				ddForm.init();
			});
		});
		$left.find('[name=ddDohi]').click(function(){
			require(['dd/dohi'],function(ddDohi){
				ddDohi.init();
			});
		});
		$left.find('[name=ddRped]').click(function(){
			require(['dd/rped'],function(ddRped){
				ddRped.init();
			});
		});
		$left.find('[name=ddRreg]').click(function(){
			require(['dd/rreg'],function(ddRreg){
				ddRreg.init();
			});
		});
		$left.find('[name=ddRepo]').click(function(){
			require(['dd/repo'],function(ddRepo){
				ddRepo.init();
			});
		});
		$left.find('[name=ddPedi]').click(function(){
			require(['dd/pedi'],function(ddPedi){
				ddPedi.init();
			});
		});

		/* Contabilidad */
		$left.find('[name=ctPcon]').click(function(){
			require(['ct/pcon'],function(ctPcon){
				ctPcon.init();
			});
		});
		$left.find('[name=ctTnot]').click(function(){
			require(['ct/tnot'],function(ctTnot){
				ctTnot.init();
			});
		});
		$left.find('[name=ctVeoc]').click(function(){
			require(['ct/veoc'],function(ctVeoc){
				ctVeoc.init();
			});
		});
		$left.find('[name=ctVeos]').click(function(){
			require(['ct/veos'],function(ctVeos){
				ctVeos.init();
			});
		});
		$left.find('[name=ctNotc]').click(function(){
			require(['ct/notc'],function(ctNota){
				ctNotc.init();
			});
		});
		$left.find('[name=ctAuxs]').click(function(){
			require(['ct/auxs'],function(ctAuxs){
				ctAuxs.init();
			});
		});
		$left.find('[name=ctCntg]').click(function(){
			require(['ct/cntg'],function(ctCntg){
				ctCntg.init();
			});
		});
		$left.find('[name=ctRepo]').click(function(){
			require(['ct/repo'],function(ctRepo){
				ctRepo.init();
			});
		});

		/* CAJA */

		$left.find('[name=cjCaja]').click(function(){
			$.cookie('mode','cj');
			$.cookie('action','cjCaja');
			window.location.replace('?old=1');
		});
		$left.find('[name=cjEcom]').click(function(){
			require(['cj/ecom'],function(cjEcom){
				cjEcom.init();
			});
		});
		$left.find('[name=cjTalo]').click(function(){
			require(['cj/talo'],function(cjTalo){
				cjTalo.init();
			});
		});



		/* PRESUPUESTO */

		$left.find('[name=pr]').click(function(){
			$.cookie('mode','pr');
			$.cookie('action','prPlan');
			window.location.replace('?old=1');
		});

		
		if($.cookie('action')!=null){
			//$('#side-menu').find("li").children("ul.in").collapse("hide");
			$('[name='+$.cookie('action')+']').parent("li").addClass("active");
			$('[name='+$.cookie('mode')+']').parent("li").addClass("active");
			$('#side-menu').find("li").not('.active').children("ul.in").collapse("hide");
			$('#side-menu').find("li.active").children("ul").collapse("show");
			$left.find('[name='+$.cookie('action')+']').click();
		}else{
			$left.find('[name=da]').click();
		}
	}
};
var contextMenu = {
	conMenListSel: [
		{n: 'conMenListSel_sel',i: 'fa-search',t: 'Seleccionar'}
	],
	conMenList: [
		{n: 'conMenList_edi',i: 'fa-pencil',t: 'Editar'},
		{n: 'conMenList_eli',i: 'fa-trash-o',t: 'Eliminar'},
		{n: 'conMenList_imp',i: 'fa-print',t: 'Imprimir'}
	],
	conMenListEli: [
		{n: 'conMenListEli_ver',i: 'fa-search',t: 'Ver Detalles'},
		{n: 'conMenListEli_edi',i: 'fa-pencil',t: 'Editar'},
		{n: 'conMenListEli_eli',i: 'fa-trash-o',t: 'Eliminar'},
		{n: 'conMenListEli_imp',i: 'fa-print',t: 'Imprimir'}
	],
	conMenListEd: [
		{n: 'conMenListEd_ver',i: 'fa-search',t: 'Ver Detalles'},
		{n: 'conMenListEd_edi',i: 'fa-pencil',t: 'Editar'},
		{n: 'conMenListEd_hab',i: 'fa-check-circle',t: 'Habilitar'},
		{n: 'conMenListEd_des',i: 'fa-ban',t: 'Deshabilitar'}
	],
	/*
	 * MAESTROS GENERALES
	 */
	conMenMgMult: [
		{n: 'conMenMgMult_abr',i: 'fa-folder-open-o',t: 'Abrir Directorio'},
		{n: 'conMenMgMult_edi',i: 'fa-pencil',t: 'Editar'},
		{n: 'conMenMgMult_eli',i: 'fa-trash-o',t: 'Eliminar'},
		{n: 'conMenMgMult_des',i: 'fa-download',t: 'Descargar'}
	],
	/*
	 * CEMENTERIOS
	 */
	conMenCmEspa: [
		{n: 'conMenCmEspa_ver',i: 'fa-search',t: 'Ver Detalles'},
		{n: 'conMenCmEspa_edi',i: 'fa-pencil',t: 'Editar'},
		{n: 'conMenCmEspa_eli',i: 'fa-trash-o',t: 'Eliminar del Mapa'},
		{n: 'conMenCmEspa_ope',i: 'fa-cogs',t: 'Nueva Operaci&oacute;n'},
		{n: 'conMenCmEspa_con',i: 'fa-bank',t: 'Concesionar Espacio'},
		{n: 'conMenCmEspa_cob',i: 'fa-money',t: 'Registrar Cobro Administrativo'},
		{n: 'conMenCmEspa_reg',i: 'fa-book',t: 'Ocupante Anterior'},
		{n: 'conMenCmEspa_his',i: 'fa-book',t: 'Registro Hist&oacute;rico'},
		{n: 'conMenCmEspa_upl',i: 'fa-cogs',t: 'Digitalizar'}
	],
	conMenCmCir: [
		{n: 'conMenCmCir_ver',i: 'fa fa-users',t: 'Ver inscritos'},
		{n: 'conMenCmCir_sale',i: 'fa fa-ticket',t: 'Venta de Tickets'},
		{n: 'conMenCmCir_dow',i: 'fa fa-download',t: 'Descargar Lista'}
	],
	conMenCmRegi: [
		{n: 'conMenCmEspa_upl',i: 'fa fa-cloud-upload',t: 'Digitalizar'}
	],
	conMenCmOperlist: [
		{n: 'conMenCmOperList_det',i: 'fa-search',t: 'Ver detalles de operaci&oacute;n'},
		{n: 'conMenCmOperList_com',i: 'fa-money',t: 'Ver Comprobante de Pago'},
		{n: 'conMenCmOperList_anu',i: 'fa-ban',t: 'Anular Operaci&oacute;n'},
		{n: 'conMenCmOperList_ope',i: 'fa-ban',t: 'Nueva Operaci&oacute;n sobre mismo espacio'},
		{n: 'conMenCmOperList_eli',i: 'fa-ban',t: 'Eliminar Operaci&oacute;n'},
	],
	conMenCmHope: [
		{n: 'conMenCmHope_ver',i: 'fa-search',t: 'Ver Detalles'},
		{n: 'conMenCmHope_edi',i: 'fa-pencil',t: 'Editar Registro'},
		{n: 'conMenCmHope_eli',i: 'fa-trash-o',t: 'Eliminar Registro'},
		{n: 'conMenCmHope_com',i: 'fa-print',t: 'Completar Datos B&aacute;sicos'}
	],
	conMenCmOper: [
		{n: 'conMenCmOper_pro',i: 'fa-contact',t: 'Detalles de Propietario'},
		{n: 'conMenCmOper_ocu',i: 'fa-contact',t: 'Detalles de Ocupante'},
		{n: 'conMenCmOper_editPro',i: 'fa-pencil',t: 'Editar Propietario'},
		{n: 'conMenCmOper_coc',i: 'fa-pencil',t: 'Concesi&oacute;n'},
		{n: 'conMenCmOper_asi',i: 'fa-pencil',t: 'Asignaci&oacute;n'},
		{n: 'conMenCmOper_cos',i: 'fa-pencil',t: 'Construcci&oacute;n'},
		{n: 'conMenCmOper_amp',i: 'fa-pencil',t: 'Ampliaci&oacute;n'},
		{n: 'conMenCmOper_inh',i: 'fa-pencil',t: 'Inhumaci&oacute;n'},
		{n: 'conMenCmOper_traInt',i: 'fa-pencil',t: 'Traslado Interno'},
		{n: 'conMenCmOper_traExt',i: 'fa-pencil',t: 'Traslado Externo (hacia otro cementerio)'},
		{n: 'conMenCmOper_traExtExt',i: 'fa-pencil',t: 'Traslado Externo (desde otro cementerio)'},
		{n: 'conMenCmOper_col',i: 'fa-pencil',t: 'Colocaci&oacute;n'},
		{n: 'conMenCmOper_trs',i: 'fa-pencil',t: 'Traspaso'},
		{n: 'conMenCmOper_anc',i: 'fa-pencil',t: 'Finalizar Concesi&oacute;n'},
		{n: 'conMenCmOper_ana',i: 'fa-pencil',t: 'Reasignaci&oacute;n'},
		{n: 'conMenCmOper_regiOcup',i: 'fa-pencil',t: 'Registrar Ocupante Anterior'},
		{n: 'conMenCmOper_ren',i: 'fa-pencil',t: 'Renovaci&oacute;n'},
		{n: 'conMenCmOper_anu',i: 'fa-pencil',t: 'Anular Operaci&oacute;n'},
		{n: 'conMenCmOper_con',i: 'fa-pencil',t: 'Conversi&oacute;n'}
	],
	conMenCmEnti: [
		{n: 'conMenCmEnti_ver',i: 'fa-search',t: 'Ver detalles'},
		{n: 'conMenCmEnti_edi',i: 'fa-pencil',t: 'Editar Datos de Entidad'},
		{n: 'conMenCmEnti_ope',i: 'fa-plus',t: 'Nueva Operaci&oacute;n'},
		{n: 'conMenCmEnti_cob',i: 'fa-contact',t: 'Registrar Cobro Administrativo'}
	],
	/*
	 * INMUEBLES
	 */
	conMenInComp: [
		{n: 'conMenInComp_imp',i: 'fa-print',t: 'Imprimir Comprobante'},
		{n: 'conMenInComp_anu',i: 'fa-ban',t: 'Anular Comprobante'},
		//{n: 'conMenInComp_cam',i: 'fa-ban',t: 'Realizar Cambio de Nombre'},
		{n: 'conMenInComp_pag',i: 'fa-money',t: 'Modificar Forma de Pago'},
		{n: 'conMenInComp_eli',i: 'fa-trash-o',t: 'Eliminar Comprobante'},
		{n: 'conMenInComp_eco',i: 'fa-money',t: 'Generar comprobante electronico'},
		{n: 'conMenInComp_eim',i: 'fa-print',t: 'Imprimir comprobante electronico'}
	],
	conMenInRein: [
		{n: 'conMenInRein_imp',i: 'fa-print',t: 'Imprimir Recibo de Ingresos'},
		{n: 'conMenInRein_pla',i: 'fa-file-excel-o',t: 'Generar Planilla'},
		{n: 'conMenInRein_anu',i: 'fa-ban',t: 'Anular Recibo de Ingresos'}
	],
	/*
	 * LOGISTICA
	 */
	conMenLgCuad: [
		{n: 'conMenLgCuad_ver',i: 'fa-search',t: 'Ver cuadro'},
		{n: 'conMenLgCuad_edi',i: 'fa-pencil',t: 'Editar cuadro'},
		{n: 'conMenLgCuad_env',i: 'fa-share-square',t: 'Enviar cuadro'},
		{n: 'conMenLgCuad_apr',i: 'fa-check-circle',t: 'Aprobar cuadro'},
		{n: 'conMenLgCuad_vig',i: 'fa-check-circle-o',t: 'Establecer como Vigente'},
		{n: 'conMenLgCuad_amp',i: 'fa-user-plus',t: 'Ampliar y Habilitar'},
		{n: 'conMenLgCuad_xls',i: 'fa-file-excel-o',t: 'Generar Excel'},
		{n: 'conMenLgCuad_eli',i: 'fa-trash-o',t: 'Eliminar'}
	],
	conMenLgProd: [
		{n: 'conMenLgProd_ver',i: 'fa-search',t: 'Ver Detalles'},
		{n: 'conMenLgProd_edi',i: 'fa-pencil',t: 'Editar'},
		{n: 'conMenLgProd_hab',i: 'fa-check-circle',t: 'Habilitar'},
		{n: 'conMenLgProd_des',i: 'fa-ban',t: 'Deshabilitar'},
		{n: 'conMenLgProd_verf',i: 'fa-search',t: 'Ver Ficha de Producto'},
		{n: 'conMenLgProd_elim',i: 'fa-trash-o',t: 'Eliminar Producto'},
		{n: 'conMenLgProd_mov',i: 'fa-bar-chart',t: 'Ver Movimientos'}
	],
	conMenLgPedp: [
		{n: 'conMenLgPedp_ver',i: 'fa-search',t: 'Ver Requerimiento'},
		{n: 'conMenLgPedp_edi',i: 'fa-search',t: 'Editar Requerimiento'},
		{n: 'conMenLgPedp_rev',i: 'fa-check',t: 'Revisar'},
		{n: 'conMenLgPedp_fin',i: 'fa-flag',t: 'Finalizar'}
	],
	conMenLgCoti: [
		{n: 'conMenLgCoti_ver',i: 'fa-search',t: 'Ver Resultados'},
		{n: 'conMenLgCoti_fin',i: 'fa-check',t: 'Finalizar Concurso'},
		{n: 'conMenLgCoti_cer',i: 'fa-flag',t: 'Cerrar Concurso'},
		{n: 'conMenLgCoti_rev',i: 'fa-trophy',t: 'Revisar Propuestas'},
		{n: 'conMenLgCoti_ing',i: 'fa-plus',t: 'Ingresar Propuesta'},
		{n: 'conMenLgCoti_pub',i: 'fa-users',t: 'Publicar Concurso'},
		{n: 'conMenLgCoti_edi',i: 'fa-pencil',t: 'Editar Concurso'}
	],
	conMenLgSoli: [
		{n: 'conMenLgSoli_ver',i: 'fa-search',t: 'Ver Solicitud'},
		{n: 'conMenLgSoli_edi',i: 'fa-pencil',t: 'Editar Solicitud'},
		{n: 'conMenLgSoli_env',i: 'fa-trophy',t: 'Enviar Solicitud'},
		{n: 'conMenLgSoli_rec',i: 'fa-check',t: 'Recepcionar Solicitud'},
		{n: 'conMenLgSoli_apr',i: 'fa-check',t: 'Aprobar Solicitud'},
		{n: 'conMenLgSoli_gen',i: 'fa-check',t: 'Generar Certificacion presupuestaria'}
	],
	conMenLgCert: [
		{n: 'conMenLgCert_ver',i: 'fa-search',t: 'Ver Certificacion'},
		{n: 'conMenLgCert_edi',i: 'fa-pencil',t: 'Editar Certificacion'},
		{n: 'conMenLgCert_apr',i: 'fa-pencil',t: 'Aprobar Certificacion'},
		{n: 'conMenLgCert_env',i: 'fa-trophy',t: 'Enviar Certificacion'},
		{n: 'conMenLgCert_rec',i: 'fa-pencil',t: 'Recepcionar Certificacion'},
		{n: 'conMenLgCert_ord',i: 'fa-check',t: 'Generar orden de compra'},
		{n: 'conMenLgCert_ors',i: 'fa-check',t: 'Generar orden de servicio'},
		{n: 'conMenLgCert_imp',i: 'fa-print',t: 'Imprimir'}
	],
	/*conMenLgDocu: [
		{n: 'conMenLgDocu_ver',i: 'fa-search',t: 'Ver'},
		{n: 'conMenLgDocu_edi',i: 'fa-pencil',t: 'Editar'},

		{n: 'conMenLgDocu_edi',i: 'fa-pencil',t: 'Enviar solicitud de certificacion'},
		{n: 'conMenLgDocu_edi',i: 'fa-pencil',t: 'Recepcionar solicitud de certificacion'},

		{n: 'conMenLgDocu_edi',i: 'fa-pencil',t: 'Aprobar orden de compra'},
		{n: 'conMenLgDocu_edi',i: 'fa-pencil',t: 'Enviar orden de compra'},

		{n: 'conMenLgDocu_fin',i: 'fa-check',t: 'Finalizar Orden de Compra'},
		{n: 'conMenLgDocu_rev',i: 'fa-trophy',t: 'Revisar Orden de Compra'},
		{n: 'conMenLgDocu_con',i: 'fa-check-square',t: 'Confirmar Entrega'}
	],*/
	conMenLgOrde: [
		{n: 'conMenLgOrde_ver',i: 'fa-search',t: 'Ver Orden de Compra'},
		{n: 'conMenLgOrde_edi',i: 'fa-pencil',t: 'Editar Orden de Compra'},
		{n: 'conMenLgOrde_apr',i: 'fa-check',t: 'Aprobar Orden de Compra'},
		{n: 'conMenLgOrde_env',i: 'fa-check',t: 'Enviar Orden de Compra'},
		{n: 'conMenLgOrde_rec',i: 'fa-check',t: 'Recepcionar Orden de Compra'},
		{n: 'conMenLgOrde_imp',i: 'fa-check',t: 'Imprimir Orden'}
	],
	conMenLgOrse: [
		{n: 'conMenLgOrse_ver',i: 'fa-search',t: 'Ver Orden de servicio'},
		{n: 'conMenLgOrse_edi',i: 'fa-pencil',t: 'Editar Orden de servicio'},
		{n: 'conMenLgOrse_apr',i: 'fa-check',t: 'Aprobar Orden de servicio'},
		{n: 'conMenLgOrse_env',i: 'fa-check',t: 'Enviar Orden de servicio'},
		{n: 'conMenLgOrse_rec',i: 'fa-check',t: 'Recepcionar Orden de servicio'},
		{n: 'conMenLgOrse_imp',i: 'fa-check',t: 'Imprimir Orden'}
	],
	conMenLgNota: [
		{n: 'conMenLgNota_ver',i: 'fa-search',t: 'Ver Nota de entrada'},
		{n: 'conMenLgNota_edi',i: 'fa-pencil',t: 'Editar Nota de entrada'},
		{n: 'conMenLgNota_fin',i: 'fa-check',t: 'Finalizar Nota de entrada'},
		{n: 'conMenLgNota_rev',i: 'fa-trophy',t: 'Revisar Nota de entrada'},
		{n: 'conMenLgNota_imp',i: 'fa-trophy',t: 'Imprimir Nota de entrada'}
		/*{n: 'conMenLgNota_con',i: 'fa-check-square',t: 'Confirmar Entrega'}*/
	],
	conMenLgPeco: [
		{n: 'conMenLgPeco_edi',i: 'fa-search',t: 'Editar PECOSA'},
		{n: 'conMenLgPeco_rev',i: 'fa-pencil',t: 'Revisar PECOSA'},
		{n: 'conMenLgPeco_fin',i: 'fa-check',t: 'Finalizar PECOSA'},
		{n: 'conMenLgPeco_anu',i: 'fa-check',t: 'Anular PECOSA'},
		{n: 'conMenLgPeco_def',i: 'fa-trophy',t: 'Definir entrega'},
		{n: 'conMenLgPeco_con',i: 'fa-trophy',t: 'Confirmar recepci&oacute;n'},
		{n: 'conMenLgPeco_dar',i: 'fa-trophy',t: 'Dar de alta'},
		{n: 'conMenLgPeco_ver',i: 'fa-trophy',t: 'Ver PECOSA'},
		{n: 'conMenLgPeco_imp',i: 'fa-trophy',t: 'Imprimir'}
	],
	/*
	 * USA
	 */
	conMenUsProg: [
		{n: 'conMenUsProg_imp',i: 'fa-print',t: 'Imprimir Programaci&oacute;n'},
		{n: 'conMenUsProg_ver',i: 'fa-search',t: 'Ver Detalles'},
		{n: 'conMenUsProg_edi',i: 'fa-pencil',t: 'Editar'},
		{n: 'conMenUsProg_apr',i: 'fa-check',t: 'Publicar Programaci&oacute;n'}
	],
	/*
	 * PERSONAL
	 */
	conMenPePlan: [
		{n: 'conMenPePlan_edi',i: 'fa-pencil',t: 'Editar'},
		{n: 'conMenPePlan_tra',i: 'fa-pencil',t: 'Trabajadores'},
		{n: 'conMenPePlan_gen',i: 'fa-pencil',t: 'Generar Planilla'},
		{n: 'conMenPePlan_ibo',i: 'fa-pencil',t: 'Imprimir Boletas'},
		{n: 'conMenPePlan_dma',i: 'fa-pencil',t: 'Descargar maestro Planilla'},
		{n: 'conMenPePlan_sma',i: 'fa-pencil',t: 'Subir maestro Planilla'},
		{n: 'conMenPePlan_gpe',i: 'fa-pencil',t: 'Exportar planilla electr&oacute;nica'},
		//{n: 'conMenPeSist_hab',i: 'fa-check-circle',t: 'Habilitar'},
		//{n: 'conMenPeSist_des',i: 'fa-ban',t: 'Deshabilitar'}
	],

	conMenPeSist: [
		{n: 'conMenPeSist_ver',i: 'fa-search',t: 'Ver Detalles'},
		{n: 'conMenPeSist_edi',i: 'fa-pencil',t: 'Editar'},
		{n: 'conMenPeSist_act',i: 'fa-refresh',t: 'Actualizar Porcentajes'},
		{n: 'conMenPeSist_hab',i: 'fa-check-circle',t: 'Habilitar'},
		{n: 'conMenPeSist_des',i: 'fa-ban',t: 'Deshabilitar'}
	],
	conMenPeCont: [
		{n: 'conMenPeCont_ver',i: 'fa-search',t: 'Ver Detalles'},
		{n: 'conMenPeCont_edi',i: 'fa-pencil',t: 'Editar'},
		{n: 'conMenPeCont_def',i: 'fa-refresh',t: 'Definir Campos'},
		{n: 'conMenPeCont_hab',i: 'fa-check-circle',t: 'Habilitar'},
		{n: 'conMenPeCont_des',i: 'fa-ban',t: 'Deshabilitar'}
	],
	conMenPeTrab: [
		{n: 'conMenPeTrab_ver',i: 'fa-search',t: 'Ver Detalles'},
		{n: 'conMenPeTrab_veb',i: 'fa-check',t: 'Ver Bonos'},
		{n: 'conMenPeTrab_veh',i: 'fa-check',t: 'Ver Hist&oacute;rico del Trabajador'},
		{n: 'conMenPeTrab_edi',i: 'fa-pencil',t: 'Editar Trabajador'},
		{n: 'conMenPeTrab_ede',i: 'fa-pencil',t: 'Editar Entidad'},
		{n: 'conMenPeTrab_acf',i: 'fa-refresh',t: 'Actualizar Ficha'},
		{n: 'conMenPeTrab_acl',i: 'fa-check',t: 'Actualizar Legajo'},
		{n: 'conMenPeTrab_agr',i: 'fa-check',t: 'Agregar Bono'},
		{n: 'conMenPeTrab_des',i: 'fa-ban',t: 'Deshabilitar'},
		{n: 'conMenPeTrab_eli',i: 'fa-trash',t: 'Eliminar Trabajador'},
		{n: 'conMenPeTrab_ret',i: 'fa-ban',t: 'Retenci&oacute;n Judicial'},
		{n: 'conMenPeTrab_iml',i: 'fa-print',t: 'Imprimir Legajo'},
		{n: 'conMenPeTrab_imf',i: 'fa-print',t: 'Imprimir Ficha'},
		{n: 'conMenPeTrab_pad',i: 'fa fa-user-plus',t: 'Padres'}
	],
	conMenPeAsis: [
		{n: 'conMenPeAsis_hor',i: 'fa-calendar',t: 'Ver Horarios y Programaci&oacute;n'},
		//{n: 'conMenPeAsis_inc',i: 'fa-check',t: 'Clasificar Incidencias'},
		{n: 'conMenPeAsis_asi',i: 'fa-search',t: 'Ver Asistencia'}
	],
	conMenPeConc: [
		{n: 'conMenPeConc_ver',i: 'fa-trash',t: 'Eliminar'},
		{n: 'conMenPeConc_edi',i: 'fa-edit',t: 'Editar'},
		{n: 'conMenPeConc_hab',i: 'fa-check-circle',t: 'Habilitar'},
		{n: 'conMenPeConc_des',i: 'fa-ban',t: 'Deshabilitar'},
	],
	/*
	 * HOSPITALIZACION
	 */
	conMenHoCont: [
		{n: 'conMenHoCont_ver',i: 'fa-search',t: 'Ver Detalles'},
		{n: 'conMenHoCont_aum',i: 'fa-plus',t: 'Agregar medicinas'},
		{n: 'conMenHoCont_pab',i: 'fa fa-exchange',t: 'Mover de Pabellon'},
		{n: 'conMenHoCont_dis',i: 'fa-minus',t: 'Descontar medicinas'},
		{n: 'conMenHoCont_dev',i: 'fa-retweet',t: 'Devolver todo'},
		{n: 'conMenHoCont_imp',i: 'fa-print',t: 'Imprimir Reporte'},
		{n: 'conMenHoCont_des',i: 'fa-undo',t: 'Deshacer &uacute;ltimo Movimiento'}
	],
	conMenHoHosp: [
		{n: 'conMenHoHosp_edi',i: 'fa-pencil',t: 'Completar Datos de Paciente'},
		{n: 'conMenHoHosp_alt',i: 'fa-check',t: 'Dar de Alta al Paciente'},
		{n: 'conMenHoHosp_eli',i: 'fa-trash-o',t: 'Eliminar Registro'}
	],
	/*
	 * TESORERIA
	 */
	conMenTsRein: [
		{n: 'conMenTsRein_ver',i: 'fa-search',t: 'Ver Detalles'},
		{n: 'conMenTsRein_rec',i: 'fa-check',t: 'Recepcionado y A&ntilde;adido a Libro Bancos'}
	],
	conMenTsCj: [
		{n: 'conMenTsCj_edi',i: 'fa-pencil',t: 'Editar'},
		{n: 'conMenTsCj_esta',i: 'fa fa-check-square',t: 'Cambiar Estado'},
		{n: 'conMenTsCj_eli',i: 'fa-trash-o',t: 'Eliminar'}
		
	],
	conMenTsCjdo: [
		{n: 'conMenTsCjdo_edi',i: 'fa-pencil',t: 'Editar'},
		{n: 'conMenTsCjdo_eli',i: 'fa-trash-o',t: 'Eliminar'},
		{n: 'conMenTsCjdo_apro',i: 'fa-check-circle',t: 'Aprobado'},
		{n: 'conMenTsCjdo_anul',i: 'fa fa-window-close-o',t: 'Anular'}
		//{n: 'conMenTsCj_pend',i: 'fa-ban',t: 'Pendiente'},
		
	],
	conMenTsReci: [
		{n: 'conMenTsReci_edit_d',i: 'fa-pencil',t: 'Editar'},
		{n: 'conMenTsReci_eli',i: 'fa-trash-o',t: 'Eliminar'},
		{n: 'conMenTsReci_imp',i: 'fa-print',t: 'Imprimir'}
		//{n: 'conMenTsReci_apro',i: 'fa-check-circle',t: 'Aprobado'},
		//{n: 'conMenTsReci_edit_p',i: 'fa-pencil',t: 'Editar'},
		//{n: 'conMenTsCj_pend',i: 'fa-ban',t: 'Pendiente'},
		
	],
	conMenTsApr: [
		{n: 'conMenTsApr_apro',i: 'fa-check-circle',t: 'Aprobar'}
		//{n: 'conMenTsReci_edit_p',i: 'fa-pencil',t: 'Editar'},
		//{n: 'conMenTsCj_pend',i: 'fa-ban',t: 'Pendiente'},
		
	],
	conMenTsCjse: [
		{n: 'conMenTsCjse_edi',i: 'fa-pencil',t: 'Editar'},
		{n: 'conMenTsCjse_repoaux',i: 'fa fa-folder-open',t: 'Reporte de Auxiliar Estandar'},
		{n: 'conMenTsCjse_repogaspu',i: 'fa fa-folder-open',t: 'Reporte de Gastos Publicos'},
		{n: 'conMenTsCjse_repo',i: 'fa fa-file-excel-o',t: 'Reporte de Rendicion de fondos'},
		{n: 'conMenTsCjse_cerrar',i: 'fa fa-window-close',t: 'Cerrar Sesion'},
		{n: 'conMenTsCjse_eli',i: 'fa-trash-o',t: 'Eliminar Sesion'},
		{n: 'conMenTsCjse_reset',i: 'fa-trash-o',t: 'Recalculo de Movimientos'}
	],
	conMenTsLibo: [
		{n: 'conMenTsLibo_add',i: 'fa fa-plus-circle',t: 'Agregar Comprobantes'},
		{n: 'conMenTsLibo_cerrar',i: 'fa fa-window-close',t: 'Cerrar Libro'},
		{n: 'conMenTsLibo_repo',i: 'fa fa-file-excel-o',t: 'Reporte de Sesion'},
		{n: 'conMenTsLibo_eli',i: 'fa-trash-o',t: 'Eliminar'}
	],
	/*
	 * FARMACIA
	 */
	conMenFaProd: [
		{n: 'conMenFaProd_ver',i: 'fa-search',t: 'Ver Detalles'},
		{n: 'conMenFaProd_edi',i: 'fa-pencil',t: 'Editar Producto'},
		{n: 'conMenFaProd_inc',i: 'fa-plus',t: 'Incrementar Stock'}
	],
	/*
	 * AGUA CHAPI
	 */
	conMenAgComp: [
		{n: 'conMenAgComp_imp',i: 'fa-print', t: 'Imprimir Comprobante'},
		{n: 'conMenAgComp_edc'  ,i: 'fa-pencil',t: 'Editar Concepto'},
		{n: 'conMenAgComp_anu',i: 'fa-ban',   t: 'Anular Comprobante'},
		{n: 'conMenAgComp_pag',i: 'fa-money', t: 'Modificar Forma de Pago'},
		{n: 'conMenAgComp_eli',i: 'fa-ban',   t: 'Eliminar Comprobante'}
	],
	/*
	 * GESTION DE PROYECTOS
	 */
	conMenGeProy: [
		{n: 'conMenGeProy_ver',i: 'fa-search',t: 'Ver Detalles'},
		{n: 'conMenGeProy_gnt',i: 'fa-search',t: 'Diagrama de Proyectos'},
		{n: 'conMenGeProy_edi',i: 'fa-pencil',t: 'Editar Detalles Generales'},
		{n: 'conMenGeProy_hab',i: 'fa-check-circle',t: 'Habilitar Proyecto'},
		{n: 'conMenGeProy_des',i: 'fa-ban',t: 'Deshabilitar Proyecto'},
		{n: 'conMenGeProy_ava',i: 'fa-tasks',t: 'Editar Avance'}
	],
	/*
	 * TESORERIA
	 */
	conMenTsComp: [
		{n: 'conMenTsComp_ver',i: 'fa-search',t: 'Ver'},
		{n: 'conMenTsComp_edi',i: 'fa-pencil',t: 'Editar'},
		{n: 'conMenTsComp_pag',i: 'fa-check-circle',t: 'Pagar'},
		{n: 'conMenTsComp_anu',i: 'fa-ban',t: 'Anular'}
	],
	/*
	 * CONTABILIDAD
	 */
	 conMenCtPcon: [
		//{n: 'conMenTsComp_ver',i: 'fa-search',t: 'Ver'},
		{n: 'conMenCtPcon_edi',i: 'fa-pencil',t: 'Editar'},
		{n: 'conMenCtPcon_ord',i: 'fa-check-circle',t: 'Ordenar Sub-cuentas'},
		{n: 'conMenCtPcon_nue',i: 'fa-ban',t: 'Nueva Cuenta'},
		{n: 'conMenCtPcon_hab',i: 'fa-ban',t: 'Habilitar'},
		{n: 'conMenCtPcon_des',i: 'fa-ban',t: 'Deshabilitar'}
	],
	conMenCtVeoc: [
		{n: 'conMenCtVeoc_ver',i: 'fa-search',t: 'Ver'},
		{n: 'conMenCtVeoc_ing',i: 'fa-pencil',t: 'Ingresar informacion contable'},
		{n: 'conMenCtVeoc_apr',i: 'fa-check-circle',t: 'Aprobar y crear auxiliares'}
	],
	//Archivo Central
	conMenAreas: [
		{n: 'conMenAreas_edi',i: 'fa-pencil',t: 'Editar'},
		{n: 'conMenAreas_eli',i: 'fa-trash-o',t: 'Eliminar'}
		
	],
	conMenRedo: [
		{n: 'conMenRedo_edi',i: 'fa-pencil',t: 'Editar'},
		{n: 'conMenRedo_inf',i: 'fa-check-circle',t: 'Imprimir'},
		{n: 'conMenRedo_eli',i: 'fa-trash-o',t: 'Eliminar'}
		
		
	],
	conMenPear: [
		{n: 'conMenPear_print',i: 'fa-search',t: 'Imprimir'},
		{n: 'conMenPear_edi',i: 'fa-pencil',t: 'Editar'}
		
		
	],
	conMenRegistro: [
		{n: 'conMenRegistro_edi',i: 'fa-pencil',t: 'Editar'},
		{n: 'conMenRegistro_eli',i: 'fa-trash-o',t: 'Eliminar'},
		{n: 'conMenRegistro_hab',i: 'fa-check-circle',t: 'Digitalizado'},
		{n: 'conMenRegistro_des',i: 'fa-ban',t: 'No Digitalizado'}, 
		{n: 'conMenRegistro_sub',i: 'fa fa-cloud-upload',t: 'Subir Archivo'},
		{n: 'conMenRegistro_dow',i: 'fa fa fa-cloud-download',t: 'Descargar Archivo'}
	],
	conMenForm: [
		{n: 'conMenForm_edi',i: 'fa-pencil',t: 'Editar'},
		{n: 'conMenForm_sub',i: 'fa fa-cloud-upload',t: 'Subir Archivo'},
		{n: 'conMenForm_dow',i: 'fa fa fa-cloud-download',t: 'Descargar Archivo'},
		{n: 'conMenForm_eli',i: 'fa-trash-o',t: 'Eliminar'}
		
	],
	//moises heresi //
	conMenMheresi: [
		{n: 'conMenMheresi_fron',i: 'fa-search',t: 'Informe Frontal'},
		{n: 'conMenMheresi_pabe',i: 'fa fa-exchange',t: 'Mover de Pabellon'},
		{n: 'conMenMheresi_edi',i: 'fa-pencil',t: 'Editar'},
		{n: 'conMenMheresi_soci',i: 'fa fa-id-card',t: 'Ficha de Salud Publica'},
		{n: 'conMenMheresi_tarje',i: 'fa fa-id-card-o',t: 'Tarjeta de Admicion'},
		{n: 'conMenMheresi_carnet',i: 'fa fa-address-book',t: 'Carnet'},
		{n: 'conMenMheresi_eli',i: 'fa-ban',t: 'Eliminar'}
		
	],
	conMenFPsico: [
		{n: 'conMenFPsico_edi',i: 'fa-pencil',t: 'Modificar'},
		{n: 'conMenFPsico_info',i: 'fa-check-circle',t: 'Informe'},
		//{n: 'conMenFPsico_evol',i: 'fa fa-level-up',t: 'A&ntildeadir Evolucion'},
		{n: 'conMenFPsico_eli',i: 'fa-ban',t: 'Eliminar'}
	],
	conMenCon_Paciente: [
		{n: 'conMenCon_Paciente_edi',i: 'fa-pencil',t: 'Mover de Pabellon'},
		{n: 'conMenCon_Paciente_info',i: 'fa-check-circle',t: 'Informe'},
		{n: 'conMenCon_Paciente_alta',i: 'fa fa-wheelchair-alt aria-hidden="true"',t: 'Dar de Alta'},
		{n: 'conMenCon_Paciente_eli',i: 'fa-ban',t: 'Eliminar'}
	],
	conMenFMedica: [
		{n: 'conMenFMedica_edi',i: 'fa-pencil',t: 'Modificar'},
		{n: 'conMenFMedica_info',i: 'fa-check-circle',t: 'Informe'},
		{n: 'conMenFMedica_evol',i: 'fa fa-level-up',t: 'A&ntildeadir Evolucion'},
		{n: 'conMenFMedica_eli',i: 'fa-ban',t: 'Eliminar'}
	],
	conMenFSocial: [
		{n: 'conMenFSocial_edi',i: 'fa-pencil',t: 'Modificar'},
		{n: 'conMenFSocial_info',i: 'fa-check-circle',t: 'Informe'},
		{n: 'conMenFSocial_cate',i: 'fa fa-level-up',t: 'Cambiar Categoría'},
		{n: 'conMenFSocial_eli',i: 'fa-ban',t: 'Eliminar'}
	],
	conMenPDiario: [
		{n: 'conMenPDiario_edi',i: 'fa-pencil',t: 'Modificar'},
		{n: 'conMenPDiario_cons',i: 'fa fa-user-md',t: 'Agregar Consulta'},
		{n: 'conMenPDiario_info',i: 'fa-check-circle',t: 'Informe'},
		{n: 'conMenPDiario_eli',i: 'fa-ban',t: 'Eliminar'}
	],
	conMenMhComp: [
		{n: 'conMenMhComp_imp',i: 'fa-print',t: 'Imprimir Comprobante'},
		{n: 'conMenMhComp_anu',i: 'fa-ban',t: 'Anular Comprobante'},
		{n: 'conMenMhComp_eli',i: 'fa-trash-o',t: 'Eliminar Comprobante'}
	],
	conMenMhEvol: [
		{n: 'conMenMhEvol_evol',i: 'fa fa-level-up',t: 'A&ntildeadir Evolucion'},
		{n: 'conMenMhEvol_eli',i: 'fa-trash-o',t: 'Eliminar Historia'}
	],
	conMenMhCama: [
		{n: 'conMenMhCama_edit',i: 'fa-pencil',t: 'Modificar'},
		{n: 'conMenMhCama_eli',i: 'fa-trash-o',t: 'Eliminar'}
	],
	conMenMhMov: [
		{n: 'conMenMhMov_ingr',i: 'fa fa-user-plus',t: 'Ingresar Paciente'},
		{n: 'conMenMhMov_alta',i: 'fa fa-wheelchair-alt aria-hidden="true"',t: 'Alta de Paciente'},
		{n: 'conMenMhMov_tras',i: 'fa fa-arrows-h',t: 'Traslados'}
	],

	//LOGISTICA LAST
	conMenLgGuia: [
		{n: 'conMenLgGuia_ver',i: 'fa-search',t: 'Ver'},
		{n: 'conMenLgGuia_edi',i: 'fa-pencil',t: 'Editar'},
		{n: 'conMenLgGuia_anu',i: 'fa-trash-o',t: 'Anular'},
		{n: 'conMenLgGuia_imp',i: 'fa-print',t: 'Imprimir'}
	],

	//CAJA DE FACTURACION ELECTRONICA
	conMenEcom: [
		{n: 'conMenEcom_ver',i: 'fa-search',t: 'Ver'},
		{n: 'conMenEcom_edi',i: 'fa-pencil',t: 'Editar'},
		{n: 'conMenEcom_fir',i: 'fa-check-square-o',t: 'Firmar'},
		{n: 'conMenEcom_env',i: 'fa-print',t: 'Enviar Sunat'},
		{n: 'conMenEcom_gnc',i: 'fa-level-down',t: 'Generar Nota de credito'},
		{n: 'conMenEcom_gnd',i: 'fa-level-up',t: 'Generar Nota de debito'},
		{n: 'conMenEcom_imp',i: 'fa-print',t: 'Imprimir Comprobante'},
		{n: 'conMenEcom_im2',i: 'fa-print',t: 'Imprimir Comprobante A4'},
		{n: 'conMenEcom_anu',i: 'fa-trash-o',t: 'Dar de baja al Comprobante'},
		{n: 'conMenEcom_fop',i: 'fa-print',t: 'Modificar forma de pago'},
		{n: 'conMenEcom_chk',i: 'fa-check-circle',t: 'Verificar estado de factura'},
		{n: 'conMenEcom_del',i: 'fa-trash-o',t: 'Eliminar Borrador'},
	],
};
define(
	function(){
		return navg;
	}
);