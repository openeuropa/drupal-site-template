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

## Create the project

The project is built using [Composer create-project](https://getcomposer.org/doc/03-cli.md#create-project).
This is the equivalent of doing a `git clone` followed by a `composer install`.

One does not need to be in this repository in order to use the `composer create-project` command. 
The project can be created using the following command:

```bash
composer create-project openeuropa/drupal-site-template --stability=dev <dg-name>-<project-id>-reference
```

For local development, to test the Setup Wizard, run `composer setup` from the root of this project.

This will download the starterkit into the `<dg-name>-<project-id>-reference` folder and a
wizard will ask you for the project name and your organisation. It will use this
information to personalize your project's configuration files.

The installer will then download all dependencies for the project. This process
takes several minutes. At the end you will be asked whether to remove the
existing version history. It is recommended to confirm this question so that you
can start your project with a clean slate.

After installing the dependencies, install a clean installation of your site, using the following command:

```bash
./vendor/bin/run toolkit:install-clean
```

Using default configuration, the development site files should be available in the `build` directory.

Before to commit your project on your repository, export the configuration on `config/sync`
using the following command:.

```bash
./vendor/bin/drush cex
```

## Install and run the project from Github
If you want to install and deploy Drupal Site Template in a local development environment with the Github codebase, here there is a set of steps to making it possible. 


### **Prerequisites:**
+ Docker 
+ Docker Compose

### **Installing Docker**

```bash
sudo apt install -y build-essential apt-transport-https ca-certificates jq curl software-properties-common file
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo apt-key add -
sudo add-apt-repository "deb [arch=amd64] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable"
sudo apt update
sudo apt install -y docker-ce
sudo chmod 666 /var/run/docker*
systemctl is-active docker
```

### **Installing Docker Compose**

```bash
VERSION=$(curl --silent https://api.github.com/repos/docker/compose/releases/latest | jq .name -r)
DESTINATION=/usr/local/bin/docker-compose
sudo curl -L https://github.com/docker/compose/releases/download/${VERSION}/docker-compose-$(uname -s)-$(uname -m) -o $DESTINATION
sudo chmod 755 $DESTINATION
docker-compose --version
```

### **First:** You have to stop your Apache webserver, in order to free the port:80 

```bash
sudo /etc/init.d/apache2 stop
```
### **Second:** Clone the Drupal Site project inside your working directory

```bash
git clone https://github.com/openeuropa/drupal-site-template.git
```

### **Third:** Run the project (general and the web container for Drupal)

```bash 
cd drupal-site-template
docker-compose up -d
docker-compose exec web composer install
```

### **Fourth:** Get the ID of the web container and inspect it

```bash
docker ps
docker container inspect IDCONTAINER
```

### **Fifth:** Get the IP of the container and load it in browser

```bash
sensible-browser IPCONTAINER:8080
```

### **Sixth:** Connect to the container

```bash
docker exec -it IDCONTAINER /bin/bash 
```

### **Seventh:** Run the installer / Task Runner

```bash
./vendor/bin/run toolkit:install-clean
```

### **Eighth:** Export configuration files from database to config/sync

```bash
./vendor/bin/drush cex
```

### **Ninth:** Visit your new project in

```bash
http://IPCONTAINER:8080/web
```

### **Happy Hacking!** 

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
