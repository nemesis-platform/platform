Feature: Личные сообщения
  Участники должны иметь возможность отправлять личные сообщения
  Участники, не прошедшие процедуру верификации телефона не должны
  иметь возможность написать сообщение

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
      | email          | lastname | firstname | middlename |
      | sender@test    | Васильев | Василий   | Васильевич |
      | recipient@test | Иванов   | Иван      | Иванович   |

    Given season data for "Тестовый сезон" with:
      | user           | category     |
      | sender@test    | Профессионал |
      | recipient@test | Профессионал |

  @javascript
  Scenario: Отправка личных сообщений
    Given user "sender@test" has confirmed phone "5551324567"
    Given user "recipient@test" has confirmed phone "5551324568"
    And I am on the homepage
    When I logged in as "sender@test"
    And I am on "/account"
    Given I am on "/users/list"
    And I should see "Иванов"
    When I follow "Иванов"
    Then I should see "Написать"
    When I follow "Написать"
    Then I should see "Написать сообщение"
    And I should see "Иванов И. И."
    When I fill in "Сообщение" with "Тестовое сообщение"
    And I press "Отправить"
    Then I should see "Тестовое сообщение"
    And I am on the homepage
    When I logged in as "recipient@test"
    And I am on "/account"
    When I follow "Личные сообщения"
    Then I should see "Тестовое сообщение"
    And I should see "Новое"


#  @javascript
#  Scenario: Отправка личных сообщений неподтвержденным пользователем
#    Given user "recipient@test" has confirmed phone "5551324568"
#    And I am on the homepage
#    When I logged in as "sender@test"
#    And I am on "/account"
#    Given I am on "/users/list"
#    And I should see "Иванов"
#    When I follow "Иванов"
#    Then I should see "Написать" in the "span.disabled" element


#  @javascript
#  Scenario: Отправка личных сообщений неподтвержденному пользователю
#    Given user "sender@test" has confirmed phone "5551324567"
#    And I am on the homepage
#    When I logged in as "sender@test"
#    And I am on "/account"
#    Given I am on "/users/list"
#    And I should see "Иванов"
#    When I follow "Иванов"
#    Then I should see "Написать"
#    When I follow "Написать"
#    Then I should see "Написать сообщение"
#    And I should see "Иванов И. И."
#    When I fill in "Сообщение" with "Тестовое сообщение"
#    And I press "Отправить"
#    Then I should see "Тестовое сообщение"
#    And I am on the homepage
#    When I logged in as "recipient@test"
#    And I am on "/account"
#    Then I should not see "Личные сообщения"
