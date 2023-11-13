<?php

namespace App\Controllers;

use CodeIgniter\I18n\Time;
use App\Models\Vault;

class File extends Home {
    public function index() {
        return $this::view('upload');
    }

    public function upload() {
        $throttler = \Config\Services::throttler();
        if ($throttler->check($this->request->getIPAddress(), 60, MINUTE)) {
            $file = $this->request->getFile('file');
            $filename = $file->getClientName();
            $filesize = $file->getSize();
            $filetype = $file->getMimeType();
            $filehash = hash_file('sha256', $file);
            $clientip = $this->request->getIPAddress();
            if (! $file->isValid()) {
                $this->_show_error();
                return;
            }
            if ($filesize > 20971520 || $filesize === 0) {
                $this->_show_error();
                return;
            }

            $file_vault = new Vault();

            $data = array(
                    'filedata' => base64_encode(file_get_contents($file))
                    );
            $data_json = json_encode($data);

            $v = $file_vault->wrap($data_json);

            if (!isset($v['errno'])) {
                $this->_show_error();
                exit;
            }

            $vault_error = $v['errno'];
            $vault_answer = $v['response'];

            if ($vault_error === 0) {
                $json_array = json_decode($vault_answer, true);
                if (isset($json_array['errors'][0])) {
                    // $data['error'] = $json_array['errors'][0];
                    $this->_show_error();
                    return;
                } else {
                    $filetoken = json_encode($json_array['wrap_info']['token']);
                }

                $meta_vault = new Vault();

                $data = array(
                        'filename' => $filename,
                        'filesize' => $filesize,
                        'filetype' => $filetype,
                        'filehash' => $filehash,
                        'filetoken' => trim($filetoken, '"'),
                        'clientip' => $clientip,
                );
                $data_json = json_encode($data);

                $v = $meta_vault->wrap($data_json);

                if (!isset($v['errno'])) {
                    $this->_show_error();
                    exit;
                }

                $vault_error = $v['errno'];
                $vault_answer = $v['response'];

                if ($vault_error === 0) {
                    $json_array = json_decode($vault_answer, true);
                    if (isset($json_array['errors'][0])) {
                        $data['error'] = $json_array['errors'][0];
                    } else {
                        $data['token'] = json_encode($json_array['wrap_info']['token']);
                        $until = new Time('now');
                        $until = $until->addSeconds((int) trim(getenv('VAULT_WRAP_TTL'), '"'));
                        $data['until'] = $until->toDateTimeString() . ' (GMT ' . date('P') . ')';
                    }
                    echo $this::show('share_download', $data);
                } else {
                    $this->_show_error();
                }
            } else {
                $this->_show_error();
            }
        } else {
            $this->_show_error();
        }
    }
}
