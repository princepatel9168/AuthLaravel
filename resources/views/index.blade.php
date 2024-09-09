@extends('layouts.app')

@section('content')
<style>
    .slotDiv.selected {
        background-color: black;
        color: white;
    }
</style>
<?php
// echo '<pre>';print_r(session('id'));die;
function convertTo12HourFormat($hour24)
{
    // Create a DateTime object from the 24-hour format time
    $dateTime = DateTime::createFromFormat('H', $hour24);

    // Return the time in 12-hour format with AM/PM
    return $dateTime->format('g A'); // 'g' for 12-hour format without leading zero, 'A' for AM/PM
}
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
                <div class="card-header">{{ __('Book Slots') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                </div>

                <div class="card-body">
                    <!-- <div class="form-group">
                            <label>Source</label>
                            <select id="search-source" class="form-control" style="width: 100%;"></select>
                        </div>
                        <div class="form-group">
                            <label>Destination</label>
                            <select id="search-destination" class="form-control" style="width: 100%;"></select>
                        </div> -->
                    <form action="{{ route('index') }}" method="get">
                        <div class="row">
                            <div class="col-md-4">
    
                                <div class="form-group">
                                    <label>Date</label>
                                    <input type="date" name="date" class="form-control dateInput">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary" style="margin-top: 1.9rem;">Submit</button>
    
                            </div>
                        </div>
                    </form>    
                    <h5 class="text-center">Available Slots</h5>
                    <form method="POST" action="{{ route('bookSlots') }}">
                        @csrf
                        <input type="hidden" name="date" class="form-control dateInput">
                        @if($data)
                            @for($i = 0; $i < count($data); $i += 4)
                                <div class="row text-center">
                                    @for($j = 0; $j < 4; $j++)
                                        @if(isset($data[$i + $j]))
                                            <div class="col-md-2 slotDiv" data-id="{{ $data[$i + $j]->id }}" style="border: 2px solid black; border-radius: 0.6rem; margin: 10px;
                                                                    cursor:pointer">
                                                Slot {{$i + $j + 1}} <br>
                                                {{ convertTo12HourFormat($data[$i + $j]->from) }} to
                                                {{ convertTo12HourFormat($data[$i + $j]->to) }}
                                            </div>
                                        @endif
                                    @endfor
                                </div>
                            @endfor
                        @endif
                        <input type="hidden" name="slots" id="selectedSlots">
                        <!-- Hidden field to store selected slot IDs -->
                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary" style="margin-top: 1.9rem;">Book
                                Slots</button>
                                <a href="{{ route('home') }}" class="btn btn-danger" style="margin-top: 1.9rem;">Home</a>
                        </div>
                    </form>


                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
    // Get the current date in the Asia/Kolkata time zone
    const today = new Date().toLocaleString("en-GB", { timeZone: "Asia/Kolkata" }).split(',')[0];
    const [day, month, year] = today.split('/'); // Split the date in DD/MM/YYYY format
    const formattedDate = `${year}-${month}-${day}`; // Reformat to YYYY-MM-DD

    const phpDate = '{{ $todayDate }}';
    // Get all elements with class 'dateInput'
    const dateInputs = document.getElementsByClassName('dateInput');

    // Loop through the NodeList and set the value and min attribute for each element
    for (let i = 0; i < dateInputs.length; i++) {
        dateInputs[i].value = phpDate;
        dateInputs[i].setAttribute('min', formattedDate);
    }



    // script.js
    $(document).ready(function () {
        $('#search-source').select2({
            placeholder: 'Source',
            allowClear: true,
            ajax: {
                url: '/search-items', // Laravel route defined in web.php
                dataType: 'json',
                delay: 250, // Delay to throttle request
                data: function (params) {
                    return {
                        query: params.term // Search query
                    };
                },
                processResults: function (data) {
                    // Process the data into the format expected by Select2
                    return {
                        results: data
                    };
                },
                cache: true
            },
            minimumInputLength: 2 // Minimum characters required to trigger search
        });

        $('#search-destination').select2({
            placeholder: 'Destination',
            allowClear: true,
            ajax: {
                url: '/search-items', // Laravel route defined in web.php
                dataType: 'json',
                delay: 250, // Delay to throttle request
                data: function (params) {
                    return {
                        query: params.term // Search query
                    };
                },
                processResults: function (data) {
                    // Process the data into the format expected by Select2
                    return {
                        results: data
                    };
                },
                cache: true
            },
            minimumInputLength: 2 // Minimum characters required to trigger search
        });

        let selectedSlot = null;

        $('.slotDiv').click(function () {
            // Deselect the previously selected slot, if any
            if (selectedSlot) {
                $(selectedSlot).removeClass('selected');
            }

            // Toggle selection state
            if (selectedSlot !== this) {
                $(this).addClass('selected');
                selectedSlot = this; // Update the selected slot
                $('#selectedSlots').val($(this).data('id')); // Set hidden input value to the current slot ID
            } else {
                selectedSlot = null; // Reset selected slot if the same slot is clicked again
                $('#selectedSlots').val(''); // Clear hidden input value
            }
        });

    });


</script>
@endsection