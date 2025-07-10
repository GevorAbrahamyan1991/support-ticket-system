import "./bootstrap";

document.addEventListener("DOMContentLoaded", function () {
    const logoutForm = document.getElementById("logout-form");
    if (logoutForm) {
        logoutForm.addEventListener("submit", function (e) {
            e.preventDefault();
            const csrfToken = document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content");
            const logoutButton = logoutForm.querySelector(
                'button[type="submit"]'
            );
            const spinner = document.getElementById("logout-spinner");
            if (logoutButton && spinner) {
                logoutButton.disabled = true;
                spinner.classList.remove("d-none");
            }
            window.axios
                .post(
                    logoutForm.action,
                    {},
                    {
                        headers: {
                            "X-CSRF-TOKEN": csrfToken,
                        },
                    }
                )
                .then(function (response) {
                    window.location.href = "/";
                })
                .catch(function (error) {
                    if (logoutButton && spinner) {
                        logoutButton.disabled = false;
                        spinner.classList.add("d-none");
                    }
                    alert("Logout failed. Please try again.");
                });
        });
    }
});
