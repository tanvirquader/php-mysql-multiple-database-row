<?php

class Model {

	public static function test() {
        try {
            $data['role'] = 'administrator';
            $qry = "SELECT * FROM users WHERE role = :role";
            return DB::query($qry, $data);
        }
        catch(Exception $e) {
            echo $e->getMessage();
        }
	}


    public static function rechargeHistory($client_id) {
        try {
            $search=isset($_GET['search']) ? $_GET['search'] : '';
            $page=isset($_GET['page']) ? $_GET['page'] : '1';
            $limit=isset($_GET['limit']) ? $_GET['limit'] : '10';
            $per_page=isset($_GET['per_page']) ? $_GET['per_page'] : '10';
            $start = $per_page * ($page-1);

            $data = array('id_client'=> $client_id);

            $search_condition = '';
            if($search){
                $search_condition = " AND called_number like :search ";
                $data['search'] = '%'.$search.'%';
            }

            $qry = "SELECT * FROM payments WHERE id_client = :id_client $search_condition ORDER BY id DESC LIMIT $start,$limit ";
            $total = self::getTotal($qry,$data);
            $result = DB::query($qry, $data);
            $data = array('total'=>$total, 'per_page'=>$per_page,'data'=>$result );
            return json_encode($data);

        }
        catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public static function recharge($client_id, $amount) {
        try {
            $qry = "update clientsshared set account_state = account_state+$amount WHERE id_client = :id_client limit 1";
            DB::execute($qry, array('id_client' => $client_id));

            $qry = "INSERT INTO payments (id_client,client_type,money,data,type,description, by_whom, reseller_level, invoice_id)";
            $qry .= " VALUES (:id_client, :client_type, :money, :data, :type, :description, :by_whom, :reseller_level, :invoice_id)";
            $data = array(
                'id_client' => $client_id,
                'client_type' => 32,
                'money' => $amount,
                'data' => 'now()',
                'type' => '1',
                'description' => 'Payment Added By PayPal from MyDeshiShop',
                'by_whom' => '',
                'reseller_level' => '1',
                'invoice_id' => ''
            );
            return DB::execute($qry, $data);
            return 'Done';

        }
        catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public static function getTotal($query, $data) {
        try {
            $qry = substr($query, strpos($query, 'FROM') );
            $qry = 'Select count(*) as total '.$qry;
            //$qry = str_replace("*"," count(*) as total ",$query);
            $qry = substr($qry, 0, strpos($qry, 'ORDER'));
            $data =  DB::getRow($qry, $data);
            return $data['total'];
        }
        catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public static function clientId($pin) {
        try {
            $qry = "SELECT * FROM clientsshared WHERE login = :login";
            $client =  DB::getRow($qry, array('login' => $pin));
            if(!$client)  die('client not found');
            return $client['id_client'];
        }
        catch(Exception $e) {
            die('error');
        }
    }

    public static function user($pin) {
        try {
            $qry = "SELECT * FROM clientsshared WHERE login = :login";
            $client =  DB::getRow($qry, array('login' => $pin));
            return json_encode($client);
        }
        catch(Exception $e) {
            die('error');
        }
    }



    public static function checkAuth() {
        try {
            $user = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : '' ;
            $pass = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : '';
            if(!$user) die('error');
            if(!$pass) die('error');
            if($user != 'amarPhone' && $pass != 'amarChabi') die('error');
        }
        catch(Exception $e) {
            die('error');
        }
    }

    /*
     * Report Section
     */
    public static function talk_time_daily() {
        try {
            $search=isset($_GET['search']) ? $_GET['search'] : '';
            $page=isset($_GET['page']) ? $_GET['page'] : '1';
            $limit=isset($_GET['limit']) ? $_GET['limit'] : '10';
            $per_page=isset($_GET['per_page']) ? $_GET['per_page'] : '10';
            $start = $per_page * ($page-1);

            $form = isset($_GET['form']) ? $_GET['form'] : '';
            $to = isset($_GET['to']) ? $_GET['to'] : '';

            $qry = "SELECT
                      DATE_FORMAT(call_start, '%d %b %Y') AS cdr_date,
                      COUNT(*) AS total_call,
                      FORMAT(SUM(duration),0) AS total_duration,
                      FORMAT(SUM(cost), 2) AS total_coast
                    FROM
                      calls
                    WHERE call_start BETWEEN '".$form."' AND '".$to."'
                    GROUP BY DAY(call_start)
                    ORDER BY DAY(call_start) DESC  LIMIT $start,$limit";
            $data = DB::getResult($qry, null , $per_page);
            return json_encode($data);
        }
        catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public static function talk_time() {
        try {
            $page=isset($_GET['page']) ? $_GET['page'] : '1';
            $limit=isset($_GET['limit']) ? $_GET['limit'] : '20';
            $per_page=isset($_GET['per_page']) ? $_GET['per_page'] : '20';
            $start = $per_page * ($page-1);
            $form = isset($_GET['form']) ? $_GET['form'] : '';
            $to = isset($_GET['to']) ? $_GET['to'] : '';

            $qry = "SELECT * FROM
                      calls
                    WHERE call_start BETWEEN '".$form."' AND '".$to."'
                    ORDER BY DAY(call_start) DESC  LIMIT $start,$limit";
            $total = self::getTotal($qry,null);
            $result = DB::query($qry, null);
            $data = array('total'=>$total, 'per_page'=>$per_page,'data'=>$result );
            return json_encode($data);
        }
        catch(Exception $e) {
            echo $e->getMessage();
        }
    }



}