class LoginForm {
    constructor() {
        this.loginForm = document.getElementById('loginForm');
        this.signupForm = document.getElementById('signupForm');
        this.init();
    }

    init() {
        if (this.loginForm) this.bindLoginEvents();
        if (this.signupForm) this.bindSignupEvents();
        this.bindForgotPassword();
    }


    bindLoginEvents() {
        this.loginForm.addEventListener('submit', (e) => this.handleLogin(e));
    }

    async handleLogin(e) {
    e.preventDefault();

    const email = document.getElementById("loginEmail").value.trim();
    const password = document.getElementById("loginPassword").value;

    if (!this.validateEmail(email) || !this.validatePassword(password)) return;

    try {
        const res = await fetch("php/login.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ email, password }),
            credentials: "include"
        });

        const data = await res.json();
        console.log("Server response (login):", data);

        if (data.success) {
            localStorage.setItem("loggedIn", "true");
            if (data.username) localStorage.setItem("username", data.username);
            if (data.user_id) localStorage.setItem("user_id", data.user_id);
            window.location.href = "main.php";
        } else {
            alert(data.message || "Login failed");
        }


    } catch (err) {
        alert("Login failed. Please try again.");
    }
}

    bindSignupEvents() {
        this.signupForm.addEventListener('submit', (e) => this.handleSignup(e));
    }

    async handleSignup(e) {
    e.preventDefault();

    const name = document.getElementById("signupName").value.trim();
    const email = document.getElementById("signupEmail").value.trim();
    const password = document.getElementById("signupPassword").value;

    if (!name) {
        alert("Name is required");
        return;
    }
    if (!this.validateEmail(email) || !this.validatePassword(password)) return;

    try {
        const res = await fetch("php/signup.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ name, email, password })
        });

        const data = await res.json();
        console.log("Server response (signup):", data);
        alert(data.message);

        if (data.message.includes("success") || data.message.includes("already")) {
            document.getElementById("flip").checked = false;

            document.getElementById("loginEmail").value = email;
        }
    } catch (err) {
        alert("Signup failed. Please try again.");
    }
}

bindForgotPassword() {
    const forgotLink = document.getElementById("forgotLink"); 
    const popup = document.getElementById("forgotPopup");
    const closeBtn = document.querySelector(".close"); 
    const submitBtn = document.getElementById("forgotSubmit");

    if (forgotLink) {
        forgotLink.addEventListener("click", (e) => {
            e.preventDefault();
            popup.style.display = "flex";  
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener("click", () => {
            popup.style.display = "none"; 
        });
    }

    if (submitBtn) {
        submitBtn.addEventListener("click", async () => {
            const email = document.getElementById("forgotEmail").value.trim();
            if (!this.validateEmail(email)) return;

            try {
                const res = await fetch("php/forgot_password.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ email })
                });
                const data = await res.json();
                alert(data.message);
                popup.style.display = "none";
            } catch (err) {
                alert("Error sending reset link.");
            }
        });
    }
}  

    validateEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!email) {
            alert("Email is required");
            return false;
        }
        if (!emailRegex.test(email)) {
            alert("Please enter a valid email address");
            return false;
        }
        return true;
    }

    validatePassword(password) {
        if (!password) {
            alert("Password is required");
            return false;
        }
        if (password.length < 6) {
            alert("Password must be at least 6 characters");
            return false;
        }
        return true;
    }
}

document.addEventListener("DOMContentLoaded", () => {
    new LoginForm();
});

