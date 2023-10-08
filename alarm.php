<?php
  function getData($method){
      $url = 'https://api.ukrainealarm.com/api/v3/';
      $access_token = '';     // API Key. Отримати на api.ukrainealarm.com
      $data = [];
      $ch = curl_init($url.$method);
      $http_headers = ['Content-Type:application/json','authorization:'.$access_token];
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_TIMEOUT, 10000);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $http_headers);
      $result = curl_exec($ch);
      $info = curl_getinfo($ch);
      if($info["http_code"] == 200){
        $data = json_decode($result, true);
      }
      return $data;
  }
  //ід регіона потім він не потрібен
  //$res = getData('regions');
  // var_dump($res);
 
  //можлиіо усі регіони вибрать чи указать сві ід
  $res = getData('alerts/22'); // 22 - це Харківська обл.
  //var_dump($res);
  


if (isset($res[0]['activeAlerts']) && empty($res[0]['activeAlerts'])) {
    $time = strtotime($res[0]['lastUpdate']);
    $newTime = date('H:i', $time + 10800); // +3 години т.к. на сервер інший час

    $text = "🟢 $newTime Відбій тривоги в Харків.обл.";

} elseif (isset($res[0]['activeAlerts']) && count($res[0]['activeAlerts']) > 0) {
    $time = strtotime($res[0]['lastUpdate']);
    $newTime = date('H:i', $time + 10800); // +3 години

    $text =  "🔴 $newTime Повітряна тривога в Харків.обл.!";
}

echo $text;

$mysqli = new mysqli("localhost", "root", "password", "database"); // Дані MySQL - адреса, логін, пароль, база
if ($mysqli->connect_error) {
    die("Error mysql: " . $mysqli->connect_error);
}
$mysqli->set_charset("utf8mb4"); // без цього не буде працювати зелений та червоний кружечок
$content = $mysqli->real_escape_string($text);

$sql = "UPDATE design SET content='$content' WHERE template=1"; // Приклад запису в MySQL

// Виводить повідомлення про помилку MySQL якщо вона є
//
// if ($mysqli->query($sql) === TRUE) {
//    echo "Mysql: Ok";
// } else {
//    echo "Mysql: " . $mysqli->error;
// }

$mysqli->close();
?>

