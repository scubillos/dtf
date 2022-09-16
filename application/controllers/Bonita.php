<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bonita extends CI_Controller {

    private $request;

    private $lastCurl;
    
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

    private function BonitaCurl($host, $action, $method, $token, $cookie, $data = [], $contentType = null) {
        $headers = [ 
            'X-Bonita-API-Token: ' . $token,
            'Cookie: ' . $cookie,
        ];

        if (!is_null($contentType)) {
            $headers[] = 'Content-Type: '. $contentType;
        }

        $ch = curl_init($host . $action);
        // Establecer URL y otras opciones apropiadas
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if (count($data) != 0 && ($method == "POST" || $method == "PUT")) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        
        // Capturar la URL y pasarla al navegador
        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        $this->lastCurl = [
            "result" => $result,
            "info" => $info
        ];
        $result = json_decode($result);
        if (curl_errno($ch)) {
            return [
                "success" => false,
                "status" => $info["http_code"],
            ];
        }
        curl_close($ch);

        $data = [];
        if ($result !== null) {
            $data = $result;
        }

        return [
            "success" => true,
            "data" => $data,
            "status" => $info["http_code"],
        ];
    }

    public function GetProcess() {
        $process = $this->request->process;
        $host = $this->request->host;
        $token = $this->request->token;
        $cookie = $this->request->cookie;

        $res = $this->BonitaCurl($host, 'API/bpm/process?s=' . $process, 'GET', $token, $cookie);
        
        if (!$res["success"]) {
            $this->output->set_status_header($res["status"])
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode(['message' => "Error en GetProcess"], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        } else {
            $this->output->set_status_header($res["status"])
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode(['data' => $res["data"][0]], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }
    }

    public function StartProcess($processId) {
        $host = $this->request->host;
        $token = $this->request->token;
        $cookie = $this->request->cookie;

        $res = $this->BonitaCurl($host, 'API/bpm/process/' . $processId . '/instantiation', 'POST', $token, $cookie);
        
        if (!$res["success"]) {
            $this->output->set_status_header($res["status"])
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode(['message' => "Error en StartProcess"], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        } else {
            $this->output->set_status_header($res["status"])
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode(['data' => $res["data"]], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }
    }

    public function HumanTask($deployedBy) {
        $host = $this->request->host;
        $token = $this->request->token;
        $cookie = $this->request->cookie;

        $params = [
            "c" => '50',
            "d" => 'rootContainerId',
            "f" => 'user_id%3D' . $deployedBy,
            "o" => 'displayName ASC',
            "p" => '0',
        ];
        

        $res = $this->BonitaCurl($host, 'portal/resource/app/userAppBonita/task-list/API/bpm/humanTask?c=50&d=rootContainerId&f=state%253Dready&f=user_id%253D22&o=displayName+ASC&p=0', 'GET', $token, $cookie);
        
        if (!$res["success"]) {
            $this->output->set_status_header($res["status"])
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode(['message' => "Error en HumanTask"], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        } else {
            $this->output->set_status_header($res["status"])
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode(['data' => end($res["data"])], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }
    }

    public function AssignActor() {
        $host = $this->request->host;
        $token = $this->request->token;
        $cookie = $this->request->cookie;
        $humanTaskId = $this->request->humanTaskId;
        $deployedBy = $this->request->deployedBy;

        $data = [
            "assigned_id" => $deployedBy,
        ];

        $res = $this->BonitaCurl($host, 'portal/resource/app/userAppBonita/task-list/API/bpm/humanTask/' . $humanTaskId, 'PUT', $token, $cookie, $data);

        if (!$res["success"]) {
            $this->output->set_status_header($res["status"])
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode(['message' => "Error en AssignActor"], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        } else {
            $this->output->set_status_header($res["status"])
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode(['data' => $res], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }
    }

    public function ExecuteProcess() {
        $host = $this->request->host;
        $token = $this->request->token;
        $cookie = $this->request->cookie;
        $requestData = $this->request->request;
        $humanTaskId = $this->request->humanTaskId;

        $contentType = 'application/json';
        $data = [
            "solicitudDevolucionInput" => [
                "invoceNumber" => $requestData->id_factura,
                "typeDocument" => $requestData->tipo_documento,
                "numberDocument" => $requestData->num_documento,
                "catOldProduct" => "Ropa interior",
                "oldProduct" => $requestData->id_producto_ant,
                "catNewProduc" => "Ropa",
                "newProduct" => $requestData->id_producto_nuevo,
                "email" => $requestData->email,
                "observation" => $requestData->observaciones,
                "ciudadDestino" => $requestData->ciudad,
                "fechaSolicitud" => date("Y-m-d"),
            ],
        ];

        $res = $this->BonitaCurl($host, 'portal/resource/taskInstance/Customer/1.0/CreateRequest/API/bpm/userTask/' . $humanTaskId . '/execution', 'POST', $token, $cookie, $data, $contentType);

        if (!$res["success"]) {
            $this->output->set_status_header($res["status"])
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode(['message' => "Error en AssignActor"], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        } else {
            $this->output->set_status_header($res["status"])
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode(['data' => $res], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }
    }
}
