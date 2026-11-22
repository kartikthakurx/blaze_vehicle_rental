
// Function to Open Sidebar
function openSidebar() {
    document.getElementById("sidebar").classList.add("active");
}

function closeSidebar() {
    document.getElementById("sidebar").classList.remove("active");
}



// openbox enquery
function openModal() {
    document.getElementById("enquiryModal").style.display = "flex";
}

function closeModal() {
    document.getElementById("enquiryModal").style.display = "none";
}

// Close modal when clicking outside content
window.onclick = function (event) {
    let modal = document.getElementById("enquiryModal");
    if (event.target === modal) {
        modal.style.display = "none";
    }
}

function openModal1(modalId) {
    document.getElementById(modalId).style.display = "flex";
}

// Function to Close Modal
function closeModal1(modalId) {
    document.getElementById(modalId).style.display = "none";
}