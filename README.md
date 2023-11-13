pass
====

Simple password / file sharing tool w/ HashiCorp Vault backend (https://www.vaultproject.io/).

Written using PHP framework CodeIgniter (https://codeigniter.com/).

### Initial Setup

Sample cronjob, nginx config & approle token init script are stored in `setup/`.

The application in `pass/` was developed on a weekend as a proof of concept during product evaluation. Don't expect anything fancy, but it does it's job quite nicely.

#### HashiCorp Vault

Create `wrap` policy

```
path "sys/wrapping/wrap" {
  capabilities = ["create"]
}
```

Setup `wrap` approle

```
vault auth enable approle
vault policy write wrap policies/wrap.hcl
vault write auth/approle/role/wrap token_policies="wrap" token_ttl=48h token_max_ttl=96h
vault read auth/approle/role/wrap/role-id
vault write -f auth/approle/role/wrap/secret-id
``` 

#### pass.example.com

Install nginx, php-fpm and modules.

```
apt install php-fpm php-intl php-curl php-mbstring
```

Don't forget to set `post_max_size` and `upload_max_filesize` in fpm's `php.ini`.

```
post_max_size = 32M
upload_max_filesize = 32M
```

Avoid duplicate Cache-Control headers w/
```
session.cache_limiter = ''
```

Setup application root.

```
chown -R root:root /var/www/pass
chown -R www-data:www-data /var/www/pass/writable

touch /var/www/pass/.env
chown www-data:www-data /var/www/pass/.env
chmod 0640 /var/www/pass/.env
```

Initialize approle token renewal. Be sure to swap `ROLE_ID` and `SECRET_ID` in `init_pass.sh` with the actual IDs.

```
chmod 0600 /root/bin/init_pass.sh
chown root:root /root/bin/init_pass.sh
bash /root/bin/init_pass.sh
```

Install crontab

```
vim /etc/cron.d/renew_pass_token
```

Replace all `pass.example.com` and `vault.example.com` occurrences.
