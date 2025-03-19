# WordPress Enviroment project for Headless CMS

This project attempts to install and build services that are required for running WordPress with Headless CMS theme and its plugins.

## Environment URLs

### Local

* http://localhost:8080

**Note**: Locally there is no certificate so use `http://` URLs.

## Local development setup

### Prerequisites

- git
- docker compose / podman compose

### Required steps

Clone this project's repository and enter it:

```bash
git clone https://github.com/devgeniem/client-hkih-environment && cd client-hkih-environment
```

Create .env.app file:

```bash
cp .env.example .env.app
```

Build dev image and start dev container:

```bash
docker compose build dev && docker compose up -d dev
```

Run install and build steps in dev container:

```bash
docker compose exec dev /bin/sh -c "composer install && cd web/app/themes/hkih && npm install && npm run build"
```

Create database in db container:

```bash
docker compose exec db /bin/sh -c "mysqladmin create wordpress && echo 'Database probably created'"
```

Use WP-CLI in dev container to create WordPress site:

```bash
docker compose exec dev sh -c "wp core multisite-install --url=http://localhost:8080 --title=xyz --admin_user=root --admin_password=root --admin_email=first.lastname@test.com --skip-email"
```

## Things to do after running the required commands

Verify the site works by login into admin (root/root):

[http://localhost:8080/wp-login.php](http://localhost:8080/wp-login.php)

Verify the site works by visiting the site:

[http://localhost:8080/](http://localhost:8080/)

The project requires the following plugins to work, which must be purchased separately as it does not come with the project:

- [https://www.advancedcustomfields.com/](https://www.advancedcustomfields.com/)
- [https://polylang.pro/](https://polylang.pro/) (not tested with free version)
- Install plugins under mu-plugins folder

Activate HKIH theme:

My sites > Network Admin > Sites
Edit localhost:8080 site > Themes > HKIH (enable)

Activate HKIH theme to XYZ site:

My sites > xyz > Dashboard
Appearance > Theme > HKIH (Activate)

Activate plugins:

Plugins

## TBD

- README cleanup
- Include steps that require interaction from user (e.g. enter container and ...) to Dockerfile - the goal is to execute all the necessary steps and have services running by issuing a single command: "docker compose up"
- Install required themes and plugins using WP-CLI, uninstall redundant plugins and themes - create a shell script for this which includes "wp core multisite-install ..." command!