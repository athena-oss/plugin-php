Feature: Anonymous User performs a search

  As a Anonymous User
  I want to perform a search for a string
  So that I can get a list of results related with my search

  Scenario: Searched string returns results
    Given the current location is the home page
    When the Anonymous User writes "athena" in the search box
    And the Anonymous User performs a click in the search button
    Then the current location should be results page