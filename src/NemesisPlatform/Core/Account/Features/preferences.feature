Feature: Управление настройками

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
      | email     | lastname | firstname | middlename |
      | user@test | Иванов   | Иван      | Иванович   |

    Given season data for "Тестовый сезон" with:
      | user      | category     |
      | user@test | Профессионал |

    And auto redirection enabled
    Given I am on the homepage
    And I logged in as "user@test"

  Scenario: Просмот анкеты работает
    Given I am on "/account/"
    When I follow "Профиль"
    Then I should be on "/account/preferences/"
    And I should see "Просмотреть анкету"
    When I follow "Просмотреть анкету"
    Then I should see "Иванов Иван"

  @javascript
  Scenario: Список участников отображает участников
    Given I am on "/users/list"
    Then I should see "Иванов"
    When I follow "Иванов"
    Then I should see "Иванов"
