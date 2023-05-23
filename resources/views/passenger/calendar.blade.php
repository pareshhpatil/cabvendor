@extends('layouts.app')
@section('content')
<style>
    body {
        display: -webkit-box;
        display: -webkit-flex;
        display: -moz-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-pack: center;
        -webkit-justify-content: center;
        -moz-box-pack: center;
        -ms-flex-pack: center;
        justify-content: center;
        -webkit-box-align: center;
        -webkit-align-items: center;
        -moz-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        -webkit-align-content: center;
        -ms-flex-line-pack: center;
        align-content: center;
    }

    #calendarContainer,
    #organizerContainer {
        float: left;
        margin: 5px;
    }
</style>
<link rel='stylesheet' href='/assets/css/calendar.css'>


<div id="appCapsule" class="full-height">
    <div id="app" class="section tab-content mb-1">
        <div id="calendarContainer"></div>
    </div>
    <div id="app" class="section tab-content mb-1">
    <div id="organizerContainer"></div>
    </div>

</div>

</div>
@endsection

@section('footer')
<script src='/assets/js/calendar.js'></script>

<script>
    "use strict";

    // function that creates dummy data for demonstration
    function createDummyData() {
        var date = new Date();
        var data = {};

        for (var i = 0; i < 10; i++) {
            data[date.getFullYear() + i] = {};

            for (var j = 0; j < 12; j++) {
                data[date.getFullYear() + i][j + 1] = {};

                for (var k = 0; k < Math.ceil(Math.random() * 10); k++) {
                    var l = Math.ceil(Math.random() * 28);

                    try {
                        data[date.getFullYear() + i][j + 1][l].push({
                            startTime: "10:00",
                            endTime: "12:00",
                            text: "Some Event Here"
                        });
                    } catch (e) {
                        data[date.getFullYear() + i][j + 1][l] = [];
                        data[date.getFullYear() + i][j + 1][l].push({
                            startTime: "10:00",
                            endTime: "12:00",
                            text: "Some Event Here"
                        });
                    }
                }
            }
        }

        return data;
    }

    // creating the dummy static data
    var data = createDummyData();
    console.log(data);

    // initializing a new calendar object, that will use an html container to create itself
    var calendar = new Calendar(
        "calendarContainer", // id of html container for calendar
        "small", // size of calendar, can be small | medium | large
        [
            "Wednesday", // left most day of calendar labels
            3 // maximum length of the calendar labels
        ],
        [
            "#e8481e", // primary color
            "#161129", // primary dark color
            "#FFFFFF", // text color
            "#FFFFFF" // text dark color
        ]
    );

    // initializing a new organizer object, that will use an html container to create itself
    var organizer = new Organizer(
        "organizerContainer", // id of html container for calendar
        calendar, // defining the calendar that the organizer is related to
        data // giving the organizer the static data that should be displayed
    );
</script>


@endsection