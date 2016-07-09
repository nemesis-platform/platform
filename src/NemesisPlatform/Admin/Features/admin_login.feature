Feature: Управление порталом
  Чтобы провести чемпионат, администратор должен
  иметь возможность войти в панель управления

  Background:
    Given empty database
    And users with:
      | email      | lastname | firstname | middlename |
      | admin@test | Петров   | Петр      | Петрович   |

    Given I am on the homepage
    And I log in as "admin@test" with roles:
      | ROLE_ADMIN |

  Scenario: Вход в панель управления
    Given I am on "/admin/"
    Then I should see "Панель управления"
