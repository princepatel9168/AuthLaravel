@extends('layouts.app')

@section('content')
<?php 
// $days = ['Monday'];
$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturnday', 'Sunday'];
?>
@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <!-- <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('You are logged in!') }}
                </div> -->

                <div>
                    @foreach ($days as $singleDay)
                        <form action="{{ route('addslot') }}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-md-3">{{$singleDay}}</div>
                                <input type="hidden" name="day" value="{{$singleDay}}">
                                <div class="col-md-3">
                                    <select name="fromslot" id="fromslot">
                                        @foreach($timeSlots as $slot)
                                            <?php //if(isset($fromDisableSlot[$singleDay]) && !in_array($slot['slot'],$fromDisableSlot[$singleDay])){ ?>
                                            <option value="{{$slot['slot']}}">{{$slot['slot']}}:00</option>
                                            <?php //} ?>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="toslot" id="toslot">
                                        @foreach($timeSlots as $slot)
                                            <?php //if(isset($toDisableSlot[$singleDay]) && !in_array($slot['slot'],$toDisableSlot[$singleDay])){ ?>
                                            <option value="{{$slot['slot']}}">{{$slot['slot']}}:00</option>
                                            <?php //} ?>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-success">Submit</button>
                                </div>
                            </div>
                        </form><br>
                    @endforeach
                    <div class="row">
                        <a href="{{ route('home') }}" class="btn btn-danger">Home</a>
                    </div>
                    <!-- <div class="row">

                    </div> -->
                </div>
            </div>
        </div>
    </div>
</div>


@endsection