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

    let commentForm = document.querySelector(".add-comment-form");
    if (commentForm) {
        commentForm.addEventListener("submit", function (e) {
            e.preventDefault();
            let btn = commentForm.querySelector("button[type=submit]");
            let spinner = btn.querySelector(".spinner-border");
            let feedback = commentForm.querySelector(".ajax-feedback");
            feedback.innerHTML = "";
            btn.disabled = true;
            if (spinner) spinner.classList.remove("d-none");
            let formData = new FormData(commentForm);
            fetch(commentForm.action, {
                method: "POST",
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": commentForm.querySelector(
                        'input[name="_token"]'
                    ).value,
                    Accept: "application/json",
                },
                body: formData,
            })
                .then((response) => response.json())
                .then((data) => {
                    commentForm.reset();
                    if (data.success) {
                        feedback.innerHTML =
                            '<div class="alert alert-success">' +
                            (data.message || "Comment added!") +
                            "</div>";
                        setTimeout(function () {
                            feedback.innerHTML = "";
                        }, 3000);
                    } else if (data.message) {
                        feedback.innerHTML =
                            '<div class="alert alert-danger">' +
                            data.message +
                            "</div>";
                    }
                })
                .catch(() => {
                    feedback.innerHTML =
                        '<div class="alert alert-danger">Failed to add comment.</div>';
                    setTimeout(function () {
                        feedback.innerHTML = "";
                    }, 3000);
                })
                .finally(() => {
                    btn.disabled = false;
                    if (spinner) spinner.classList.add("d-none");
                });
        });
    }

    let select = document.getElementById("status-select");
    let form = document.getElementById("status-update-form");
    if (select && form) {
        select.addEventListener("change", function () {
            let formData = new FormData(form);
            let feedback = form.querySelector(".ajax-feedback");
            feedback.innerHTML = "";
            fetch(form.action, {
                method: "POST",
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": form.querySelector('input[name="_token"]')
                        .value,
                    Accept: "application/json",
                },
                body: formData,
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.status) {
                        updateTicketDetails({
                            status: data.status,
                            agent: null,
                            agent_id: null,
                        });
                    }
                    if (data.success) {
                        feedback.innerHTML =
                            '<div class="alert alert-success">' +
                            (data.message || "Status updated!") +
                            "</div>";
                        setTimeout(function () {
                            feedback.innerHTML = "";
                        }, 3000);
                    } else if (data.message) {
                        feedback.innerHTML =
                            '<div class="alert alert-danger">' +
                            data.message +
                            "</div>";
                    }
                })
                .catch(() => {
                    feedback.innerHTML =
                        '<div class="alert alert-danger">Failed to update status.</div>';
                    setTimeout(function () {
                        feedback.innerHTML = "";
                    }, 3000);
                });
        });
    }

    let assignForm = document.querySelector(".assign-agent-form");
    if (assignForm) {
        assignForm.addEventListener("submit", function (e) {
            e.preventDefault();
            let btn = assignForm.querySelector("button[type=submit]");
            let spinner = btn.querySelector(".spinner-border");
            let feedback = document.getElementById("assign-feedback");
            feedback.innerHTML = "";
            btn.disabled = true;
            if (spinner) spinner.classList.remove("d-none");
            let formData = new FormData(assignForm);
            fetch(assignForm.action, {
                method: "POST",
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": assignForm.querySelector(
                        'input[name="_token"]'
                    ).value,
                    Accept: "application/json",
                },
                body: formData,
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success && data.ticket) {
                        updateTicketDetails(data.ticket);
                        feedback.classList.remove("alert-danger");
                        feedback.classList.add("alert-info");
                        feedback.textContent = "Ticket assigned successfully!";
                        feedback.classList.remove("d-none");
                    }
                })
                .catch(() => {
                    feedback.classList.remove("alert-info");
                    feedback.classList.add("alert-danger");
                    feedback.textContent = "Failed to assign ticket.";
                    feedback.classList.remove("d-none");
                })
                .finally(() => {
                    btn.disabled = false;
                    if (spinner) spinner.classList.add("d-none");
                });
        });
    }

    const ticketId = document.getElementById("ticket-page")?.dataset.ticketId;
    if (window.Echo && ticketId) {
        window.Echo.private(`ticket.${ticketId}`).listen(
            "CommentAdded",
            (e) => {
                console.log("New comment event:", e);
                fetch(window.location.href, {
                    headers: { "X-Requested-With": "XMLHttpRequest" },
                })
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.comments_html) {
                            let commentsSection =
                                document.getElementById("comments-section");
                            if (commentsSection) {
                                commentsSection.innerHTML = data.comments_html;
                            }
                        }
                    });
            }
        );
    }
});

