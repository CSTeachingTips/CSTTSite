docroot=docroot

theme_repo=
theme_name=

deploy_repo=git@bitbucket.org:atendesigngroup/deploykit.git
deploy_name=deploykit

profile_repo=
profile_name=

install: drush-make install-profile

init-aten: init get-profile get-theme

init: git get-deploy

drush-make:
	- cd $(docroot) && drush make -y --no-core profiles/$(profile_name)/$(profile_name).make

install-profile: local-settings
	- cd $(docroot) && drush si -y $(profile_name)

local-settings: drupal
	mv default.settings.php $(docroot)/sites/default/settings.php
	mv local-settings.inc $(docroot)/sites/default/local-settings.inc

git:
	rm README.md
	rm -rf .git
	git init

get-profile: drupal
	git clone --quiet --depth 1 $(profile_repo) $(docroot)/profiles/$(profile_name)
	rm -rf $(docroot)/profiles/$(profile_name)/.git
	cd $(docroot)/sites/all/modules && mkdir contrib custom dev patched features

get-theme: drupal zen
	git clone --quiet --depth 1 $(theme_repo) $(docroot)/sites/all/themes/$(theme_name)
	rm -rf $(docroot)/sites/all/themes/$(theme_name)/.git

zen:
	- drush dl zen --destination=$(docroot)/sites/all/themes/

drupal:
	- drush dl drupal --drupal-project-rename=$(docroot)
	- rm $(docroot)/.gitignore

get-deploy:
	git submodule --quiet add $(deploy_repo) $(deploy_name)
	cp $(deploy_name)/hosts.example settings/hosts
	cp $(deploy_name)/deploykit.conf.example settings/deploykit.conf
	cp $(deploy_name)/vagrant.yml.example settings/vagrant.yml
	cp $(deploy_name)/default.yml settings/dev.yml
	cp $(deploy_name)/default.yml settings/stage.yml
	cp $(deploy_name)/default.yml settings/prod.yml
