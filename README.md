# HTMX Drupal Module

This module is experimental and intended for development and testing purposes.
It's a playground for trying out new ideas and implementations with HTMX in
a Drupal environment.

## Features of HTMX Plus Module
- **HTMXExtension Twig Extension:**
Provides a custom Twig extension to manage HTMX attributes in one place,
offering more control and flexibility in templates.

- **Debug Extension:**
Includes a debug extension that can be enabled via the module's settings form.
When enabled, it attaches the htmx_plus/debug library to responses,
aiding in development and debugging.

- **Demo Functionality:**
Contains a small demo feature that generates a random number, showcasing the
module's capabilities and providing a simple example of its usage.

- **Drush Command:**
Provides a Drush command `htmx:debug` to toggle debug mode on all HTMX elements.
You can enable debug mode with `drush htmx:debug on` and disable it with
`drush htmx:debug off`.

- **HTMX Plus Web 1.0 App:**
The `htmx_plus_web_1_0_app` submodule provides the HTMX Plus Web 1.0 Application
from the book ["Hypermedia Systems"](https://hypermedia.systems/).

## Code Standards and Static Analysis
- **PHPStan:**
PHPStan is used for static analysis with the highest level of strictness.
```sh
phpstan --level max
```

- **Twig Linter:**
Twig templates are linted using ["Twig-CS-Fixer"](https://github.com/VincentLanglet/Twig-CS-Fixer).


- **PHPCS:**
PHP CodeSniffer is used with the Drupal and DrupalPractice standards.
```sh
phpcs --standard=Drupal,DrupalPractice
```

---

### Note
Some items are intentionally inconsistent because I am just playing around.
