#!/bin/sh

#https://pranavprakash.net/2017/02/04/automate-mongodb-backups-using-cron-and-shell-script/

#=====================================================================
# Set the following variables as per your requirement
#=====================================================================
# Database Name to backup
MONGO_DATABASE="beneficencia"
# Database host name
MONGO_HOST="127.0.0.1"
# Backup directory
BACKUPS_DIR="/opt/bitnami/apache2/htdocs/backup/$MONGO_DATABASE"
# Database user name
DBUSERNAME="root"
# Database password
DBPASSWORD="fxeutQ9M"
# Authentication database name
DBAUTHDB="admin"
#=====================================================================

TIMESTAMP=$(date +%F-%H%M)
BACKUP_NAME="$MONGO_DATABASE-$TIMESTAMP"

echo "Deteniendo Apache..."
sudo /opt/bitnami/ctlscript.sh stop apache
echo "Deteniendo MongoDB..."
sudo /opt/bitnami/ctlscript.sh restart mongodb
echo "--------------------------------------------"
echo "Haciendo Backup de $MONGO_DATABASE ..."
echo "--------------------------------------------"
# Create backup directory
if ! mkdir -p "$BACKUPS_DIR"; then
  echo "No se puede crear el directorio de backup en $BACKUPS_DIR. Ve y arreglalo!" 1>&2
  exit 1;
fi;
# Create dump
mongodump -d $MONGO_DATABASE --host=$MONGO_HOST --username $DBUSERNAME --password $DBPASSWORD --authenticationDatabase $DBAUTHDB
# Renombrar directorio dump a nombre backup
mv dump "$BACKUP_NAME"
# Comprimir backup
echo "--------------------------------------------"
echo "Comprimiendo Colecciones..."
echo "--------------------------------------------"
tar -zcvf "$BACKUPS_DIR"/"$BACKUP_NAME".tgz "$BACKUP_NAME"
# Delete uncompressed backup
rm -rf "$BACKUP_NAME" +
echo "--------------------------------------------"
echo "Backup de la base de datos completo!"
echo "--------------------------------------------"

echo "Reiniciar los servicios"
sudo /opt/bitnami/ctlscript.sh restart
echo "Listo!"
echo "--------------------------------------------"

