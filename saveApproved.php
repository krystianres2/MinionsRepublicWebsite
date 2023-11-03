<!-- nie działa -->
<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $jsonList = $_POST["listOfApprovedUsers"];
    $myList = json_decode($jsonList);
    foreach ($myList as $item) {
        echo "Received item: " . $item . "<br>";
    }
}else{
    echo "Not a POST request";
}
?>