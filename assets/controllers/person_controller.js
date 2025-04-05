
import { Controller } from "@hotwired/stimulus";

/**
 * @property {HTMLFormElement} formTarget
 * @property {HTMLElement} successMessageTarget
 * @property {HTMLElement} statusTarget
 * @property  {string} entityTypeValue
 * @property  {string} verbValue
 */
export default class extends Controller {
    static targets = ["form",  "status"];
    static values = {
        entityType: String,
        verb: String
    }
    connect() {
        console.log(`we have entity type ${this.entityTypeValue} and verb ${this.verbValue}`);
    }
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
                    // need to work on this
                    throw new Error(`Unexpected response status: ${response.status}, Content-Type: ${contentType}`);
                }
            })
            .then(data => {
                if (typeof data === "string") {
                    // validation failed; replace form with updated HTML
                    this.formTarget.outerHTML = data;
                } else {
                    // Success: Hide form, show success status message
                    this.formTarget.classList.add("d-none");
                    this.statusTarget.innerHTML = `This ${this.entityTypeValue} has been successfully ${this.verbValue}.` ;
                    this.statusTarget.classList.remove("d-none");

                }
            })
            .catch(error => console.error("Error submitting form:", error));
    }
}
