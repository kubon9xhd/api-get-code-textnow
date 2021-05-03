<?php
error_reporting(0);
defined('BASEPATH') or exit('No direct script access allowed');
class TexNowAPI extends CI_Controller
{
    private const PASSWORD = "nguyenduchung113@@";
    function __construct()
    {
        parent::__construct();
        $this->load->model('api_model');
        $this->load->library('curlsetting');
        $this->load->library('form_validation');
    }
    function index()
    {
        echo 'hello bro';
    }
    function get_code()
    {
        $data['password'] = $this->input->post("password");
        $data['cookie'] = $this->input->post("cookie");
        if ($data['password'] == TexNowAPI::PASSWORD) {
            $username = ($this->getUsername($data['cookie'])) ? $this->getUsername($data['cookie']) : "";
            if (!empty($username)) {
                $code = ($this->getMessage($username, $data['cookie'])) ? $this->getMessage($username, $data['cookie']) : "";
                if (empty($code)) {
                    echo (json_encode(array(
                        "status" => 102,
                        "message" => "Can not get code"
                    )));
                    return;
                }
                echo json_encode(
                    array(
                        "status" => 200,
                        "code" => $code
                    )
                );
            } else {
                echo json_encode(array(
                    "status" => 101,
                    "message" => "Cookie die bro!!"
                ));
            }
        } else {
            echo json_encode(array(
                "status" => 100,
                "message" => "Access denied!!"
            ));
        }
    }
    private function getUsername($cookie)
    {
        // api get username
        $url = "https://www.textnow.com/api/sessions";
        try {
            $user_account = json_decode($this->curlsetting->curl_post($url, "GET", null, $cookie));
            if (empty($user_account->result->username) || !isset($user_account->result->username) || $user_account->result->username == null) {
                return false;
            }
            return $user_account->result->username;
        } catch (Exception $ex) {
            return false;
        }
    }
    private function getMessage($username, $cookie)
    {
        $url = "https://www.textnow.com/api/users/" . $username . "/messages?start_message_id=0&direction=future";
        $message = json_decode($this->curlsetting->curl_post($url, "GET", null, $cookie));
        try {
            if (empty($message->messages[0]->message) || !isset($message->messages[0]->message) || $message->messages[0]->message == null) {
                return false;
            }
            $str = explode(" ", $message->messages[0]->message);
            return $str[1];
        } catch (Exception $ex) {
            return false;
        }
    }
}
