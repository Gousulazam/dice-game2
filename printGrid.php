<?php


$grid = $_POST['grid'];

$gridTable = '<table class="table table-bordered">';
$rowCordinate = $grid-1;
$maxPosition = $grid*$grid;

for ($i = 1; $i <= $grid; $i++) {
    $gridTable .= "<tr>";
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
        $gridTable .= "<td>
        $dicePosition<br>
        ($columnCordinate,$rowCordinate)
        </td>";
        $columnCordinate++;
        $maxPosition--;
    }
    $gridTable .= "</tr>";
    $rowCordinate--;
}

$gridTable .= '</table>
<h5>No of playes is 3</h5>
<button class="btn btn-primary rounded" id="start_game">Start Game </button>
';

echo $gridTable;
