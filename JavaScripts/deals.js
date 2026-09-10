document.addEventListener("DOMContentLoaded", function () {
	document.querySelectorAll(".deal-card[data-venue]").forEach(function (card) {
		card.style.cursor = "pointer";
		card.addEventListener("click", function (e) {
			// Prevent double navigation if clicking the button
			if (e.target.classList.contains("view-venue")) return;
			var venueId = card.getAttribute("data-venue");
			if (venueId) {
				window.location.href = "venue_details.php?id=" + venueId;
			}
		});
	});
});
