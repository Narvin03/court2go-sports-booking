document.addEventListener("DOMContentLoaded", function () {
	// Inject sports into dropdown
	const dropdownList = document.getElementById("venue-dropdown-list");
	dropdownList.innerHTML =
		`<li data-value="">Everything</li>` +
		sportsData
			.map((sport) => `<li data-value="${sport}">${sport}</li>`)
			.join("");

	// Venue list element
	const venueList = document.getElementById("venue-list");

	// Favorites logic
	const FAVORITES = { ids: new Set(), loaded: false };

	async function loadFavoriteIds() {
		try {
			const res = await fetch("./php/favorites_list.php");
			if (!res.ok) throw new Error("Failed to fetch favorites");
			const json = await res.json();
			if (json.ok && Array.isArray(json.ids)) {
				FAVORITES.ids = new Set(json.ids.map(Number));
				FAVORITES.loaded = true;
				syncFavoriteStars();
			}
		} catch (e) {
			console.error("Error loading favorites:", e);
		}
	}

	async function toggleFavoriteOnServer(venueId) {
		try {
			const star = document.querySelector(
				`.fav-star[data-venue-id="${venueId}"]`
			);
			if (!star) throw new Error("Favorite star not found");

			star.style.pointerEvents = "none";

			const res = await fetch("./php/favorites_toggle.php", {
				method: "POST",
				headers: { "Content-Type": "application/json" },
				body: JSON.stringify({ venue_id: venueId }),
			});

			const json = await res.json();
			if (!json.ok) throw new Error("Failed to toggle favorite on server");

			if (json.is_favorited) {
				FAVORITES.ids.add(venueId);
			} else {
				FAVORITES.ids.delete(venueId);
			}

			star
				.querySelector(".fav-icon")
				.classList.toggle("active", json.is_favorited);
			star.style.pointerEvents = "";
			return json.is_favorited;
		} catch (err) {
			console.error("Error toggling favorite:", err);
			alert("Could not update favorites. Please try again.");
		}
	}

	function syncFavoriteStars() {
		document.querySelectorAll(".fav-star[data-venue-id]").forEach((star) => {
			const id = Number(star.dataset.venueId);
			const on = FAVORITES.ids.has(id);
			star.querySelector(".fav-icon").classList.toggle("active", on);
			star.title = on ? "Remove from favourites" : "Add to favourites";
		});
	}

	// Render venues with favorite and action buttons
	function renderVenues(venues) {
		venueList.innerHTML = venues
			.map(
				(venue) => `
            <div class="card venue">
                <img src="${
									venue.image ? venue.image : "./Assets/venues/default.jpg"
								}" alt="${venue.name}">
                <div class="card-content">
                    <div>
                        <div class="card-title-row" style="display:flex; align-items:center; justify-content:space-between;">
                            <div class="card-title">${venue.name}</div>
                            <span class="fav-star" data-venue-id="${
															venue.id
														}" title="Toggle favourite">
                                <svg class="fav-icon${
																	FAVORITES.loaded &&
																	FAVORITES.ids.has(venue.id)
																		? " active"
																		: ""
																}" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                </svg>
                            </span>
                        </div>
                        <div class="card-sport"><span class="sport-label">Sport:</span><span class="sport-value">${
													venue.sport_category
												}</span></div>
                        <div class="card-desc">${venue.description}</div>
                        <div style="font-size:13px; color:#b6c7e6; margin-left:12px;">${
													venue.location
												}</div>
                    </div>
                    <div class="card-buttons">
                        <a href="venue_details.php?id=${
													venue.id
												}" class="card-btn view"><i class="fa-regular fa-eye"></i> View</a>
                        <a href="payment.php?court_id=${
													venue.id
												}" class="card-btn book">Book Now</a>
                    </div>
                </div>
            </div>
        `
			)
			.join("");
		// Add click event for star
		document.querySelectorAll(".fav-star").forEach((star) => {
			star.onclick = async function () {
				const venueId = Number(this.dataset.venueId);
				const isFav = await toggleFavoriteOnServer(venueId);
				this.querySelector(".fav-icon").classList.toggle("active", isFav);
			};
		});
	}

	// Initial render
	renderVenues(venuesData);
	loadFavoriteIds();

	// Dropdown logic
	const dropdownBtn = document.getElementById("venue-dropdown-toggle");
	const dropdownSelected = document.getElementById("venue-dropdown-selected");
	let selectedSport = "";

	dropdownBtn.addEventListener("click", function (e) {
		e.stopPropagation();
		dropdownList.style.display =
			dropdownList.style.display === "block" ? "none" : "block";
	});

	document.addEventListener("click", function () {
		dropdownList.style.display = "none";
	});

	function attachDropdownListeners() {
		dropdownList.querySelectorAll("li").forEach(function (item) {
			item.onclick = function () {
				dropdownSelected.textContent = item.textContent;
				selectedSport = item.getAttribute("data-value");
				dropdownList.style.display = "none";
				filterVenues();
			};
		});
	}
	attachDropdownListeners();
	dropdownBtn.addEventListener("click", attachDropdownListeners);

	// Filtering logic
	const searchInput = document.getElementById("search-input");
	const searchBtn = document.querySelector(".venue-search-btn");

	function filterVenues() {
		const sport = selectedSport.trim().toLowerCase();
		const query = searchInput.value.trim().toLowerCase();

		const filtered = venuesData.filter((venue) => {
			const venueSport = (venue.sport_category || "").toLowerCase();
			const venueName = (venue.name || "").toLowerCase();
			const venueLocation = (venue.location || "").toLowerCase();

			let show = true;
			if (sport && sport !== "everything" && !venueSport.includes(sport))
				show = false;
			if (
				query &&
				!(venueName.includes(query) || venueLocation.includes(query))
			)
				show = false;
			return show;
		});
		renderVenues(filtered);
		attachDropdownListeners();
	}

	searchBtn.addEventListener("click", function (e) {
		e.preventDefault();
		filterVenues();
	});

	searchInput.addEventListener("keydown", function (e) {
		if (e.key === "Enter") {
			e.preventDefault();
			filterVenues();
		}
	});

	searchInput.addEventListener("input", filterVenues);
});
