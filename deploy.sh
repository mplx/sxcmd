#!/bin/bash

# deploy sxcmd
# requires... php, git, box https://github.com/box-project/box2

set -e

# Check tag parameter
if [ $# -ne 1 ]; then
  echo "Usage: `basename $0` <tag>"
  exit 65 # EX_DATAERR
fi
TAG=$1

php -r "if(preg_match('/^\d+\.\d+\.\d+\$/',\$argv[1])) exit(0); else { echo 'tag is invalid' . PHP_EOL ; exit(65); }" $TAG

# Tag latest commit
git tag ${TAG}

# Build phar
time box build

# Manifest
php build/manifest.php ${TAG}

# Upload phar
bin/sxcmd file:upload mplx-sx-public ./build/sxcmd-${TAG}.phar download:/sxcmd/release/sxcmd-${TAG}.phar --time
bin/sxcmd file:upload mplx-sx-public ./build/sxcmd-${TAG}.phar download:/sxcmd/release/sxcmd-latest.phar --time
bin/sxcmd file:upload mplx-sx-public ./manifest.json download:/sxcmd/release/manifest.json --time

# Commit new version
git commit -m "Version ${TAG}" ./manifest.json
git push --tags --progress "mplx-gitlab" master
