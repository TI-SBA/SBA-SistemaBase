mhDash = {
	init: function(p){
		if(p==null) p = {};
		K.initMode({
			mode: 'mh',
			action: 'mh', 
			titleBar: {
				title: 'Resumen de SBPA' 
			}
		});
		$('#side-menu').find("li").not('.active').children("ul.in").collapse("hide");
		
		new K.Panel({
			contentURL: 'mh/dash',
			store: false,
			buttons: {
				'Actualizar Informaci&oacute;n': { 
					icon: 'fa-refresh',
					type: 'success',
					f: function(){
						mhDash.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
	   			new K.grid({
					$el: p.$w.find('[name=gridInhu]'),
					search: false,
					pagination: false,
					cols: ['Fecha','Fallecido a Enterrar','Funeraria','Ingreso'],
					onlyHtml: true
				});
				K.block(); 
				$.post('mh/padi/get_dash',function(data){
                    p.data = data;
                    console.log(p.data);
					/*****************************************************************************************************
					* EXPEDIENTES
					*****************************************************************************************************/
					var ctx = p.$w.find("#expedientes").get(0).getContext("2d");
					var myDoughnutChart = new Chart(ctx).Pie([
						{
					        value: p.data[4][1][1],
					        color:"#3371ff",
    						highlight: "#3371ff",
    						label: "Cercado"
					    },
					    {
					        value: p.data[4][1][2],
					        color: "#542fec",
					        highlight: "#542fec",
					        label: "Alto Selva Alegre"
					    },
					    {
					        value: p.data[4][1][3],
					        color: "#df3d60",
					        highlight: "#df3d60",
					        label: "Cayma"
					    },
					    {
					        value: p.data[4][1][4],
					        color: "#094f48",
					        highlight: "#094f48",
					        label: "Cerro Colorado"
					    },
					    {
					        value: p.data[4][1][7],
					        color: "#cecb18",
					        highlight: "#cecb18",
					        label: "Jacobo Hunter"
					    },
					    {
					        value: p.data[4][1][9],
					        color: "#000000",
					        highlight: "#000000",
					        label: "Mariano Melgar"
						},
					    {
					        value: p.data[4][1][10],
					        color: "#3bfaef",
					        highlight: "#3bfaef",
					        label: "Miraflores"
						},
					    {
					        value: p.data[4][1][12],
					        color: "#3ba723",
					        highlight: "#3ba723",
					        label: "Paucarpata"
					    },
					    {
					        value: p.data[4][1][26],
					        color: "#c36a21",
					        highlight: "#c36a21",
					        label: "Yanahuara"
						},
					    {
					        value: p.data[4][1][29],
					        color: "#e3281f",
					        highlight: "#e3281f",
					        label: "Jose Luis Bustamante Y Rivero"
						}/*,
					    {
					        value: p.data[4][1][11],
					        color: "#781841",
					        highlight: "#781841",
					        label: "Mollebaya"
						},
					    {
					        value: p.data[4][1][12],
					        color: "#651878",
					        highlight: "#651878",
					        label: "Paucarpata"
					    },
					    {
					        value: p.data[4][1][13],
					        color: "#281878",
					        highlight: "#281878",
					        label: "Pocsi"
						},
					    {
					        value: p.data[4][1][14],
					        color: "#184c78",
					        highlight: "#184c78",
					        label: "Polobaya"
					    },
					    {
					        value: p.data[4][1][15],
					        color: "#094f48",
					        highlight: "#094f48",
					        label: "Quequeña"
					    },
					    {
					        value: p.data[4][1][16],
					        color: "#094f13",
					        highlight: "#094f13",
					        label: "Sabandia"
					    },
					    {
					        value: p.data[4][1][17],
					        color: "#4f4f09",
					        highlight: "#4f4f09",
					        label: "Sachaca"
					    },
					    {
					        value: p.data[4][1][18],
					        color: "#027c32",
					        highlight: "#027c32",
					        label: "San Juan de Siguas"
					    },
					    {
					        value: p.data[4][1][19],
					        color: "#777978",
					        highlight: "#777978",
					        label: "San Juan de Tarucani"
					    },
					    {
					        value: p.data[4][1][20],
					        color: "#af947a",
					        highlight: "#af947a",
					        label: "Santa Isabel de Siguas"
					    },
					    {
					        value: p.data[4][1][21],
					        color: "#8baf7a",
					        highlight: "#8baf7a",
					        label: "Santa Rita de Siguas"
					    },
					    {
					        value: p.data[4][1][22],
					        color: "#7a90af",
					        highlight: "#7a90af",
					        label: "Socabaya"
					    },
					    {
					        value: p.data[4][1][23],
					        color: "#9c7aaf",
					        highlight: "#9c7aaf",
					        label: "Tiabaya"
					    },
					    {
					        value: p.data[4][1][24],
					        color: "#af7a9c",
					        highlight: "#af7a9c",
					        label: "Uchumayo"
					    },
					    {
					        value: p.data[4][1][25],
					        color: " #0e6655",
					        highlight: " #0e6655",
					        label: "Vitor"
					    },
					    {
					        value: p.data[4][1][26],
					        color: "#2e4053",
					        highlight: "#2e4053",
					        label: "Yanahuara"
					    },
					    {
					        value: p.data[4][1][27],
					        color: "#000000",
					        highlight: "#000000",
					        label: "Yarabamba"
					    },
					    {
					        value: p.data[4][1][28],
					        color: "#181E4C",
					        highlight: "#181E4C",
					        label: "Yura"
					    },
					    {
					        value: p.data[4][1][29],
					        color: "#22153C",
					        highlight: "#22153C",
					        label: "José Luis Bustamante Y Rivero"
					    }*/
					],{
						legendTemplate : '<ul class=\"list-group clear-list m-t <%=name.toLowerCase()%>-legend\">'
							+'<% for (var i=0; i<segments.length; i++){%>'
								+'<li class="list-group-item">'
									+'<span style=\"background-color:<%=segments[i].fillColor%>\"></span>'
									+'<%if(segments[i].label){%><%=segments[i].label%><%}%>'
									+' (<%if(segments[i].value){%><%=segments[i].value%><%}%>)'
								+'</li>'
							+'<%}%>'
						+'</ul>'
					});
					document.getElementById('js-legend-2').innerHTML = myDoughnutChart.generateLegend();
					K.unblock();
				},'json');
			}
		});
	}
};
define(
	[],
	function(){
		return mhDash;
	}
);