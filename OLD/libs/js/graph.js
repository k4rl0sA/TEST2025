document.addEventListener("DOMContentLoaded", function() {
    google.charts.load('current', {'packages':['gauge']});
    google.charts.setOnLoadCallback(drawChart);

    let chart;
    let data;
    const options = {
        width: 400, 
        height: 400,
        greenFrom: 0, 
        greenTo: 50,
        yellowFrom: 50, 
        yellowTo: 75,
        redFrom: 75, 
        redTo: 100,
        minorTicks: 5
    };

    function drawChart() {
        data = google.visualization.arrayToDataTable([
            ['Label', 'Value'],
            ['Riesgo', 0]
        ]);

        chart = new google.visualization.Gauge(document.getElementById('chart_div'));
        updateChartData();
        setInterval(updateChartData, 5000); // Actualizar el gráfico cada 5 segundos
    }

    function updateChartData() {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState == XMLHttpRequest.DONE) {
                if (xhr.status == 200) {
                    var responseData = JSON.parse(xhr.responseText);
                    if (responseData.riesgo !== undefined) {
                        data.setValue(0, 1, responseData.riesgo);
                        chart.draw(data, options);
                    } else {
                        console.error('Error en los datos recibidos:', responseData);
                    }
                }
            }
        };
        xhr.open('POST', 'lib.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.send('a=gra&tb='+mod+'&type=radar'); // Ajusta 'tb=tb_valor' según sea necesario
    }
});

