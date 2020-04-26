rm -fr backup
mkdir backup
tar cvzf backup/documents-"$(date '+%Y-%m-%d').tar.gz".tgz documents
mysqldump -h bamwv4bnune4xlv6b42i-mysql.services.clever-cloud.com -P 20366 -u uxxmg3fwsr5ymjnr -p$MYSQL_ADDON_PASSWORD bamwv4bnune4xlv6b42i > backup/db-"$(date '+%Y-%m-%d').sql"
