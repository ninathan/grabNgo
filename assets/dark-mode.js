// document.addEventListener("DOMContentLoaded", function () {
//     const toggleButton = document.getElementById("darkModeToggle");
//     const body = document.body;
//     console.log("Dark mode script is running!");


//     // Check if dark mode was previously enabled
//     if (localStorage.getItem("darkMode") === "enabled") {
//         body.classList.add("dark-mode");
//     }

//     toggleButton.addEventListener("click", function () {
//         body.classList.toggle("dark-mode");

//         // Save user preference in localStorage
//         if (body.classList.contains("dark-mode")) {
//             localStorage.setItem("darkMode", "enabled");
//         } else {
//             localStorage.setItem("darkMode", "disabled");
//         }
//     });
// });
