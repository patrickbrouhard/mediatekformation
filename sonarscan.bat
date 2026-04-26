@echo off
echo ==========================================
echo   Lancement de l'analyse SonarQube...
echo ==========================================

sonar-scanner.bat ^
  -D"sonar.projectKey=mediatekformation" ^
  -D"sonar.sources=." ^
  -D"sonar.host.url=http://localhost:9000" ^
  -D"sonar.token=sqp_53cd60c1871b6602caf326b6f5380b0d594ebdf7"

echo ==========================================
echo   Analyse terminee !
echo ==========================================
pause