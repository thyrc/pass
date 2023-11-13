server {
    listen 80 default_server;
    server_name _;

    return 301 https://pass.example.com;

    access_log /var/log/nginx/pass.example.com.access.log combined;
    error_log /var/log/nginx/pass.example.com.error.log warn;
}

server {
    listen 443 ssl;
    server_name pass.example.com;
    index index.php;

    access_log /var/log/nginx/pass.example.com.access.log combined;
    error_log /var/log/nginx/pass.example.com.error.log warn;

    ssl_certificate /etc/ssl/letsencrypt/example.com/fullchain.pem;
    ssl_certificate_key /etc/ssl/letsencrypt/example.com/privkey.pem;

    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
    add_header X-XSS-Protection "1; mode=block";

    error_page 404 /index.php;

    client_max_body_size 32m;

    root /var/www/pass/public/;

    location / {
        try_files $uri $uri/ /index.php$is_args$args;

        location = /index.php {
            fastcgi_pass unix:/run/php/php-fpm.sock;
            include fastcgi.conf;
        }
    }

    location ~ /\. {
	return 404;
    }

    location ~ /\.php$ {
	return 404;
    }
}
