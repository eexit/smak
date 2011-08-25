#!/bin/sh
git submodule --quiet update --recursive
curl -s http://downloads.atoum.org/nightly/mageekguy.atoum.phar -o ./tests/lib/atoum.phar
