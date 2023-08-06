$(document).ready(function() {
  // get current date and time
  var now = new Date();
  var year = now.getFullYear();
  var month = ('0' + (now.getMonth() + 1)).slice(-2);
  var day = ('0' + now.getDate()).slice(-2);
  var hour = ('0' + now.getHours()).slice(-2);
  var minute = ('0' + now.getMinutes()).slice(-2);
  var datetime = year + '-' + month + '-' + day + 'T' + hour + ':' + minute;

  // set default value for datetime field
  $('#datetime').attr('value', datetime);

  $.ajax({
    url: '../php/form_handle.php',
    type: 'POST',
    data: {action: 'get_cars'},
    success: function(cars) {
      cars = JSON.parse(cars);
      for (var i = 0; i < cars.length; i++) {
        $('#car').append($('<option>', {value: cars[i], text: cars[i]}));
      }
    }
  });
});

