# Change Log

All notable changes to this project will be documented in this file.
See [Conventional Commits](https://conventionalcommits.org) for commit guidelines.

## 4.0.9 (2021-06-05)

**Note:** Version bump only for package @devowl-wp/real-category-library





## 4.0.8 (2021-05-25)


### chore

* compatibility with latest antd version
* migarte loose mode to compiler assumptions
* polyfill setimmediate only if needed (CU-jh3czf)
* prettify code to new standard
* revert update of typedoc@0.20.x as it does not support monorepos yet
* upgrade dependencies to latest minor version


### ci

* move type check to validate stage


### fix

* do not rely on install_plugins capability, instead use activate_plugins so GIT-synced WP instances work too (CU-k599a2)


### test

* make window.fetch stubbable (CU-jh3cza)





## 4.0.7 (2021-05-14)

**Note:** Version bump only for package @devowl-wp/real-category-library





## 4.0.6 (2021-05-12)


### fix

* product type is not copied to other language in WPML





## 4.0.5 (2021-05-11)

**Note:** Version bump only for package @devowl-wp/real-category-library





## 4.0.4 (2021-05-11)


### fix

* compatibility with post formats (CU-j76m7v)
* featured functionality for WooCommerce products (CU-j76m7v)
* introduce new developer filter RCL/Categories to modify read categories (CU-uvak0t)


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


### style

* compatibility with newest WooCommerce version





## 4.0.3 (2021-04-27)


### ci

* push plugin artifacts to GitLab Generic Packages registry (CU-hd6ef6)





## 4.0.2 (2021-04-15)


### fix

* do not make WPML and PolyLang terms automatically hierarchical (CU-gq7rrn)





## 4.0.1 (2021-03-30)


### docs

* formatting error in wordpress.org product description corrected





# 4.0.0 (2021-03-23)


### build

* plugin tested for WordPress 5.7 (CU-f4ydk2)


### chore

* directly link to new settings page in Welcome page after plugin activation (CU-dcy665)
* new developer hooks RCL/Typenow, RCL/TableCheckboxName and RCL/ForcePostTypes
* remove option in WooCommerce to make attributes hierarchical (CU-dcy665)
* removed options in Screen settings cause you will find it new settings page (CU-dcy665)
* review 1 (CU-dcy665)
* update antd to 4.8 (CU-dcy665)
* update link to Real Media Library in options page (CU-dcy665)
* update translations (CU-fz392b)


### ci

* do not show license form for E2E tests
* upload artifacts to license.devowl.io (CU-fq1kd8)


### docs

* add GIFs and new header image in wordpress.org description (CU-60d07j)
* rewrite wordpress.org product description (CU-60d07j)


### feat

* automatically install and activate Real Custom Post Order on button click (CU-dcy665)
* automatically make none-hierarchical taxonomies hierarchical
* introduce new automatic plugin updater (CU-fq1kd8)
* new options page in Settings > Category Management (CU-dcy665)
* rewrite English and German translation (CU-dcy665)
* translation to Dutch (CU-dcy665)
* translation to French (CU-dcy665)
* translation to Italian (CU-dcy665)
* translation to Spanish (CU-dcy665)


### fix

* better compatibility with Custom Post Type UI (CU-dcy665)
* compatibility with WP Job Openings


### style

* improve compatibility with WooCommerce list table (CU-dcy665)


### BREAKING CHANGE

* please reactivate your current license to get latest updates for PRO
* if you want to force none-hierarchical taxonomies use custom filter
or rename the taxonomy so it contains "_tag"
* WooCommerce attributes are automatically hierarchical and can no longer be disabled





## 3.5.7 (2021-03-03)


### fix

* posts are no longer droppable (hotfix, CU-f4yh7t)





## 3.5.6 (2021-03-02)


### fix

* respect language of newsletter subscriber to assign to correct newsletter (CU-aar8y9)


### test

* typing mistakes (CU-ewzae8)





## 3.5.5 (2021-02-24)


### chore

* rename go-links to new syntax (#en621h)
* **release :** publish [ci skip]


### docs

* rename test drive to sanbox (#ef26y8)
* update README to be compatible with Requires at least (CU-df2wb4)





## 3.5.4 (2021-02-02)


### fix

* compatibility with Elementor template library when clicking Add New button (CU-d13prj)
* compatibility with FooBox lightbox (CU-dczh1k)





## 3.5.3 (2021-01-24)


### fix

* compatibility with Password Protected Categories plugin





## 3.5.2 (2021-01-18)


### fix

* compatibility with JetEngine (CU-c6wp6e)





## 3.5.1 (2021-01-11)


### build

* reduce javascript bundle size by using babel runtime correctly with webpack / babel-loader


### chore

* **release :** publish [ci skip]
* **release :** publish [ci skip]





# 3.5.0 (2020-12-15)


### feat

* add toolbar button to view and edit category details (CU-bazvh7)


### fix

* compatibility with WP File Download (CU-bazty6)





## 3.4.8 (2020-12-10)

**Note:** Version bump only for package @devowl-wp/real-category-library





## 3.4.7 (2020-12-09)

**Note:** Version bump only for package @devowl-wp/real-category-library





## 3.4.6 (2020-12-09)


### chore

* new host for react-aiot git repository (CU-9rq9c7)
* update to cypress v6 (CU-7gmaxc)
* update to webpack v5 (CU-4akvz6)
* updates typings and min. Node.js and Yarn version (CU-9rq9c7)
* **release :** publish [ci skip]


### fix

* allow to directly drag&drop folder structure without toolbar button (CU-2cfq3f)
* automatically deactivate lite version when installing pro version (CU-5ymbqn)
* automatically deactivate lite version when installing pro version (CU-5ymbqn)





## 3.4.5 (2020-12-01)


### chore

* update dependencies (CU-3cj43t)
* update major dependencies (CU-3cj43t)
* update to composer v2 (CU-4akvjg)
* update to core-js@3 (CU-3cj43t)
* **release :** publish [ci skip]


### refactor

* enforce explicit-member-accessibility (CU-a6w5bv)





## 3.4.4 (2020-11-24)


### fix

* compatibility with upcoming WordPress 5.6 (CU-amzjdz)
* use no-store caching for WP REST API calls to avoid issues with browsers and CloudFlare (CU-agzcrp)





## 3.4.3 (2020-11-18)

**Note:** Version bump only for package @devowl-wp/real-category-library





## 3.4.2 (2020-11-17)

**Note:** Version bump only for package @devowl-wp/real-category-library





## 3.4.1 (2020-11-12)


### ci

* make scripts of individual plugins available in review applications (#a2z8z1)
* release to new license server (#8wpcr1)





# 3.4.0 (2020-10-23)


### chore

* merge tsconfig.json with backend-coding


### feat

* route PATCH PaddleIncompleteOrder (#8ywfdu)


### refactor

* use "import type" instead of "import"





## 3.3.9 (2020-10-16)


### build

* use node modules cache more aggressively in CI (#4akvz6)


### chore

* rename folder name (#94xp4g)


### fix

* count for WooCommerce products





## 3.3.8 (2020-10-09)

**Note:** Version bump only for package @devowl-wp/real-category-library





## 3.3.7 (2020-10-08)


### chore

* **release :** version bump





## 3.3.6 (2020-09-29)


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





## 3.3.5 (2020-09-22)


### fix

* import settings (#82rk4n)
* remove urldecode as it is no longer needed





## 3.3.4 (2020-08-31)


### fix

* change of software license from GPLv3 to GPLv2 due to Envato Market restrictions (#4ufx38)





## 3.3.3 (2020-08-26)


### chore

* **release :** publish [ci skip]


### ci

* install container volume with unique name (#7gmuaa)


### perf

* remove transients and introduce expire options for better performance (#7cqdzj)





## 3.3.2 (2020-08-17)


### ci

* prefer dist in composer install





## 3.3.1 (2020-08-11)


### chore

* backends for monorepo introduced


### fix

* translation to german not applied (#76pbuh)





# 3.3.0 (2020-07-30)


### feat

* check support status for Envato license #CU-6pubwg
* introduce dashboard with assistant (#68k9ny)
* WordPress 5.5 compatibility (#6gqcm8)


### fix

* REST API notice in admin dashboard





## 3.2.23 (2020-07-02)


### chore

* allow to define allowed licenses in root package.json (#68jvq7)
* update dependencies (#3cj43t)


### fix

* correct error message when creating a duplicate category


### test

* cypress does not yet support window.fetch (#5whc2c)





## 3.2.22 (2020-06-17)


### chore

* add RCL/Node/Visible filter so you can programmatically hide categories in the tree
* update plugin updater newsletter text (#6gfghm)





## 3.2.21 (2020-06-12)


### chore

* i18n update (#5ut991)





## 3.2.20 (2020-05-27)


### build

* improve plugin build with webpack parallel builds


### ci

* use hot cache and node-gitlab-ci (#54r34g)


### docs

* redirect user documentation to new knowledgebase (#5etfa6)





## 3.2.19 (2020-05-20)

**Note:** Version bump only for package @devowl-wp/real-category-library





## 3.2.18 (2020-05-14)


### docs

* new wordpress.org assets #6jbg2r





## 3.2.17 (2020-05-12)


### build

* cleanup temporary i18n files correctly


### fix

* avoid flickering at page load (#42ggat)
* correctly enqueue dependencies (#52jf92)
* effeciently make search results of categories droppable (#4wn81h)
* links not clickable on touch devices (#4yhhyd)
* use WooCommerce' core sorting mechanism instead of own (#5pp9b)





## 3.2.16 (2020-04-27)


### chore

* add hook_suffix to enqueue_scripts_and_styles function (#4ujzx0)


### docs

* update user documentation and redirect to help.devowl.io (#6c9urq)


### fix

* droppable does no longer work after searching for a folder / category (#4wn81h)
* error after renaming an item without changing the name (#4wm93q)


### test

* add smoke tests (#4rm5ae)
* automatically retry cypress tests (#3rmp6q)





## 3.2.15 (2020-04-20)

**Note:** Version bump only for package @devowl-wp/real-category-library





## 3.2.14 (2020-04-16)

**Note:** Version bump only for package @devowl-wp/real-category-library





## 3.2.13 (2020-04-16)


### build

* adjust legal information for envato pro version (#46fjk9)
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


### docs

* broken links in developer documentation (#5yg1cf)


### fix

* link to Real Custom Post Order (#5ygvhw)


### style

* reformat php codebase (#4gg05b)


### test

* avoid session expired error in E2E tests (#3rmp6q)





## 3.2.12 (2020-03-31)


### chore

* update dependencies (#3cj43t)
* **release :** publish [ci skip]


### ci

* use concurrency 1 in yarn disclaimer generation


### fix

* posts could not be dragged when RCPO is active (#4cqgwj)


### style

* run prettier@2 on all files (#3cj43t)


### test

* configure jest setupFiles correctly with enzyme and clearMocks (#4akeab)
* generate test reports (#4cg6tp)





## 3.2.11 (2020-03-27)


### fix

* category tree not loaded even if tree view activated





## 3.2.10 (2020-03-23)


### build

* initial release of WP Real Custom Post Order plugin (#46ftef)





## 3.2.9 (2020-03-13)


### build

* migrate real-category-library to monorepo (#3ugu6a)


### fix

* i18n is not correctly initialized





## 3.2.8 (2020-03-10)
* prepare for WordPress 5.4
* fix bug with quick edit after fast mode content
* fix bug with WooCommerce panel
* update links to devowl.io

## 3.2.7 (2019-11-07)
* fix drag&drop of categories now represents the correct order after movement
* fix bug with ReactJS v17 warnings in your console

## 3.2.6 (2019-10-04)
* fix bug with two instances of MobX loaded

## 3.2.5 (2019-08-20)
* improve experience when sorting post entries
* fix bug with sort mode in subcategories
* fix bug with search box height in some cases that it needed too much space

## 3.2.4 (2019-06-02)
* fix bug when copy post that it is draggable again

## 3.2.3 (2019-05-07)
* add "title" attribute to tree node for accessibility
* update to latest AIOT version

## 3.2.2 (2019-03-19)
* add button to expand/collapse all node items
* fix bug with style/script dependencies
* fix bug with missing animations
* improve performance: Loading a tree with 10,000 nodes in 1s (the old way in 30s)

## 3.2.1 (2018-12-10)
* add notice to the tree if the product is not yet registered

# 3.2.0 (2018-10-27)
* add auto update functionality
* fix bug with new created folders and droppable posts
* fix bug with WPML API requests

## 3.1.1 (2018-08-17)
* fix bug with relocating categories to a category with no childs yet

# 3.1.0 (2018-08-05)
* improve the custom order performance
* improve the way of handling custom order
* fix bug with mass categories
* fix bug with "Plain" permalink structure
* fix bug with collapsable/expandable folders

## 3.0.6 (2018-July-20)
* improve error handling with plugins like Clearfy
* fix bug with "&" in category names
* fix bug with PHP 5.3
* fix bug with non-SSL API root urls
* fix bug with pagination in list mode after switching folder
* fix bug with Gutenberg 3.1.x (https://git.io/f4SXU)

## 3.0.5 (2018-06-15)
* add compatibility with WP Dark Mode plugin
* add help message if WP REST API is not reachable through HTTP verbs
* fix bug with scroll container in media modal in IE/Edge/Firefox
* Use global WP REST API parameters instead of DELETE / PUT

## 3.0.4 (2018-06-4)
* fix bug with spinning loader when permalink structure is "Plain"
* fix bug with german translation
* fix bug with IE11/Edge browser

## 3.0.3 (2018-05-17)
* fix bug with WPML and fetching a tree from another language within admin dashboard

## 3.0.2 (2018-05-08)
* improve performance
* fix bug with switching from category to "All posts"
* add Mobx State Tree for frontend state management

## 3.0.1 (2018-03-09)
* fix bug with mobile devices

# 3.0.0 (2018-02-28)
* Complete code rewrite
* ... Same functionality with improved performance
* ... with an eye on smooth user interface and experience
* The plugin is now available in the following languages: English, German
* fix bug with WooCommerce 3.3.x product attributes
* Sidebar is now fully written in ReactJS v16
* The plugin is now bundled with webpack v3
* Minimum of PHP 5.3 required now (in each update you'll find v2.4 for PHP 5.0+ compatibility)
* Minimum of WordPress 4.4 required now (in each update you'll find v2.4 for 4.0+ compatibility)
* PHP Classes modernized with autoloading and namespaces
* WP REST API v2 for API programming, no longer use admin-ajax.php for your CRUD operations
* Implemented cachebuster to avoid cache problems
* ApiGen for PHP Documentation
* JSDoc for JavaScript Documentation
* apiDoc for API Documentation
* WP HookDoc for Filters & Actions Documentation
* Custom filters and actions which affected the tree ouput are now removed, you have to do this in JS now
* All JavaScript events / hooks are removed now - contact me so I can implement for you

# 2.4.0 (2018-01-16)
* add support for WooCommerce attributes (through an option)
* improve the tax switcher (when multiple category types are available)

## 2.3.2 (2017-11-24)
* fix bug with hidden sidebar without resized before
* add filter to hide category try for specific taxonomies (RCL/Available)

## 2.3.1 (2017-10-31)
* fix bug after creating a new post the nodes are not clickable
* fix bug when switching taxonomy when fast mode is deactivated

# 2.3.0 (2017-10-28)
* add ability to expand/collapse the complete sidebar by doubleclick the resize button
* fix bug with WooCommerce 3.x
* fix bug with touch devices (no dropdown was touchable)
* fix bug with ESC key in rename mode
* fix bug with creating a new folder and switch back to previous
* fix bug with taxonomy switcher (especially WooCommerce products)
* improve the save of localStorage items within one row per tree instance

## 2.2.1 (2017-09-22)
* improve the tax switcher when more than two taxonomies are available
* fix bug when switching to an taxonomy with no categories
* add new filter to disable RCL sorting mechanism

# 2.2.0 (2017-06-24)
* add full compatibility with WordPress 4.8
* add ESC to close the rename category action
* add F2 handler to rename a category
* add double click event to open category hierarchy
* add search input field for categories
* fix bug with some browsers when local storage is disabled

## 2.1.1 (2017-03-24)
* add https://matthias-web.com as author url
* improve the way of rearrange mode, the folders gets expand after 700ms of hover
* fix bug with > 600 categories
* fix bug with styles and scripts
* fix bug with rearrange

# 2.1.0 (2016-11-24)
* add new version of AIO tree view (1.3.1)
* add the MatthiasWeb promotion dialog
* add responsivness
* improve performance with lazy loading of categories
* improve changelog
* Use rootParentId in jQuery AIO tree
* fix bug with jQuery AIO tree version when RML is enabled

## 2.0.2 (2016-09-09)
* Conflict with jQuery.allInOneTree

## 2.0.1 (2016-09-02)
* add minified scripts and styles
* fix capability bug while logged out
* add Javascript polyfill's to avoid browser incompatibility
* fix bug for crashed safari browser
* fix bug with boolval function

# 2.0.0 (2016-08-08)
* add more userfriendly toolbar (ported from RML)
* add fixed header
* add "fast mode" for switching between taxonomies without page reload
* add "fast mode" for switching between categories without page reload
* add "fast mode" for switching between pages without page reload
* add taxonomy to pages
* add custom order for taxonomies
* add new advertisment system for MatthiasWeb
* Complete recode of PHP and Javascript

## 1.1.1 (2016-01-20)
* add facebook advert on plugin activation
* fix count of categories

# 1.1.0 (2015-11-28)
* fix conditional tag to create / sort items
* fix hierarchical read of categories
* fix append method with CTRL - now tap and hold any key to append

## 1.0.2 (2015-11-13)
* remove unnecessary code
* fix jquery conflict

## 1.0.1 (2015-11-10)
* fix javascript error for firefox, ie and opera

# 1.0.0 (2015-11-08)
* initial Release
