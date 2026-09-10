document.addEventListener("DOMContentLoaded", () => {
	const editBtn = document.getElementById("editBtn");
	const logoutBtn = document.getElementById("logoutBtn");
	const popup = document.getElementById("editPopup");
	const popup2 = document.getElementById("deletePopup");
  const cancelBtn = document.getElementById("cancelBtn");
  const backBtn = document.getElementById("backBtn");
	const saveBtn = document.getElementById("saveBtn");

	editBtn.addEventListener("click", () => {
		popup.style.display = "block";
	});

	cancelBtn.addEventListener("click", () => {
		popup.style.display = "none";
	});

	backBtn.addEventListener("click", () => {
		popup2.style.display = "none";
	});

	saveBtn.addEventListener("click", async () => {
		const updatedData = {
			username: document.getElementById("editUsername").value.trim(),
			birthday: document.getElementById("editBirthday").value.trim(),
			bio: document.getElementById("editBio").value.trim(),
			age: document.getElementById("editAge").value.trim(),
			gender: document.getElementById("editGender").value.trim(),
			location: document.getElementById("editLocation").value.trim(),
		};

		try {
			const res = await fetch("php/update_profile.php", {
				method: "POST",
				headers: { "Content-Type": "application/json" },
				body: JSON.stringify(updatedData),
			});

			const data = await res.json();
			if (data.success) {
				alert("Profile updated successfully!");
				location.reload();
			} else {
				alert("Update failed: " + (data.message || "Unknown error"));
			}
		} catch (err) {
			console.error(err);
			alert("Error updating profile.");
		}
	});

	// Logout
	logoutBtn.addEventListener("click", async () => {
		try {
			const res = await fetch("php/logout.php", { method: "POST" });
			const data = await res.json();
			if (data.success) {
				window.location.href = "loginandregister.php";
			}
		} catch (err) {
			console.error(err);
			window.location.href = "loginandregister.php";
		}
	});
});

document.addEventListener("DOMContentLoaded", () => {
	const birthdayInput = document.getElementById("editBirthday");
	const ageInput = document.getElementById("editAge");

	birthdayInput.addEventListener("change", () => {
		const birthday = new Date(birthdayInput.value);
		if (!isNaN(birthday.getTime())) {
			const today = new Date();
			let age = today.getFullYear() - birthday.getFullYear();
			const m = today.getMonth() - birthday.getMonth();
			if (m < 0 || (m === 0 && today.getDate() < birthday.getDate())) {
				age--;
			}
			ageInput.value = age >= 0 ? age : "";
		} else {
			ageInput.value = "";
		}
	});
});

document.getElementById("avatarInput").addEventListener("change", function () {
	const formData = new FormData();
	formData.append("avatar", this.files[0]);

	fetch("php/upload_avatar.php", {
		method: "POST",
		body: formData,
	})
		.then((res) => res.json())
		.then((data) => {
			if (data.success) {
				document.getElementById("profileImage").src =
					"php/show_avatar.php?id=<?= $user['id'] ?>&t=" + new Date().getTime();
			} else {
				alert("Upload failed: " + data.message);
			}
		})
		.catch((err) => alert("Error: " + err));
});

// === Load favourite venues into the profile page ===
document.addEventListener("DOMContentLoaded", async () => {
	const container = document.getElementById("favoriteVenues");
	if (!container) return;

	try {
		const res = await fetch("php/favorites_list.php?joined=1");
		if (!res.ok) throw 0;
		const json = await res.json();
		if (!json.ok) throw 0;

		const venues = Array.isArray(json.venues) ? json.venues : [];
		if (venues.length === 0) {
			container.innerHTML =
				'<p style="color:#6b7280">No favourites yet. Tap the ★ on any venue to save it.</p>';
			return;
		}

		container.innerHTML = venues
			.map(
				(v) => `
      <article class="fav-card">
        <img src="${v.image || "./Assets/venues/default.jpg"}" alt="${
					v.name || "Venue"
				}">
        <div class="fav-body">
          <div class="fav-name">${v.name || ""}</div>
          <div class="fav-sport">${v.sport_category || ""}</div>
          <div class="fav-loc">${v.location || ""}</div>
          <div class="fav-actions">
            <a class="fav-btn" href="venue_details.php?id=${v.id}">View</a>
            <a class="fav-btn book" href="payment.php?court_id=${v.id}">Book</a>
          </div>
        </div>
      </article>
    `
			)
			.join("");
	} catch (e) {
		container.innerHTML =
			'<p style="color:#ef4444">Could not load favourites.</p>';
	}
});

function toggleFavorite(venueId) {
	fetch("php/toggle_favorite.php", {
		method: "POST",
		headers: { "Content-Type": "application/x-www-form-urlencoded" },
		body: "venue_id=" + encodeURIComponent(venueId),
	})
		.then((res) => res.json())
		.then((data) => {
			if (data.success) {
				if (data.action === "removed") {
					const row = document.querySelector(
						`.venue-row[data-id='${venueId}']`
					);
					if (row) row.remove();

					if (!document.querySelector(".venue-row")) {
						document.getElementById("favoritesList").innerHTML =
							'<p id="noFavoritesMsg" class="no-favorites-msg">Choose your favourite venue right now!</p>';
					}
				} else {
					alert("Added to favorites!");
				}
			} else {
				alert("Error: " + data.error);
			}
		})
		.catch((err) => {
			alert("Request failed: " + err);
		});
}

let currentBooking = null;

function showDeletePopup(booking) {
	currentBooking = booking;

	document.getElementById("popupImage").src = booking.image_path;
	document.getElementById("popupVenue").innerText = booking.venue_name;
	document.getElementById("popupDate").innerText = booking.booking_date;
	document.getElementById("popupTime").innerText =
		booking.start + " - " + booking.end;
	document.getElementById("popupPrice").innerText = booking.price ?? "N/A";

	document.getElementById("deletePopup").style.display = "flex";
}
document
	.getElementById("confirmDeleteBtn")
	.addEventListener("click", function () {
		const bookingId = currentBooking.id;

		fetch("php/cancel_booking.php", {
			method: "POST",
			headers: { "Content-Type": "application/x-www-form-urlencoded" },
			body: "booking_id=" + encodeURIComponent(bookingId),
		})
			.then((res) => res.json())
			.then((data) => {
				if (data.success) {
					const bookingRow = document.querySelector(
						`.booking-row[data-id='${bookingId}']`
					);
					if (bookingRow) {
						const statusDiv = bookingRow.querySelector(".booking-status");
						statusDiv.innerText = "Cancelled";
						statusDiv.classList.remove("status-booked", "status-completed");
						statusDiv.classList.add("status-cancelled");

						bookingRow.classList.add("cancelled");
						bookingRow.onclick = null;
					}

					document.getElementById("deletePopup").style.display = "none";
				} else {
					alert("Failed to cancel: " + data.error);
				}
			})
			.catch((err) => {
				alert("Error: " + err);
			});
	});
