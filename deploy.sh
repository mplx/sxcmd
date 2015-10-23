#!/bin/bash

# deploy sxcmd
# requires... php, git, composer, box https://github.com/box-project/box2

set -e

# Check tag parameter
if [ $# -ne 1 ]; then
  echo "Usage: `basename $0` <tag>"
  exit 65 # EX_DATAERR
fi
TAG=$1

php -r "if(preg_match('/^\d+\.\d+\.\d+\$/',\$argv[1])) exit(0); else { echo 'tag is invalid' . PHP_EOL ; exit(65); }" $TAG

# Clean vendor of req-dev
composer install --no-dev

# Tag latest commit
git tag ${TAG}

# Remove previous build
if [ -f ./build/sxcmd-${TAG}.phar]; then
    rm -f ./build/sxcmd-${TAG}.phar
fi

# Build phar
time box build

# Re-Install req-dev vendor stuff
composer install

# Manifest
php build/manifest.php ${TAG}

# Upload phar
bin/sxcmd file:upload sxcmd-deploy ./build/sxcmd-${TAG}.phar download:/sxcmd/release/sxcmd-${TAG}.phar --time
bin/sxcmd file:upload sxcmd-deploy ./build/sxcmd-${TAG}.phar download:/sxcmd/release/sxcmd-latest.phar --time
bin/sxcmd file:upload sxcmd-deploy ./manifest.json download:/sxcmd/release/manifest.json --time

# Commit new version
git commit -m "Version ${TAG}" ./manifest.json
git push --tags --progress "mplx-gitlab" master
