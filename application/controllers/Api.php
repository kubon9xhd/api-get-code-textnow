<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Api extends CI_Controller
{
    private const PASSWORD = "textnowapi2021@@";
    public function __construct()
    {
        parent::__construct();
        $this->load->model('api_model');
        $this->load->library('form_validation');
    }
    function index()
    {
        if ($this->input->get("password") != Api::PASSWORD) {
            echo json_encode(array());
            return;
        }
        $data = $this->api_model->fetch_all();
        echo json_encode($data->result_array());
    }
    function insert()
    {
        // config data
        $config = [
            [
                'field' => 'username',
                'label' => 'Username',
                'rules' => 'required|min_length[3]',
                'errors' => [
                    'required' => 'We need both username and password',
                    'min_length' => 'Minimum Username length is 3 characters',
                    'alpha_dash' => 'You can only use a-z 0-9 _ . – characters for input',
                ],
            ],
            [
                'field' => 'password',
                'label' => 'Password',
                'rules' => 'required|min_length[6]',
                'errors' => [
                    'required' => 'You must provide a Password.',
                    'min_length' => 'Minimum Password length is 6 characters',
                ],
            ],
            [
                'field' => 'phonenumber',
                'label' => 'phonenumber',
                'rules' => 'required|min_length[10]',
                'errors' => [
                    'required' => 'You must provide a phonenumber.',
                    'min_length' => 'Minimum phonenumber length is 10 characters',
                ],
            ],
            [
                'field' => 'cookie',
                'label' => 'Cookie',
                'rules' => 'required|min_length[15]',
                'errors' => [
                    'required' => 'You must provide a Cookie.',
                    'min_length' => 'Minimum Cookie length is 15 characters',
                ],
            ],
        ];
        // xss clean data
        $stream_clean = $this->security->xss_clean($this->input->raw_input_stream);
        header('Content-Type: application/json');
        $request = json_decode($stream_clean);
        // echo json_encode($request);
        // return;
        if ($request == null || $request->acc == "") {
            echo json_encode(array(
                "error" => 105,
                "msg" => "Please input field data."
            ));
            return;
        }
        $acc = explode("|", $request->acc);
        $data = array(
            "username" => $acc[0],
            "password" => $acc[1],
            "phonenumber" => $acc[2],
            "cookie" => $acc[3]
        );
        $this->form_validation->set_data($data);
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() != FALSE) {
            // check doublicate 
            $condition = array(
                'username' => $data['username'],
                'password' => $data['password']
            );
            if ($this->api_model->checkDuplicate($condition) != null) {
                echo json_encode(
                    array(
                        "error" => 104,
                        "msg" => "Account exist in database."
                    )
                );
                return;
            }
            $data["status"] = 1;
            $data['created_date'] = time();
            if ($this->api_model->insert_api($data) != null)
                echo json_encode($data);
            else
                echo json_encode(
                    array(
                        "error" => 104,
                        "msg" => "Now can not insert to DB, pls contact to dev"
                    )
                );
        } else {
            print_r(json_encode($this->form_validation->error_array()));
        }
    }
    function get_acc()
    {
        header('Content-Type: application/json');
        $password = $this->input->post("password");
        if (APi::PASSWORD != $password) {
            echo json_encode(array(
                "status" => 100,
                "msg" => "Access denied!!"
            ));
            return;
        }
        // config data
        $config = [
            [
                'field' => 'username',
                'label' => 'Username',
                'rules' => 'required|min_length[3]',
                'errors' => [
                    'required' => 'We need both username and password',
                    'min_length' => 'Minimum Username length is 3 characters',
                    'alpha_dash' => 'You can only use a-z 0-9 _ . – characters for input',
                ],
            ],
            [
                'field' => 'password',
                'label' => 'Password',
                'rules' => 'required|min_length[6]',
                'errors' => [
                    'required' => 'You must provide a Password.',
                    'min_length' => 'Minimum Password length is 6 characters',
                ],
            ],
            [
                'field' => 'phonenumber',
                'label' => 'phonenumber',
                'rules' => 'required|min_length[10]',
                'errors' => [
                    'required' => 'You must provide a phonenumber.',
                    'min_length' => 'Minimum phonenumber length is 10 characters',
                ],
            ],
        ];
        // get data
        $data_get = $this->api_model->find_one_random();
        // echo json_encode($data_get);
        // return;
        if ($data_get == null) {
            echo json_encode(array(
                "error" => 105,
                "msg" => "Can not get field data."
            ));
            return;
        }
        // echo var_dump($data_get);
        // return;
        $data = array(
            "username" => $data_get[0]['username'],
            "password" => $data_get[0]['password'],
            "phonenumber" => $data_get[0]['phonenumber']
        );
        $this->form_validation->set_data($data);
        $this->form_validation->set_rules($config);
        header('Content-Type: application/json');
        if ($this->form_validation->run() != FALSE) {
            $data['status'] = 0;
            $data['code'] = "";
            try {
                if ($this->api_model->update_api($data)) {
                    echo json_encode(array(
                        "phonenumber" => $data['phonenumber']
                    ));
                    return;
                } else {
                    echo json_encode(array(
                        "error" => 103,
                        "msg" => "Error identified random error"
                    ));
                    return;
                }
            } catch (Exception $ex) {
                echo json_encode(array(
                    "error" => 103,
                    "msg" => "Error identified random error"
                ));
                return;
            }
        } else {
            print_r(json_encode($this->form_validation->error_array()));
        }
    }
    function get_code()
    {
        header('Content-Type: application/json');
        $password = $this->input->post("password");
        $phone = $this->input->post("phone",TRUE);
        if (APi::PASSWORD != $password) {
            echo json_encode(array(
                "status" => 100,
                "msg" => "Access denied!!"
            ));
            return;
        }
        // config data
        $config = [
            [
                'field' => 'username',
                'label' => 'Username',
                'rules' => 'required|min_length[3]',
                'errors' => [
                    'required' => 'We need both username and password',
                    'min_length' => 'Minimum Username length is 3 characters',
                    'alpha_dash' => 'You can only use a-z 0-9 _ . – characters for input',
                ],
            ],
            [
                'field' => 'password',
                'label' => 'Password',
                'rules' => 'required|min_length[6]',
                'errors' => [
                    'required' => 'You must provide a Password.',
                    'min_length' => 'Minimum Password length is 6 characters',
                ],
            ],
            [
                'field' => 'phonenumber',
                'label' => 'phonenumber',
                'rules' => 'required|min_length[10]',
                'errors' => [
                    'required' => 'You must provide a phonenumber.',
                    'min_length' => 'Minimum phonenumber length is 10 characters',
                ],
            ],
            [
                'field' => 'cookie',
                'label' => 'Cookie',
                'rules' => 'required|min_length[15]',
                'errors' => [
                    'required' => 'You must provide a Cookie.',
                    'min_length' => 'Minimum Cookie length is 15 characters',
                ],
            ],
        ];
        // get data
        $data_get = $this->api_model->find_one(array(
            "phonenumber" => $phone,
            "status" => 0
        ));

        if ($data_get == null) {
            echo json_encode(array(
                "error" => 105,
                "msg" => "Can not get field data."
            ));
            return;
        }
        // echo var_dump($data_get);
        // return;
        $data = array(
            "username" => $data_get->username,
            "password" => $data_get->password,
            "phonenumber" => $data_get->phonenumber,
            "cookie" => $data_get->cookie
        );
        $this->form_validation->set_data($data);
        $this->form_validation->set_rules($config);
        header('Content-Type: application/json');
        if ($this->form_validation->run() != FALSE) {
            $data['status'] = 0;
            $api = "http://localhost:8080/api-text-now/CodeIgniter/index.php/texnowapi/get_code";
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => $api,
                CURLOPT_USERAGENT => 'POST',
                CURLOPT_POST => 1,
                CURLOPT_SSL_VERIFYPEER => false, //Bỏ kiểm SSL
                CURLOPT_POSTFIELDS => http_build_query(array(
                    'cookie' => $data['cookie'],
                    'password' => 'nguyenduchung113@@'
                ))
            ));
            $result = json_decode(curl_exec($curl));
            // var_dump($result);
            curl_close($curl);
            // return;
            if ($result->status != 200) {
                if ($result->status == 101) {
                    $data['status'] = -1;
                    $data['code'] = "";
                    $this->api_model->update_api(
                        $data
                    );
                    echo json_encode(array(
                        "error" => 101,
                        "msg" => "Cookie die"
                    ));
                    return;
                }
                if ($result->status == 102) {
                    echo json_encode(array(
                        "error" => 102,
                        "msg" => "Can not get code."
                    ));
                    return;
                }
            } else {
                try {
                    $code = $result->code;
                    if (isset($code)) {
                        $data['code'] = $code;
                        $data['status'] = 0;
                        if ($this->api_model->update_api($data)) {
                            echo json_encode(array(
                                "code" => $code
                            ));
                            return;
                        } else {
                            echo json_encode(array(
                                "error" => 103,
                                "msg" => "Error identified random error"
                            ));
                            return;
                        }
                    }
                } catch (Exception $ex) {
                    echo json_encode(array(
                        "error" => 103,
                        "msg" => "Error identified random error"
                    ));
                    return;
                }
            }
        } else {
            print_r(json_encode($this->form_validation->error_array()));
        }
    }
}
