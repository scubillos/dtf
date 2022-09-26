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

        $res = $this->LoginCurl($credentials->username, $credentials->password, $this->request->host);
        if ($res["success"]) {
            $this->output->set_status_header($status)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode(['data' => $res], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        } else {
            $this->output->set_status_header(401)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode(['message' => "Error al ingresar en Bonita"], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }
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
        $info = curl_getinfo($ch);
        $this->lastCurl = [
            "result" => $result,
            "info" => $info
        ];
        if (curl_errno($ch)) {
            return [
                "success" => false,
                "message" => "Error en el curl de login de Bonita",
                "status" => $info["http_code"],
            ];
        }
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

        if (is_array($cookies) && isset($cookies["X-Bonita-API-Token"])) {
            return [
                "success" => true,
                "token" => $cookies["X-Bonita-API-Token"],
                "cookie" => $cookie,
            ];
        } else {
            return [
                "success" => false,
                "message" => "Error en el login de Bonita",
                "status" => 401,
            ];
        }
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
                "id_factura" => $requestData->id_factura,
                "tipo_documento" => $requestData->tipo_documento,
                "num_documento" => $requestData->num_documento,
                "categoria_producto_ant" => $requestData->categoria_producto_ant,
                "id_producto_ant" => $requestData->id_producto_ant,
                "valor_producto_ant" => $requestData->valor_producto_ant,
                "categoria_producto_nuevo" => $requestData->categoria_producto_nuevo,
                "id_producto_nuevo" => $requestData->id_producto_nuevo,
                "valor_producto_nuevo" => $requestData->valor_producto_nuevo,
                "email" => $requestData->email,
                "motivo" => $requestData->motivo,
                "observaciones" => $requestData->observaciones,
                "ciudad" => $requestData->ciudad,
                "fecha_solicitud" => date("Y-m-d") . "T" . date("H:i:s") . ".000Z",
            ],
        ];

        $res = $this->BonitaCurl($host, 'portal/resource/taskInstance/Customer/1.0/CreateRequest/API/bpm/userTask/' . $humanTaskId . '/execution', 'POST', $token, $cookie, $data, $contentType);

        if ($res["status"] == 204) {
            $this->output->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode(['data' => $res], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        } else {
            $this->output->set_status_header($res["status"])
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode(['message' => "Error en executeProcess"], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }
    }
}
