<div class="col-sm-12">
    <div class="col-sm-3">Device ID:</div> <div class="col-sm-9"> {{$location['device_id']}} </div>
    <div class="col-sm-3">Type:</div> <div class="col-sm-9"> {{ \App\Device::getTypeCollection()[$location['type']] }} </div>
    <div class="col-sm-3">Address:</div> <div class="col-sm-9"> {{$location['latest_location']['device_location']['address_string']}} </div>
</div>