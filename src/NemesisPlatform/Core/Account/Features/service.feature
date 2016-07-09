Feature: Сервисы сайта
  Чтобы нормально пользоваться сайтом основные служебные сервисы должны работать корректно

  Background:
    Given empty database
    Given a site with:
      | full_name     | short_name | base_url  | support_email | active | theme                 |
      | Тестовый сайт | test       | localhost | admin@test    | 1      | basic_bootstrap_theme |
    Given a "simple_account_team" block for "Тестовый сайт" weighted 1 at "account"
    Given users with:
      | email     | lastname | firstname | middlename |
      | user@test | Иванов   | Иван      | Иванович   |

    Given I am on the homepage
    And auto redirection enabled

  Scenario: Логин
    Given I am on the homepage
    And user "user@test" has password "p@ssword"
    When I fill in the following:
      | E-mail | user@test |
      | Пароль | p@ssword  |
    And I press "Войти"
    Then I should be on "/"
    When I am on "/account/"
    And I should see "Здравствуйте, Иван Иванович" in the "h2" element

  Scenario: Логин с редиректорм
    Given I am on the homepage
    And user "user@test" has password "p@ssword"
    And I am on "/account/"
    Then I should be on "/login"
    When I fill in the following:
      | E-mail | user@test |
      | Пароль | p@ssword  |
    And I press "Войти"
    Then I should be on "/account/"
    And I should see "Здравствуйте, Иван Иванович" in the "h2" element

  Scenario: Выход из системы
    Given I logged in as "user@test"
    And I am on "/account/"
    When I follow "Выйти"
    And I should be on the homepage
    When I am on "/account/"
    Then I should be on "/login"
