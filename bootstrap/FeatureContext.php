<?php

use Behat\Behat\Context\ClosuredContextInterface,
  Behat\Behat\Context\TranslatedContextInterface,
  Behat\Behat\Context\BehatContext,
  Behat\Behat\Exception\PendingException,
  Behat\Behat\Event\FeatureEvent;
use Behat\Gherkin\Node\PyStringNode,
  Behat\Gherkin\Node\TableNode;
use Drupal\DrupalExtension\Event\EntityEvent;
use Drupal\Component\Utility\Random;
use Drupal\Exception\BootstrapException;
use Drupal\DrupalExtension\Context\DrupalContext;


/**
 * Features context.
 */
class FeatureContext extends DrupalContext
{

  public $users = array();

  /**
   * Initializes context.
   * Every scenario gets it's own context object.
   *
   * @param array $parameters context parameters (set them up through behat.yml)
   */
  public function __construct(array $parameters)
  {
    // Initialize your context here
  }


  /**
   * Written using the same structure as assertAuthenticatedByRole but with
   * added domain access specific information.
   *
   * @Given /^I login as a "([^"]*)" from "([^"]*)"$/
   */
  public function iLoginAsAFrom($role, $domain) {

    // Check if a user with this role is already logged in.
    if ($this->loggedIn() && $this->user && isset($this->user->role) && $this->user->role == $role) {
      return TRUE;
    }

    // Define your domain ID's here with a short code which you can call from
    // the feature
    $domains = array(
      'global' => 1,
      'se' => 2,
    );

    // Create user (and project)
    $user = (object) array(
      'name' => Random::name(8),
      'pass' => Random::name(16),
      'role' => $role,
    );
    $user->mail = "{$user->name}@example.com";

    // Add the domain access specific options here.
    if (isset($domains[$domain])) {
      $user->domain_user = array(
        $domains[$domain] => $domains[$domain],
      );
    }

    $this->getDriver()->userCreate($user);

    $this->users[$user->name] = $this->user = $user;

    if ($role == 'authenticated user') {
      // Nothing to do.
    }
    else {
      $this->getDriver()->userAddRole($user, $role);
    }

    // Login.
    $this->login();

    return TRUE;
  }


  /**
   * Because we're creating users in this context, the default drupal context
   * won't clear these users out so we have to do it ourselves.
   *
   * This code is taken from afterScenario().
   *
   * @AfterScenario
   */
  public function deleteUsers() {
    // Remove any users that were created.
    if (!empty($this->users)) {
      foreach ($this->users as $user) {
        $this->getDriver()->userDelete($user);
      }
      $this->getDriver()->processBatch();
    }
  }

}
