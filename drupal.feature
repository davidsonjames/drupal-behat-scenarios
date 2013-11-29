@api
Feature: Drupal
  As a Drupal tester
  I want to be able test Drupal
  So Drupal is tested

  Scenario: Add an image to a form
    When I am on "node/add/page"
    When I fill in the following:
      | Title  | Test Behat Page                                           |
      | Body   | You will need to define your files_path in your behat.yml |
    And I attach the file "test.jpg" to "edit-field-news-image-und-0-upload"
    And I press "Save"