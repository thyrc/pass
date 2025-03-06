<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\I18n\Time;
use App\Models\Vault;

class Home extends BaseController {
    public function index() {
        helper(['form', 'url']);
        return $this::view('wrap');
    }

    public function view($page = 'wrap') {
        echo view('templates/header');
        echo view('pages/'.$page);
        echo view('templates/footer');
    }

    public function show($page = 'wrap', $data = null) {
        echo view('templates/header', $data);
        echo view('pages/'.$page, $data);
        echo view('templates/footer', $data);
    }

    protected function _show_error() {
            echo view('templates/header');
            echo view('pages/error');
            echo view('templates/footer');
    }

    public function wrap() {
        helper(['form', 'url']);

        $input = $this->validate([
                'secret' => 'required|max_length[32768]'
        ]);

        if (!$input) {
            echo $this::view('wrap', [
                    'validation' => $this->validator
            ]);
        } else {
            // return $this->response->redirect(site_url('/wrap'));
            $throttler = \Config\Services::throttler();
            if ($throttler->check($this->request->getIPAddress(), 120, MINUTE)) {
                $secret = esc($this->request->getPost('secret'));
                $data = array('secret'=> $secret,
                              'clientip' => $this->request->getIPAddress(),
                );
                $data_json = json_encode($data);

                $vault = new Vault();

                $v = $vault->wrap($data_json);

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
                        $until = $until->addSeconds((int) trim(getenv('BAO_WRAP_TTL'), '"'));
                        $data['until'] = $until->toDateTimeString() . ' (GMT ' . date('P') . ')';
                    }
                    echo $this::show('share', $data);
                } else {
                    $this->_show_error();
                }
            } else {
                $this->_show_error();
            }
        }
    }
}
