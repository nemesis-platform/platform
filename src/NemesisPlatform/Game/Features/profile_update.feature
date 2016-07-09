Feature: Обновление аккаунта
  Старые пользователи должны быть способны
  зайти в свой аккаунт и обновить профиль
  для участия в новом сезоне

  Background:
    Given empty database
    Given a site with:
      | full_name     | short_name | base_url  | support_email | active | theme                 |
      | Тестовый сайт | test       | localhost | admin@test    | 1      | basic_bootstrap_theme |
    Given a "simple_account_team" block for "Тестовый сайт" weighted 1 at "account"

    Given a season in "Тестовый сайт" with:
      | name           | short_name | description      | active | registration_open |
      | Тестовый сезон | testseason | Сезон для тестов | 1      | 1                 |

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
    And auto redirection enabled

  @javascript
  Scenario: Регистрация на новый сезон
    Given I logged in as "user@team"
    And I am on "/account/"
    Then I should see "Вы не участвуете в сезоне"
    And I should see "Обновить анкету"
    When I follow "Обновить анкету"
    Then I should see "Тестовый сезон"
    And I should see "Обновить"
    And I fill in "Город" with "город"
    And I wait for "Город" autocomplete to finish
    And I click on the first "Город" autocomplete element
    And I select "Студент" from "Категория"
#    Then text "ВУЗ" should be visible
#    When I fill in "ВУЗ" with "Тестовый"
#    And I wait for "ВУЗ" autocomplete to finish
#    And I click on the first "ВУЗ" autocomplete element
    And I press "Обновить"
    And I follow redirection
    Then I should be on "/account/"
    And I should see "Успешное обновление анкеты"

  Scenario: Ссылка профиля работает
    Given I logged in as "user@team"
    And I am on "/account/preferences/"
    And the response status code should be 200
    And I should see "Профиль"
    And I should see "Иванов Иван Иванович"

  Scenario: Заполнение дополнительной информации
    Given I logged in as "user@team"
    And I am on "/account/preferences/"
    Then I should see "Дополнительная информация"
    When I follow "Дополнительная информация"
    Then I should be on "/account/preferences/edit_info"
    And I should see "Сохранить"
    When I fill in "О себе" with "Дополнительная информация об аккаунте"
    And I press "Сохранить"
    Then I should be on "/account/preferences/edit_info"
    And I should see "Данные обновлены"
    When I follow "Просмотреть анкету"
    Then I should see "Иванов"
    And I should see "Дополнительная информация об аккаунте"

  Scenario: Смена пароля
    Given user "user@team" has password "p@ssword"
    When I should be on "/"
    When I submit form with button "Войти"
      | _username | user@team |
      | _password | p@ssword  |
    When I am on "/account/"
    And I should see "Иван Иванович"
    And I am on "/account/preferences/"
    Then I should see "Сменить пароль"
    When I follow "Сменить пароль"
    Then I should be on "/account/preferences/password_change"
    And I should see "Изменить пароль"
    When I fill in "Старый пароль" with "p@ssword"
    And I fill in "Новый пароль" with "secret"
    And I fill in "Повторите" with "secret"
    And I press "Изменить пароль"
    Then I should be on "/account/preferences/"
    And I should see "Пароль успешно изменен"
    When I follow "Выйти"
    Then I should be on the homepage
    When I fill in the following:
      | E-mail | user@team |
      | Пароль | secret    |
    And I press "Войти"
    Then I should be on "/"
    When I am on "/account/"
    And I should see "Иван Иванович"
