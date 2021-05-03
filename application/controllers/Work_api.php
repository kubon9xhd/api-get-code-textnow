<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Work_api extends CI_Controller{
    function index(){
        $this->load->view('api_view');
    }
    function action(){
        if($this->input->post('data_action')){
            $data_action = $this->input->post('data_action');
            if($data_action == "fetch_all"){
                $api_url = "http://localhost:8080/api-text-now/CodeIgniter/api";
                $client = curl_init($api_url);
                curl_setopt($client,CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($client);
                curl_close($client);
                $result = json_decode($response);
                // if want to build web html
            }
        }
    }
}
?>
