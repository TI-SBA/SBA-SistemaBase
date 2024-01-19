reDash = {
  init: function(p) {
    if (p == null) p = {}
    K.initMode({
      mode: 're',
      action: 're',
      titleBar: {
        title: 'Dashboard de ingresos'
      }
    })
    $('#side-menu').find("li").not('.active').children("ul.in").collapse("hide")

    new K.Panel({
      contentURL: 're/repo/dashboard',
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
        K.block();
        $.post('re/repo/ingresos_inmuebles', function(data) {
          p.data = data;
          /*****************************************************************************************************
           * RECAUDACION
           *****************************************************************************************************/
          var ctx = p.$w.find("#recaudacion").get(0).getContext("2d");
          let inmuebles = p.data.anomes.data
          let datasets = [];
          for (var key in inmuebles) {
              // skip loop if the property is from prototype
              if (!inmuebles.hasOwnProperty(key)) continue;
              var o = Math.round, r = Math.random, s = 255;
              let dataset = {
                label: key,
                fillColor: 'rgba(' + o(r()*s) + ',' + o(r()*s) + ',' + o(r()*s) + ',0.2)',
                strokeColor: 'rgba(' + o(r()*s) + ',' + o(r()*s) + ',' + o(r()*s) + ',1)',
                pointColor: 'rgba(' + o(r()*s) + ',' + o(r()*s) + ',' + o(r()*s) + ',1)',
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: 'rgba(' + o(r()*s) + ',' + o(r()*s) + ',' + o(r()*s) + ',1)',
                data: Object.values(inmuebles[key]),
              }
              datasets.push(dataset);
          }
          console.log(p.data.anomes.legend)
          var data = {
            labels: Object.values(p.data.anomes.legend),
            datasets: datasets
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
          K.unblock();
        }, 'json');
      }
    });
  }
};
define(
  ['mg/enti', 'ct/pcon', 'mg/titu'],
  function() {
    return reDash;
  }
);