document.addEventListener("DOMContentLoaded", function () {
    const filterForm = document.getElementById("filter-form");
    const ticketsList = document.getElementById("tickets-list");
    const ticketsLoading = document.getElementById("tickets-loading");
    if (filterForm && ticketsList && ticketsLoading) {
        function serializeForm(form) {
            const params = new URLSearchParams();
            for (const el of form.elements) {
                if (!el.name || el.disabled) continue;
                if (
                    (el.type === "checkbox" || el.type === "radio") &&
                    !el.checked
                )
                    continue;
                params.append(el.name, el.value);
            }
            return params.toString();
        }

        function fetchTickets(params) {
            ticketsLoading.style.display = "block";
            window.axios
                .get(
                    filterForm.getAttribute("action") ||
                        window.location.pathname,
                    {
                        params: params
                            ? Object.fromEntries(new URLSearchParams(params))
                            : {},
                        headers: { Accept: "application/json" },
                    }
                )
                .then(function (response) {
                    if (response.data && response.data.html) {
                        ticketsList.innerHTML = response.data.html;
                    }
                })
                .finally(function () {
                    ticketsLoading.style.display = "none";
                });
        }

        filterForm.addEventListener("submit", function (e) {
            e.preventDefault();
            fetchTickets(serializeForm(filterForm));
        });

        filterForm.querySelectorAll("select, input").forEach(function (el) {
            el.addEventListener("change", function () {
                filterForm.dispatchEvent(new Event("submit"));
            });
        });

        ticketsList.addEventListener("click", function (e) {
            const target = e.target.closest("a");
            if (target && target.closest(".pagination")) {
                e.preventDefault();
                const url = new URL(target.href);
                fetchTickets(url.search.slice(1));
            }
        });
    }
});

document.addEventListener("DOMContentLoaded", function () {
    const assignSelect = document.getElementById("assign-agent-select");
    const assignForm = document.querySelector(".assign-agent-form");
    if (assignSelect && assignForm) {
        assignSelect.addEventListener("change", function () {
            if (!assignSelect.value) return;
            let btn = assignForm.querySelector("button[type=submit]");
            let feedback = document.getElementById("assign-feedback");
            feedback.innerHTML = "";
            let formData = new FormData(assignForm);
            formData.set("agent_id", assignSelect.value);
            if (btn) btn.disabled = true;
            fetch(assignForm.action, {
                method: "POST",
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": assignForm.querySelector(
                        'input[name="_token"]'
                    ).value,
                    Accept: "application/json",
                },
                body: formData,
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success && data.ticket) {
                        updateTicketDetails(data.ticket);
                        feedback.classList.remove("alert-danger");
                        feedback.classList.add("alert-info");
                        feedback.textContent = "Ticket assigned successfully!";
                        feedback.classList.remove("d-none");
                    } else if (data.message) {
                        feedback.classList.remove("alert-info");
                        feedback.classList.add("alert-danger");
                        feedback.textContent = data.message;
                        feedback.classList.remove("d-none");
                    }
                })
                .catch(() => {
                    feedback.classList.remove("alert-info");
                    feedback.classList.add("alert-danger");
                    feedback.textContent = "Failed to assign ticket.";
                    feedback.classList.remove("d-none");
                })
                .finally(() => {
                    if (btn) btn.disabled = false;
                });
        });
    }
});

function updateTicketDetails(ticket) {
    let badge = "";
    if (ticket.status === "open") {
        badge =
            '<span class="badge bg-success"><i class="bi bi-unlock"></i> Open</span>';
    } else if (ticket.status === "in_progress") {
        badge =
            '<span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split"></i> In Progress</span>';
    } else if (ticket.status === "closed") {
        badge =
            '<span class="badge bg-secondary"><i class="bi bi-lock"></i> Closed</span>';
    }
    let statusEl = document.getElementById("ticket-status");
    if (statusEl) statusEl.innerHTML = badge;
    let agentEl = document.getElementById("ticket-agent");
    if (agentEl) agentEl.textContent = ticket.agent ? ticket.agent.name : "-";
    if (ticket.agent_id) {
        let assignForms = document.querySelectorAll(".assign-agent-form");
        assignForms.forEach(function (f) {
            f.remove();
        });
    }
}
