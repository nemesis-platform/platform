Feature: Регистрация нового пользователя
  Чтобы пользователь смог зарегистрироваться в сайте,
  он должен попасть на страницу регистрации, увидеть на
  ней ссылку регистрации и перейти по ней.
  Послче чего он должен заполнить анкету, отправить ее и
  получить письмо со ссылкой активации. Перейдя по ней
  он должен получить активированный аккаунт

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
    Given a league "Студенческая лига" in "Тестовый сезон"
    Given a category "Профессионал" in "Профессиональная лига" with:
#      | vuz_prof |
    Given a category "Студент" in "Студенческая лига" with:
#      | vuz |
    Given I am on the homepage
    And auto redirection disabled

  Scenario: Ссылка регистрации существует и работает
    Given I am on the homepage
    Then I should see "Регистрация"
    When I follow "Регистрация"
    Then I should be on "/register"
    And the response status code should be 200
    And I should see "Регистрация" in the "h1" element
    And I should see "Зарегистрироваться"

  @javascript
  Scenario: Дополнительные поля анкеты не показываются до выбора категории
    Given I am on "/register"
#    Then text "ВУЗ" should not be visible
    When I select "Студент" from "Категория"
#    Then text "ВУЗ" should be visible

  @javascript
  Scenario: Полностью заполненная форма отправляется
    Given I am on "/register"
    When I fill in "Почта" with "test@localhost"
     # Choose field by name because login form has Пароль field too
    And I fill in "registration_form[account][password][first]" with "p@ssword"
    And I fill in "Повторите пароль" with "p@ssword"
    And I fill in "Фамилия" with "Иванов"
    And I fill in "Имя" with "Иван"
    And I fill in "Отчество" with "Иванович"
    And I fill in "Дата рождения" with "05.05.1991"
    And I fill in "Город" with "город"
    And I wait for "Город" autocomplete to finish
    And I click on the first "Город" autocomplete element
    And I select "Студент" from "Категория"
#    Then text "ВУЗ" should be visible
#    When I fill in "ВУЗ" with "Тестовый"
#    And I wait for "ВУЗ" autocomplete to finish
#    And I click on the first "ВУЗ" autocomplete element
    And I check "Правилами чемпионата"
    And I press "Зарегистрироваться"
    Then I should be on "/register/success"
    And I should see "Вы успешно прошли процесс заполнения регистрационной анкеты"
    When I obtain confirmation link for "test@localhost"
    And I follow the email url
    Then I should see "Вы успешно активировали вашу учётную запись"
    When I fill in the following:
      | E-mail | test@localhost |
      | Пароль | p@ssword       |
    And I press "Войти"
    Then I should be on "/account/"
    Then I should see "Здравствуйте, Иван Иванович!" in the "h2" element


  @mink:symfony2
  Scenario: Регистрация и подтвеждение аккаунта
    Given I am on "/register"
    And I submit form with button "Зарегистрироваться"
      | registration_form[account][email]                    | test@localhost |
      | registration_form[account][password][first]          | p@ssword       |
      | registration_form[account][password][second]         | p@ssword       |
      | registration_form[account][lastname]                 | Фамилия        |
      | registration_form[account][firstname]                | Имя            |
      | registration_form[account][middlename]               | Отчество       |
      | registration_form[account][birthDate]                | 05.05.1991     |
      | registration_form[participant][category]             | 1              |
#      | registration_form[participant][values][vuz][storage] | 1              |
    And I should receive an email for "test@localhost"
    And email should contain url
    And I follow redirection
    And I should be on "/register/success"
    And I should see "Вы успешно прошли процесс заполнения регистрационной анкеты"
    When I follow the email url
    And I follow redirection
    Then I should see "Вы успешно активировали вашу учётную запись"
    When I submit form with button "Войти"
      | _username | test@localhost |
      | _password | p@ssword       |
    And I follow redirection
    When I am on "/account/"
    And I should see "Здравствуйте, Имя Отчество!" in the "h2" element
