tiDash = {
  init: function(p) {
    if (p == null) p = {}
    K.initMode({
      mode: 'ti',
      action: 'ti',
      titleBar: {
        title: 'Resumen de los Sistemas'
      }
    });
    $('#side-menu').find('li').not('.active').children('ul.in').collapse('hide')

    new K.Panel({
      contentURL: 'ti/sist',
      store: false,
      buttons: {
        'Actualizar Informaci&oacute;n': {
          icon: 'fa-refresh',
          type: 'success',
          f: function() {
            dashboard.init();
          }
        }
      },
      onContentLoaded: function() {
        p.$w = $('#mainPanel');
        new K.grid({
          $el: p.$w.find('[name=gridInhu]'),
          search: false,
          pagination: false,
          cols: ['Fecha', 'Fallecido a Enterrar', 'Funeraria', 'Ingreso'],
          onlyHtml: true
        });
        K.block();
        $.post('ti/sist/get_dash', function(data) {
          p.data = data;
          /*****************************************************************************************************
           * SESIONES
           *****************************************************************************************************/
          var ctx = p.$w.find("#sesiones").get(0).getContext("2d");
          var data = {
            labels: p.data.sesiones.legend,
            datasets: [{
                label: "Total",
                fillColor: "rgba(220,220,220,0.2)",
                strokeColor: "rgba(220,220,220,1)",
                pointColor: "rgba(220,220,220,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(220,220,220,1)",
                data: p.data.sesiones.total,
              },
              /*{
                  label: "Alquileres",
                  fillColor: "rgba(151,187,205,0.2)",
                  strokeColor: "rgba(151,187,205,1)",
                  pointColor: "rgba(151,187,205,1)",
                  pointStrokeColor: "#fff",
                  pointHighlightFill: "#fff",
                  pointHighlightStroke: "rgba(151,187,205,1)",
                  data: p.data.recaudacion.alquileres
              },
              {
                  label: "Playas",
                  fillColor: "rgba(130,140,205,0.2)",
                  strokeColor: "rgba(100,110,205,1)",
                  pointColor: "rgba(130,140,205,1)",
                  pointStrokeColor: "#fff",
                  pointHighlightFill: "#fff",
                  pointHighlightStroke: "rgba(100,110,205,1)",
                  data: p.data.recaudacion.playas
              }*/
            ]
          };
          var lineChart = new Chart(ctx).Line(data, {
            legendTemplate: '<ul class="list-group clear-list m-t">' +
              '<% for (var i=0; i<datasets.length; i++) { %>' +
              '<li class="list-group-item">' +
              '<span style=\"background-color:<%=datasets[i].strokeColor%>\"></span>' +
              '<% if (datasets[i].label) { %><%= datasets[i].label %><% } %>' +
              '</li>' +
              '<% } %>' +
              '</ul>'
          });
          document.getElementById('js-legend-1').innerHTML = lineChart.generateLegend();
          /*****************************************************************************************************
           * EXPEDIENTES
           *****************************************************************************************************/
          /*var ctx = p.$w.find("#expedientes").get(0).getContext("2d");
          var myDoughnutChart = new Chart(ctx).Doughnut([
          	{
                  value: p.data.expedientes[0],
                  color:"#F7464A",
                  highlight: "#FF5A5E",
                  label: "Concluidos"
              },
              {
                  value: p.data.expedientes[1],
                  color: "#46BFBD",
                  highlight: "#5AD3D1",
                  label: "Pendientes"
              }
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
          document.getElementById('js-legend-2').innerHTML = myDoughnutChart.generateLegend();*/
          /*****************************************************************************************************
           * INHUMACIONES DE LOS PROXIMOS TRES DIAS
           *****************************************************************************************************/
          /*if(p.data.inhumaciones!=null){
          	for(var i=0; i<p.data.inhumaciones.length; i++){
          		var inhumacion = p.data.inhumaciones[i],
          		$row = $('<tr class="item">');
          		$row.append('<td>'+ciHelper.date.format.bd_ymdhi(inhumacion.programacion.fecprog)+'</td>');
          		$row.append('<td>'+mgEnti.formatName(inhumacion.ocupante)+'</td>');
          		$row.append('<td>'+mgEnti.formatName(inhumacion.inhumacion.funeraria)+'</td>');
          		$row.append('<td>'+inhumacion.inhumacion.puerta+'</td>');
          		p.$w.find('[name=gridInhu] tbody').append($row);
          	}
          }*/
          K.unblock();
        }, 'json');
      }
    });
  }
};
define(
  //['mg/enti','ct/pcon','mg/titu'],
  /*function(mgEnti,ctPcon,mgTitu){
  	return tiDash;
  }*/
  function() {
    return tiDash;
  }
);