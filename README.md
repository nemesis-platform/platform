# Nemesis Competition Platform
© 2013-2016 Pavel Batanov <pavel@batanov.me> https://github.com/scaytrase

# Purpose

Organizing online team competition with team gathering, information, draft etc. 
The idea is to make extensible platform which could be easily adopted to the
required competition flow and rules

# Features (current)

* Multi-domain codebase allowing you to share the userdata around the platform
* Built-in CMS for creating news, pages and menus
* Customizable per-domain skins
* Customizable blocks
* Mobile 

# Feature (planned)

* Content and UI translation per site
* UI translation with user preferences

# Requirements

## Production

* `phing` on the PATH (or replace `phing` calls to absolute phing executable 
like `php path/to/phing.phar`)
* `composer`

* PHP `~5.5 || ~7.0`
* ext-curl
* ext-imagick
* ext-json
* ext-intl
* ext-pdo_* - choose right for your database (see below)

Also see the general symfony requirements http://symfony.com/doc/current/reference/requirements.html

## Development

* ext-xdebug

# Deployment

```sh
    git clone https://github.com/nemesis-platform/platform.git platform
    cd platform
    # replace composer parameter to "php path/to/composer.phar" if not on the path
    # omit otherwise
    phing build.prod.deploy -DComposer=composer
```

# Configuration

After installing, the sample `parameters.yml` file will be generated at the
`app/config` path

## Database

Default configuration looks like

```yaml
    database_driver: pdo_sqlite
    database_host: null
    database_port: null
    database_name: null
    database_user: null
    database_password: null
    database_path: "%kernel.root_dir%/../var/nemesis.db"
```

Change the values according to you database vendor and configuration

### Drivers

Currently supported drivers are:

 * MySQL
 * SQLite
 
Other vendors supported by the Doctrine DBAL project are allowed, but not really tested

## Mailer

You can use the internal PHP `mail` function to send emails or configure proper mailer 

```yaml
    mailer_transport: mail
    mailer_host: 127.0.0.1
    mailer_user: null
    mailer_password: null
```

## Other settings

```yaml
    secret: ThisTokenIsNotSoSecretChangeIt # The salt for tokens etc, change it to the random value
    photo_path: uploads/avatars/           # path to store user avatars
    ckeditor_uploads: uploads/ckeditor/    # path to store CKEditor uploads
    maintenance_url:                       # fallback url for administrating (when no site configured, see below)
        - localhost
    admin_usernames:                       # list of the admin usernames (emails)
        - pavel@batanov.me

```

## Creating first admin and site

When the installation has no data, you can either configure the site using the fallback url or
using the console command. Nevertheless the admin user has to be created using the console af the first time

```sh
    # this creates a site for responding on your-domain.tld. Make sure
    # DNS is configured properly
    bin/console nemesis:admin:create-site your-domain.tld "Yoursite"
    # this creates or updates the user to be the admin of the site
    # if user does not exist - additional information will be asked
    bin/console nemesis:admin:create-admin mail@your-domain.tld
```

## Web server

Make sure your web server points to the `web/` directory of the project and upper
directories are not available from the outside

