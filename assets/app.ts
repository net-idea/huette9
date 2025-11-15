/**
 * Main Application Entry Point
 *
 * This is the main TypeScript file for the Hütte9 application.
 * It imports all styles, JavaScript libraries, and initializes
 * the Stimulus framework for interactive components.
 */

// Import self-hosted web fonts first (Manrope)
import './styles/fonts.scss';

// Main Hütte9 styles based on Bootstrap
import './styles/app.scss';

// Import Bootstrap JavaScript (includes Popper.js for dropdowns/tooltips)
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

// Start the Stimulus application
import './bootstrap';

// Theme toggle functionality (light/dark mode)
import './scripts/theme-toggle';
