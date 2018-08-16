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
* [Docker Compose](https://docs.docker.com/compose/install/)

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

## 2. Configuration

First let's move into the project's folder:

```
$ cd my-openeuropa-site
```

The project ships with default configuration that is intended to run the
website on the Docker containers we provide. If you are content with using
these, you can skip directly to step 3.

If you want to run the website using your locally installed LAMP stack, you
will want to change the configuration to match your local system.

Customize the default configuration values by copying `runner.yml.dist` to
'runner.yml`:

```
$ cp runner.yml.dist runner.yml
```

Now edit `runner.yml` with your most beloved text editor. You will want to set
the database host to `localhost`, and provide the correct database name and
credentials. Also update the `base_url` and Selenium path to match your local
environment.

## 3. Installation

### 3.1. Using Docker

Download and start the supplied Docker images:

```
$ docker-compose up -d
```

Next, install the website:

```
$ docker-compose exec web ./vendor/bin/run drupal:site-install
```

Once this completes your website will be available on 
[http://localhost:8080/web/](http://localhost:8080/web/)

To verify whether everything works as expected, you can run the example Behat
test suite:

```
$ docker-compose exec web ./vendor/bin/behat
```

### 3.2. Using a local LAMP stack

Install the website using the task runner:

```
$ ./vendor/bin/run drupal:site-install
```

The site will be available through your local web server.

To verify whether everything works as expected, you can run the example Behat
test suite:

```
$ ./vendor/bin/behat
```

## 4. Commit and push

The final step is to create a new git repository and commit all the files. A
`.gitignore` file is provided to ensure you only commit your own project files.

```
$ git init
$ git add .
$ git commit -m "Initial commit."
```

Now you are ready to push your project to your chosen code hosting service.
