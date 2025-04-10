
import { Controller } from "@hotwired/stimulus";

/**
 * @property {HTMLFormElement} formTarget
 * @property {HTMLElement} successMessageTarget
 * @property {HTMLElement} statusTarget
 * @property  {string} entityTypeValue
 * @property  {string} verbiageValue
 */
export default class extends Controller {
    static targets = ["form",  "status"];
    static values = {
        entityType: String,
        verbiage: String
    }
    connect() {
        console.log(`we have entity type ${this.entityTypeValue} and verbiage '${this.verbiageValue}'`);
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
                }

                if (contentType.includes("application/json")) {
                    return response.json().then(json => {
                        if (!response.ok) {
                            // Server-side error, still JSON
                            throw new Error(json.error || "Unknown server error");
                        }
                        return json;
                    });
                }

                throw new Error(`Unexpected response status: ${response.status}, Content-Type: ${contentType}`);
            })
            .then(data => {
                if (typeof data === "string") {
                    // validation failed; replace form with updated HTML
                    this.formTarget.outerHTML = data;
                } else {
                    // Success: Hide form, show success status message
                    // const css_class = 'btn btn-sm btn-success flex-grow-1 text-center';
                    // right now we don't need:
                    // const what = this.entityTypeValue;
                    this.statusTarget.classList.remove("alert-danger");
                    this.statusTarget.classList.add("alert-success");
                    const name = data.name;
                    const id = data.id;
                    const verbiage = this.verbiageValue;
                    const basePath = '/people';

                    this.statusTarget.innerHTML =
                        `<strong>${name}</strong> has been successfully ${verbiage}.<br>
                            <div class="py-3 text-center">
                                <a href="${basePath}/view/${id}" >view</a> |
                                <a href="${basePath}/update/${id}" >edit</a> | 
                                <a href="${basePath}/add">add another</a>
                            </div>`
                    this.formTarget.classList.add("d-none");
                    this.statusTarget.classList.remove("d-none");

                }
            })
            .catch(
                error => {
                    console.error("Error submitting form:", error)
                    this.statusTarget.classList.remove("d-none");
                    this.statusTarget.classList.remove("alert-success");
                    this.statusTarget.classList.add("alert-danger");
                    this.statusTarget.innerHTML = `<h3>Server error</h3><p>${error.message}</p>`;
                }
            );
    }
}
