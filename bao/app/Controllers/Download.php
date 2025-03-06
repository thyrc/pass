<?php

namespace App\Controllers;

use App\Models\Vault;

class Download extends Unwrap {
    protected function _display_download($vault_error = null, $vault_answer = '') {
        $session = session();
        if ($vault_error === 0) {
            $json_array = json_decode($vault_answer, true);
            if (isset($json_array['errors'][0])) {
                $data['error'] = json_encode($json_array['errors'][0],JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
            } else {
                $data['filename'] = trim(json_encode($json_array['data']['filename'],JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE), '"');
                $data['filesize'] = trim(json_encode($json_array['data']['filesize'],JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE), '"');
                $data['filetype'] = trim(json_encode($json_array['data']['filetype'],JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE), '"');
                $data['filehash'] = trim(json_encode($json_array['data']['filehash'],JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE), '"');
                $data['filetoken'] = trim(json_encode($json_array['data']['filetoken'],JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE), '"');
            }
            $session->set($data);
            echo $this::show('download', $data);
        } else {
            $this->_show_error();
        }
    }

    public function show_download($token = "token") {
        $vault = new Vault();
        $v = $vault->unwrap($token);

        $vault_error = $v['errno'];
        $vault_answer = $v['response'];

        $this->_display_download($vault_error, $vault_answer);
    }

    public function thankyou() {
        echo $this::view('thankyou', []);
    }

    public function download() {
        helper(['url']);
        $vault = new Vault();
        $session = session();

        $v = $vault->unwrap($session->get('filetoken'));

        $vault_error = $v['errno'];
        $vault_answer = $v['response'];

        $json_array = json_decode($vault_answer, true);

        if (isset($json_array['errors'][0])) {
            return $this->response->redirect(site_url('/'));
        }

        header('Content-Disposition: attachment; filename="' . $session->get('filename') . '"');
        header('Content-Type: ' . esc($session->get('filetype')), true);
        header('Expires: 0');
        header('Cache-Control: private');
        header('Pragma: public');
        header('Content-Length: ' . esc($session->get('filesize')));
        echo base64_decode(trim(json_encode($json_array['data']['filedata'],JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE), '"'));
        exit;
    }
}
