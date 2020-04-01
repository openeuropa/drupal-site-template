# OpenEuropa template for Drupal 8 projects

[![Build Status](https://drone.fpfis.eu/api/badges/openeuropa/drupal-site-template/status.svg?branch=master)](https://drone.fpfis.eu/openeuropa/drupal-site-template)

**Please note: this repository contains code that is necessary to generate
a new Drupal 8 project, please read carefully this README.md. Do not clone this repository.**

You need to have the following software installed on your local development environment:

* [Git](https://git-scm.com/)
* [Docker](https://docker.com/)
* [Docker Compose](https://docs.docker.com/compose/install/)

## How do I get my new OpenEuropa project codebase?

First of all choose carefully the name of your new project, it should respect the
following convention:

```
<dg-name>-<project-id>-reference
```

After that contact the QA team, they will create for you a new repository at:

```
https://github.com/ec-europa/<dg-name>-<project-id>-reference
```

Then generate your new Drupal 8 project codebase by running the following command:

```bash
docker run --rm -ti -v $PWD:/var/www/html -w /var/www/html fpfis/httpd-php-dev:7.3 composer create-project openeuropa/drupal-site-template --stability=dev --remove-vcs <dg-name>-<project-id>-reference
```

This will download this starterkit into the `<dg-name>-<project-id>-reference` folder and a
wizard will ask you for the project name and your organisation. It will use this
information to personalize your project's configuration files.

The installer will then download all dependencies for the project. This process
takes several minutes. At the end you will be asked whether to remove the
existing version history. It is recommended to confirm this question so that you
can start your project with a clean slate.

After completing the command above you can push the content of `<dg-name>-<project-id>-reference`
to the GitHub repository created for you by the QA team.

## Ok, I've got my codebase, what should I do now?

1. The steps to get your new site up and running can be found in
`<dg-name>-<project-id>-reference/README.md`.
2. Check the [OpenEuropa Documentation](https://github.com/openeuropa/documentation)
for a list of available components, best practices, etc.
3. Make sure you master the concepts of [Configuration Management](https://www.drupal.org/docs/8/configuration-management)
and the related development workflow in Drupal 8.

## Should I clone this GitHub project?

No, this repository will only generate your new project's codebase, you then need
to push the generated codebase to a dedicated repository, as explained above.
