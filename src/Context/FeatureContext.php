<?php

declare(strict_types = 1);

namespace OpenEuropa\my_site\Context;

use Behat\Behat\Context\SnippetAcceptingContext;
use Drupal\DrupalExtension\Context\RawDrupalContext;

/**
 * Defines step definitions that are generally useful for the project.
 */
class FeatureContext extends RawDrupalContext implements SnippetAcceptingContext {

  /**
   * Checks that a 200 OK response occurred.
   *
   * @Then I should get a valid web page
   */
  public function assertSuccessfulResponse(): void {
    $this->assertSession()->statusCodeEquals(200);
  }

  /**
   * Checks that a 403 Access Denied error occurred.
   *
   * @Then I should get an access denied error
   */
  public function assertAccessDenied(): void {
    $this->assertSession()->statusCodeEquals(403);
  }

}
