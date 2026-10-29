// ==========================================
// SmartCrop - Main JavaScript
// ==========================================


// Page loaded message
console.log("SmartCrop Website Loaded Successfully");


// ==========================================
// LANGUAGE SELECTION
// ==========================================

function changeLanguage(language) {

    if (language === "en") {
        alert("Language changed to English");
    }

    else if (language === "mr") {
        alert("भाषा मराठीमध्ये बदलली आहे");
    }

    else if (language === "hi") {
        alert("भाषा हिंदी में बदल दी गई है");
    }
    
    else if (language === "ja") {
        alert("言語が日本語に変更されました");
    }

}


// ==========================================
// COMMON BUTTON MESSAGE
// ==========================================

function showMessage(message) {
    alert(message);
}


// ==========================================
// FORM VALIDATION
// ==========================================

function validateForm() {

    const name = document.getElementById("name");
    const email = document.getElementById("email");

    if (name && name.value.trim() === "") {
        alert("Please enter your name.");
        return false;
    }

    if (email && email.value.trim() === "") {
        alert("Please enter your email.");
        return false;
    }

    return true;
}


// ==========================================
// MOBILE NAVIGATION
// ==========================================

function toggleMenu() {

    const navLinks = document.querySelector(".nav-links");

    if (navLinks) {
        navLinks.classList.toggle("active");
    }

}


// ==========================================
// CURRENT YEAR
// ==========================================

const currentYear = new Date().getFullYear();

const yearElements = document.querySelectorAll(".current-year");

yearElements.forEach(function(element) {
    element.textContent = currentYear;
});