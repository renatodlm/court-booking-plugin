jQuery(document).ready(function ($) {
   $(document).ready(function () {
      var dateTimeArray;
      try {
         dateTimeArray = JSON.parse(courtAjax.datetimes) ?? [];
      } catch (e) {
         dateTimeArray = [];
      }

      var courtTimes = $('#courtTimes').DataTable({
      });

      function updateDateTimeList() {
         courtTimes.clear();

         dateTimeArray.forEach(function (datetime, index) {
            var parts = datetime.split(' ');
            var date = parts[0].replace(/-/g, '/').split('/').filter(function (element) {
               return element !== null && element !== undefined && element !== "";
            }).join('/');
            var time = parts[1];

            courtTimes.row.add([
               `<strong>${date}</strong> <span>${time}</span>`,
               `<button type="button" class="remove-datetime" data-index="${index}">Remove</button>`
            ]);
         });

         courtTimes.draw();

         $('#datetimes-field').val(JSON.stringify(dateTimeArray));
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

      $('#courtTimes tbody').on('click', '.remove-datetime', function () {
         var index = $(this).data('index');
         dateTimeArray.splice(index, 1);
         updateDateTimeList();
      });
   });

   let courtRegister = $('#courtRegister').DataTable({
   });

   $('.remove-btn').on('click', function () {
      var row = $(this).closest('tr');
      var courtId = row.data('court-id');

      const formData = new FormData();
      formData.append('action', 'court_form_remove_participant')
      formData.append('courtId', courtId)

      fetch(courtAjax.url, {
         method: 'POST',
         body: formData
      })
         .then(response => response.json())
         .then(data => {
            console.log(data)
            if (data.success) {
               location.reload();
            }
         })
         .catch(error => {
            console.error('Erro:', error);
         });
   });

});
