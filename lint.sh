#!/bin/bash

# Lint Stylesheet files
yarn run lint:css:fix

# Type check TypeScript files
yarn run tsc:check

# Lint HTML files
#yarn run lint:html:fix

# Lint Twig files
php bin/console lint:twig templates

./php-cs-fixer.sh
