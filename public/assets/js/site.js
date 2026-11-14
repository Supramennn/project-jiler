(function () {
    "use strict";

    document.addEventListener("DOMContentLoaded", function () {
        setupHeaderScroll();
        setupReveal();
        setupMobileNav();
        setupSidebar();
        setupPaymentMethods();
        setupCopyButtons();
        setupConfirmForms();
        setupPolling();
        setupCountdown();
        setupPrintButtons();
    });

    function setupHeaderScroll() {
        var header = document.querySelector("[data-site-header]");

        if (!header) {
            return;
        }

        function update() {
            header.classList.toggle("is-scrolled", window.scrollY > 8);
        }

        update();
        window.addEventListener("scroll", update, { passive: true });
    }

    function setupReveal() {
        if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
            return;
        }

        var selector = [
            ".site-body .fact",
            ".site-body .section-head",
            ".site-body .product-row",
            ".site-body .step",
            ".site-body .faq details",
            ".site-body .cta-band-inner",
            ".site-body .product-detail",
            ".site-body .checkout-grid",
            ".site-body .payment-box",
            ".site-body .result-card",
            ".admin-content .admin-page-head",
            ".admin-content .stat-cell",
            ".admin-content .panel",
            ".admin-content .table-wrap",
            ".admin-content .filters"
        ].join(", ");

        var targets = document.querySelectorAll(selector);

        if (!targets.length) {
            return;
        }

        if (!("IntersectionObserver" in window)) {
            return;
        }

        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) {
                    return;
                }

                entry.target.classList.add("is-visible");
                observer.unobserve(entry.target);
            });
        }, {
            threshold: 0.08,
            rootMargin: "0px 0px -6% 0px"
        });

        targets.forEach(function (target) {
            var siblings = target.parentElement
                ? Array.prototype.slice.call(target.parentElement.children)
                : [];
            var index = siblings.indexOf(target);
            var delay = Math.min(Math.max(index, 0), 6) * 55;

            target.style.setProperty("--reveal-delay", delay + "ms");
            target.classList.add("reveal");
            observer.observe(target);
        });
    }

    function setupMobileNav() {
        var toggle = document.querySelector("[data-nav-toggle]");
        var nav = document.querySelector("[data-site-nav]");

        if (!toggle || !nav) {
            return;
        }

        function close() {
            nav.classList.remove("is-open");
            toggle.setAttribute("aria-expanded", "false");
            document.body.classList.remove("no-scroll");
        }

        toggle.addEventListener("click", function () {
            var open = toggle.getAttribute("aria-expanded") === "true";
            toggle.setAttribute("aria-expanded", open ? "false" : "true");
            nav.classList.toggle("is-open", !open);
            document.body.classList.toggle("no-scroll", !open);
        });

        nav.querySelectorAll("a").forEach(function (link) {
            link.addEventListener("click", close);
        });

        document.addEventListener("keydown", function (event) {
            if (event.key === "Escape" && nav.classList.contains("is-open")) {
                close();
                toggle.focus();
            }
        });

        window.addEventListener("resize", function () {
            if (window.innerWidth > 760) {
                close();
            }
        });
    }

    function setupSidebar() {
        var toggle = document.querySelector("[data-sidebar-toggle]");
        var sidebar = document.querySelector("[data-admin-sidebar]");
        var overlay = document.querySelector("[data-admin-overlay]");

        if (!toggle || !sidebar) {
            return;
        }

        function close() {
            sidebar.classList.remove("is-open");
            if (overlay) {
                overlay.classList.remove("is-open");
            }
            toggle.setAttribute("aria-expanded", "false");
        }

        toggle.addEventListener("click", function () {
            var open = !sidebar.classList.contains("is-open");
            sidebar.classList.toggle("is-open", open);
            if (overlay) {
                overlay.classList.toggle("is-open", open);
            }
            toggle.setAttribute("aria-expanded", open ? "true" : "false");
        });

        if (overlay) {
            overlay.addEventListener("click", close);
        }

        sidebar.querySelectorAll("a").forEach(function (link) {
            link.addEventListener("click", function () {
                if (window.innerWidth <= 900) {
                    close();
                }
            });
        });

        document.addEventListener("keydown", function (event) {
            if (event.key === "Escape" && sidebar.classList.contains("is-open")) {
                close();
                toggle.focus();
            }
        });
    }

    function setupPaymentMethods() {
        var options = document.querySelectorAll(".method-option");

        if (!options.length) {
            return;
        }

        function refresh() {
            options.forEach(function (option) {
                var input = option.querySelector("input[type='radio']");
                option.classList.toggle("is-selected", Boolean(input && input.checked));
            });
        }

        options.forEach(function (option) {
            var input = option.querySelector("input[type='radio']");

            if (input) {
                input.addEventListener("change", refresh);
            }
        });

        refresh();
    }

    function setupCopyButtons() {
        document.querySelectorAll("[data-copy]").forEach(function (button) {
            button.addEventListener("click", function () {
                var value = button.getAttribute("data-copy") || "";
                var original = button.textContent;

                copyText(value).then(function () {
                    button.textContent = "Tersalin";
                    window.setTimeout(function () {
                        button.textContent = original;
                    }, 1600);
                }).catch(function () {
                    button.textContent = "Gagal menyalin";
                    window.setTimeout(function () {
                        button.textContent = original;
                    }, 1600);
                });
            });
        });
    }

    function copyText(value) {
        if (navigator.clipboard && window.isSecureContext) {
            return navigator.clipboard.writeText(value);
        }

        return new Promise(function (resolve, reject) {
            var field = document.createElement("textarea");
            field.value = value;
            field.setAttribute("readonly", "");
            field.style.position = "fixed";
            field.style.left = "-9999px";
            document.body.appendChild(field);
            field.select();

            try {
                var ok = document.execCommand("copy");
                document.body.removeChild(field);
                ok ? resolve() : reject();
            } catch (error) {
                document.body.removeChild(field);
                reject(error);
            }
        });
    }

    function setupConfirmForms() {
        document.querySelectorAll("form[data-confirm]").forEach(function (form) {
            form.addEventListener("submit", function (event) {
                var message = form.getAttribute("data-confirm");

                if (message && !window.confirm(message)) {
                    event.preventDefault();
                }
            });
        });
    }

    function setupPolling() {
        var target = document.querySelector("[data-poll-url]");

        if (!target) {
            return;
        }

        var url = target.getAttribute("data-poll-url");
        var note = document.querySelector("[data-poll-note]");
        var attempts = 0;
        var timer = null;

        function tick() {
            attempts++;
            fetch(url, { headers: { Accept: "application/json" } })
                .then(function (response) {
                    return response.ok ? response.json() : null;
                })
                .then(function (data) {
                    if (!data || !data.ok) {
                        stop();
                        return;
                    }

                    if (data.status === "paid" || data.status === "expired" || data.status === "gagal") {
                        window.location.reload();
                        return;
                    }

                    if (note) {
                        note.textContent = "Status diperiksa otomatis. Pembaruan terakhir " + new Date().toLocaleTimeString("id-ID") + ".";
                    }

                    if (attempts >= 60) {
                        stop();
                    }
                })
                .catch(function () {
                    if (attempts >= 5) {
                        stop();
                    }
                });
        }

        function stop() {
            if (timer) {
                window.clearInterval(timer);
            }

            if (note) {
                note.textContent = "Pembaruan otomatis berhenti. Muat ulang halaman untuk memeriksa status.";
            }
        }

        timer = window.setInterval(tick, 5000);
        window.setTimeout(tick, 5000);
    }

    function setupCountdown() {
        document.querySelectorAll("[data-expires]").forEach(function (target) {
            var expires = target.getAttribute("data-expires");

            if (!expires) {
                return;
            }

            var end = new Date(expires.replace(" ", "T")).getTime();

            if (Number.isNaN(end)) {
                return;
            }

            function tick() {
                var remaining = end - Date.now();

                if (remaining <= 0) {
                    target.textContent = "Waktu pembayaran sudah habis.";
                    return;
                }

                var hours = Math.floor(remaining / 3600000);
                var minutes = Math.floor((remaining % 3600000) / 60000);
                var seconds = Math.floor((remaining % 60000) / 1000);
                var parts = [];

                if (hours > 0) {
                    parts.push(hours + " jam");
                }

                parts.push(minutes + " menit");
                parts.push(seconds + " detik");
                target.textContent = "Sisa waktu " + parts.join(" ");
                window.setTimeout(tick, 1000);
            }

            tick();
        });
    }

    function setupPrintButtons() {
        document.querySelectorAll("[data-print]").forEach(function (button) {
            button.addEventListener("click", function () {
                window.print();
            });
        });
    }
})();
