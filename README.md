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

Starting a new project is a 4 step procedure:

## 0. Prerequisites

You need to have the following software installed on your local development environment:

* [Composer](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx).

## 1. Create the project

The project can be created using the following command:

```
$ composer create-project openeuropa/drupal-site-template --stability=dev my-openeuropa-site
```

This will download the starterkit into the `my-openeuropa-site` folder and a
wizard will ask you for the project name and your organisation. It will use this
information to personalize your project's configuration files.

The installer will then download all dependencies for the project. This process
takes several minutes. At the end you will be asked whether to remove the
existing version history. It is recommended to confirm this question so that you
can start your project with a clean slate.
