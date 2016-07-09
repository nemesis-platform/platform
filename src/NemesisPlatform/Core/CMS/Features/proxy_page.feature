Feature: Прокси-страницы
  Чтобы обеспечить красивые пути для разных модулей в разных сайтах
  необходимо иметь возможность конфигуриремой передачи запроса по псевдониму
  нужному контроллеру

  Background:
    Given empty database
    Given a site with:
      | full_name     | short_name | base_url  | support_email | active | theme                 |
      | Тестовый сайт | test       | localhost | admin@test    | 1      | basic_bootstrap_theme |
    Given a "simple_account_team" block for "Тестовый сайт" weighted 1 at "account"

    Given a season in "Тестовый сайт" with:
      | name           | short_name | description      | active | registration_open |
      | Тестовый сезон | testseason | Сезон для тестов | 1      | 1                 |

    Given I am on the homepage

  Scenario: Прокси страницы работают
    Given page entity in "Тестовый сайт" with
      | author     | template | content               | title                  | alias          |
      | admin@test | layout   | Тестовый контент path | Тестовая страница path | some/path/here |
    Given a proxy page "some/proxy/path" routed to "page_by_alias" in site "Тестовый сайт" with
      | alias | some/path/here |
    When I am on "/some/proxy/path"
    Then I should see "Тестовый контент path"
