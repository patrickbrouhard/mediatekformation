#!/bin/sh

# Active le mode coverage pour Xdebug
export XDEBUG_MODE=coverage

# Lance PHPUnit avec génération du rapport XML
php bin/phpunit --coverage-xml var/coverage

# Message de confirmation
echo "Coverage généré dans var/coverage/"