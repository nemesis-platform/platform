Feature: Управление сайтами
  Чтобы провести чемпионат, администратор должен
  иметь возможность создать сайт и управлять им

  Background:
    Given empty database
    And users with:
      | email      | lastname | firstname | middlename |
      | admin@test | Петров   | Петр      | Петрович   |

    Given I am on the homepage
    And I log in as "admin@test" with roles:
      | ROLE_ADMIN |

  Scenario: Создание сайта
    Given I am on "/admin/"
    Then I should see "Сайты"
    When I follow "Сайты"
    Then I should see "Управление сайтами"
    When I follow "Добавить сайт"
    Then I should see "Создать сайт"
    When I fill in the following:
      | Название          | Тестовый сайт |
      | Короткое название | Тест          |
      | Хост              | localhost     |
      | Контактный адрес  | admin@test    |
    And I check "Активный"
    And I press "Создать сайт"
    Then I should see "Сайт успешно создан"
    When I follow "Сайты"
    Then I should see "Тестовый сайт"

  Scenario: Редактирование сайта
    Given a site with:
      | full_name     | short_name | base_url  | support_email | active |
      | Тестовый сайт | Тест       | localhost | invalid@test  | 1      |
    Given I am on "/admin/"
    When I follow "Сайты"
    Then I should see "Тестовый сайт"
    And I should see "Редактировать"
    When I follow "Редактировать"
    Then I should see "Тестовый сайт"
    And I should see "Обновить сайт"
    When I fill in "Контактный адрес" with "admin@email"
    And I press "Обновить сайт"
    Then I should see "Сайт успешно обновлен"
    When I follow "Сайты"
    Then I should see "Тестовый сайт"
    And I should see "admin@email"

  Scenario: Создание сезона
    Given a site with:
      | full_name     | short_name | base_url  | support_email | active |
      | Тестовый сайт | Тест       | localhost | admin@test    | 1      |
    Given I am on "/admin/"
    When I follow "Сайты"
    Then I should see "Тестовый сайт"
    When I follow "Добавить сезон"
    Then I should see "Создать сезон"
    When I fill in the following:
      | Полное название   | Тестовый сезон   |
      | Короткое название | ТестСезон        |
      | Описание          | Сезон для тестов |
    And I check "Активный"
    And I check "Регистрация открыта"
    And I press "Создать сезон"
    Then I should see "Успешное создание сезона"
    And I should see "Тестовый сезон"
    Given I am on "/admin/"
    When I follow "Сайты"
    Then I should see "ТестСезон"
