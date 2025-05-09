// Add to existing code
function setAuthHeaders() {
    return {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('cinevault_token')}`
    };
}

// Add to login success handler
localStorage.setItem('cinevault_token', data.token);
localStorage.setItem('cinevault_user', JSON.stringify(data.user));
updateAuthUI();

// Add to logout handler
localStorage.removeItem('cinevault_token');
localStorage.removeItem('cinevault_user');
updateAuthUI();

// Add UI update function
function updateAuthUI() {
    const user = JSON.parse(localStorage.getItem('cinevault_user'));
    const authLinks = document.querySelector('.auth-links');
    const userMenu = document.querySelector('.user-menu');

    if (user) {
        authLinks.style.display = 'none';
        userMenu.style.display = 'block';
        document.querySelector('.user-avatar').src = user.avatar;
        document.querySelector('.username').textContent = user.username;
    } else {
        authLinks.style.display = 'block';
        userMenu.style.display = 'none';
    }
}

// Call on page load
updateAuthUI();