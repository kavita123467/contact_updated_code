document.addEventListener('DOMContentLoaded', function() {
  const form = document.querySelector('form');
  const errorContainer = document.getElementById('error-messages');
  const refreshButton = document.getElementById('refresh-captcha');

  if (form) {
      form.addEventListener('submit', function(event) {
          event.preventDefault();

          const formData = new FormData(form);
          const jsonData = Object.fromEntries(formData);

          // Correctly retrieve date and time from sessionStorage
          const date = sessionStorage.getItem('Date'); // Note the capital 'D'
          const time = sessionStorage.getItem('timeslot'); // Changed from 'time' to 'timeslot'

          console.log('Date:', date);
          console.log('Time:', time);

          const appointment = {
              date: date,
              time: time
          };

          // Add the appointment object to jsonData
          jsonData.appointment = appointment;

          fetch('check_availability.php', {
              method: 'POST',
              headers: {
                  'Content-Type': 'application/json'
              },
              body: JSON.stringify(jsonData)
          })
          .then(response => response.json())
          .then(data => {
              if (data.success) {
                  console.log("Appointment booked successfully!");
                  window.location.href = 'success_page.php'; // Optional: redirect to a success page
              } else if (data.errors) {
                  errorContainer.innerHTML = ''; // Clear previous errors
                  data.errors.forEach(error => {
                      const errorElement = document.createElement('p');
                      errorElement.textContent = error;
                      errorElement.style.color = 'red';
                      errorContainer.appendChild(errorElement);
                  });
              }
          })
          .catch(error => {
              console.error('Error sending data:', error);
          });
      });
  } else {
      console.error('Form not found'); // Log error if form is not found
  }

  if (refreshButton) {
      refreshButton.addEventListener('click', function() {
          fetch('refresh_captcha.php') // Ensure this points to your PHP file that generates the captcha
              .then(response => response.text()) // Expecting HTML response
              .then(data => {
                  document.querySelector('label[for="captcha"]').innerHTML = 'Captcha: ' + data;
              })
              .catch(error => console.error('Error refreshing captcha:', error));
      });
  }

  const navigationEntries = performance.getEntriesByType('navigation');
  if (navigationEntries.length > 0 && navigationEntries[0].type === 'reload') {
      console.log("Page reloaded");
  } else {
      console.info("This page is not reloaded");
  }
});

// Add this function to save date and time to sessionStorage
function saveTimeInSessionStorage() {
  const selectedDate = document.getElementById('date');
  const selectedTime = document.getElementById('selected-time');
  sessionStorage.setItem('Date', selectedDate.value);
  sessionStorage.setItem('timeslot', selectedTime.value);
}

// Make sure to call this function when a date and time are selected
document.getElementById('submit-btn').addEventListener('click', saveTimeInSessionStorage);