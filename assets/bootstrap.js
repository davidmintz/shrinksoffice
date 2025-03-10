import { startStimulusApp } from '@symfony/stimulus-bundle';
import AutocompleteController from './controllers/autocomplete_controller.js';

// Start Stimulus and register the controller
const app = startStimulusApp();
app.register('autocomplete', AutocompleteController);

console.log("✅ Manually registered AutocompleteController");

// Enable Stimulus debug mode
window.Stimulus = app;
window.Stimulus.debug = true;
console.log("✅ Stimulus debug mode enabled");

