@fixture-cases.yml
@fixture-custom_report.yml
@fixture-activities.yml
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

  Scenario: Set activity list configuration
    When I go to Customers/Contacts
    And I click View Charlie in grid
    Then there is 10 records in activity list
    And activity list must be sorted descending by updated date
    When I go to System/Configuration
    And I click "Display settings"
    And I set configuration to:
      | Sort direction            | Ascending |
      | Items Per Page By Default | 25        |
    And I save form
    When I go to Customers/Contacts
    And I click View Charlie in grid
    Then there is 13 records in activity list
    And activity list must be sorted ascending by updated date
    When I go to System/Configuration
    And I click "Display settings"
    And I set configuration to:
      | Sort by field             | Created date |
      | Sort direction            | Descending   |
      | Items Per Page By Default | 10           |
    And I save form
    When I go to Customers/Contacts
    And click View Charlie in grid
    Then I see following records in activity list with provided order:
      | -1 days |
      | -2 days |
      | -3 days |
      | -4 days |
      | -5 days |
      | -6 days |
      | -7 days |
      | -8 days |
      | -9 days |

  Scenario: Change sidebar settings
    Given right sidebar is visible
    When I go to System/Configuration
    And click "Display settings"
    And I set configuration to:
      | Enable left sidebar  | Yes |
      | Enable right sidebar | No  |
    And save form
    Then right sidebar is out of sight
    And left sidebar is visible
    When I go to System/Configuration
    And click "Display settings"
    And I set configuration to:
      | Enable left sidebar  | Yes |
      | Enable right sidebar | Yes |
    And save form
    Then left sidebar is visible
    And right sidebar is visible

  Scenario: Change calendar color settings
    When I set configuration to:
      | Event colors    | Apple green, Cornflower Blue, Mercury, Melrose, Mauve, Alizarin Crimson, Aqua, Aquamarine, Azure, Beige, Black, Lime |
      | Calendar colors | Alizarin Crimson, Beige, Black, Lime, Melrose, Mercury, Apple green, Cornflower Blue, Mauve, Aqua, Aquamarine, Azure |
    And save form
    And go to Activities/ Calendar Events
    And press "Create Calendar event"
    Then I should see following available "Event Form" colors:
      | Apple green, Cornflower Blue, Mercury, Melrose, Mauve, Alizarin Crimson, Aqua, Aquamarine, Azure, Beige, Black |
    When I click My Calendar in user menu
    Then I should see following available "Calendar" colors:
      | Alizarin Crimson, Beige, Black, Lime, Melrose, Mercury, Apple green, Cornflower Blue, Mauve, Aqua, Aquamarine, Azure |

  Scenario: Change taxonomy color settings
    When I go to System/Configuration
    And I click "Display settings"
    And set configuration to:
      | Taxonomy Colors | Cornflower Blue, Mercury, Melrose, Mauve, Alizarin Crimson, Aqua, Aquamarine, Azure, Beige, Black, Lime |
    And save form
    And go to System/Tags Management/Taxonomies
    And press "Create Taxonomy"
    Then I should see following available "TaxonomyForm" colors:
      | Cornflower Blue, Mercury, Melrose, Mauve, Alizarin Crimson, Aqua, Aquamarine, Azure, Beige, Black, Lime |

    Scenario: Change reports settings
      When I go to Reports & Segments/Calendar Events/Test Report
      Then I should not see "Show SQL Query"
      When I go to System/Configuration
      And click "Display settings"
      And set configuration to:
        | Display SQL in Reports and Segments | true |
      And save form
      Then I go to Reports & Segments/Calendar Events/Test Report
      And I should see "Show SQL Query"
