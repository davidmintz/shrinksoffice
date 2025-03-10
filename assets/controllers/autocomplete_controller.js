import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["input", "results"];

    connect() {
        this.results = [];
        console.log("autocomplete_controller: this is connect()")
    }

    async search(event) {
        console.log("autocomplete_controller: this is search()")
        const query = event.target.value;

        if (query.length < 2) {
            this.clearResults();
            return;
        }

        const response = await fetch(`/api/search-payers?q=${query}`);
        this.results = await response.json();
        console.log("Search results:", this.results);
        this.showResults();
    }

    showResults() {
        let resultsHtml = this.results.map(
            person => `<li data-id="${person.id}" data-action="click->autocomplete#select" class="list-group-item">${person.firstname} ${person.lastname}</li>`
        ).join("");

        this.resultsTarget.innerHTML = `<ul class="list-group">${resultsHtml}</ul>`;
    }

    clearResults() {
        this.resultsTarget.innerHTML = "";
    }

    select(event) {

        const personId = event.target.dataset.id;
        this.inputTarget.value = personId;
        this.clearResults();
    }
}
