/**
 * Stimulus Framework Bootstrap
 *
 * This file initializes the Stimulus framework and registers all controllers
 * from both the controllers.json configuration file and the controllers/ directory.
 *
 * Stimulus controllers provide reactive behavior to HTML elements through
 * data-controller attributes.
 */

import { startStimulusApp } from '@symfony/stimulus-bridge';
import type { Application } from '@hotwired/stimulus';

/**
 * Initialize and start the Stimulus application.
 *
 * Controllers are loaded lazily for better performance and registered automatically
 * when they're needed. Both .ts and .js controller files are supported.
 */
export const app: Application = startStimulusApp(require.context(
    '@symfony/stimulus-bridge/lazy-controller-loader!./controllers',
    true,
    /\.[jt]sx?$/
));
