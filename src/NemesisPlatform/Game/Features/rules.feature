Feature: Правила раундов
  Чтобы проводить раунды с разной логикой администраторы должны
  иметь возможность изменять ключевые моменты логики раундов в системе

  Background:
    Given empty database
    Given a site with:
      | full_name     | short_name | base_url  | support_email | active | theme                 |
      | Тестовый сайт | test       | localhost | admin@test    | 1      | basic_bootstrap_theme |
    Given a "simple_account_team" block for "Тестовый сайт" weighted 1 at "account"

    Given a season in "Тестовый сайт" with:
      | name         | short_name | description      | active | registration_open |
      | Тестовый сезон | testseason | Сезон для тестов | 1      | 1                 |

    Given a combined league "Профессиональная лига" in "Тестовый сезон"
    Given a category "Профессионал" in "Профессиональная лига"

    Given users with:
      | email      | lastname | firstname | middlename |
      | user1@team | Иванов   | Иван      | Иванович   |
      | user2@team | Петров   | Петр      | Петрович   |

    Given season data for "Тестовый сезон" with:
      | user       | category     |
      | user1@team | Профессионал |
      | user2@team | Профессионал |

    And auto redirection enabled
    Given I am on the homepage
    And I logged in as "user1@team"
    Given I am on "/account/"
    And I should see "user1@team"

  Scenario: Создание нескольких команд невозможно в сезоне с правилом "Одна команда"
    Given a "single team" rule for season "Тестовый сезон"
    Given a "create team" rule for season "Тестовый сезон"
    Given I am on "/account/"
    Then I should see "Создать команду"
    And I should not see "Тестовая команда"
    When I follow "Создать команду"
    And I fill in the following:
      | Название  | Тестовая команда   |
      | О команде | Команда для тестов |
    And I press "Создать"
    Then I should be on "/account/"
    And I should see "Тестовая команда"
    Then I should not see "Создать команду"

  Scenario: Создание нескольких команд невозможно в сезоне с без правила "Одна команда"
    Given a "create team" rule for season "Тестовый сезон"
    Given I am on "/account/"
    Then I should see "Создать команду"
    And I should not see "Тестовая команда"
    When I follow "Создать команду"
    And I fill in the following:
      | Название  | Тестовая команда   |
      | О команде | Команда для тестов |
    And I press "Создать"
    Then I should be on "/account/"
    And I should see "Тестовая команда"
    Then I should see "Создать команду"
    And I should not see "Вторая команда"
    When I follow "Создать команду"
    And I fill in the following:
      | Название  | Вторая команда             |
      | О команде | Для многопроектовых команд |
    And I press "Создать"
    Then I should be on "/account/"
    And I should see "Тестовая команда"
    And I should see "Вторая команда"
    And I should see "Создать команду"

  Scenario: Кнопка приглашения не доступна при максимальном числе участников в команде
    Given a "create team" rule for season "Тестовый сезон"
    Given a "team max members" rule for season "Тестовый сезон"
      | max | 1 |
    Given I am on "/account/"
    Then I should see "Создать команду"
    And I should not see "Тестовая команда"
    When I follow "Создать команду"
    And I fill in the following:
      | Название  | Тестовая команда   |
      | О команде | Команда для тестов |
    And I press "Создать"
    Then I should be on "/account/"
    And I should see "Тестовая команда"
    And I should see "Пригласить" in the ".disabled" element
    Given I logged in as "user2@team"
    And I am on "/account/"
    When I view team "Тестовая команда"
    Then I should not see "Подать заявку"

  Scenario: Кнопка приглашения доступна при числе участников меньше максимального
    Given a "create team" rule for season "Тестовый сезон"
    Given a "team max members" rule for season "Тестовый сезон"
      | max | 2 |
    Given I am on "/account/"
    Then I should see "Создать команду"
    And I should not see "Тестовая команда"
    When I follow "Создать команду"
    And I fill in the following:
      | Название  | Тестовая команда   |
      | О команде | Команда для тестов |
    And I press "Создать"
    Then I should be on "/account/"
    And I should see "Тестовая команда"
    And I should see "Пригласить" in the "a.btn[title=Пригласить]" element
    Given I logged in as "user2@team"
    And I am on "/account/"
    When I view team "Тестовая команда"
    Then I should see "Подать заявку"
