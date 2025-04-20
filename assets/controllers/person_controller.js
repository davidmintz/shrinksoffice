// noinspection SymfonyImportMapModuleIsNotInstalled
import BaseFormController from '../lib/base_form_controller.js';

/**
 * @property {HTMLFormElement} formTarget
 * @property {HTMLElement} statusTarget
 * @property  {string} entityTypeValue
 * @property  {string} verbiageValue
 */
export default class extends BaseFormController {

    success(data) {
        this.statusTarget.classList.remove("alert-danger", "d-none");
        this.statusTarget.classList.add("alert-success");

        this.statusTarget.innerHTML = `
        <div class="d-flex align-items-center">
            ${this.checkmarkHtml}
            <div>${data.message}</div>
        </div>
        <div class="pt-3 text-center">
            <a href="/people/view/${data.id}">view</a> |
            <a href="/people/update/${data.id}">edit</a> |
            <a href="/people/add">add another</a>
        </div>
    `;

        this.formTarget.classList.add("d-none");
    }

}
