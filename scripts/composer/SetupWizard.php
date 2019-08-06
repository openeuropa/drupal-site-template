<?php

declare(strict_types = 1);

namespace DrupalSiteTemplate\composer;

use Composer\Json\JsonFile;
use Composer\Script\Event;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Setup wizard to handle user input during initial composer installation.
 *
 * @phpcs:ignorefile Generic.PHP.ForbiddenFunctions
 */
class SetupWizard {

  /**
   * The setup wizard.
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
    $composer_filename = $event->getComposer()->getConfig()->getConfigSource()->getName();
    $params = [];

    // Ask for the project name, and suggest the various machine names.
    $params['project_name'] = 'My Europa Site';
    $params['organization_name'] = 'OpenEuropa';
    $params['description'] = 'A website built on the OpenEuropa platform.';

    $params['project_name'] = $event->getIO()->ask('<info>What is the (human readable) project name?</info> [<comment>' . $params['organization_name'] . '</comment>]? ', $params['organization_name']);
    $params['organization_name'] = $event->getIO()->ask('<info>What is the (human readable) name of the organization?</info> [<comment>' . $params['organization_name'] . '</comment>]? ', $params['organization_name']);

    $params['sanitized_project_name'] = strtolower(preg_replace('/[^a-zA-Z ]/', '', trim($params['project_name'])));
    $params['sanitized_organization_name'] = preg_replace('/[^a-zA-Z ]/', '', trim($params['organization_name']));
    $params['camelcased_organization_name'] = preg_replace('/ /', '', ucwords($params['sanitized_organization_name']));

    $params['machine_name'] = preg_replace('/\s+/', '_', $params['sanitized_project_name']);
    $params['package_name'] = preg_replace('/\s+/', '-', strtolower($params['sanitized_organization_name'])) . '/' . preg_replace('/\s+/', '-', $params['sanitized_project_name']);

    $params['machine_name'] = $event->getIO()->ask('<info>What is the (machine readable) project name?</info> [<comment>' . $params['machine_name'] . '</comment>]? ', $params['machine_name']);
    $params['package_name'] = $event->getIO()->ask('<info>What is the package name?</info> [<comment>' . $params['package_name'] . '</comment>]? ', $params['package_name']);

    $params['description'] = $event->getIO()->ask('<info>Provide a description</info> [<comment>' . $params['description'] . '</comment>]? ', $params['description']);

    // Define the namespace for the project.
    $params['namespace'] = $params['camelcased_organization_name'] . '\\' . $params['machine_name'] . '\\';

    self::updateConfig($composer_filename, $params);
    self::updateRunnerFile($params);
    self::cleanFile();
    self::createLibDir();
    self::composerDumpAutoload();

    return TRUE;
  }

  /**
   * Update PHP namespaces.
   *
   * @param string $composer_filename
   *   The filename of composer.
   * @param array $params
   *   The array of parameters.
   *
   * @throws \Exception
   */
  private static function updateConfig(string $composer_filename, array $params): void {
    // Load the Composer manifest so we can manipulate it.
    $composer_json = new JsonFile($composer_filename);
    $config = $composer_json->read();

    // Update values in the Composer manifest.
    $config['name'] = $params['package_name'];
    $config['description'] = $params['description'];

    // Remove the configuration related to the setup wizard.
    $config['autoload']['classmap'] = array_diff($config['autoload']['classmap'], ['scripts/composer/SetupWizard.php', 'scripts/composer/CheckSetupWizard.php']);
    if (empty($config['autoload']['classmap'])) {
      unset($config['autoload']['classmap']);
    }

    if (empty($config['autoload'])) {
      unset($config['autoload']);
    }

    $config['scripts']['post-create-project-cmd'] = array_diff($config['scripts']['post-create-project-cmd'], ['DrupalSiteTemplate\\composer\\CheckSetupWizard::check', 'DrupalSiteTemplate\\composer\\SetupWizard::cleanup']);
    if (empty($config['scripts']['post-create-project-cmd'])) {
      unset($config['scripts']['post-create-project-cmd']);
    }

    $config['scripts']['post-root-package-install'] = array_diff($config['scripts']['post-root-package-install'], ['DrupalSiteTemplate\\composer\\SetupWizard::setup']);
    if (empty($config['scripts']['post-root-package-install'])) {
      unset($config['scripts']['post-root-package-install']);
    }

    if (empty($config['scripts'])) {
      unset($config['scripts']);
    }

    $composer_json->write($config);
  }

  /**
   * Update the configuration file.
   *
   * @param array $params
   *   The array of parameters.
   */
  private static function updateRunnerFile(array $params): void {
    $filenames = [
      'runner.yml.dist',
      '.env',
    ];

    foreach ($filenames as $filename) {
      $file = file_get_contents($filename);

      $file = preg_replace('/My OpenEuropa site/', trim($params['project_name']), $file);
      $file = preg_replace('/openeuropa_site/', $params['machine_name'], $file);

      file_put_contents($filename, $file);
    }
  }

  /**
   * Clean file from the repo.
   */
  private static function cleanFile(): void {
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

    // Remove the CI files.
    unlink('.drone.yml');
    unlink('packages.json');

    // Remove github files.
    unlink('.github/pull_request_template.md');
    rmdir('.github');
  }

  /**
   * Create all folder for custom code on a lib folder.
   */
  private static function createLibDir(): void {
    // Create folder for custom code.
    $fs = new Filesystem();
    $fs->mkdir('lib');

    $dirs = [
      'modules',
      'profiles',
      'themes',
    ];
    foreach ($dirs as $dir) {
      if (!$fs->exists('lib/' . $dir)) {
        $fs->mkdir('lib/' . $dir);
        $fs->touch('lib/' . $dir . '/.gitkeep');
      }
    }

    // Create folder for configuration.
    $fs->mkdir('config');
    $fs->mkdir('config/sync');
    $fs->touch('config/sync/.gitkeep');
  }

  /**
   * Dump autoload after updating composer.json "autoload" values.
   */
  private static function composerDumpAutoload(): void {
    exec('composer dump-autoload');
  }

  /**
   * Remove the setup wizard file.
   *
   * @param \Composer\Script\Event $event
   *   The Composer event that triggered the wizard.
   */
  public static function cleanup(Event $event): void {
    unlink('scripts/composer/CheckSetupWizard.php');
    unlink('scripts/composer/SetupWizard.php');
    rmdir('scripts/composer');
    rmdir('scripts');
    $event->getIO()->write('Setup wizard file cleaned.');
  }

}
