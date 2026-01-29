"use strict";

(function () {

    document.addEventListener("DOMContentLoaded", function () {

        var passwordToggles = document.querySelectorAll("[data-password-toggle]");

        passwordToggles.forEach(function (toggle) {

            toggle.addEventListener("click", function () {

                var targetId = toggle.getAttribute("data-password-toggle");
                var input = document.getElementById(targetId);

                if (! input) {

                    return;
                }

                var eyeIcon = document.getElementById(targetId + "-eye-icon");
                var eyeOffIcon = document.getElementById(targetId + "-eye-off-icon");

                if (input.type === "password") {

                    input.type = "text";

                    if (eyeIcon) {

                        eyeIcon.classList.add("hidden");
                    }

                    if (eyeOffIcon) {

                        eyeOffIcon.classList.remove("hidden");
                    }

                } else {

                    input.type = "password";

                    if (eyeIcon) {

                        eyeIcon.classList.remove("hidden");
                    }

                    if (eyeOffIcon) {

                        eyeOffIcon.classList.add("hidden");
                    }
                }
            });
        });
    });

})();
