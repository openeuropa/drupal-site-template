<?php

declare(strict_types = 1);

namespace DrupalSiteTemplate\composer;

use Composer\Json\JsonFile;
use Composer\Script\Event;

/**
 * Setup wizard to handle user input during initial composer installation.
 */
class SetupWizard {

  /**
   * The setup wizard.
   *
   * PHP Mess Detector is throwing unreasonable errors.
   * Remove the following @SuppressWarnings once OPENEUROPA-792 is fixed.
   * @link https://webgate.ec.europa.eu/CITnet/jira/browse/OPENEUROPA-792
   *
   * @SuppressWarnings(PHPMD.LongVariable)
   *
   * @param \Composer\Script\Event $event
   *   The Composer event that triggered the wizard.
   *
   * @return bool
   *   TRUE on success.
   *
   * @throws \Exception
   *   Thrown when an error occurs during the setup.
   */
  public static function setup(Event $event): bool {
    // Load the Composer manifest so we can manipulate it.
    $composer_filename = $event->getComposer()->getConfig()->getConfigSource()->getName();
    $composer_json = new JsonFile($composer_filename);
    $config = $composer_json->read();

    // Ask for the project name, and suggest the various machine names.
    $project_name = $event->getIO()->ask('<info>What is the (human readable) project name?</info> [<comment>My Europa Site</comment>]? ', 'My Europa Site');
    $organization_name = $event->getIO()->ask('<info>What is the (human readable) name of the organization?</info> [<comment>OpenEuropa</comment>]? ', 'OpenEuropa');

    $sanitized_project_name = strtolower(preg_replace('/[^a-zA-Z ]/', '', trim($project_name)));
    $sanitized_organization_name = preg_replace('/[^a-zA-Z ]/', '', trim($organization_name));
    $camelcased_organization_name = preg_replace('/ /', '', ucwords($sanitized_organization_name));

    $machine_name = preg_replace('/\s+/', '_', $sanitized_project_name);
    $machine_name = $event->getIO()->ask('<info>What is the (machine readable) project name?</info> [<comment>' . $machine_name . '</comment>]? ', $machine_name);

    $package_name = preg_replace('/\s+/', '-', strtolower($sanitized_organization_name)) . '/' . preg_replace('/\s+/', '-', $sanitized_project_name);
    $package_name = $event->getIO()->ask('<info>What is the package name?</info> [<comment>' . $package_name . '</comment>]? ', $package_name);

    $description = 'A website built on the OpenEuropa platform.';
    $description = $event->getIO()->ask('<info>Provide a description</info> [<comment>' . $description . '</comment>]? ', $description);

    // Define the namespace for the project.
    $namespace = $camelcased_organization_name . '\\' . $machine_name . '\\';

    // Update values in the Composer manifest.
    $config['name'] = $package_name;
    $config['description'] = $description;

    if (!empty($config['autoload']['psr-4'])) {
      unset($config['autoload']['psr-4']);
    }
    $config['autoload']['psr-4'][$namespace] = './src/';

    // Update PHP namespaces.
    $filenames = glob('src/*/*.php');
    if ($filenames === FALSE) {
      throw new \RuntimeException('An error occurred while reading the contents of the src/ folder.');
    }
    $filenames[] = 'behat.yml.dist';
    foreach ($filenames as $filename) {
      $file = file_get_contents($filename);
      $file = preg_replace('/' . preg_quote('OpenEuropa\my_site\\', '/') . '/', $namespace, $file);
      file_put_contents($filename, $file);
    }

    // Update the configuration file.
    $file = file_get_contents('runner.yml.dist');
    $file = preg_replace('/My OpenEuropa site/', trim($project_name), $file);
    $file = preg_replace('/openeuropa_site/', $machine_name, $file);
    file_put_contents('runner.yml.dist', $file);

    // Setup the site README.md.
    unlink('README.md');
    rename('README.md.dist', 'README.md');

    // Setup the site .gitignore.
    unlink('.gitignore');
    rename('.gitignore.dist', '.gitignore');

    // Remove the CHANGELOG.md.
    if (file_exists('CHANGELOG.md')) {
      unlink('CHANGELOG.md');
    }

    // Remove the .drone.yml.
    unlink('.drone.yml');

    // Remove the configuration related to the setup wizard.
    unset($config['scripts']['cleanup']);
    unset($config['scripts']['setup']);
    $config['autoload']['classmap'] = array_diff($config['autoload']['classmap'], ['scripts/composer/SetupWizard.php']);
    if (empty($config['autoload']['classmap'])) {
      unset($config['autoload']['classmap']);
    }
    $config['scripts']['post-create-project-cmd'] = array_diff($config['scripts']['post-create-project-cmd'], ['@cleanup']);
    if (empty($config['scripts']['post-create-project-cmd'])) {
      unset($config['scripts']['post-create-project-cmd']);
    }
    $config['scripts']['post-root-package-install'] = array_diff($config['scripts']['post-root-package-install'], ['@setup']);
    if (empty($config['scripts']['post-root-package-install'])) {
      unset($config['scripts']['post-root-package-install']);
    }

    $composer_json->write($config);

    // Dump autoload after updating composer.json "autoload" values.
    exec('composer dump-autoload');

    return TRUE;
  }

  /**
   * Removes the setup wizard.
   */
  public static function cleanup(): void {
    unlink('scripts/composer/SetupWizard.php');
  }

}
