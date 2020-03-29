cp conf.php ../../htdocs/conf 
mkdir documents
tar -C documents -xvzf documents.tgz
rm -fr ../../documents
mv documents ../..
