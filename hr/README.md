Advanced human ressource management
=========

Module HRM+

Licence
-------

GPLv3 or (at your option) any later version.

See COPYING for more information.

INSTALL
-------

- Make sure Dolibarr (v >= 3.6) is already installed and configured on your server.

- In your Dolibarr installation directory, edit the htdocs/conf/conf.php file

- Find the following lines:

		//$=dolibarr_main_url_root_alt ...
		//$=dolibarr_main_document_root_alt ...

- Uncomment these lines (delete the leading "//") and assign a sensible value according to your Dolibarr installation

	For example :

	- UNIX:

			$dolibarr_main_url_root = 'http://localhost/Dolibarr/htdocs';
			$dolibarr_main_document_root = '/var/www/Dolibarr/htdocs';
			$dolibarr_main_url_root_alt = '/custom';
			$dolibarr_main_document_root_alt = '/var/www/Dolibarr/htdocs/custom';

	- Windows:

			$dolibarr_main_url_root = 'http://localhost/Dolibarr/htdocs';
			$dolibarr_main_document_root = 'C:/My Web Sites/Dolibarr/htdocs';
			$dolibarr_main_url_root_alt = '/custom';
			$dolibarr_main_document_root_alt = 'C:/My Web Sites/Dolibarr/htdocs/custom';

- From your browser:

	- log in as a Dolibarr administrator

	- go to "Setup" -> "Modules"

	- the module is under tabs "module interface"

	- Find module HRM+ and activate it

	- Go to module configuration and set it up


Contributions
-------------

Feel free to contribute and report defects at aspangaro.dolibarr@gmail.com