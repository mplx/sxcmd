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
box build

# Manifest
php build/manifest.php ${TAG}

# Upload phar
bin/sxcmd file:upload mplx-sx-public ./build/sxcmd.phar download:/sxcmd/release/sxcmd-latest.phar
bin/sxcmd file:upload mplx-sx-public ./build/sxcmd.phar download:/sxcmd/release/sxcmd-{$TAG}.phar