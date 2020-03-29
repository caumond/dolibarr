./hook-setup.sh
mkdir documents
tar -C documents -xvzf documents.tgz
rm -fr ../../documents/*
mv -f documents/* ../../documents/
rm -fr documents

