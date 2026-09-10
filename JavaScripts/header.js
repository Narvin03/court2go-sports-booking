document.addEventListener("DOMContentLoaded", function () {
  const burger = document.getElementById("burger-menu");
  const sidebar = document.getElementById("sidebar-nav");

  console.log("burger:", burger, "sidebar:", sidebar);

  if (burger && sidebar) {
    burger.addEventListener("click", function () {
      console.log("burger clicked!");
      sidebar.classList.toggle("active");
      console.log(sidebar.className); 
    });
    // Optional: close sidebar when clicking outside
    document.addEventListener("click", function (e) {
      if (
        sidebar.classList.contains("active") &&
        !sidebar.contains(e.target) &&
        e.target !== burger
      ) {
        sidebar.classList.remove("active");
      }
    });
  }
});