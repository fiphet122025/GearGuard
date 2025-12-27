// Confirm delete / critical actions
function confirmAction(message) {
    return confirm(message || "Are you sure?");
}

// Auto-hide alerts (optional)
setTimeout(() => {
    const alerts = document.querySelectorAll(".alert");
    alerts.forEach(a => a.style.display = "none");
}, 3000);
