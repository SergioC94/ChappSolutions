$(document).ready(function () {
    /**
     * Show dateranger with its configuration
     * startDate: start value ('YYYY-MM-DD)
     * endDate: end value ('YYYY-MM-DD)
     * local: date format
     * isInvalidDate: Check if the date is invalid
     */
    $('input[name="daterange"]').daterangepicker({
      startDate: moment(),
      endDate: moment().add(1, 'days'),
      locale: {format: 'YYYY-MM-DD'},
      opens: 'left',
      isInvalidDate: function(date){
          if(date.year() > "2022" || date < moment().subtract(1,'days')){
              return true;
          }
      }
    },);

    /**
     * Functionality of the search button.
     * ajax request with dates and number of guests
     * Success: Return a room table
     */
    $('#SearchButton').click(function(e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "/room/new",
            data:{
                daterange: $('input[name="daterange"]').val(),
                guests: $('#SelectGuests').val()
            },
            success: function(result) {
                $('#RoomsList').html(result);
            },
            error: function(result) {
                console.log(result)
            }
        });
    });
});