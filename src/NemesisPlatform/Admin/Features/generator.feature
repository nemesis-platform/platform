Feature: Генерация команд
  Чтобы провести тренинг или демо, администратор должен быть способен
  сгенерировать набор команд для сезона

  Background:
    Given empty database
    And users with:
      | email      | lastname | firstname | middlename |
      | admin@test | Петров   | Петр      | Петрович   |

    Given a site with:
      | full_name     | short_name | base_url  | support_email | active |
      | Тестовый сайт | Тест       | localhost | admin@test    | 1      |

    Given I am on the homepage
    And I log in as "admin@test" with roles:
      | ROLE_ADMIN |

  @todo
  Scenario: Генерация команд и проверка логина
    Given I am on "/admin/"
    Then I should see "Панель управления"
    When I follow "Генерация данных"
#    Then I should see "Цепной генератор сезона"

