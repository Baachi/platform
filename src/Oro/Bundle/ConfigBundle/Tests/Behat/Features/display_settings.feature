@fixture-cases.yml
Feature: Display settings manage
  In order to control system display behavior
  As Administrator
  I need to be able to change display settings parameters

  Scenario: Show/hide recent emails in user bar
    Given I login as administrator
    Then recent emails block must be visible
    When I go to System/Configuration
    And I click "Display settings"
    And I set configuration to:
      | Show recent emails | false |
    And I save form
    Then recent emails block should not be visible

  Scenario: Enable/disable WYSIWYG editor
    When I go to Activities/Calendar Events
    And press "Create Calendar event"
    And I should see WYSIWYG editor
    When I go to System/Configuration
    And I click "Display settings"
    When I set configuration to:
      | Enable WYSIWYG editor | false |
    And I save form
    And I go to Activities/Cases
    And press "Create Case"
    Then I should not see WYSIWYG editor

  Scenario: Change records in grid per page amount
    When I go to Activities/Cases
    Then per page amount must be 25
    And records in grid should be 25
    When I go to System/Configuration
    And I click "Display settings"
    And I set configuration to:
      | Items per Page by Default | 10 |
    And I save form
    When I go to Activities/Cases
    Then per page amount must be 10
    And records in grid should be 10

  Scenario: Enable/disable locking grid header
    When I go to Activities/Cases
    Then I see that grid has scrollable header
    When I go to System/Configuration
    And I click "Display settings"
    And I set configuration to:
      | Lock headers in grids | off |
    And I save form
    When I go to Activities/Cases
    Then I see that grid header is sticky

  Scenario: Enable/disable navigation through grid entity from a view page
    When I go to Activities/Cases
    And I click view 1 in grid
    Then I should see entity pagination controls
    When I go to System/Configuration
    And I click "Display settings"
    And I set configuration to:
      | Record Pagination | off |
    And I save form
    Then I go to Activities/Cases
    And I click view 1 in grid
    Then I should see no pagination controls
    And I go to System/Configuration
    And I click "Display settings"
    Then I set configuration to:
      | Record Pagination | on |
    And I save form

  Scenario: Set record pagination limit
    When I go to Activities/Cases
    And I click view 1 in grid
    Then I should see entity pagination controls
    When I go to System/Configuration
    And I click "Display settings"
    And I set configuration to:
      | Record Pagination limit | 20 |
    And I save form
    Then I go to Activities/Cases
    And I click view 1 in grid
    Then I should see no pagination controls
