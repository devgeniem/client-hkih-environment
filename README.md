- [WordPress Enviroment project for Headless CMS.](#wordpress-enviroment-project-for-headless-cms)
  - [Environment URLs](#environment-urls)
    - [Local](#local)
  - [Local development setup](#local-development-setup)


# WordPress Enviroment project for Headless CMS.

## Environment URLs

### Local

* http://localhost.test:8080

**Note**: Locally there is no certificate so use `http://` URLs.

## Local development setup

```
# Clone project
git clone git@github.com:City-of-Helsinki/headless-cms.git

# Create env-file.
copy .env.example and rename it to .env.app

# Build and start containers
docker compose build dev
docker compose up -d dev

# Move inside dev container
docker compose exec dev sh

# Install composer dependencies
composer install

# Build theme assets
cd web/app/themes/hkih
npm install
npm run build

# Create database
docker compose exec db mysqladmin create staging

docker compose exec dev sh
# ... then, in dev shell run
wp core multisite-install --url=http://localhost:8080 --title=xyz --admin_user=root --admin_password=root --admin_email=first.lastname@test.com --skip-email

# Verify the site works by login into admin
open http://localhost.test:8080/wp-login.php

# Verify the site works by visiting the site
open http://localhost.test:8080/wp-login.php

# The project requires the following plugins to work, which must be purchased separately as it does not come with the project.
- https://www.advancedcustomfields.com/
- https://polylang.pro/ (Not tested with free version)
- Install plugins under mu-plugins folder

# Activate HKIH theme
My sites > Network Admin > Sites
Edit localhost:8080 site > Themes > HKIH (enable)

# Activate HKIH theme to XYZ site
My sites > xyz > Dashboard
Appearance > Theme > HKIH (Activate)

# Activate plugins
Plugins

```