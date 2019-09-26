# OpenEuropa template for Drupal projects

This is based on [drupal-composer/drupal-project](https://github.com/drupal-composer/drupal-project).

[![Build Status](https://drone.fpfis.eu/api/badges/openeuropa/drupal-site-template/status.svg?branch=master)](https://drone.fpfis.eu/openeuropa/drupal-site-template)

This project template provides a starter kit for creating a website using the
OpenEuropa Drupal 8 platform. It will install the [OpenEuropa Profile](https://github.com/openeuropa/oe_profile)
which is a lightweight installation profile that includes the minimal number
of modules that are required to enable the [OpenEuropa Theme](https://github.com/openeuropa/oe_theme).
Using this theme will ensure that the project complies with the guidelines for
[European Component Library](https://github.com/ec-europa/europa-component-library).

In order to build the functionality of the website you are free to use any of the
[OpenEuropa components](https://github.com/openeuropa/openeuropa/blob/master/docs/openeuropa-components.md).

## Prerequisites

You need to have the following software installed on your local development environment:

* [Composer](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx).
* [Docker Compose](https://docs.docker.com/compose/install/)
* PHP 7.1 or greater (only needed during project creation)

## Create the project

The project is built using [Composer create-project](https://getcomposer.org/doc/03-cli.md#create-project).

A new project can be created using the following command:

```bash
composer create-project openeuropa/drupal-site-template --stability=dev <dg-name>-<project-id>-reference
```

This will download the starterkit into the `<dg-name>-<project-id>-reference` folder and a
wizard will ask you for the project name and your organisation. It will use this
information to personalize your project's configuration files.

The installer will then download all dependencies for the project. This process
takes several minutes. At the end you will be asked whether to remove the
existing version history. It is recommended to confirm this question so that you
can start your project with a clean slate.

Now enter into the newly created site folder and start the Docker containers:

```bash
cd "<dg-name>-<project-id>-reference"
docker-compose up -d
```

After all the containers have started (the time required can vary based on the host machine),
perform a clean installation of your site, using the following command:

```bash
docker-compose exec web ./vendor/bin/run toolkit:install-clean
```

Using default configuration, the development site files should be available in the `web` directory.

Before to commit your project on your repository, export the configuration on `config/sync`
using the following command:.

```bash
docker-compose exec web ./vendor/bin/drush cex
```

## Drone

A `.drone.yml` file is provided for running CI tests on Drone. Further details of how to set this up can be found in the
 [drone documentation](https://docs.drone.io/).

## Project management

It is recommended that the version of `oe_theme` is locked to the current minor version before going live with the
project, so that updates to the theme do not cause problems to a running site. We recommend that this is periodically
updated to the latest version, after doing manual testing.

A separate `.gitignore` file is provided which is used for the project. Drupal scaffold files should be committed after
running composer install or update. See the
[Drupal scaffold documentation](https://github.com/drupal-composer/drupal-scaffold/blob/master/README.md#limitation)
for further details.

Further details of how to build sites, install Drupal and run tests can be found in the `README.md` found within your site
 folder.

Now you are ready to push your project to your chosen code hosting service.
