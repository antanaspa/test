@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Add a device</div>

                    <div class="panel-body">

                        @if(count($errors))
                            <div class="alert alert-danger">
                                Please correct all of the fields marked as invalid.
                                <br/>
                                <ul>
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(session()->has('message'))
                            <div class="alert alert-{{session()->get('message')['type']}}">
                                {{ session()->get('message')['text'] }}
                            </div>
                        @endif

                        {!! Form::open(['action' => 'DeviceController@store']) !!}
                        <div class="form-group {{ $errors->has('type') ? 'has-error' : '' }}">
                            {{Form::label('type', 'Type')}}
                            {{ Form::select('type', \App\Device::getTypeCollection(),null, ['class' => 'form-control']) }}
                        </div>
                        <div class="form-group {{ $errors->has('lat') ? 'has-error' : '' }}">
                            {{Form::label('lat', 'Lattitude')}}

                            {!! Form::text('lat', '', ['class' => 'form-control']) !!}
                        </div>
                        <div class="form-group {{ $errors->has('lng') ? 'has-error' : '' }}">
                            {{Form::label('lng', 'Longitude')}}

                            {!! Form::text('lng', '', ['class' => 'form-control']) !!}
                        </div>
                        <div class="form-group {{ $errors->has('device_id') ? 'has-error' : '' }}">
                            {{Form::label('device_id', 'Device ID')}}

                            {!! Form::text('device_id', '', ['class' => 'form-control']) !!}
                        </div>

                        {!! Form::submit('Add', ['class' => 'form-control']) !!}
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
