#!/bin/sh
#
# eWeBuKi install script
#
# (c) Stefan Krister
#     stefan.krister (at) creative.chaos.de
#
# $Id$
#

function ask()
{
	question=$1
	default=$2
	
	echo
	echo "[Return] um den Defaultwert zu uebernehmen."
	echo -n "$question [$default] "
	read answer
	if [ -z "$answer" ]  # Return gedrueckt?
	then
		answer=$default
	fi
}

#
# main
#
echo
echo "eWeBuKi install script"
echo

which cvs >/dev/null 2>&1
if [ ! $? ]
then
	echo "cvs ist installiert - soll der aktuelle cvs-Inhalt von eWeBuKi abgezogen"
	ask "werden? (ja/nein) " "ja"
	if [ "$answer" = "ja" ]
	then
		echo "CVS wird aktuell Abgezogen ..."
		cvs -z3 -d:pserver:anonymous@cvs.ewebuki.berlios.de:/cvsroot/ewebuki co snapshot > /dev/null
	fi
else
	echo "cvs ist nicht installiert. Es wird der Inhalt des Archivs zur Installation benutzt."
fi

echo
echo "Die Dateien muessen nun in das Webroot verschoben werden."
ask "Pfad zum Webroot?" "/var/www/htdocs/ewebuki"
webroot=$answer

if [ ! -d $webroot ]
then
	echo "Das Verzeichnis $webroot ist _nicht_ vorhanden."
	echo "Ende."
	exit 1
fi

owner=`ls -ld $webroot | awk '{print $3}' `
group=`ls -ld $webroot | awk '{print $4}' `

echo "Kopieren der Dateien nach $webroot ..."
(cd basement && tar cf - . ) | (cd $webroot && tar xvfp -) >/dev/null
echo "Rechte anpassen ($owner/$group) ..."
chown -R $owner:$group $webroot

#
# Datenbank
#
mysqlparams="--batch --skip-column-names"

ask "Benutzername zum Einrichten der Datenbank?" "dbadmin"
dbadmin=$answer

ask "Passwort von $dbadmin?" "secret"
dbadminpw=$answer

mysqlparams="$mysqlparams --user=$dbadmin --password=$dbadminpw"

#
# Passen name/passwort?
#
mysqladmin -u $dbadmin -p$dbadminpw status >/dev/null 2>&1
if [ $? == 1 ] # Fehlermeldung?
then
	echo "Mit $dbadmin und dem Passwort $dbadminpw klappt die Datenbank-"
	echo "anmeldung nicht."
	echo "Ende."
	exit 1
fi

#
# Hat der User ausreichende Rechte?
#
grants=`echo "show grants for $dbadmin@localhost" | mysql $mysqlparams`

if [ `echo $grants | grep -c "GRANT ALL PRIVILEGES ON"` = 0 ]
then 
	echo "Der Benutzer $dbadmin hat nicht genuegend Rechte."
	echo "Ende."
	exit 1
fi

databases=`echo "show databases" | mysql $mysqlparams`
echo
echo "Es gibt auf ihrem System bereits folgende Datenbanken:"
for database in $databases
do
	echo -n "$database "
done

echo
ask "Welche Datenbank soll fuer eWeBuKi benutzt werden? " "ewebuki"

for database in $databases
do
	if [ "$database" = "$answer" ]
	then
		echo "Im Moment geht das noch nicht."
		echo "Die Datenbank darf noch nicht existieren!"
		echo "Ende."
		exit 1 
	fi
done

database=$answer

echo
echo "eWeBuKi-Datenbank $database wird erzeugt ..."
echo "create database $database;" | mysql $mysqlparams
echo "eWeBuKi-Tabellen werden angelegt ..."

echo "use $database;" > setup.sql
cat basement/sql/eWeBuKi.mysql.sql >> setup.sql

mysql $mysqlparams < setup.sql

echo
echo "Jetzt braucht es noch einen Benutzer der auf die eWeBuKi-Datenbank"
echo "$database zugreifen kann. Aus Sicherheitsgruenden wird diesem Benutzer"
echo "nur die Rechte select, insert, update und delete gegeben."
ask "Benutzername?" "ewebuki"
username=$answer
ask "Passwort fuer $username?" "secret"
userpass=$answer

echo "use $database;" > setup.sql
echo "grant select, insert, update, delete on *.* to $username@localhost identified by '$userpass';" >> setup.sql
echo "flush privileges;" >> setup.sql
mysql $mysqlparams < setup.sql
rm setup.sql

echo
echo "So, nachdem wir Dateien und Datenbank haben, verschieben"
echo "wir jetzt ein wenig unsere Dateien. Damit sich beim Update"
echo "deine Einstellungen mit meinen vertragen, kommen alle"
echo "Files an denen was zum anpassen ist, als php-dist. Vor dem"
echo "ersten Start ändern wir also genau diese Files ..."

cd $webroot

mv index.php.auto-dist index.php
mv .htaccess-dist .htaccess
echo "RewriteRule ^html basic/main.php [T =application/x-httpd-php]" >> .htaccess

cd conf
for datei in *.php-dist
do
	base=`basename $datei .php-dist`
	mv $datei $base.php
done

# in der site.cfg.php tragen wir nun unsere Zugangsdaten für die Datenbank ein:

cp site.cfg.php site.cfg.php.bak

sed  -e "s/define ('DATABASE', 'eWeBuKi');/define ('DATABASE', '$database');/" \
     -e "s/define ('DB_USER', 'changeme');/define ('DB_USER', '$username');/" \
     -e "s/define ('DB_PASSWORD', 'changeme');/define ('DB_PASSWORD', '$userpass');/" \
     site.cfg.php.bak > site.cfg.php

rm site.cfg.php.bak

cd $webroot/modules/admin
for datei in *.php-dist
do
	base=`basename $datei .php-dist`
	mv $datei $base.php
done

#
# fertig!
#
