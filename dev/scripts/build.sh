#!/bin/bash

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
cp -R vendor $RELEASE_DIR/tagconciergefree/vendor
cp -R views $RELEASE_DIR/tagconciergefree/views
cp logo.png $RELEASE_DIR/tagconciergefree/logo.png
cp tagconciergefree.php $RELEASE_DIR/tagconciergefree/tagconciergefree.php
cp LICENSE.md $RELEASE_DIR/tagconciergefree/LICENSE.md

cd $RELEASE_DIR && zip -r $RELEASE_FILE . && mv $RELEASE_FILE "../$RELEASE_FILE" && cd -

rm -Rf $RELEASE_DIR
