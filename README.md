pass
========

### Initial Setup

Sample cronjob, nginx config & approle token init and renewal script are
stored in `setup/`.

The application in `bao/` was developed with codeigniter but should(tm) be
compatible with any PSR framework with minimal changes.

#### Vault

Create `wrap` policy

```
path "sys/wrapping/wrap" {
  capabilities = ["create"]
}
```

Setup `wrap` approle.
Use `token_bound_cidrs` to tie the wrapping token to the host running the service.

```
bao auth enable approle
bao policy write wrap policies/wrap.hcl
bao write auth/approle/role/wrap token_policies="wrap" token_ttl=48h token_period=48h token_bound_cidrs=["127.0.0.1/32"]
bao read auth/approle/role/wrap/role-id
bao write -f auth/approle/role/wrap/secret-id
``` 

The Role ID and Secret ID are needed to generate a token later (`init_bao.sh`).

Enable audit.log

```
mkdir /var/log/openbao
chown openbao:openbao /var/log/openbao
bao audit enable file file_path=/var/log/openbao/audit.log
```

#### bao.example.com

Install nginx, php-fpm, modules and tools.

```
apt install php-fpm php-intl php-curl php-mbstring jq apache2-utils cron logrotate
```

Don't forget to set `post_max_size` and `upload_max_filesize` in fpm's
`php.ini`.

```
post_max_size = 32M
upload_max_filesize = 32M
```

And make sure the nginx user can access the php-fpm socket.

```
listen.owner = www-data
listen.group = www-data
```

Avoid duplicate Cache-Control headers w/

```
session.cache_limiter = ''
```

and update session lifetime.

```
session.gc_maxlifetime = 21600
```

Setup application root.

```
chown -R www-data:www-data /var/www/bao/writable

touch /var/www/bao/.env
chown www-data:www-data /var/www/bao/.env
chmod 0640 /var/www/bao/.env
```

Setup bacsic auth.

```
touch /etc/nginx/bao.pwd
chmod 0644 /etc/nginx/bao.pwd
```

Initialize approle token.
A combination of a Role ID and Secret ID is required.

```
chmod 0600 /root/bin/init_bao.sh
chmod 0600 /root/bin/renew_bao_token.sh
chmod 0600 /root/bin/rotate_basic_auth.sh
chown root:root /root/bin/init_bao.sh
chown root:root /root/bin/renew_bao_token.sh
chown root:root /root/bin/rotate_basic_auth.sh

bash /root/bin/init_bao.sh
bash /root/bin/rotate_basic_auth.sh
```

Install crontab

```
vim /etc/cron.d/renew_bao_token
```

Replace all bao.example.com and occurrences and change the `vault_server`
variable according to your setup.
