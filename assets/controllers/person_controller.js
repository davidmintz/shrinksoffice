
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
        console.log(`we have entity type ${this.entityTypeValue} and verb ${this.verbiageValue}`);
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
                    const css_class = 'btn btn-sm btn-success flex-grow-1 text-center';
                    // I guess right now we don't need:
                    //const what = this.entityTypeValue;
                    const name = data.name;
                    const id = data.id;
                    const verbiage = this.verbiageValue;
                    const basePath = '/people';

                    this.statusTarget.innerHTML =
                       `<strong>${name}</strong> has been successfully ${verbiage}.
                       <div class="d-flex justify-content-center gap-2 w-50 mx-auto">
                            <!-- <div class="btn-group btn-group-sm mt-2" role="group"> -->
                                <a href="${basePath}/view/${id}" class="${css_class}">view</a>
                                <a href="${basePath}/update/${id}" class="${css_class}">edit</a>
                                <a href="${basePath}/add" class="${css_class}">add another</a>
                            <!-- </div> -->
                        </div>`;

                    this.formTarget.classList.add("d-none");
                    this.statusTarget.classList.remove("d-none");

                }
            })
            .catch(
                error => { console.error("Error submitting form:", error) }
            );
    }
}
