<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bonita extends CI_Controller {

    private $request;
    
    public function __construct()
    {
        parent::__construct();
        $this->request = json_decode(file_get_contents('php://input'));
    }

    public function Login()
    {
        $data = [];
        $status = 200;
        $credentials = $this->request->credentials;

        $data = $this->LoginCurl($credentials->username, $credentials->password, $this->request->host);
        $this->output->set_status_header($status)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode(['data' => $data], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    private function LoginCurl($username, $password, $host) {
        $ch = curl_init($host);

        $data = array('username' =>$username, 'password' => $password, 'redirect' => 'false');
        curl_setopt($ch, CURLOPT_URL, $host . 'loginservice');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        $result = curl_exec($ch);
        // get cookie
        // multi-cookie variant contributed by @Combuster in comments
        preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $result, $matches);
        $cookies = array();
        foreach($matches[1] as $item) {
            parse_str($item, $cookie);
            $cookies = array_merge($cookies, $cookie);
        }
        curl_close($ch);

        $cookie = "";
        foreach ($cookies as $key => $value) {
            $cookie .= "$key=$value;";
        }

        return [
            "token" => $cookies["X-Bonita-API-Token"],
            "cookie" => $cookie,
        ];
    }

    public function GetProcess() {
        $status = 200;
        $process = $this->request->process;
        $host = $this->request->host;
        $token = $this->request->token;
        $cookie = $this->request->cookie;
        $headers = [ 
            'X-Bonita-API-Token: ' . $token,
            'Cookie: ' . $cookie,
        ];

        $ch = curl_init($host . 'API/bpm/process?s=' . $process);
        // Establecer URL y otras opciones apropiadas
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        // Capturar la URL y pasarla al navegador
        $result = curl_exec($ch);
        $result = json_decode($result);
        if (curl_errno($ch)) {
            $info = curl_getinfo($ch);
            var_dump($info);
        }
        curl_close($ch);

        $data = is_array($result) ? $result[0] : [];

        $this->output->set_status_header($status)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode(['data' => $data], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
}
