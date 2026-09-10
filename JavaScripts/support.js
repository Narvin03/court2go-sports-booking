document.addEventListener("DOMContentLoaded", () => {
	const contactForm = document.getElementById("contact-form");
	const popup = document.getElementById("thankyou-popup");
	const close = document.getElementById("close-popup");


	function validateName(value, fieldId, fieldLabel) {
		if (!value.trim()) {
			document.getElementById(fieldId).textContent = `${fieldLabel} cannot be blank`;
			return false;
		}
		return true;
	}

	function validateEmail(value, fieldId) {
		const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
		if (!value.trim()) {
			document.getElementById(fieldId).textContent = "Email cannot be blank";
			return false;
		}
		if (!emailRegex.test(value)) {
			document.getElementById(fieldId).textContent = "Invalid email format";
			return false;
		}
		return true;
	}

	function validatePhone(value, fieldId) {
		const phoneRegex = /^\d{10}$/; // 10 digits
		if (!value.trim()) {
			document.getElementById(fieldId).textContent = "Phone number cannot be blank";
			return false;
		}
		if (!phoneRegex.test(value)) {
			document.getElementById(fieldId).textContent = "Invalid phone number format";
			return false;
		}
		return true;
	}

	function validateMessage(value, fieldId) {
		if (!value.trim()) {
			document.getElementById(fieldId).textContent = "Message cannot be blank";
			return false;
		}
		return true;
	}

	contactForm.addEventListener("submit", async (e) => {
		e.preventDefault();

		document.querySelectorAll(".msg").forEach((el) => (el.textContent = ""));

		const firstName = contactForm["f-name"].value;
		const lastName = contactForm["l-name"].value;
		const email = contactForm["email"].value;
		const phone = contactForm["phone"].value;
		const message = contactForm["message"].value;

		const isValid =
			validateName(firstName, "msg_fname", "First name") &&
			validateName(lastName, "msg_lname", "Last name") &&
			validateEmail(email, "msg_email") &&
			validatePhone(phone, "msg_phone") &&
			validateMessage(message, "msg_message");

		if (isValid) {
			try {
				await fetch(contactForm.action, {
					method: contactForm.method,
					body: new FormData(contactForm),
				});
			} catch (err) {
				console.warn("Submit error (ignored for popup):", err);
			}

			popup.style.display = "flex";
			popup.setAttribute("aria-hidden", "false");
			close.focus();
		}
	});

	function hidePopup() {
		popup.style.display = "none";
		popup.setAttribute("aria-hidden", "true");
		contactForm.reset();
	}

	close.addEventListener("click", hidePopup);
	popup.addEventListener("click", (e) => {
		if (e.target === popup) hidePopup();
	});
	document.addEventListener("keydown", (e) => {
		if (e.key === "Escape") hidePopup();
	});
});