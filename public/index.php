<?
// if(!isset($_SESSION['user_dispatch'])) header('Location: https://www.google.com/');
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>Dispatch Map</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no"/>
    <style type="text/css">
        html, body, #map-canvas {
            height: 100%;
            margin: 0;
        }
        .gm-style-iw-c {
            margin: 5px !important;
        }
        .gm-ui-hover-effect {
            margin-right: 10px !important;
            margin-top: 10px !important;
        }
    </style>
</head>
<body>

<div id="map-canvas"></div>

<script src="http://maps.google.com/maps/api/js?sensor=false&key=AIzaSyAqpzO-0Zvh3sUA18bNTUGXWGdb-6mxg9Q"></script>
<script src="js/SanMap.min.js"></script>

<script>
    var nVehicles = 0;
    var vehiclesList = [];
    var ip = "localhost:8080";
    // Сменить домен под текущий в этом месте;

    var satType = new SanMapType(0, 3, function (zoom, x, y) {
        return x == -1 && y == -1
            ? "tiles/sat.outer.png"
            : "tiles/sat." + zoom + "." + x + "." + y + ".png";
    });


    var map2Type = new SanMapType(0, 4, function (zoom, x, y) {
        return x == -1 && y == -1
            ? "tiles/sanandreas.blank.png"
            : "tiles/sanandreas." + zoom + "." + x + "." + y + ".png";
    });


    var map = SanMap.createMap(document.getElementById('map-canvas'),
        {'Спутник': satType, 'Карта дорог': map2Type}, 2, null, false, 'Спутник');

    window.onload = function(){
        var socket = new WebSocket('ws://' + ip);

        socket.onopen = function(event){ alert('Успешное подключение!'); };

        socket.onclose = function(event){
            if(event.wasClean){
                alert('Соединение закрыто!');
            }else{
                alert('Связь потеряна....');
            }
        };

        socket.onmessage = function(event){
            var data = JSON.parse(event.data);
            if (Object.keys(data).length !== nVehicles) {
                nVehicles = Object.keys(data).length;
                for (var entry in vehiclesList) {
                    if (entry.indexOf("_marker") !== -1) {
                        vehiclesList[entry].setMap(null);
                    }
                }
                vehiclesList = [];
            }
            for (var i in data) {
                (function(key) {
                    if (data.hasOwnProperty(key)) {

                        if (typeof vehiclesList[key + '_marker'] == 'undefined') {
                            vehiclesList[key + '_marker'] = new google.maps.Marker({
                                position: SanMap.getLatLngFromPos(data[key]['x'], data[key]['y']),
                                map: map,
                                icon: {
                                    path: google.maps.SymbolPath.CIRCLE,
                                    scale: 10.5,
                                    fillColor: "#F00",
                                    fillOpacity: 0.4,
                                    strokeWeight: 0.8
                                },
                            });
                            vehiclesList[key + '_window'] = new google.maps.InfoWindow({
                                content: '<b>' + data[key]['title'] + '</b><br>' + data[key]['description']
                            });
                            vehiclesList[key + '_event'] = google.maps.event.addListenerOnce(vehiclesList[key + '_marker'], 'click', function() {
                                vehiclesList[key + '_window'].open(map, vehiclesList[key + '_marker']);
                            });
                        }

                        vehiclesList[key + '_marker'].setPosition(
                            SanMap.getLatLngFromPos(data[key]['x'], data[key]['y'])
                        );
                    }
                })(i);
            }
        }


        socket.onerror = function(event){
            alert('Ошибка: ' + event.message);
        };
    };

</script>
</body>