<?php

function ConnectDatabase() {
    
    $servername = "localhost";
    $username = "********";
    $password = "********";
    // Create connection
    $conn = new mysqli($servername, $username, $password);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
//    echo "<br>Connected successfully";
    return $conn;
}

function ChooseDatabase($conn) {
    
    $sql = "USE **********";
    if ($conn->query($sql) === TRUE) {
 //       echo "<br>Database selected";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    return $conn;
}

function PerformQuery($conn, $project, $table, $whereCond) {
    // Form the SQL query
    $sql = "SELECT ". $project . " FROM " .  $table . " WHERE " . $whereCond;
    //echo "<br>";
    //echo $sql;
    // Perform the query
    $result = $conn->query($sql);
    //printf("<br> Made it here with result %s",$result);
    // Extract the results
    //$row = $result->fetch_assoc();
    //echo "<br> and made it here";
    //echo $row;
    return $result;
}

function CheckForNValidResorts($conn, $n, $userLocation, $tripDate, $tripDuration, $userBudget, $budgetRange) {
    // Form where condition search for cases that meet this
    // REQUIRED:  Need Resort cost, travel cost and lift ticket cost
    
    // Resort Cost Query -- Get list of resorts, lift ticket costs and stay costs
    // Form query => SELECT ResortName, StayPrice, LiftTicketPrice FROM StayPricing WHERE Date <= tripDate
    // AND DATE_ADD(Date, INTERVAL 7 DAY) > tripDate);
    // This will return a list of 50 resortnames, their stay prices and their liftticketprices.
    $resortPriceProj = "ST.ResortName, StayPrice, LiftTicketPrice, Price";
    $resortPriceTable = "StayPricing AS ST, Flight AS FL";
    $resortPriceWhere = "ST.Date <= '" . $tripDate . "' AND DATE_ADD(ST.Date, INTERVAL 7 DAY) > '" . $tripDate . "'";
    $resortPriceWhere = $resortPriceWhere . " AND FL.Date <= '" . $tripDate . "' AND DATE_ADD(FL.Date, INTERVAL 7 DAY) > '" . $tripDate . "'";
    $resortPriceWhere = $resortPriceWhere . " AND ST.ResortName = FL.ResortName AND StartCity = '" . $userLocation . "'";
    $resortPriceRes = PerformQuery($conn, $resortPriceProj, $resortPriceTable, $resortPriceWhere);
    //printf("<br>The number of results found is %d",$resortPriceRes->num_rows);
    $i = 0;
    $row = $resortPriceRes->fetch_assoc();
    while($row = $resortPriceRes->fetch_assoc()) {        
        $resort = $row["ResortName"];
        $totalPrice = ($row["StayPrice"] + $row["LiftTicketPrice"]) * $tripDuration + $row["Price"];
        $totalBudget = $userBudget * (1.0 + $budgetRange);
        if ($totalPrice < $totalBudget) {
            $i = $i + 1;
        }
    }
    if($i >= $n)
        return 1;
    else
        return 0;
}

function GetValidResorts($conn, $n, $userLocation, $tripDate, $tripDuration, $userBudget, $budgetRange) {

    $resortPriceProj = "ST.ResortName, StayPrice, LiftTicketPrice, Price";
    $resortPriceTable = "StayPricing AS ST, Flight AS FL";
    $resortPriceWhere = "ST.Date <= '" . $tripDate . "' AND DATE_ADD(ST.Date, INTERVAL 7 DAY) > '" . $tripDate . "'";
    $resortPriceWhere = $resortPriceWhere . " AND FL.Date <= '" . $tripDate . "' AND DATE_ADD(FL.Date, INTERVAL 7 DAY) > '" . $tripDate . "'";
    $resortPriceWhere = $resortPriceWhere . " AND ST.ResortName = FL.ResortName AND StartCity = '" . $userLocation . "'";

    $resortPriceRes = PerformQuery($conn, $resortPriceProj, $resortPriceTable, $resortPriceWhere);
    $i = 0;
    $row = $resortPriceRes->fetch_assoc();
    while($row = $resortPriceRes->fetch_assoc()) {
        $resort = $row["ResortName"];
        $totalPrice = ($row["StayPrice"] + $row["LiftTicketPrice"]) * $tripDuration + $row["Price"];
        $totalBudget = $userBudget * (1.0 + $budgetRange);
        if ($totalPrice < $totalBudget) {
            $resortList[$i] = $resort;
            $i = $i + 1;
        }
    }
    $returnResort[0] = $resortList[0];
    $returnResort[1] = $resortList[$i -1];
    return $returnResort;
}

function GetResortImage($conn,$resort) {
    
    $project = "ImageURL";
    $table = "Resort";
    $whereCond = "ResortName = '" . $resort . "'";
    $result = PerformQuery($conn, $project, $table, $whereCond);
    $row = $result->fetch_assoc();
    $image = $row["ImageURL"];
    //printf("<br> The return value is %s",$image);
    return $image;
}

function GetResortInfo($conn,$resort) {
    
    $project = "*";
    $table = "Resort";
    $whereCond = "ResortName = '" . $resort . "'";
    $result = PerformQuery($conn, $project, $table, $whereCond);
    return $result->fetch_assoc();
}

function InsertHistoryRecord($conn, $UserName, $ResortName, $LikedByUser) {
    $sql = "INSERT INTO UserHistory (username, ResortName, LikedByUser) VALUES ('" . $UserName . "', '" . $ResortName . "', " . $LikedByUser . ")";
    //printf("<br>Here is the sql command for insert: %s",$sql);
    if ($conn->query($sql) === TRUE) {
        $returnVal = "New record created successfully.";
    } else {
        $returnVal = "Error: " . $sql . "<br>" . $conn->error;
    }
    return $returnVal;
}

function CheckHistoryRecord($conn, $UserName, $ResortName) {
    $whereCond = "username = '" . $UserName . "' AND ResortName = '" . $ResortName . "'";
    $result = PerformQuery($conn, "username", "UserHistory", $whereCond);
    $number_rows = $result->num_rows;
    //printf("<br>The number of rows found = %d",$number_rows);
    if($number_rows == 0)
        return 0;
    else
        return 1;    
}

function UpdateHistoryRecord($conn, $UserName, $ResortName, $LikedByUser) {
    $sql = "UPDATE UserHistory SET LikedByUser = " . $LikedByUser . " WHERE username = '" . $UserName . "' AND ResortName =  '" . $ResortName . "'";
    //printf("<br>Here is the sql command for insert: %s",$sql);
    if ($conn->query($sql) === TRUE) {
        $returnVal = "New record created successfully.";
    } else {
        $returnVal = "Error: " . $sql . "<br>" . $conn->error;
    }
    return $returnVal;
}

function HistoryManage($conn, $username, $feedback, $resort) {
    //printf("<br>The feedback value is %s",$feedback);
    //printf("<br>The resort was %s",$resort);
    // Perform an insert into the user history
    $likeVal = 0;
    if(!strcmp($feedback, 'like')) {
        $likeVal = 1;
    }
    else {
        $likeVal = 0;
    }
    // TODO:  Need to determine if an update or an insert is required
    if(!CheckHistoryRecord($conn, $username, $resort))
        InsertHistoryRecord($conn, $username, $resort, $likeVal);
    else
        UpdateHistoryRecord($conn, $username, $resort, $likeVal);
}

function GetAverageRating($conn) {
    $sql = "SELECT AVG(rating) FROM Resort";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $average = $row["AVG(rating)"];
    return $average;
}

function GetWeatherForecast($conn, $resort, $date) {
    $resortProj = "*";
    $resortTable = "WeatherForecast";
    $resortWhere = "Date <= '" . $date . "' AND DATE_ADD(Date, INTERVAL 7 DAY) > '" . $date . "'";
    $resortWhere = $resortWhere . " AND ResortName = '" . $resort . "'";    
    $result = PerformQuery($conn, $resortProj, $resortTable, $resortWhere);
    return $result->fetch_assoc();
}

function DisplayImageAndTitle($leftResortInfo,$rightResortInfo) {
    // Get the image URLs
    $leftResortImage = $leftResortInfo["ImageURL"];
    $rightResortImage = $rightResortInfo["ImageURL"];
    
    // Get the resort names, cities and states
    $leftResortName = $leftResortInfo["ResortName"];
    $rightResortName = $rightResortInfo["ResortName"];
    $leftResortCity = $leftResortInfo["City"];
    $rightResortCity = $rightResortInfo["City"];
    $leftResortState = $leftResortInfo["State"];
    $rightResortState = $rightResortInfo["State"];
    
    // Place two images in table rows
    // TODO:  Get all the info on the screen
    printf("<td><img src='%s' alt='Left Resort'></td>",$leftResortImage);
    printf("<td><img src='%s' alt='Right Resort'></td></tr>",$rightResortImage);
    printf("<tr><td>The beautiful %s resort in %s, %s</td>",$leftResortName,$leftResortCity,ucwords($leftResortState));
    printf("<td>The magestic %s resort in %s, %s</td></tr>",$rightResortName,$rightResortCity,ucwords($rightResortState));
}

function DisplayRatingandWeather($conn,$leftResortInfo,$rightResortInfo,$tripDate) {
    // Compute the average rating of the resort to compare with these resorts
    $leftResortName = $leftResortInfo["ResortName"];
    $rightResortName = $rightResortInfo["ResortName"];
    $average_rating = GetAverageRating($conn);
    $leftRating = $leftResortInfo["rating"];
    $rightRating = $rightResortInfo["rating"];
    printf("<tr><td>%s has a rating of %1.2f compared to an average of %1.2f</td>",$leftResortName,$leftRating,$average_rating);
    printf("<td>%s has a rating of %1.2f compared to an average of %1.2f</td></tr>",$rightResortName,$rightRating,$average_rating);
    
    // Find the forecast on the snow level
    $leftWeather = GetWeatherForecast($conn, $leftResortName, $tripDate);
    $rightWeather = GetWeatherForecast($conn, $rightResortName, $tripDate);
    printf("<tr><td>The status is expected to be %s with a snow depth of %1.2f cm</td>",$leftWeather["Status"],$leftWeather["SnowDepth"]);
    printf("<td>The status is expected to be %s with a snow depth of %1.2f cm</td></tr>",$rightWeather["Status"],$rightWeather["SnowDepth"]);
    
}

?>