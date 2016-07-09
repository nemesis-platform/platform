Feature: Управление командой

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
      | user3@team | Сидоров  | Сидр      | Сидорович  |
      | user4@team | Андреев  | Андрей    | Андреевич  |
      | user5@team | Васьков  | Василий   | Василиевич |

    Given season data for "Тестовый сезон" with:
      | user       | category     |
      | user1@team | Профессионал |
      | user2@team | Профессионал |
      | user3@team | Профессионал |
      | user4@team | Профессионал |
      | user5@team | Профессионал |

    Given team "Тестовая команда" with captain "user1@team" in season "Тестовый сезон"
      | email      |
      | user1@team |
      | user2@team |
      | user3@team |
      | user4@team |

    And auto redirection enabled
    Given I am on the homepage
    And I logged in as "user1@team"
    Given I am on "/account/"
    And I should see "user1@team"
    And I should see "Тестовая команда"

  Scenario: Расформирование команды
    Given a "create team" rule for season "Тестовый сезон"
    And I should see "Расформировать"
    When I follow "Расформировать"
    Then I should be on "/account/"
    And I should not see "Тестовая команда"
    And I should see "У вас есть возможность создать команду"

  Scenario: Редактирование команды
    Then I should see "Редактировать"
    When I follow "Редактировать"
    Then I should see "Редактировать команду"
    When I fill in the following:
      | Название | Команда тест |
    And I press "Сохранить"
    Then I should see "Команда отредактирована"
    And I should be on "/account/"
    And I should see "Команда тест"

  Scenario: Выход из команды
    Given I logged in as "user4@team"
    And I am on "/account/"
    Then I should see "Члены вашей команды"
    When I follow "Покинуть команду"
    Then I should not see "Члены вашей команды"

  @javascript @insulated
  Scenario: Приглашение в команду с помощью поля автодополнения
    Then I should see "Пригласить"
    When I follow "Пригласить"
    Then I should see "Пригласить участника"
    When I fill in "ФИО участника" with "Васьков"
    And I wait for "ФИО участника" autocomplete to finish
    And I click on the first "ФИО участника" autocomplete element
    And I press "Пригласить"
    Then I should see "приглашен в вашу команду"
    Given I am on "/account/"
    Then I should see "Приглашения в команду"
    Given I logged in as "user5@team"
    And I am on "/account/"
    Then I should see "user5@team"
    And I should see "Вас пригласили в команду"
    When I follow "Принять"
    Then I should see "Члены вашей команды"

  Scenario: Подача заявки в команду
    Given I logged in as "user5@team"
    Given a "create team" rule for season "Тестовый сезон"
    And I am on "/account/"
    And I should see "У вас есть возможность создать команду"
    When I view team "Тестовая команда"
    Then I should see "Тестовая команда"
    And I should see "Подать заявку"
    When I follow "Подать заявку"
    Then I should see "Вы подали заявку на вступление в команду"
    Given I logged in as "user1@team"
    And I am on "/account/"
    And I should see "Тестовая команда"
    And I should see "Заявки на включение в команду"
    When I follow "Принять заявку"
    Then I should not see "Заявки на включение в команду"
    Given I logged in as "user5@team"
    And I am on "/account/"
    Then I should see "Тестовая команда"
    Then I should see "Члены вашей команды"

  @javascript
  Scenario: Переход в профиль команды через список команд
    Given I logged in as "user5@team"
    Given a "request team" rule for season "Тестовый сезон"
    And I am on "/account/"
    And I should see "У вас есть возможность вступить в команду"
    And I should see "Подать заявку"
    When I follow "Подать заявку"
    Then I should see "Тестовая команда"
    When I follow "Тестовая команда"
    Then I should see "Подать заявку"
    When I follow "Подать заявку"
    Then I should see "Вы подали заявку на вступление в команду"
    And I should see "Отозвать"
