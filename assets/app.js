import './vendor/bootstrap/bootstrap.index.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import 'bootstrap/dist/css/bootstrap.min.css';
import './styles/app.css';

const env = document.body.dataset.env;

if (env === 'dev') {
    // noinspection ES6UnusedImports
    import('test-helpers')
        .then(({ fillFakePersonForm }) => {
            window.fillFakePersonForm = fillFakePersonForm;
        })
        .catch(err => console.error("Failed to load dev helpers:", err));
}

console.log('This is assets/app.js - welcome to AssetMapper! 🎉');
