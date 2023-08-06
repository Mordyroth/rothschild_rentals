$(document).ready(function() {
    $('.update-button').on('click', function() {
        const row_id = $(this).data('row_id');
        const row = data.find(item => item.row_id === row_id);

        if (row) {
            const params = $.param({ ...row, update: 'bill_to_investor' });

            console.log('Sending car_nickname:', row.car_nickname);

            $.get('update_expenses.php', params, function(response) {
                console.log(response);
                location.reload();
            });
        } else {
            console.error('Car not found with row_id:', row_id);
        }
    });

    const modal = document.getElementById("detailsModal");
    const closeBtn = document.getElementsByClassName("close")[0];

    $('.trips-button').on('click', function () {
        const prevTripsJson = $(this).closest('tr').find('td:hidden').eq(0).html();
        const prevTrips = JSON.parse(prevTripsJson);

        const afterTripsJson = $(this).closest('tr').find('td:hidden').eq(1).html();
        const afterTrips = JSON.parse(afterTripsJson);

        let prevTripsHtml = '<h3>Previous Trips</h3>';
        prevTrips.forEach((trip, index) => {
            const highlightClass = index === 0 ? 'highlight-red' : '';

            const returnDate = new Date(trip.return_date);
            const daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            const dayOfWeek = daysOfWeek[returnDate.getDay()];
            const month = (returnDate.getMonth() + 1).toString().padStart(2, '0');
            const day = returnDate.getDate().toString().padStart(2, '0');
            let hours = returnDate.getHours();
            const minutes = returnDate.getMinutes().toString().padStart(2, '0');
            const ampm = hours >= 12 ? 'pm' : 'am';
            hours = hours % 12;
            hours = hours ? hours : 12;
            const formattedReturnDate = `<b>${dayOfWeek}</b> ${month}/${day} ${hours}:${minutes} ${ampm}`;

prevTripsHtml += `<div class="${highlightClass}"><p>Return Date: ${formattedReturnDate}</p>`;

            prevTripsHtml += `<p>Vehicle Key: ${trip.vehicle_key}</p>`;
            prevTripsHtml += `<p>Last Name: ${trip.last_name}</p>`;
            prevTripsHtml += `<p>${trip.channel}</p></div>`;
            prevTripsHtml += '<hr>';
        });


        // Display the data from the clicked row
        let clickedRowDataHtml = '<h3>Transaction Details</h3>';
        clickedRowDataHtml += `<p>` + $(this).data("car_nickname") + `</p>`;
        const transactionFormatDate = new Date($(this).data("transaction_format_date"));
        const daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        const dayOfWeek = daysOfWeek[transactionFormatDate.getDay()];
        const month = (transactionFormatDate.getMonth() + 1).toString().padStart(2, '0');
        const day = transactionFormatDate.getDate().toString().padStart(2, '0');
        let hours = transactionFormatDate.getHours();
        const minutes = transactionFormatDate.getMinutes().toString().padStart(2, '0');
        const ampm = hours >= 12 ? 'pm' : 'am';
        hours = hours % 12;
        hours = hours ? hours : 12;
        const formattedTransactionFormatDate = `<b>${dayOfWeek}</b> ${month}/${day} ${hours}:${minutes} ${ampm}`;

        clickedRowDataHtml += `<p>${formattedTransactionFormatDate}</p>`;
        clickedRowDataHtml += `<p>` + $(this).data("type") + `</p>`;
        clickedRowDataHtml += `<p>` + $(this).data("amount") + `</p>`;
        clickedRowDataHtml += `<p>` + $(this).data("location_abbr") + `</p>`;
        clickedRowDataHtml += '<hr>';


        let afterTripsHtml = '<h3>After Trips</h3>';
        afterTrips.forEach((trip, index) => {
            const highlightClass = index === 0 ? 'highlight-red' : '';

            const pickupDate = new Date(trip.pickup_date);
            const daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            const dayOfWeek = daysOfWeek[pickupDate.getDay()];
            const month = (pickupDate.getMonth() + 1).toString().padStart(2, '0');
            const day = pickupDate.getDate().toString().padStart(2, '0');
            let hours = pickupDate.getHours();
            const minutes = pickupDate.getMinutes().toString().padStart(2, '0');
            const ampm = hours >= 12 ? 'pm' : 'am';
            hours = hours % 12;
            hours = hours ? hours : 12;
            const formattedPickupDate = `<b>${dayOfWeek}</b> ${month}/${day} ${hours}:${minutes} ${ampm}`;

            afterTripsHtml += `<div class="${highlightClass}"><p>Pickup Date: ${formattedPickupDate}</p>`;

            afterTripsHtml += `<p>Vehicle Key: ${trip.vehicle_key}</p>`;
            afterTripsHtml += `<p>Last Name: ${trip.last_name}</p>`;
            afterTripsHtml += `<p>${trip.channel}</p></div>`;
            afterTripsHtml += '<hr>';
        });

        $('#prevTrips').html(prevTripsHtml + clickedRowDataHtml + afterTripsHtml);

        modal.style.display = "block";
    });

    closeBtn.onclick = function () {
        modal.style.display = "none";
    };

    window.onclick = function (event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    };
});

