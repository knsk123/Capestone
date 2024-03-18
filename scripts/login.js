
function toggleLogin() {
    document.getElementById("login-form").style.display = "block";
    document.getElementById("signup-form").style.display = "none";
    document.getElementById("login-toggle").classList.add("active");
    document.getElementById("signup-toggle").classList.remove("active");
}

function toggleSignup() {
    document.getElementById("signup-form").style.display = "block";
    document.getElementById("login-form").style.display = "none";
    document.getElementById("signup-toggle").classList.add("active");
    document.getElementById("login-toggle").classList.remove("active");
}
