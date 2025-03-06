<?php

namespace App\Controllers;

use App\Models\Vault;

class Unwrap extends Home {
    protected function _display_secret($vault_error = null, $vault_answer = '', $secret_only = false) {
        if ($vault_error === 0) {
            $agent = $this->request->getUserAgent();
            $json_array = json_decode($vault_answer, true);
            if (isset($json_array['errors'][0])) {
                $data['error'] = json_encode($json_array['errors'][0],JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_LINE_TERMINATORS);
            } else {
                if ($secret_only !== true) {
                    $data['secret'] = json_encode($json_array['data'],JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_LINE_TERMINATORS);
                } else {
                    $data['secret'] = json_decode(json_encode($json_array['data']['secret'],JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_LINE_TERMINATORS));
                }
            }
            if ($agent->isBrowser()) {
                echo $this::show('show', $data);
            } else {
                if (isset($data['error'])) {
                    echo $data['error'];
                } else {
                    echo html_entity_decode($data['secret'],ENT_QUOTES|ENT_HTML5);
                }
            }
        } else {
            $this->_show_error();
        }
    }

    public function unwrap($token = "token") {
        $vault = new Vault();
        $v = $vault->unwrap($token);

        $vault_error = $v['errno'];
        $vault_answer = $v['response'];

        $this->_display_secret($vault_error, $vault_answer, false);
    }

    public function unwrap_secret($token = "token") {
        $vault = new Vault();
        $v = $vault->unwrap($token);

        $vault_error = $v['errno'];
        $vault_answer = $v['response'];

        $this->_display_secret($vault_error, $vault_answer, true);
    }
}
