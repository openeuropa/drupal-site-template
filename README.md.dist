# OpenEuropa Drupal project.

This project starter kit will create a website using
[OpenEuropa components](https://github.com/openeuropa/documentation/blob/master/docs/openeuropa-components.md).
It will install only two components:

- [The OpenEuropa Profile](https://github.com/openeuropa/oe_profile):
  a lightweight Drupal installation profile that includes the minimal number of
  modules to help get your started
- [The OpenEuropa Theme](https://github.com/openeuropa/oe_theme): the official
  Drupal 8 theme of the European Commission which will ensure that the project
  complies with the [European Component Library](https://github.com/ec-europa/europa-component-library)
  guidelines.

In order to build the functionality of the website you are free to use any of the
[OpenEuropa components](https://github.com/openeuropa/openeuropa/blob/master/docs/openeuropa-components.md).

## Prerequisites

You need to have the following software installed on your local development environment:

* [Docker Compose](https://docs.docker.com/compose/install/)
* PHP 7.2 or greater (needed to run [GrumPHP](https://github.com/phpro/grumphp) Git hooks)

Having the following installed locally is also recommended, but not mandatory:

* [Composer](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx).

**Please be aware:** the OpenEuropa team will only address support requests
if you use the provided `docker-compose.yml`.

## Troubleshooting

If you run `composer install` for the first time from within the Docker container GrumPHP
will set its Git hook paths to the container's ones.

If you get such error messages reinitialize GrumPHP paths on your host machine
(meaning, outside the container) by running:

```bash
./vendor/bin/grumphp git:deinit
./vendor/bin/grumphp git:init
```

## Configuration

The project ships with default setup configuration that is intended to run the
website on the Docker containers we provide.

To customize the default configuration values copy `runner.yml.dist` to `runner.yml`:

```bash
cp runner.yml.dist runner.yml
```

Now edit `runner.yml` with your most beloved text editor and change setup
configuration as needed.

## Site build and installation

The shipped `docker-compose.yml` file provides the necessary services and tools
to install, run and test an OpenEuropa Drupal 8 site.

By default, Docker Compose reads two files, a `docker-compose.yml` and an
optional `docker-compose.override.yml` file. By convention, the `docker-compose.yml`
contains your base configuration. The override file, as its name implies,
can contain configuration overrides for existing services or entirely new services.

If a service is defined in both files, Docker Compose merges the configurations.

Find more information on Docker Compose extension mechanism on
[the official Docker Compose documentation](https://docs.docker.com/compose/extends/).

To start, run:

```bash
docker-compose up -d
```

This will run the Docker containers in the background, i.e. it will "daemonize" them.

Then:

```bash
docker-compose exec web composer install
docker-compose exec web ./vendor/bin/run toolkit:install-clean
```

The site build will be available in the `web` directory and the site itself
will be reachable at: [http://localhost:8080/web](http://localhost:8080/web).

Before to commit your project on your repository, export the configuration on `config/sync`
using the following command:

```bash
docker-compose exec web ./vendor/bin/drush cex
```

## Commit and push

The final step is to have a new git repository and commit all the files. A
`.gitignore` file is provided to ensure you only commit your own project files.

If you have not been already provided with one please contact your management
and/or the Quality Assurance team.

```bash
git init
git add .
git commit -m "Initial commit."
```

Now you are ready to push your project to your chosen code hosting service.

## Running the tests

To run the coding standards and other static checks:

```bash
docker-compose exec web ./vendor/bin/grumphp run
```

To run Behat tests:

```bash
docker-compose exec web ./vendor/bin/behat
```

## Continuous integration and deployment

To check the status of the continuous integration of your project, go to [Drone](https://drone.fpfis.eu/).

A pipeline - created and maintained by DevOps - is applied by default.
It manages the code review of the code, it runs all tests on the repository and
builds the site artifact for the deployment.

You can control which commands will be ran during deployment by creating
and pushing a `.opts.yml` file.

If none is found the following one will be ran:

```yml
upgrade_commands:
  - './vendor/bin/drush state:set system.maintenance_mode 1 --input-format=integer -y'
  - './vendor/bin/drush updatedb -y'
  - './vendor/bin/drush cache:rebuild'
  - './vendor/bin/drush state:set system.maintenance_mode 0 --input-format=integer -y'
  - './vendor/bin/drush cache:rebuild'
```

The following conventions apply:

- Every push on the site's deployment branch (usually `master`) will trigger
  a deployment on the acceptance environment
- Every new tag on the site's deployment branch (usually `master`) will
  trigger a deployment on production
