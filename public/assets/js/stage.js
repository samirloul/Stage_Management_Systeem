document.addEventListener("DOMContentLoaded", () => {
    const nav = document.getElementById("mainNav");
    const menuToggle = document.getElementById("menuToggle");

    if (menuToggle && nav) {
        menuToggle.addEventListener("click", () => {
            nav.classList.toggle("show");
        });
    }

    document.querySelectorAll("[data-confirm-delete]").forEach((form) => {
        form.addEventListener("submit", (event) => {
            const ok = window.confirm("Weet je zeker dat je dit item wilt verwijderen?");
            if (!ok) {
                event.preventDefault();
            }
        });
    });

    // Small algorithm example: loop through alerts and hide them after 5 seconds.
    const alerts = document.querySelectorAll(".auto-dismiss");
    for (let i = 0; i < alerts.length; i += 1) {
        const alertBox = alerts[i];
        window.setTimeout(() => {
            alertBox.style.opacity = "0";
            alertBox.style.transition = "opacity 300ms ease";
            window.setTimeout(() => {
                alertBox.remove();
            }, 320);
        }, 5000);
    }

    // Demonstrates conditional logic with switch for future UI extensions.
    const page = document.body.dataset.page || "default";
    switch (page) {
        case "students":
        case "companies":
        case "internships":
        case "reviews":
            break;
        default:
            break;
    }
});
