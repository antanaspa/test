@extends('layouts.app')

@section('content')

    <script src="https://maps.googleapis.com/maps/api/js?key={!! Config::get('settings.google_api_key') !!}&callback=initialize" async defer></script>
    <script type="text/javascript">

        var markerCount = 0;
        var markers = [];
        var map;

        function initialize() {
            var myLatlng = new google.maps.LatLng({!! Config::get('settings.default_lat_lng') !!});
            var map_canvas = document.getElementById('map');
            var map_options = {
                center: myLatlng,
                zoom: 5,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            }

            map = new google.maps.Map(map_canvas, map_options);
        }

        function removeMarkers() {
            for (var i = 0; i < markers.length; i++ ) {
                markers[i].setMap(null);
            }
            markers.length = 0;
        }


        function addMarkerToMap(lat, long, htmlMarkupForInfoWindow){
            var infowindow = new google.maps.InfoWindow();
            var myLatLng = new google.maps.LatLng(lat, long);
            var marker = new google.maps.Marker({
                position: myLatLng,
                map: map,
            });

            markerCount++;

            google.maps.event.addListener(marker, 'click', (function(marker, markerCount) {
                return function() {
                    infowindow.setContent(htmlMarkupForInfoWindow);
                    infowindow.open(map, marker);
                }
            })(marker, markerCount));
            markers.push(marker);
            map.panTo(myLatLng)
        }

        $(document).ready(function() {
            var selectForm = $('#selectDevice');

            var $selectDevice=$("#select2");

            var selectInstance = $selectDevice.select2();


            $selectDevice.on('select2:opening', function () {
                $.get("{{action("DeviceController@getList")}}",null, function(response) {
                    $.each(response.data.device, function(i, item) {
                        if (!$selectDevice.find("option[value='" + item.id + "']").length) {

                            var newOption = new Option(item.device_id, item.id, false, false);
                            $selectDevice.append(newOption).trigger('change');

                        }
                    });
                });

            });


            $selectDevice.on('change', function () {
                refreshMap();
            });

            function refreshMap() {
                $('#largestDistance').html('');

                $.post("{{action("DeviceController@getLocation")}}",selectForm.serialize(), function(response){
                    if(typeof response.success !== "undefined" && response.success) {
                        removeMarkers();
                        $.each(response.data.device, function(i, item) {
                            var $coord = item.latest_location.device_location.location.coordinates;
                            addMarkerToMap($coord[1], $coord[0], item.popupContent);
                        });
                        if (markers.length > 1) {
                            var bounds = new google.maps.LatLngBounds();
                            for (var i = 0; i < markers.length; i++) {
                                bounds.extend(markers[i].getPosition());
                            }
                            map.setCenter(bounds.getCenter());
                            map.fitBounds(bounds);
                        }

                        if (typeof response.data.distance.from !== 'undefined') {
                            $('#largestDistance').html(response.data.distance.device + ' : ' + response.data.distance.from + ' = ' + response.data.distance.value + ' km');
                        }
                    }
                });
            }



            window.setInterval(function(){
                refreshMap();
            }, 5000);


        });

    </script>



        <div class="container-full">
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-3">
                    {!! Form::open(['action' => 'DeviceController@getLocation', 'id' => 'selectDevice']) !!}
                        {{ Form::label('device', 'Select device(s) to show:') }}
                        {{ Form::select('device[]', $pointCollection, null, ['id' => 'select2', 'class' => 'form-control', 'multiple' => 'multiple']) }}
                    {!! Form::close() !!}

                    Devices with largest distance between them:
                    <div id="largestDistance"></div>
                </div>
                <div class="col-md-9 zeropad" style="height: 500px;">
                    <div id="map"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
