// noinspection SymfonyImportMapModuleIsNotInstalled
import BaseFormController from '../lib/base_form_controller.js';

/**
 * @property {HTMLFormElement} formTarget
 * @property {HTMLElement} statusTarget
 * @property  {string} entityTypeValue
 * @property  {string} verbiageValue
 */
export default class extends BaseFormController {
    static values = {
        entityType: String,
        verbiage: String
    }

    success(data) {
        const name = data.name;
        const id = data.id;
        const verbiage = this.verbiageValue;
        const basePath = '/people';

        this.statusTarget.classList.remove("alert-danger", "d-none");
        this.statusTarget.classList.add("alert-success");

        this.statusTarget.innerHTML = `
            <strong>${name}</strong> has been successfully ${verbiage}.<br>
            <div class="py-3 text-center">
                <a href="${basePath}/view/${id}" >view</a> |
                <a href="${basePath}/update/${id}" >edit</a> | 
                <a href="${basePath}/add">add another</a>
            </div>
        `;
        this.formTarget.classList.add("d-none");
    }
}
