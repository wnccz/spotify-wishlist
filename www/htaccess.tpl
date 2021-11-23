# Apache configuration file (see https://httpd.apache.org/docs/current/mod/quickreference.html)
Require all granted

# disable directory listing
<IfModule mod_autoindex.c>
	Options -Indexes
</IfModule>

# enable cool URL
<IfModule mod_rewrite.c>
	RewriteEngine On
	# RewriteBase /

	# use HTTPS
	# RewriteCond %{HTTPS} !on
	# RewriteRule .? https://%{HTTP_HOST}%{REQUEST_URI} [R=301,L]

	# prevents files starting with dot to be viewed by browser
	RewriteRule /\.|^\.(?!well-known/) - [F]

	# front controller
	RewriteCond %{REQUEST_FILENAME} !-f
	RewriteCond %{REQUEST_FILENAME} !-d
	RewriteRule !\.(pdf|js|mjs|ico|gif|jpg|jpeg|png|webp|svg|css|rar|zip|7z|tar\.gz|map|eot|ttf|otf|woff|woff2)$ index.php [L]
</IfModule>

# enable gzip compression
<IfModule mod_deflate.c>
	<IfModule mod_filter.c>
		AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css application/javascript application/json application/xml image/svg+xml
	</IfModule>
</IfModule>
