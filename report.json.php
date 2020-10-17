<?php

/*======================================================================
Copyright 2020, Riverside Rocks and the DUDB Authors

Licensed under the the Apache License v2.0 (the "License")

You may get a copy at
https://apache.org/licenses/LICENSE-2.0.txt

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
========================================================================*/

include "includes/apis.php";

/*

Requirments for making a request to the report API:

  - POST request
  - API Key (key)
  - User ID (id)
  - Details (details)
  - 

*/

if($_SERVER['REQUEST_METHOD'] !== "POST"){
    $mes = array(
        "message" => "Error, please use POST for this endpoint."
    );
    $send = json_encode($mes, true);
    die($send);
}

// The code from the report page, but its the backend only.

if(isset($_POST["id"])){
    if(! $_POST["key"]){
        die("Missing API Key");
    }
    /*======================
    |   Define Variables   |
    ======================*/
    $discord_id = $conn -> real_escape_string(xss($_POST["id"]));
    $post_key = $conn -> real_escape_string(xss($_POST["key"]));

    $sql = "SELECT * FROM keysa WHERE keya='${post_key}'";
    $result = $conn->query($sql);

    if($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
        $row["discord_id"] = $discord_reporter;
        break;
      }
    }

    if($discord_reporter = ""){
      $mes = array(
        "message" => "Error, invalid API key."
      );
      $send = json_encode($mes, true);
      die($send);
    }
    $discord_token = $_ENV['BOT_TOKEN'];

    $curl = curl_init();
    curl_setopt_array($curl, [
      CURLOPT_RETURNTRANSFER => 1,
      CURLOPT_URL => "https://discord.com/api/v8/users/${id}",
      CURLOPT_USERAGENT => 'Dangerous User DB'
    ]);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
      "Authorization: Bot ${discord_token}",
    ));
    $resp = curl_exec($curl);
    curl_close($curl);

    $api = json_decode($resp, true);

    $reporter_username = $conn -> real_escape_string(xss($api["username"]));
    if($reporter_username == ""){
      $mes = array(
        "message" => "Error, invalid user."
      );
      $send = json_encode($mes, true);
      die($send);
    }

    $cat = $conn -> real_escape_string(xss("api-report"));
    $epoch = time();
    $details = $conn -> real_escape_string(xss($_POST["details"]));

    // Note, we don't halt the request here if there are not details. Details are
    // not required for a report.
    
    $sql = "INSERT INTO reports (discord_id, reporter_discord_id, reporter_discord_username, cat, details, epoch) VALUES ('${discord_id}', '${discord_reporter}', '${reporter_username}', '${cat}', '${details}', '${epoch}')";
    $result = $conn->query($sql);

    $mes = array(
      "message" => "Success"
    );
    $send = json_encode($mes, true);
    die($send);
}

?>
