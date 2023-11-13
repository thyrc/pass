<?php

namespace App\Controllers;

use App\Models\Vault;

class Token extends Unwrap {
    public function index() {
        helper(['form', 'url']);
        return $this::view('token');
    }

    public function unwrap_token() {
        helper(['form', 'url']);

        $input = $this->validate([
                'token' => 'required|min_length[8]|max_length[256]'
        ]);

        if (!$input) {
            echo $this::view('token', [
                    'validation' => $this->validator
            ]);
        } else {
            $vault = new Vault();
            $v = $vault->unwrap(esc($this->request->GetPost('token')));

            $vault_error = $v['errno'];
            $vault_answer = $v['response'];

            $this->_display_secret($vault_error, $vault_answer, false);
        }
    }
}
