@fixture-OroSecurityTestBundle:user.yml
@fixture-OroSecurityTestBundle:shipping-method.yml
@fixture-OroSecurityTestBundle:shipping-method-rule.yml
@fixture-OroSecurityTestBundle:payment-method.yml
@fixture-OroSecurityTestBundle:payment-method-rule.yml
@fixture-OroSecurityTestBundle:commerce-fixtures.yml
@fixture-OroSecurityTestBundle:order.yml
Feature: Store front MUST NOT contain XSS vulnerabilities on all accessible pages

  Scenario: Create different window session
    Given sessions active:
      | Admin  |first_session |
      | User   |second_session|
    Given I proceed as the Admin
    And I login to admin area as fixture user "xss_user"
    Given I proceed as the User
    And I login to store frontend as fixture customer user "amanda"

  Scenario: Check store front profile pages for XSS vulnerability
    Given I proceed as the User
    When I visiting pages listed in "frontend profile urls"
    Then I should not get XSS vulnerabilities

  Scenario: Check store front catalog pages for XSS vulnerability
    Given I proceed as the Admin
    And I go to System/ Configuration
    And follow "Commerce/Catalog/Special Pages" on configuration sidebar
    And uncheck "Use default" for "Enable all products page" field
    And I check "Enable all products page"
    And save form
    Then I should see "Configuration saved" flash message
    When I proceed as the User
    And I visiting pages listed in "frontend catalog urls"
    Then I should not get XSS vulnerabilities

  Scenario: Check store front product view pages with default template for XSS vulnerability
    Given I proceed as the User
    When I visiting pages listed in "frontend product view urls"
    Then I should not get XSS vulnerabilities

  Scenario: Check store front product view pages with Short Page for XSS vulnerability
    Given I proceed as the Admin
    And I follow "Commerce/Design/Theme" on configuration sidebar
    And fill "Page Templates Form" with:
      | Use Default  | false             |
      | Product Page | Short page        |
    And save form
    And I should see "Configuration saved" flash message
    When I proceed as the User
    And I visiting pages listed in "frontend product view urls"
    Then I should not get XSS vulnerabilities

  Scenario: Check store front product view pages with Two columns page for XSS vulnerability
    Given I proceed as the Admin
    And fill "Page Templates Form" with:
      | Use Default  | false             |
      | Product Page | Two columns page  |
    And save form
    And I should see "Configuration saved" flash message
    When I proceed as the User
    And I visiting pages listed in "frontend product view urls"
    Then I should not get XSS vulnerabilities

  Scenario: Check store front product view pages with List page for XSS vulnerability
    Given I proceed as the Admin
    And fill "Page Templates Form" with:
      | Use Default  | false             |
      | Product Page | List page         |
    And save form
    And I should see "Configuration saved" flash message
    When I proceed as the User
    And I visiting pages listed in "frontend product view urls"
    Then I should not get XSS vulnerabilities

  Scenario: Check multi step checkout for XSS vulnerability
    Given I visiting pages listed in "frontend shopping list view url"
    And I click "Create Order"
    And I click "Continue"
    And I click "Continue"
    And I click "Continue"
    And I click "Continue"
    Then I should not get XSS vulnerabilities

  Scenario: Check one step step checkout for XSS vulnerability
    Given I proceed as the Admin
    And I go to System/ Workflows
    And I click Activate "Single Page Checkout" in grid
    And I click "Activate"
    Then I proceed as the User
    And I visiting pages listed in "frontend shopping list view url"
    And I click "Create Order"
    Then I should not get XSS vulnerabilities

  Scenario: Check store front order related for XSS vulnerability
    Given I proceed as the Admin
    # Enable access to RFQ edit form
    And I go to System/ Workflows
    And I click Deactivate "RFQ Submission Flow" in grid
    And I click "Yes, Deactivate"
    # Make quote available at store front
    Then I set quote "quote" status to "sent_to_customer"
    Then I proceed as the User
    When I visiting pages listed in "frontend order related urls"
    Then I should not get XSS vulnerabilities
