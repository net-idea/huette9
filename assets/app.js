/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// Bootstrap CSS (from node_modules – local)
import 'bootstrap/dist/css/bootstrap.min.css';

// Shared theme styles
import './styles/theme.css';

// Theme-specific styles
import './styles/theme-light.css';
import './styles/theme-dark.css';

// Import Bootstrap JavaScript (local bundle with Popper)
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

// Start the Stimulus application
import './bootstrap.js';

// Import TypeScript
import './scripts/theme-toggle.ts';
