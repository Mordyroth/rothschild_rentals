$(document).ready(function() {
  getCarList();
});

function getCarList() {
  $.ajax({
    url: "../php/index.php",
    type: "GET",
    dataType: "json",
    success: function(data) {
      displayCarList(data);
    },
    error: function(jqXHR, textStatus, errorThrown) {
    }
  });
}

function displayCarList(carList) {
  // Split carList into two arrays based on location
  var homeCars = [];
  var awayCars = [];
  for (var i = 0; i < carList.length; i++) {
    var car = carList[i];
    if (car.location === "home") {
      homeCars.push(car);
    } else {
      awayCars.push(car);
    }
  }

  // Sort homeCars array by battery percentage
  homeCars.sort(function(a, b) {
    return a.battery - b.battery;
  });

  // Concatenate the two arrays back together
  var sortedCarList = homeCars.concat(awayCars);

  // Generate HTML for the sorted car list
  var carListHTML = "";
  for (var i = 0; i < sortedCarList.length; i++) {
    var car = sortedCarList[i];
     const first = car.name.split(' ')[0];
     const vehicleId = car.vehicleId;
    carListHTML += "<div class='w3-cell-row'>";
      carListHTML += "<div class='w3-cell' style='width:30%'>";
        carListHTML += "<img class='w3-circle' src=" + "../images/" + first + ".png" + " style='width:100%'>";
      carListHTML += "</div>";
      carListHTML += '<div class="w3-cell w3-container">';
        carListHTML += '<h3>' + car.name + '</h3>';
          carListHTML += '<h2 style="color: blue;">' + car.battery + '</h2>';
          carListHTML += "<p class='location'>" + car.location + "</p>";
          if (car.charging_state !== "Disconnected") {
            carListHTML += "<p class='charging-state'>" + car.charging_state + "</p>";
            carListHTML += "<p class='time-to-full'>Time to Full:" + car.time_to_full + "</p>";
            carListHTML += "<p class='amps'>" + car.amps + " Amps<span class='kw'>" + car.kw + " KW</span></p>";
            carListHTML += "<img class='bolt-icon' data-vehicle-id='" + car.id + "' src='../images/bolt.svg'>";
          }
          // Inside the displayCarList function
          carListHTML += car.locked ? "<img class='lock-icon' data-vehicle-id='" + car.id + "' src='../images/locked.svg'>" : "<img class='lock-icon' data-vehicle-id='" + car.id + "' src='../images/unlocked.svg'>";
           
          carListHTML += "<img class='spinner' data-vehicle-id='" + car.id + "' src='../images/spinner.gif' style='display: none;'>"; // Add this line to include the spinner HTML
      carListHTML += "</div>";
    carListHTML += "</div>";
    carListHTML += "<hr>";
  }
  //carListHTML += "";
  $("#car-list").html(carListHTML);
  //console.log(carListHTML);
  $('.location').each(function() {
  // Do something with each element with class "location"
      if ($(this).text() === 'home') {
        //$(this).parent().parent().sibling().css('background-color', 'lightgreen');
        $(this).parent().parent().find(".w3-circle").css('background-color', 'lightgreen');

      }
      $(this).hide();
  });

  // Inside the displayCarList function, after $("#car-list").html(carListHTML);
  $(".lock-icon").on("click", function() {
    const vehicleId = $(this).data("vehicle-id");
    const isLocked = $(this).attr("src") === "../images/locked.svg";
    toggleLock(vehicleId, isLocked);
  });

  $(".bolt-icon").on("click", function() {
    const vehicleId = $(this).data("vehicle-id");
    unlockChargePort(vehicleId);
  });
}

function toggleLock(vehicleId, isLocked) {
  console.log(vehicleId);
  const action = isLocked ? "unlock" : "lock";
  const lockIcon = $(".lock-icon[data-vehicle-id='" + vehicleId + "']");
  const spinner = $(".spinner[data-vehicle-id='" + vehicleId + "']");

  // Hide the lock icon and show the spinner
  lockIcon.hide();
  spinner.show();

  $.ajax({
    url: "../php/lock_unlock.php",
    type: "POST",
    data: { id: vehicleId, action: action },
    success: function(data) {
      // Wait for two seconds before reloading the page
      setTimeout(function() {
        location.reload(); // Reload the page to show the updated lock status
      }, 4000);
    },
    error: function(jqXHR, textStatus, errorThrown) {
      console.log(jqXHR.responseText);
      // Hide the spinner and show the lock icon
      spinner.hide();
      lockIcon.show();
    }
  });
}

function unlockChargePort(vehicleId, isLocked) {
  console.log(vehicleId);
  const action = "open_charge_port";
  const boltIcon = $(".bolt-icon[data-vehicle-id='" + vehicleId + "']");
  const spinner = $(".spinner[data-vehicle-id='" + vehicleId + "']");

  // Hide the lock icon and show the spinner
  boltIcon.hide();
  spinner.show();

  $.ajax({
    url: "../php/lock_unlock.php",
    type: "POST",
    data: { id: vehicleId, action: action },
    success: function(data) {
      // Wait for two seconds before reloading the page
      setTimeout(function() {
        location.reload(); // Reload the page to show the updated lock status
      }, 4000);
    },
    error: function(jqXHR, textStatus, errorThrown) {
      console.log(jqXHR.responseText);
      // Hide the spinner and show the lock icon
      spinner.hide();
      boltIcon.show();
    }
  });
}









