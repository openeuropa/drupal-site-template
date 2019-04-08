<?php

declare(strict_types = 1);

namespace DrupalSiteTemplate\composer;

use Composer\Json\JsonFile;
use Composer\Script\Event;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Setup wizard to handle user input during initial composer installation.
 */
class CheckSetupWizard {

  /**
   * Check the function Setup Wizard.
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
  public static function check(Event $event): bool {
    // Check the configuration file.
    $composer_filename = $event->getComposer()->getConfig()->getConfigSource()->getName();
    $composer_json = new JsonFile($composer_filename);
    $config = $composer_json->read();

    self::assertConfigChange($config['name'], 'openeuropa/drupal-site-template');
    self::assertConfigChange($config['description'], 'A template for setting up an OpenEuropa Drupal site.');

    // Check the file structure.
    $filenames = [
      'lib',
      'lib/modules',
      'lib/modules/.gitkeep',
      'lib/profiles',
      'lib/profiles/.gitkeep',
      'lib/themes',
      'lib/themes/.gitkeep',
      'config',
      'config/sync',
      'config/sync/.gitkeep',
      'vendor',
      'web',
    ];
    foreach ($filenames as $filename) {
      self::assertExistFile($filename);
    }

    $filenames = [
      'README.md.dist',
      '.gitignore.dist',
      '.git',
      'CHANGELOG.md',
      '.drone.yml',
      'packages.json',
      '.github/pull_request_template.md',
      '.github',
    ];
    foreach ($filenames as $filename) {
      self::assertNotExistFile($filename);
    }

    // Check runner.yml.dist.
    $strings = [
      'My OpenEuropa site',
      'openeuropa_site',
    ];
    $assert_filenames = [
      'runner.yml.dist',
      '.env',
    ];
    foreach ($strings as $string) {
      foreach ($assert_filenames as $assert_filename) {
        self::assertFileNotContain($assert_filename, $string);
      }
    }

    $event->getIO()->write('Setup wizard checked.');
    return TRUE;
  }

  /**
   * Assert that the the configuration has been changed.
   *
   * @param string $previous
   *   The previous value.
   * @param string $actual
   *   The value that should be tested.
   */
  private static function assertConfigChange(string $previous, string $actual = ''): void {
    if ($actual === '') {
      throw new \RuntimeException('The value tested is NULL.');
    }

    if ($previous === $actual) {
      throw new \RuntimeException('The value tested did not change.');
    }
  }

  /**
   * Assert that the file exists.
   *
   * @param string $filename
   *   The filename of the file.
   */
  private static function assertExistFile(string $filename): void {
    $fs = new Filesystem();

    if (!$fs->exists($filename)) {
      throw new \RuntimeException('The ' . $filename . ' file/folder does not exist.');
    }
  }

  /**
   * Assert that the file doesn't exist.
   *
   * @param string $filename
   *   The filename of the file.
   */
  private static function assertNotExistFile(string $filename): void {
    $fs = new Filesystem();

    if ($fs->exists($filename)) {
      throw new \RuntimeException('The ' . $filename . ' file/folder should not exist.');
    }
  }

  /**
   * Assert that a file doesn't contain a string.
   *
   * @param string $filename
   *   The filename of the file.
   * @param string $string
   *   A string that the file should not contain.
   */
  private static function assertFileNotContain(string $filename, string $string): void {
    if (strpos(file_get_contents($filename), $string) !== FALSE) {
      throw new \RuntimeException('The ' . $filename . ' file should not contain $string.');
    }
  }

}
