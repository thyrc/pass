<?php

namespace App\Models;

class Vault {
    public function unwrap($token = '') {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, getenv('BAO_ADDR').'/v1/sys/wrapping/unwrap');
        curl_setopt($ch, CURLOPT_CAINFO, getenv('BAO_CACERT'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $sane_token = filter_var($token, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_BACKTICK);

        $headers = [
            "X-Vault-Token: $sane_token"
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $vresponse = curl_exec($ch);
        $verrno = curl_errno($ch);

        curl_close($ch);

        $vreturn = array(
                'errno' => $verrno,
                'response' => $vresponse,
                );

        return($vreturn);
    }

    public function wrap($data) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, getenv('BAO_ADDR').'/v1/sys/wrapping/wrap');
        curl_setopt($ch, CURLOPT_CAINFO, getenv('BAO_CACERT'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $headers = [
            "X-Vault-Token: ".getenv('BAO_TOKEN'),
            "X-Vault-Wrap-TTL: ".getenv('BAO_WRAP_TTL')
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $vresponse = curl_exec($ch);
        $verrno = curl_errno($ch);

        curl_close($ch);

        $vreturn = array(
                'errno' => $verrno,
                'response' => $vresponse,
                );

        return($vreturn);
    }
}
