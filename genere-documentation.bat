@echo off
echo Generation de la documentation...

C:\wamp64\bin\php\php8.2.26\php.exe ..\phpDocumentor.phar run --ansi --directory "C:\wamp64\www\mediatekformation\src" --target "C:/wamp64/www/docs" --title "Mediatekformation"

pause