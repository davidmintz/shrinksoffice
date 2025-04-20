import { Controller } from "@hotwired/stimulus";

/**
 * @property {HTMLFormElement} formTarget
 * @property {HTMLElement} statusTarget
 */
export default class extends Controller {

    static targets = ["form", "status"];

    checkmarkHtml = '<div class="me-2 fs-4">✅</div>';

    submit(event) {
        event.preventDefault();
        const form = this.formTarget;
        const formData = new FormData(form);

        this.statusTarget.classList.add("d-none");
        this.statusTarget.innerHTML = "";

        fetch(form.action, {
            method: form.method,
            body: formData,
            headers: {
                "X-Requested-With": "XMLHttpRequest"
            }
        })
            .then(response => {
                const contentType = response.headers.get("Content-Type") || "";

                if (response.status === 422 && contentType.includes("text/html")) {
                    return response.text();
                }

                if (contentType.includes("application/json")) {
                    return response.json().then(json => {
                        if (!response.ok) {
                            throw new Error(json.error || "Unknown server error");
                        }
                        return json;
                    });
                }

                throw new Error(`Unexpected response: ${response.status} ${contentType}`);
            })
            .then(data => {
                if (typeof data === "string") {
                    this.formTarget.outerHTML = data;
                } else {
                    this.success(data);
                }
            })
            .catch(error => {
                console.error("Form submission error:", error);
                this.error(error);
            });
    }

    success(data) {
        throw new Error("Override handleSuccess() in your subclass.");
    }

    error(error) {
        this.statusTarget.classList.remove("alert-success");
        this.statusTarget.classList.add("alert-danger");
        this.statusTarget.classList.remove("d-none");
        this.statusTarget.innerHTML = `<h3>Server error</h3><p>${error.message}</p>`;
    }
}
