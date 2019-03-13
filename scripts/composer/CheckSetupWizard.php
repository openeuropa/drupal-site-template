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
   * Check the function setup.
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

    $composer_filename = $event->getComposer()->getConfig()->getConfigSource()->getName();
    // self::updateRunnerFile($params);
    // self::composerDumpAutoload();

    // self::updateConfig($composer_filename, $params);
    $composer_json = new JsonFile($composer_filename);
    $config = $composer_json->read();

    self::assertEqualsConfig($config['name'], 'openeuropa/drupal-site-template');
    self::assertEqualsConfig($config['description'], 'A template for setting up an OpenEuropa Drupal site.');

    // Check Files.
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
      'vendor'
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
      '.github'
    ];
    foreach ($filenames as $filename) {
      self::assertNotExistFile($filename);
    }

    // Check runner.yml.dist
    $strings = [
      'My OpenEuropa site',
      'openeuropa_site'
    ];
    $assert_filename = "runner.yml.dist";
    foreach ($strings as $string) {
      self::assertFileNotContain($assert_filename, $string);
    }

    $event->getIO()->write("Setup wizard checked.");
    return TRUE;
  }

  /**
   * Dump autoload after updating composer.json "autoload" values.
   *
   * @param string $actual
   * @param string $previous
   */
  private static function assertEqualsConfig(string $actual = '', string $previous): void {
    if ($actual === '') {
      throw new \RuntimeException('The value tested is NULL.');
    }

    if ($previous === $actual) {
      throw new \RuntimeException('The value tested did not change.');
    }
  }

  /**
   * Dump autoload after updating composer.json "autoload" values.
   *
   * @param string $actual
   */
  private static function assertNotExistsConfig(string $config = ''): void {
    if ($config === '') {
      throw new \RuntimeException('The value tested is NULL.');
    }
  }

  /**
   * Dump autoload after updating composer.json "autoload" values.
   *
   * @param string $filename
   */
  private static function assertExistFile(string $filename): void {
    $fs = new Filesystem();

    if (!$fs->exists($filename)) {
      throw new \RuntimeException('The ' . $filename . ' file/folder does not exist.');
    }
  }

  /**
   * Dump autoload after updating composer.json "autoload" values.
   *
   * @param string $filename
   */
  private static function assertNotExistFile(string $filename): void {
    $fs = new Filesystem();

    if ($fs->exists($filename)) {
      throw new \RuntimeException('The ' . $filename . ' file/folder should not exist.');
    }
  }

  /**
   * Dump autoload after updating composer.json "autoload" values.
   *
   * @param string $filename
   */
  private static function assertFileNotContain(string $filename, string $string): void {
    if (strpos(file_get_contents($filename), $string) !== false) {
      throw new \RuntimeException('The ' . $filename . ' file should not contain $string.');
    }
  }

}
