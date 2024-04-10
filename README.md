pass
====

Simple password / file sharing tool

Uses HashiCorp Vault (https://www.vaultproject.io/) or OpenBao (https://github.com/openbao/openbao) as backend.

Written using PHP Framework CodeIgniter (https://codeigniter.com/).

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

Setup `wrap` approle.

Optionally use `token_bound_cidrs` to tie the wrapping token to the pass host.

```
vault auth enable approle
vault policy write wrap policies/wrap.hcl
vault write auth/approle/role/wrap token_policies="wrap" token_ttl=48h [ token_bound_cidrs="127.0.0.1/32" ]
vault read auth/approle/role/wrap/role-id
vault write -f auth/approle/role/wrap/secret-id
``` 

The Role ID and Secret ID are needed to generate a vault token later (`init_pass.sh`).

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

And make sure the nginx user can access the php-fpm socket. E.g.
```
listen.owner = www-data
listen.group = www-data
```

Setup application root.

```
chown -R root:root /var/www/pass
# user:group depends on `user` / `group` settings in your php-fpm config
chown -R www-data:www-data /var/www/pass/writable

touch /var/www/pass/.env
chown www-data:www-data /var/www/pass/.env
chmod 0640 /var/www/pass/.env
```

Initialize approle token.
A combination of a Role ID and Secret ID is required.

```
chmod 0600 /root/bin/init_pass.sh
chmod 0600 /root/bin/renew_pass_token.sh
chown root:root /root/bin/init_pass.sh
chown root:root /root/bin/renew_pass_token.sh

bash /root/bin/init_pass.sh
```

Install crontab / setup token renewal

```
vim /etc/cron.d/renew_pass_token
```

Replace all `pass.example.com` and `vault.example.com` occurrences.
