jQuery(document).ready(function ($) {
   var dateTimeArray;
   try {
      dateTimeArray = JSON.parse(courtAjax.datetimes) ?? [];
   } catch (e) {
      dateTimeArray = [];
   }

   function updateDateTimeList() {
      $('#datetime-list').empty();

      dateTimeArray.forEach(function (datetime, index) {
         var parts = datetime.split(' ');
         var date = parts[0].replace(/-/g, '/').split('/').filter(function (element) {
            return element !== null && element !== undefined && element !== "";
         }).join('/');
         var time = parts[1];
         $('#datetime-list').append('<div><strong>' + date + '</strong> <span>' + time + '</span> <div><button type="button" class="remove-datetime" data-index="' + index + '">Remove</button></div></div>');
         console.log(JSON.stringify(dateTimeArray))
         $('#datetimes-field').val(JSON.stringify(dateTimeArray));
      });
   }

   updateDateTimeList();

   $('#add-datetime').on('click', function () {
      var date = $('#date-picker').val();
      var time = $('#time-picker').val();
      if (date && time) {
         const formattedDate = date.replace(/-/g, '/').split('/').reverse().filter(function (element) {
            return element !== null && element !== undefined && element !== "";
         }).join('/');

         var datetime = formattedDate + ' ' + time;
         dateTimeArray.push(datetime);
         updateDateTimeList();
      }
   });

   $(document).on('click', '.remove-datetime', function () {
      var index = $(this).data('index');
      dateTimeArray.splice(index, 1);
      updateDateTimeList();
   });
});
