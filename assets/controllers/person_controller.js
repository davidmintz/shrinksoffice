
import { Controller } from "@hotwired/stimulus";

/**
 * @property {HTMLFormElement} formTarget
 * @property {HTMLElement} successMessageTarget
 */
export default class extends Controller {
    static targets = ["form", "successMessage"];

    submit(event) {
        event.preventDefault();

        const form = this.formTarget;
        const formData = new FormData(form);

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
                    return response.text(); // Validation failed, return HTML form
                } else if (response.ok && contentType.includes("application/json")) {
                    return response.json(); // Success, return JSON
                } else {
                    throw new Error(`Unexpected response status: ${response.status}, Content-Type: ${contentType}`);
                }
            })

            .then(data => {
                if (typeof data === "string") {
                    // Replace form with updated HTML, controller stays active
                    this.formTarget.outerHTML = data;
                } else {
                    // Success: Hide form, show success message
                    this.formTarget.classList.add("hidden");
                    this.successMessageTarget.classList.remove("hidden");
                }
            })
            .catch(error => console.error("Error submitting form:", error));
    }
}
