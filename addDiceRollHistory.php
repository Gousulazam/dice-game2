<?php
require_once "./config/dbConnection.php";
$database = new Database();
$conn = $database->connect();

function generateDiceNumber($min, $max)
{
    return rand($min, $max);
}

function fetchDiceRollHistoryPlayerWise($player){
    $database = new Database();
    $conn = $database->connect();
    $playerLastPostionquery = "SELECT * FROM dice_history WHERE player_no='$player' ORDER BY id ASC";
    $stmt = $conn->prepare($playerLastPostionquery);
    $stmt->execute();
    $playerPostionDetails = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $dice_history = array();
    $position_history = array();
    $coordinate_history = array();

    foreach ($playerPostionDetails as $key => $playerPostionDetail) {
        // print_r($playerPostionDetail);
        $dice_history[] = $playerPostionDetail['dice_roll'];
        $position_history[] = $playerPostionDetail['position'];
        $coordinate_history[] = $playerPostionDetail['cordinate'];
    }
    $positionData = array(
        "dice_history" =>$dice_history,
    "position_history" =>$position_history,
    "coordinate_history" =>$coordinate_history,
    );
    return $positionData;
}

$player = $_POST['player'];
$grid = $_POST['grid'];
$PLAYERS = 3;
$dice = generateDiceNumber(1, 6);
$playerLastPostionquery = "SELECT SUM(dice_roll) AS position FROM dice_history WHERE player_no='$player' AND dice_roll not like '%Invalid Entry%'";

$stmt = $conn->prepare($playerLastPostionquery);
$stmt->execute();

$playerPostionDetails = $stmt->fetch(PDO::FETCH_ASSOC);
$position = 0;
$cordinate = "";
$isWinner=false;
if (isset($playerPostionDetails['position'])) {
    $position+=$playerPostionDetails['position'];
    // echo "dice == $dice<br> before1 $position <br>";
    $lastPosition = $grid * $grid;

    if(($position+$dice) <= $lastPosition){
        $position+=$dice;
        // echo "before2 $position <br>";
        if(($position+$dice) == $lastPosition){
            $isWinner=true;
        }
        // die();
    }else{
        $dice2=$dice;
        $dice="Invalid Entry $dice2";
        $position=intval($position)-intval($dice);
    }

}
if($position == 0){
    $position = $dice;
}
    
    $rowCordinate = $grid-1;
    $maxPosition = $grid * $grid;

    for ($i = 1; $i <= $grid; $i++) {
        $columnCordinate = 0;
        for ($j = 1; $j <= $grid; $j++) {
            if($i%2 == 0){
                if($j==1){
                    $dicePosition=($maxPosition+1)-$grid;
                }else{
                    $dicePosition++;
                }
            }else{
                $dicePosition=$maxPosition;
            }
            if($position == $dicePosition){
                $position = $dicePosition;
             $cordinate = "($columnCordinate,$rowCordinate)";
            //  echo "$position == $maxPosition<br>
            //   $maxPosition<br>
            //  cordinate =$cordinate
            //  ";
            //  die();
            //  break;
            }
            $columnCordinate++;
            $maxPosition--;
        }
        $rowCordinate--;
    }
    // echo "$position == $maxPosition<br>
    //           $maxPosition<br>
    //          inswert before cordinate =$cordinate
    //          ";
    $insertQuery = "INSERT INTO `dice_history` (`player_no`, `dice_roll`, `position`, `cordinate`) VALUES 
                                                (:player_no, :dice_roll, :position, :cordinate);";

    $insertStmt = $conn->prepare($insertQuery);
    // Bind the Faculty value to the branch_name
    $insertStmt->bindParam(':player_no', $player);
    $insertStmt->bindParam(':dice_roll', $dice);
    $insertStmt->bindParam(':position', $position);
    $insertStmt->bindParam(':cordinate', $cordinate);
    $insertStmt->execute();
$responseTable = "";
$isWinner=false;

for ($i=1; $i <=$PLAYERS; $i++) {
    $fetchDiceRollHistoryPlayerWise = fetchDiceRollHistoryPlayerWise("player-$i");
    if(array_sum($fetchDiceRollHistoryPlayerWise['dice_history']) == ($grid*$grid)){
        $winner = "Winner";
        $isWinner=true;
    }else{
        $winner="";
    }
    $responseTable.="<tr>
                <td>Player $i <button ".($isWinner == true ?"disabled":"" )." class='btn btn-success roll-dice' value='player-$i'>Roll Dice</button></td>
                <td>".implode(",",$fetchDiceRollHistoryPlayerWise['dice_history'])."</td>
                <td>".implode(",",$fetchDiceRollHistoryPlayerWise['position_history'])."</td>
                <td>".implode(",",$fetchDiceRollHistoryPlayerWise['coordinate_history'])."</td>
                <td>$winner</td>
                </tr>";
}
$response = array("responseTable"=>$responseTable,'isWinner'=>$isWinner);
echo json_encode($response);