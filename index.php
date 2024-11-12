<?php
require_once "./config/dbConnection.php";


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dice Game</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container card upload-card">
        <div class="card-body">
            <h5 class="card-title">Welcome to dice game of 3 player</h5>
            <form id="uploadForm" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="grid" class="form-label">Grid</label>
                    <input type="number" class="form-control" id="grid" name="grid" placeholder="Enter no of grid">
                </div>
                <button type="submit" id="submitBtn" class="btn btn-primary">Submit</button>
            </form>
            <div id="responseMessage" class="mt-3"></div>
            <div id="gameResultDiv" class="mt-3"></div>
            <br>
            <input type="hidden" name="dataTransferSts" id="dataTransferSts" value="0">
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#uploadForm').on('submit', function(e) {
                e.preventDefault();
                $('#responseMessage,#gameResultDiv').html('');
                $('#submitBtn').attr('disabled', true);
                $('#submitBtn').html(`Please Wait For Result<span class="btn-spinner" style="display: none;">Loading...</span>`);

                let grid = $('#grid').val();

                if (grid) {
                    $.ajax({
                        type: "POST",
                        url: "<?php echo 'printGrid.php'; ?>",
                        data: {
                            grid
                        },
                        success: function(data) {
                            $('#responseMessage').html(data);
                            $('#submitBtn').attr('disabled', false);
                            $('#submitBtn').html(`Submit`);

                            // $('#bet_type').val('');
                        }
                    });
                } else {
                    alert('Please Enter Grid Size');
                    $('#submitBtn').attr('disabled', false);
                    $('#submitBtn').html(`Submit`);

                }
            });

            $(document).on('click', '#start_game', function() {
                $.ajax({
                    type: "get",
                    url: "<?php echo "startNewGame.php" ?>",
                    data: {},
                    // dataType: "dataType",
                    success: function(data) {
                        const players = 3;

                        let gameTable = `<table class="table table-bordered">
                <tr>
                <th>Player</th>
                <th>Dice Roll History</th>
                <th>Position History</th>
                <th>Coordinate History</th>
                <th>Winner Status</th>
                </tr>
                <tbody id="gameTbody">
                `;
                        for (let index = 1; index <= players; index++) {
                            gameTable += `<tr>
                <td>Player ${index} <button class='btn btn-success roll-dice' value="player-${index}">Roll Dice</button></td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td></td>
                </tr>`;
                        }
                        gameTable += `</tbody></table>`;
                        $('#gameResultDiv').html(gameTable);
                    }
                });
            });

            $(document).on('click', '.roll-dice', function() {
                let player = $(this).val();
                let grid = $('#grid').val();
                $('#gameTbody').html(`<tr><td colspan="5"><center>Please Wait dice is rolling...!!!</center></td></tr>`);
                $.ajax({
                    type: "POST",
                    url: "<?php echo "addDiceRollHistory.php" ?>",
                    data: {
                        player,
                        grid
                    },
                    dataType: "json",
                    success: function(data) {
                        $('#gameTbody').html(data.responseTable);
                        $('.roll-dice').attr('disabled', data.isWinner);
                    }
                });

            });
        });
    </script>
</body>

</html>