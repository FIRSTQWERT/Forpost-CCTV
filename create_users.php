#!/usr/bin/php
<?php
if (!isset($argv[1]) || !isset($argv[2]) || !isset($argv[3]) || !isset($argv[4]) || !isset($argv[5])) die ('See usage');

$start_kv=$argv[1];
$end_kv=$argv[2];
$cctv_id=$argv[3];
$street=$argv[4];
$house=$argv[5];
/*
start_kv - начальный номер квартиры
end_kv - конечный номер квартиры в подъезде
cctv_id - id из форпоста, по нему ищется подъезд-дом-улица-город
street и house - переменные, которые фигурируют только в логине
*/
$admin_login="LOGIN";
$admin_password="PASSWORD";

$dbl=new mysqli('localhost','USER','PASSWORD','BD_NAME');

for ($i=$start_kv; $i<=$end_kv; $i++) {
        if (strlen($i)==1) $kv='000'.$i;
        if (strlen($i)==2) $kv='00'.$i;
        if (strlen($i)==3) $kv='0'.$i;
        $password=generate_password(6);
        $login=$street.$house.$kv;
        $sql="SELECT id FROM entrances WHERE cctv_id='$cctv_id'";
        $res=$dbl->query($sql);
        $data=$res->fetch_assoc();
        $entrance_id=$data['id'];
        print "Plate: $i, login: $login, Password: $password, CCTV ID: $cctv_id, Plate ID: $entrance_id\n";
        $sql="INSERT INTO accounts(`entrance_id`,`plate`,`login`,`password`,`active`,`is_changed`) VALUES ('$entrance_id','$i','$login','$password','1','0');";
        $res=$dbl->query($sql);
        print "$sql\n";



        $array = array(
                'AdminLogin' => $admin_login,
                'AdminPassword' => $admin_password,
                'AccountID' => $cctv_id,
                'Login' => $login,
                'Password' => $password,
                'IsReadOnly' => '1'
                );
        $ch = curl_init('https://ADDRESS_FORPOST_WEB/system-api/AddUser');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $array);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $html = curl_exec($ch);
        curl_close($ch);


}

function generate_password($number)
  {
    $arr = array('a','b','c','d','e','f',
                 'g','h','i','j','k','l',
                 'm','n','o','p','r','s',
                 't','u','v','x','y','z',
                 'A','B','C','D','E','F',
                 'G','H','I','J','K','L',
                 'M','N','O','P','R','S',
                 'T','U','V','X','Y','Z',
                 '1','2','3','4','5','6',
                 '7','8','9','0');
    // Генерируем пароль
    $pass = "";
    for($i = 0; $i < $number; $i++)
    {
      // Вычисляем случайный индекс массива
      $index = rand(0, count($arr) - 1);
      $pass .= $arr[$index];
    }
    return $pass;
  }


?>