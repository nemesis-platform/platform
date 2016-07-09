Feature: Создание команды

  Background:
    Given empty database
    Given a site with:
      | full_name     | short_name | base_url  | support_email | active | theme                 |
      | Тестовый сайт | test       | localhost | admin@test    | 1      | basic_bootstrap_theme |
    Given a "simple_account_team" block for "Тестовый сайт" weighted 1 at "account"

    Given a season in "Тестовый сайт" with:
      | name         | short_name | description      | active | registration_open |
      | Тестовый сезон | testseason | Сезон для тестов | 1      | 1                 |

    Given a "create team" rule for season "Тестовый сезон"

    Given a combined league "Профессиональная лига" in "Тестовый сезон"
    Given a category "Профессионал" in "Профессиональная лига"


    Given users with:
      | email      | lastname | firstname | middlename |
      | user1@team | Иванов   | Иван      | Иванович   |

    Given season data for "Тестовый сезон" with:
      | user       | category     |
      | user1@team | Профессионал |

    And auto redirection enabled
    Given I am on the homepage
    And I logged in as "user1@team"
    Given I am on "/account/"
    And I should see "user1@team"

  Scenario: Создание команды
    Given user "user1@team" has password "password"
    Then I should see "Создать команду"
    And I should not see "Тестовая команда"
    When I follow "Создать команду"
    And I fill in the following:
      | Название  | Тестовая команда   |
      | О команде | Команда для тестов |
    And I press "Создать"
    Then I should be on "/account/"
    And I should see "Тестовая команда"
    And I should see "Члены вашей команды"
    When I follow "Тестовая команда"
    Then I should see "Тестовая команда"
    And I should see "Иванов"
    And I should see "Команда для тестов"
