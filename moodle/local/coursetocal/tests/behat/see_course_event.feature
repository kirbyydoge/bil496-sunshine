@local @local_coursetocal
Feature: The student can see the course as calendar event.

    Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | John      | Doe      | student1@example.com |

    And the following "courses" exist:
      | fullname | shortname | category | enddate         | startdate        |
      | Course 1 | C1        | 0        | ## today ## | ## 8 days ago ## |

    Scenario: See the course in the calendar.
    Given I log in as "student1"
    And I am on site homepage
    And I am viewing site calendar
    And I should see "Course 1"
