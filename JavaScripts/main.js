console.log("JS loaded");
function mainBtn() {
  window.location.href = "main.php";
}

document.addEventListener("DOMContentLoaded", () => {
  function setLogoutUI() {

    let authLink = document.getElementById("logIn-btn");
    if (!authLink) return;

    authLink.id = "logout-btn";
    authLink.href = "#";
    authLink.innerHTML = '<i class="fa-solid fa-sign-out-alt" style="padding-right:5px;"></i>Logout';
    authLink.onclick = (e) => {
      e.preventDefault();
      localStorage.removeItem("loggedIn");
      localStorage.removeItem("username");
      window.location.href = "loginandregister.php";
    };

    const sidebarLogout = document.querySelector(".sidebar-bottom a");
    if (sidebarLogout) {
      sidebarLogout.href = "#";
      sidebarLogout.onclick = (e) => {
        e.preventDefault();
        localStorage.removeItem("loggedIn");
        localStorage.removeItem("username");
        window.location.href = "loginandregister.php";
      };
    }
  }

  function setLoginUI() {
    const authLink = document.getElementById("logout-btn") || document.getElementById("logIn-btn");
    if (!authLink) return;
    authLink.id = "logIn-btn";
    authLink.href = "loginandregister.php";
    authLink.innerHTML = '<i class="fa-solid fa-user" style="padding-right:5px;"></i>Login / Register';
    authLink.onclick = null;

    const sidebarLogout = document.querySelector(".sidebar-bottom a");
    if (sidebarLogout) {
      sidebarLogout.style.display = "none";
    }
  }

  if (localStorage.getItem("loggedIn") === "true") {
    setLogoutUI();
  } else {
    setLoginUI();
  }
});

