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

header("Content-type: application/json");

include "includes/apis.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$servername = $_ENV['MYSQL_SERVER'];
$username = $_ENV["MYSQL_USERNAME"];
$password = $_ENV["MYSQL_PASSWORD"];
$dbname = $_ENV["MYSQL_DATABASE"];

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM reports";
$result = $conn->query($sql);

$count = 0;

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
    $count++;
}
}

$final = array("reports" => $count);

echo json_encode($final, true);
