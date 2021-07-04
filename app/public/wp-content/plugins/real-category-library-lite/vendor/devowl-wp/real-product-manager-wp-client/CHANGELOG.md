# Change Log

All notable changes to this project will be documented in this file.
See [Conventional Commits](https://conventionalcommits.org) for commit guidelines.

## 1.7.4 (2021-06-05)


### chore

* skip validate hosts in some cases


### fix

* allow hidden IP address to be valid for license activation (CU-kk4dd7)





## 1.7.3 (2021-05-25)


### chore

* compatibility with latest antd version
* migarte loose mode to compiler assumptions
* prettify code to new standard
* upgrade dependencies to latest minor version


### fix

* do not rely on install_plugins capability, instead use activate_plugins so GIT-synced WP instances work too (CU-k599a2)
* output validate hint after custom help (object destructuring, CU-kb6jyc)
* validate current host with parse_url to avoid some strange scenarios





## 1.7.2 (2021-05-14)


### fix

* sometimes the modal is not shown due to race condition with mobx.configure isolate global state





## 1.7.1 (2021-05-11)


### fix

* automatically refetch announcments for updates (CU-jn95nz)





# 1.7.0 (2021-05-11)


### feat

* introduce Learn More links to different parts of the UI (CU-gv58rr)
* translate frontend into German (CU-ex0u4a)


### fix

* automatically clear page caches after license activation / deactivation (CU-jd7t87)
* ignore IP hostname + port  while validating a new host (CU-j93gd2)
* ignore IP hostnames while validating a new host (CU-j93gd2)
* show notice about email checkbox in feedback formular when note has more than 4 words (CU-gd0zw3)
* usage with deferred scripts and content blocker (DOM waterfall, CU-gn4ng5)
* wrong announcment state directly after first license activation


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
* move WP build process to @devowl-wp/utils
* move WP i18n scripts to @devowl-wp/utils
* move WP specific typescript config to @devowl-wp/wp-webpack package
* remove @devowl-wp/development package





## 1.6.3 (2021-04-27)


### fix

* compatibility with WP_SITEURL PHP constant (CU-hd6ntd)
* do not validate new host names in WP CLI and WP Cronjob
* do not validate new hosts for free licenses





## 1.6.2 (2021-04-15)


### chore

* show old and new hostname after license deactivation


### fix

* compatibility with WPML and PolyLang when using different domains (CU-h79b76)





## 1.6.1 (2021-03-30)


### chore

* update text to explain the installation type (CU-g57mdw)


### fix

* group licenses by hostname of each blog instead of blog (CU-g751j8)


### refactor

* use composer autoload to setup constants and package localization





# 1.6.0 (2021-03-23)


### chore

* max. activations per license explain in more detail when limit reached and link to customer center (CU-fn1k7v)


### feat

* allow to migrate from older license keys (CU-fq1kd8)


### fix

* allow to only get the href for the plugin activation link and make API public (CU-fq1kd8)
* consider allow autoupdates as true if previously no auto updates exist (CU-fq1kd8)
* do not deactivate license when saving new URL through General > Settings (CU-g150eg)
* in a multisite installation only consider blogs with Real Cookie Banner active (CU-fyzukg)
* plugin could not be installed if an older version of PuC is used by another plugin
* prefill code from warning / error hint and allow 32 char (non-UUID) format codes (CU-fq1kd8)
* switch to blog while validating new hostname for license (CU-fyzukg)





## 1.5.5 (2021-03-10)


### chore

* hide some notices on try.devowl.io (CU-f53trz)
* update texts (CU-f134wh)


### fix

* automatically deactivate license when migrating / cloning the website and show notice (CU-f134wh)





## 1.5.4 (2021-03-02)


### chore

* highlight "Skip & Deactivate" button in feedback form when deactivating plugin (CU-ewzae8)


### fix

* filter duplicates in deactivation feedback and show error message (CU-ewzae8)
* filter spam deactivation feedback by length, word count and email address MX record (CU-ewzae8)
* use site url instead of home url for activating a license (CU-f134wh)
* use whitespace and refactor coding (review 1, CU-ewzae8)





## 1.5.3 (2021-02-24)


### chore

* drop moment bundle where not needed (CU-e94pnh)





## 1.5.2 (2021-02-16)


### fix

* warning (PHP) when previously no autoupdates exist





## 1.5.1 (2021-02-02)


### chore

* hotfix remove function which does not exist in < WordPress 5.5





# 1.5.0 (2021-02-02)


### feat

* introduce new checkbox to enable automatic minor and patch updates (CU-dcyf6c)





## 1.4.5 (2021-01-24)


### fix

* avoid duplicate feedback modals if other plugins of us are active (e.g. RML, CU-cx0ynw)





## 1.4.4 (2021-01-11)


### build

* reduce javascript bundle size by using babel runtime correctly with webpack / babel-loader


### chore

* **release :** publish [ci skip]





## 1.4.3 (2020-12-09)


### chore

* update to webpack v5 (CU-4akvz6)
* updates typings and min. Node.js and Yarn version (CU-9rq9c7)


### fix

* add hint for installation type for better explanation (CU-b8t6qf)





## 1.4.2 (2020-12-01)


### chore

* update dependencies (CU-3cj43t)
* update to composer v2 (CU-4akvjg)


### refactor

* enforce explicit-member-accessibility (CU-a6w5bv)





## 1.4.1 (2020-11-26)


### chore

* **release :** publish [ci skip]


### fix

* show link to account page when max license usage reached (CU-aq0g1g)





# 1.4.0 (2020-11-24)


### feat

* add hasInteractedWithFormOnce property of current blog to REST response (CU-agzcrp)


### fix

* license form was not localized to german (CU-agzcrp)
* use no-store caching for WP REST API calls to avoid issues with browsers and CloudFlare (CU-agzcrp)





## 1.3.4 (2020-11-19)


### fix

* deactivation feedback wrong REST route





## 1.3.3 (2020-11-18)


### fix

* deactivation feedback modal





## 1.3.2 (2020-11-17)


### fix

* duplicate error messages (#acypm6)





## 1.3.1 (2020-11-17)


### fix

* correctly show multisite blogname (#acwzpy)





# 1.3.0 (2020-11-03)


### feat

* allow to disable announcements (#9jwehz)
* translation (#8mrn5a)





# 1.2.0 (2020-10-23)


### feat

* route PATCH PaddleIncompleteOrder (#8ywfdu)


### fix

* typing


### refactor

* use "import type" instead of "import"





# 1.1.0 (2020-10-16)


### build

* use node modules cache more aggressively in CI (#4akvz6)


### chore

* introduce Real Product Manager WordPress client package (#8cxk67)
* update PUC (#8cxk67)
* update PUC (#8cxk67)


### feat

* add checklist in config page header (#8cxk67)
* announcements (#8cxk67)
* introduce feedback modal (#8cxk67)


### fix

* enable old auto updater instead of new one for EA (#8cxk67)
* review 1 (#8cxk67)
* review 2 (#8cxk67)
* review 3 (#8cxk67)
* review 4 (#8cxk67)
* validate response in PUC (#8cxk67)





# 1.1.0 (2020-10-16)


### build

* use node modules cache more aggressively in CI (#4akvz6)


### chore

* introduce Real Product Manager WordPress client package (#8cxk67)
* update PUC (#8cxk67)
* update PUC (#8cxk67)


### feat

* add checklist in config page header (#8cxk67)
* announcements (#8cxk67)
* introduce feedback modal (#8cxk67)


### fix

* enable old auto updater instead of new one for EA (#8cxk67)
* review 1 (#8cxk67)
* review 2 (#8cxk67)
* review 3 (#8cxk67)
* review 4 (#8cxk67)
* validate response in PUC (#8cxk67)
