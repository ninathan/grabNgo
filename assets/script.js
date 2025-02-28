// document.addEventListener("DOMContentLoaded", function () {
//     const sidebar = document.querySelector(".sidebar");
//     const toggleBtn = document.querySelector(".toggle-btn");
//     const mainContent = document.querySelector(".main-content");
//     const linkTexts = document.querySelectorAll(".link-text");

//     toggleBtn.addEventListener("click", function () {
//         sidebar.classList.toggle("collapsed");

//         if (sidebar.classList.contains("collapsed")) {
//             sidebar.style.width = "80px";
//             mainContent.style.marginLeft = "80px";
//             linkTexts.forEach(text => text.style.display = "none");
//             toggleBtn.innerHTML = '<i class="fas fa-chevron-right"></i>';
//         } else {
//             sidebar.style.width = "250px";
//             mainContent.style.marginLeft = "250px";
//             linkTexts.forEach(text => text.style.display = "inline");
//             toggleBtn.innerHTML = '<i class="fas fa-chevron-left"></i>';
//         }
//     });
// });

$(document).ready(function () {
    function loadOrders() {
        $.ajax({
            url: "fetch_orders.php",
            type: "GET",
            dataType: "json",
            success: function (response) {
                if (response.status === "success") {
                    $(".order-grid").html(response.html);
                } else if (response.status === "empty") {
                    $(".order-grid").html("<p>No orders found.</p>");
                } else {
                    $(".order-grid").html("<p>Error loading orders.</p>");
                }
            },
            error: function () {
                $(".order-grid").html("<p>Error fetching data.</p>");
            }
        });
    }

    // Load orders on page load
    loadOrders();

    // Refresh orders every 5 seconds
    setInterval(loadOrders, 5000);
});


function updateQuantity(button, change) {
    let input = button.parentElement.querySelector('.quantity-input');
    let currentValue = parseInt(input.value);

    let newValue = currentValue + change;
    if (newValue < 1) newValue = 1; // Prevent negative values

    input.value = newValue;
}


