Feature: Контент сайта
  Контент сайта должен нормально отображаться

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

  Scenario: Список новостей
    Given news entity in "Тестовый сезон" with
      | type     | title                | body               | date      |
      | default  | Тестовый заголовок 1 | Тестовая новость 1 | 1 day ago |
      | default  | Тестовый заголовок 2 | Тестовая новость 2 | + 1 day   |
      | deferred | Тестовый заголовок 3 | Тестовая новость 3 | 1 day ago |
      | deferred | Тестовый заголовок 4 | Тестовая новость 4 | + 1 day   |
      | disabled | Тестовый заголовок 5 | Тестовая новость 5 | + 1 day   |

    When I am on "/news"
    Then I should see "Тестовый заголовок 1"
    Then I should see "Тестовый заголовок 2"
    Then I should see "Тестовый заголовок 3"
    Then I should not see "Тестовый заголовок 4"
    Then I should not see "Тестовый заголовок 5"

    When I am on "/news"
    When I follow "Тестовый заголовок 1"
    Then I should see "Тестовая новость 1"

    When I am on "/news/4"
    Then the response status code should not be 200

    When I am on "/news/5"
    Then the response status code should not be 200

  Scenario: Показ страниц
    Given page entity in "Тестовый сайт" with
      | author     | template | content                 | title                    | alias          |
      | admin@test | layout   | Тестовый контент _front | Тестовая страница _front | _front         |
      | admin@test | layout   | Тестовый контент path   | Тестовая страница path   | some/path/here |

    When I am on the homepage
    Then I should see "Тестовая страница _front" in the "title" element
    Then I should see "Тестовый контент _front"

    When I am on "/some/path/here"
    Then I should see "Тестовая страница path" in the "title" element
    Then I should see "Тестовый контент path"

    When I am on "/some/invalid/path/here"
    Then the response status code should be 404
