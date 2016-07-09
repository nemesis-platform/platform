Feature: Управление телефонами
  Для того, чтобы подтвердить реальность своей анкеты
  Участники должны иметь возможность подтверить свой номер
  телефона

  Background:
    Given empty database
    Given a site with:
      | full_name     | short_name | base_url  | support_email | active | theme                 |
      | Тестовый сайт | test       | localhost | admin@test    | 1      | basic_bootstrap_theme |
    Given a "simple_account_team" block for "Тестовый сайт" weighted 1 at "account"

    Given a season in "Тестовый сайт" with:
      | name           | short_name | description      | active | registration_open |
      | Тестовый сезон | testseason | Сезон для тестов | 1      | 1                 |

    Given season "Тестовый сезон" requires mobile phone

    Given a combined league "Профессиональная лига" in "Тестовый сезон"
    Given a league "Студенческая лига" in "Тестовый сезон"
#    Given a storable form field "ВУЗ" named "vuz" of type "field_university"
    Given a category "Профессионал" in "Профессиональная лига"
    Given a category "Студент" in "Студенческая лига" with:
#      | vuz |

    Given users with:
      | email     | lastname | firstname | middlename | birthdate  |
      | user@team | Иванов   | Иван      | Иванович   | 2010-10-10 |

    Given I am on the homepage
    And I logged in as "user@team"
    And auto redirection enabled

  @mink:symfony2
  Scenario: Добавление телефона
    Given auto redirection disabled
    And I am on "/account/preferences/"
    Then I should see "Телефоны"
    When I follow "Телефоны"
    Then I should see "Добавить телефон"
    When I fill in "Номер телефона" with "5551234567"
    And I press "Добавить телефон"
    And I follow redirection
    And I should see "Выслать код"
    When I follow "Выслать код"
    Then I should receive SMS for "5551234567"
    And I follow redirection
    Then I should see "выслано сообщение, содержащее проверочный код"
    When I fill in "form[code]" with code from SMS
    And I press "Подтвердить"
    And I follow redirection
    Then I should see "Проверка пройдена успешна."
    And I should not see "Выслать код"
    And I should not see "Подтвердить"
    And I should see "5551234567"
