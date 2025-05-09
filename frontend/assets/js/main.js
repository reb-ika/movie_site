document.addEventListener("DOMContentLoaded", () => {
    console.log("CineVault frontend loaded");
  
    // Example dynamic action:
    const logoutBtn = document.getElementById("logoutBtn");
    if (logoutBtn) {
      logoutBtn.addEventListener("click", () => {
        fetch("/api/auth/logout.php", {
          method: "POST",
          credentials: "include"
        })
          .then(res => {
            if (res.ok) {
              window.location.href = "login.html";
            } else {
              alert("Logout failed");
            }
          })
          .catch(err => console.error(err));
      });
    }
  
    // Placeholder for future frontend logic like populating movie list, etc.
  });
  