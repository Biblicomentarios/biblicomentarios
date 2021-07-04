# Change Log

All notable changes to this project will be documented in this file.
See [Conventional Commits](https://conventionalcommits.org) for commit guidelines.

## 1.2.22 (2021-06-05)

**Note:** Version bump only for package @devowl-wp/real-custom-post-order





## 1.2.21 (2021-05-25)


### chore

* migarte loose mode to compiler assumptions
* polyfill setimmediate only if needed (CU-jh3czf)
* prettify code to new standard
* revert update of typedoc@0.20.x as it does not support monorepos yet
* update cypress@7
* upgrade dependencies to latest minor version


### ci

* move type check to validate stage


### fix

* do not rely on install_plugins capability, instead use activate_plugins so GIT-synced WP instances work too (CU-k599a2)


### test

* make window.fetch stubbable (CU-jh3cza)
* run smoke tests also for chore/patch branches





## 1.2.20 (2021-05-14)

**Note:** Version bump only for package @devowl-wp/real-custom-post-order





## 1.2.19 (2021-05-12)

**Note:** Version bump only for package @devowl-wp/real-custom-post-order





## 1.2.18 (2021-05-11)

**Note:** Version bump only for package @devowl-wp/real-custom-post-order





## 1.2.17 (2021-05-11)


### chore

* **release :** publish [ci skip]


### ci

* push plugin artifacts to GitLab Generic Packages registry (CU-hd6ef6)


### refactor

* create wp-webpack package for WordPress packages and plugins
* introduce eslint-config package
* introduce new grunt workspaces package for monolithic usage
* introduce new package to validate composer licenses and generate disclaimer
* introduce new package to validate yarn licenses and generate disclaimer
* introduce new script to run-yarn-children commands
* move build scripts to proper backend and WP package
* move jest scripts to proper backend and WP package
* move PHP Unit bootstrap file to @devowl-wp/utils package
* move PHPUnit and Cypress scripts to @devowl-wp/utils package
* move technical doc scripts to proper WP and backend package
* move WP build process to @devowl-wp/utils
* move WP i18n scripts to @devowl-wp/utils
* move WP specific typescript config to @devowl-wp/wp-webpack package
* remove @devowl-wp/development package
* split stubs.php to individual plugins' package





## 1.2.16 (2021-03-30)

**Note:** Version bump only for package @devowl-wp/real-custom-post-order





## 1.2.15 (2021-03-23)


### build

* plugin tested for WordPress 5.7 (CU-f4ydk2)





## 1.2.14 (2021-03-02)

**Note:** Version bump only for package @devowl-wp/real-custom-post-order





## 1.2.13 (2021-02-24)


### chore

* rename go-links to new syntax (#en621h)
* **release :** publish [ci skip]
* **release :** publish [ci skip]


### docs

* rename test drive to sanbox (#ef26y8)
* update README to be compatible with Requires at least (CU-df2wb4)





## 1.2.12 (2021-01-24)

**Note:** Version bump only for package @devowl-wp/real-custom-post-order





## 1.2.11 (2021-01-18)


### fix

* table header breaks when a column is hidden (CU-cpv9wm)





## 1.2.10 (2021-01-11)


### build

* reduce javascript bundle size by using babel runtime correctly with webpack / babel-loader


### chore

* **release :** publish [ci skip]





## 1.2.9 (2020-12-15)

**Note:** Version bump only for package @devowl-wp/real-custom-post-order





## 1.2.8 (2020-12-10)

**Note:** Version bump only for package @devowl-wp/real-custom-post-order





## 1.2.7 (2020-12-09)

**Note:** Version bump only for package @devowl-wp/real-custom-post-order





## 1.2.6 (2020-12-09)


### chore

* update to cypress v6 (CU-7gmaxc)
* update to webpack v5 (CU-4akvz6)
* updates typings and min. Node.js and Yarn version (CU-9rq9c7)
* **release :** publish [ci skip]


### fix

* typing error from Node 14 upgrade (CU-9rq9c7)





## 1.2.5 (2020-12-01)


### chore

* update dependencies (CU-3cj43t)
* update to composer v2 (CU-4akvjg)
* update to core-js@3 (CU-3cj43t)
* **release :** publish [ci skip]


### refactor

* enforce explicit-member-accessibility (CU-a6w5bv)





## 1.2.4 (2020-11-24)


### fix

* compatibility with upcoming WordPress 5.6 (CU-amzjdz)
* use no-store caching for WP REST API calls to avoid issues with browsers and CloudFlare (CU-agzcrp)





## 1.2.3 (2020-11-18)

**Note:** Version bump only for package @devowl-wp/real-custom-post-order





## 1.2.2 (2020-11-17)

**Note:** Version bump only for package @devowl-wp/real-custom-post-order





## 1.2.1 (2020-11-12)


### ci

* make scripts of individual plugins available in review applications (#a2z8z1)





# 1.2.0 (2020-10-23)


### feat

* route PATCH PaddleIncompleteOrder (#8ywfdu)


### refactor

* use "import type" instead of "import"





## 1.1.10 (2020-10-16)


### build

* use node modules cache more aggressively in CI (#4akvz6)


### chore

* rename folder name (#94xp4g)





## 1.1.9 (2020-10-09)

**Note:** Version bump only for package @devowl-wp/real-custom-post-order





## 1.1.8 (2020-10-08)


### chore

* **release :** version bump





## 1.1.7 (2020-09-29)


### build

* backend pot files and JSON generation conflict-resistent (#6utk9n)


### chore

* introduce development package (#6utk9n)
* move backend files to development package (#6utk9n)
* move grunt to common package (#6utk9n)
* move packages to development package (#6utk9n)
* move some files to development package (#6utk9n)
* remove grunt task aliases (#6utk9n)
* update dependencies (#3cj43t)
* update package.json scripts for each plugin (#6utk9n)





## 1.1.6 (2020-09-22)


### fix

* import settings (#82rk4n)





## 1.1.5 (2020-09-08)


### fix

* remove List not Sortable hint and allow to sort all views (#7etufj)





## 1.1.4 (2020-08-31)

**Note:** Version bump only for package @devowl-wp/real-custom-post-order





## 1.1.3 (2020-08-26)


### ci

* install container volume with unique name (#7gmuaa)


### perf

* remove transients and introduce expire options for better performance (#7cqdzj)





## 1.1.2 (2020-08-17)


### ci

* prefer dist in composer install





## 1.1.1 (2020-08-11)


### chore

* backends for monorepo introduced





# 1.1.0 (2020-07-30)


### docs

* optimize plugin description at wordpress.org


### feat

* check support status for Envato license #CU-6pubwg
* introduce dashboard with assistant (#68k9ny)
* WordPress 5.5 compatibility (#6gqcm8)


### fix

* REST API notice in admin dashboard
* usage with woocommerce products (#6pmaj2)





## 1.0.12 (2020-07-02)


### chore

* allow to define allowed licenses in root package.json (#68jvq7)
* update dependencies (#3cj43t)


### fix

* IE11 polyfills (#5whc2c)


### test

* cypress does not yet support window.fetch (#5whc2c)





## 1.0.11 (2020-06-17)

**Note:** Version bump only for package @devowl-wp/real-custom-post-order





## 1.0.10 (2020-06-12)


### chore

* i18n update (#5ut991)


### test

* OptionStore





## 1.0.9 (2020-05-27)


### build

* improve plugin build with webpack parallel builds


### ci

* use hot cache and node-gitlab-ci (#54r34g)





## 1.0.8 (2020-05-20)

**Note:** Version bump only for package @devowl-wp/real-custom-post-order





## 1.0.7 (2020-05-12)


### build

* cleanup temporary i18n files correctly


### fix

* correctly enqueue dependencies (#52jf92)





## 1.0.6 (2020-04-27)


### chore

* add hook_suffix to enqueue_scripts_and_styles function (#4ujzx0)


### docs

* animated Real Custom Post Order logo for wordpress.org


### test

* automatically retry cypress tests (#3rmp6q)





## 1.0.5 (2020-04-20)

**Note:** Version bump only for package @devowl-wp/real-custom-post-order





## 1.0.4 (2020-04-16)


### docs

* update link to changelog





## 1.0.3 (2020-04-16)


### build

* move test namespaces to composer autoload-dev (#4jnk84)
* reduce bundle size by ~25% (#4jjq0u)
* scope PHP vendor dependencies (#4jnk84)


### chore

* create real-ad package to introduce more UX after installing the plugin (#1aewyf)
* rename real-ad to real-utils (#4jpg5f)
* update to Cypress v4 (#2wee38)


### ci

* correctly build i18n frontend files (#4jjq0u)
* run package jobs also on devops changes


### fix

* link to Real Custom Post Order (#5ygvhw)


### style

* reformat php codebase (#4gg05b)


### test

* adjust E2E tests due to translation changes
* avoid session expired error in E2E tests (#3rmp6q)





## 1.0.2 (2020-03-31)


### chore

* update dependencies (#3cj43t)
* **release :** publish [ci skip]
* **release :** publish [ci skip]


### ci

* use concurrency 1 in yarn disclaimer generation


### docs

* replace screenshot in wordpress.org description


### style

* run prettier@2 on all files (#3cj43t)


### test

* added E2E tests (#46mact)
* configure jest setupFiles correctly with enzyme and clearMocks (#4akeab)
* generate test reports (#4cg6tp)





## 1.0.1 (2020-03-23)


### build

* initial release of WP Real Custom Post Order plugin (#46ftef)
