/* ============================================================
 * Calendar
 * This is a Demo App that was created using Pages Calendar Plugin
 * We have demonstrated a few function that are useful in creating
 * a custom calendar. Please refer docs for more information
 * ============================================================ */

(function($) {

    'use strict';

    $(document).ready(function() {
        var dashboard = $('#base').val();
        var selectedEvent;
        $.ajax({
            type: "POST",
            url: admin_url + 'utilities/get_calendar_data',
            data: {
                "start":moment().subtract(18, 'year').add(0, 'hours').format(),
                "end": moment().add(10, 'year').add(0, 'hours').format()},

            dataType : "json",
            success: function(response) {
            if(response)
                {
                    if(dashboard==1)
                    {
                        var viewType="month";
                    }
                    else{
                        var viewType="week";
                    }
                    $('#myCalendar').pagescalendar({
                        events: response,
                        view:viewType,
                        onViewRenderComplete: function() {
                            //You can Do a Simple AJAX here and update
                        },
                        onEventClick: function(event) {
                            //Open Pages Custom Quick View
                            if (!$('#calendar-event').hasClass('open'))
                                $('#calendar-event').addClass('close');


                            selectedEvent = event;
                            setEventDetailsToForm(selectedEvent);
                        },
                        onEventDragComplete: function(event) {
                            selectedEvent = event;
                            setEventDetailsToForm(selectedEvent);

                        },
                        onEventResizeComplete: function(event) {
                            selectedEvent = event;
                            setEventDetailsToForm(selectedEvent);
                        },
                        onTimeSlotDblClick: function(timeSlot) {
                            $('#calendar-event').removeClass('open');
                            //Adding a new Event on Slot Double Click
                            var newEvent = {
                                title: 'my new event',
                                class: 'bg-success-lighter',
                                start: timeSlot.date,
                                end: moment(timeSlot.date).add(1, 'hour').format(),
                                allDay: false,
                                other: {
                                    //You can have your custom list of attributes here
                                    note: 'test'
                                }
                            };
                            selectedEvent = newEvent;
                            $('#myCalendar').pagescalendar('addEvent', newEvent);
                            setEventDetailsToForm(selectedEvent);
                        }
                    });
                }
                else
                {
                    console.error('There was error fetching calendar data');
                }

            }
        });



        // Some Other Public Methods That can be Use are below \
        //console.log($('body').pagescalendar('getEvents'))
        //get the value of a property
        //console.log($('body').pagescalendar('getDate','MMMM'));

        function setEventDetailsToForm(event) {
            $('#eventIndex').val();
            $('#txtEventName').val();
            $('#txtEventCode').val();
            $('#txtEventLocation').val();
            //Show Event date
            $('#event-date').html(moment(event.start).format('MMM, D dddd'));

            $('#lblfromTime').html(moment(event.start).format('h:mm A'));
            $('#lbltoTime').html(moment(event.end).format('H:mm A'));

            //Load Event Data To Text Field
            $('#eventIndex').val(event.index);
            $('#txtEventName').val(event.title);
            $('#txtEventCode').val(event.other.code);
            $('#txtEventLocation').val(event.other.location);
        }

        $('#eventSave').on('click', function() {
            selectedEvent.title = $('#txtEventName').val();

            //You can add Any thing inside "other" object and it will get save inside the plugin.
            //Refer it back using the same name other.your_custom_attribute

            selectedEvent.other.code = $('#txtEventCode').val();
            selectedEvent.other.location = $('#txtEventLocation').val();

            $('#myCalendar').pagescalendar('updateEvent',selectedEvent);

            $('#calendar-event').removeClass('open');
        });

        $('#eventDelete').on('click', function() {
            $('#myCalendar').pagescalendar('removeEvent', $('#eventIndex').val());
            $('#calendar-event').removeClass('open');
        });
    });

})(window.jQuery);