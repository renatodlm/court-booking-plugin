document.addEventListener('DOMContentLoaded', function () {
   var courtContainer = document.querySelector('.courtContainer');

   if (courtContainer !== null) {
      document.getElementById('courtForm').addEventListener('submit', function (event) {
         event.preventDefault();

         const formData = new FormData(this);
         formData.append('action', 'court_form_add_participant')

         fetch(courtAjax.url, {
            method: 'POST',
            body: formData
         })
            .then(response => response.json())
            .then(data => {
               success = data.success

               if (data.data.message) {
                  showMessage(data.data.message, success);
               }

               if (success) {
                  this.reset();

                  if (courtAjax.redirectUrl.length > 0) {
                     window.location.href = courtAjax.redirectUrl
                  }
               }
            })
            .catch(error => {
               console.error('Erro:', error);
            });
      });


      document.getElementById('sportSelect').addEventListener('change', function () {
         var sport = this.value;
         if (sport) {
            const formData = new FormData();
            formData.append('action', 'court_form_fetch_times')
            formData.append('sport', sport)

            fetch(courtAjax.url, {
               method: 'POST',
               body: formData
            })
               .then(response => response.json())
               .then(times => {
                  if (times.success && times.data) {
                     var timeSelect = document.getElementById('timeSelect');
                     timeSelect.innerHTML = '<option value="">Selecione um Horário</option>';

                     var allTimes = [];
                     Object.keys(times.data).forEach(function (courtId) {
                        allTimes = allTimes.concat(times.data[courtId]);
                     });

                     var uniqueTimes = Array.from(new Set(allTimes));

                     uniqueTimes.sort();

                     uniqueTimes.forEach(function (time) {
                        var option = new Option(time, time);
                        timeSelect.options.add(option);
                     });
                  }
                  if (!times.success && times.data.message) {
                     showMessage(times.data.message);
                  }
               });
         }
      });

      function showMessage(message, success = false) {
         var messageContainer = document.createElement('div');
         messageContainer.innerHTML = `<svg style="width:16px;height:16px" viewBox="0 0 24 24">
  <path fill="currentColor" d="M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2M11,6V13H13V6H11M11,17V15H13V17H11Z" />
</svg> <span>${message}</span>`;
         messageContainer.classList.add('floating-message');
         if (success) {
            messageContainer.classList.add('success');
         }
         document.body.appendChild(messageContainer);

         setTimeout(() => {
            messageContainer.classList.add('hide');
            setTimeout(() => messageContainer.remove(), 400);
         }, 4000);
      }
   }
});


document.addEventListener('DOMContentLoaded', function () {
   const checkbox = document.querySelector('#free');
   const select = document.querySelector('#sportSelect');

   for (let option of select.options) {
      if (this.checked) {
         option.textContent = option.textContent.replace('Clínica de ', '');
      } else {
         if (option.value.length > 0 && console.log(!option.textContent.startsWith('Clínica de '))) {
            option.textContent = 'Clínica de ' + option.textContent;
         }
      }
   }

   checkbox.addEventListener('change', function () {
      for (let option of select.options) {
         if (this.checked) {
            option.textContent = option.textContent.replace('Clínica de ', '');
         } else {
            if (option.value.length > 0 && !option.textContent.startsWith('Clínica de ')) {
               option.textContent = 'Clínica de ' + option.textContent;
            }
         }
      }
   });
});
