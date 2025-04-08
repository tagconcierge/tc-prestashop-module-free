#!/bin/bash

docker compose -f event-inspector/docker-compose.yaml run -T --rm node-cli <<INPUT
yarn && yarn build
INPUT

docker compose -f docker-compose.yaml run -T --rm node-cli <<INPUT
yarn && yarn build
INPUT


docker-compose run -T --rm php-cli <<INPUT
rm vendor -Rf
composer install --no-dev --optimize-autoloader
INPUT

RELEASE_VERSION=$(grep -Po "this->version = '\K\d\.\d\.\d" tagconciergefree.php)
RELEASE_DIR="dist/tag-concierge-free-$RELEASE_VERSION"
RELEASE_FILE="tag-concierge-free-$RELEASE_VERSION.zip"

rm -Rf $RELEASE_DIR
mkdir -p $RELEASE_DIR/tagconciergefree

cp -R controllers $RELEASE_DIR/tagconciergefree/controllers
cp -R src $RELEASE_DIR/tagconciergefree/src
cp -R upgrade $RELEASE_DIR/tagconciergefree/upgrade
cp -R vendor $RELEASE_DIR/tagconciergefree/vendor
cp -R views $RELEASE_DIR/tagconciergefree/views
cp logo.png $RELEASE_DIR/tagconciergefree/logo.png
cp tagconciergefree.php $RELEASE_DIR/tagconciergefree/tagconciergefree.php
cp LICENSE.md $RELEASE_DIR/tagconciergefree/LICENSE.md

#override link to dist gtm inspector and settings module
rm -f $RELEASE_DIR/tagconciergefree/views/js/gtm-event-inspector.js
cp event-inspector/dist/gtm-event-inspector.js $RELEASE_DIR/tagconciergefree/views/js/gtm-event-inspector.js

rm -f $RELEASE_DIR/tagconciergefree/views/js/admin-settings.js
cp assets/dist/settings.js $RELEASE_DIR/tagconciergefree/views/js/admin-settings.js


cd $RELEASE_DIR && zip -r $RELEASE_FILE . && mv $RELEASE_FILE "../$RELEASE_FILE" && cd -

rm -Rf $RELEASE_DIR
