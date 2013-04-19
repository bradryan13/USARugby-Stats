Feature: Login with AllPlayers
  As an anonymous user
  I can click on a link to sign in with an allplayers account.

  Scenario: Create new account and login to AllPlayers.com.
    Given I am on homepage
    When I follow "login-allplayers"
    Then I should see "Log in"
    When I follow "Create new account"
    Then I should see "Create your account"
    When I fill in the following:
      |First Name:       |test      |
      |Last Name:        |from mink |
      |Password:         |test123   |
      |Confirm Password: |test123   |
      And I get rand email and fill in "E-mail:"
      And I select "Jan" from "field_birth_date[month]"
      And I select "1" from "field_birth_date[day]"
      And I select "1985" from "field_birth_date[year]"
      And I select "Male" from "field_user_gender"
      And I solve captcha and fill in "captcha_response"
      And I press "edit-submit"
    Then I should see "Access content available to your user"
    When I check "edit-levels-*"
      And I press "edit-confirm"
    Then I should see "Add New Competition"
