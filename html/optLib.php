<?php



function GetQuery($conn, $project, $table, $whereCond) {
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

function GetQueryNoWhere($conn, $project, $table) {
    // Form the SQL query
    $sql = "SELECT ". $project . " FROM " .  $table;
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
    $resortPriceProj = "ST.ResortName, StayPrice, LiftTicketPrice, Flight.Price";
    $resortPriceTable = "StayPricing AS ST, Flight AS FL";
    $resortPriceWhere = "ST.Date <= '" . $tripDate . "' AND DATE_ADD(ST.Date, INTERVAL 7 DAY) > '" . $tripDate . "'";
    $resortPriceWhere = $resortPriceWhere . " AND FL.Date <= '" . $tripDate . "' AND DATE_ADD(FL.Date, INTERVAL 7 DAY) > '" . $tripDate . "'";
    $resortPriceWhere = $resortPriceWhere . " AND ST.ResortName = FL.ResortName AND StartCity = '" . $userLocation . "'";
    $resortPriceRes = GetQuery($conn, $resortPriceProj, $resortPriceTable, $resortPriceWhere);
    //printf("<br>The number of results found is %d",$resortPriceRes->num_rows);
    $i = 0;
    //$row = $resortPriceRes->fetch_assoc();
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

// This routine 
function TotalUserVotes($username) {
        
    $totalUserVotes = @"
    SELECT
        COUNT(LikeByUser) AS LikeByUserCount
    FROM
        UserHistory
    WHERE
        UserName = ?";
    $results = DB::getInstance()->query($totalUserVotes, array(
        $username));
    $resultCount = $results->first()->LikeByUserCount;
    //printf("<br>The total votes = %d", $resultCount);
    return $resultCount;
}

function GetTotalNumResorts() {

    $totalNumResorts = @"
    SELECT
        COUNT(Resort.ResortName) AS ResortCount
    FROM
        Resort";
    $results = DB::getInstance()->query($totalNumResorts, array());
    $resultCount = $results->first()->ResortCount;
    //printf("<br>The total resorts = %d", $resultCount);
    return $resultCount;
}

function GetTotalVotes($username) {
    $totalNumVotes = @"
    SELECT
        COUNT(UserHistory.LikeByUser) AS LikeCount
    FROM
        UserHistory    
    WHERE
        username = ?";
    $results = DB::getInstance()->query($totalNumVotes, array($username));
    $resultCount = $results->first()->LikeCount;
    //printf("<br>The total votes = %d", $resultCount);
    return $resultCount;
}

function GetUserPreferences($username) {
    $userPreferences = @"
    SELECT
        level, budget
    FROM
        UserInfo
    WHERE
        username = ?";
    $results = DB::getInstance()->query($userPreferences, array($username));
    $output = array("level" => $results->first()->level, "budget" => $results->first()->budget);
    return $output;
}

function GetAvgRating() {
    
    $avgResortRate = @"
    SELECT
        AVG(rating) AS AvgRating
    FROM
        Resort";
    $results = DB::getInstance()->query($avgResortRate, array());
    $resultAvg = $results->first()->AvgRating;
    //printf("<br>The avg rating = %1.1f", $resultAvg);
    return $resultAvg;
}

function GetAvgResortValues($resortVals) {

    $sumPrice = 0.0;
    $sumRating = 0.0;
    $sumDifficulty = 0.0;
    $totalCount = $resortVals->count();
    //printf("<br> Here is the total count %d",$totalCount);
    $i = 0;
    $prices = $resortVals->getResults("TotalPrice");
    $ratings = $resortVals->getResults("RateValue");
    $difficulties = $resortVals->getResults("DifficultyValue");
    while($i < $totalCount) {
        $sumPrice = $sumPrice + $prices[$i];
        $sumRating = $sumRating + $ratings[$i];
        $sumDifficulty = $sumDifficulty + $difficulties[$i];
        $i = $i + 1;
    }
    $avgPrice = $sumPrice / $totalCount;
    $avgRating = $sumRating / $totalCount;
    $avgDifficulty = $sumDifficulty / $totalCount;
    //printf("<br> Here is the avg price %1.1f, rating %1.1f and difficulty %1.1f",$avgPrice,$avgRating,$avgDifficulty);
    return array("avgPrice" => $avgPrice, "avgRating" => $avgRating, "avgDifficulty" => $avgDifficulty);    
}



function GetAllResortValues($userLocation, $tripDate, $tripDuration) {
    
    $allResortValues = @"
    SELECT
        Resort.ResortName AS ResName,
        Flight.Price + SUM(StayPricing.StayPrice + StayPricing.LiftTicketPrice) AS TotalPrice,
        Resort.rating AS RateValue,
        Resort.difficulty AS DifficultyValue
    FROM
        Flight
    JOIN Resort ON Resort.ResortName = Flight.ResortName
    JOIN StayPricing ON StayPricing.ResortName = Resort.ResortName
    WHERE
        Flight.Date = ?
        AND Flight.StartCity = ?
        AND StayPricing.Date >= ?
        AND StayPricing.Date <= DATE_ADD(?, INTERVAL ? DAY)
    GROUP BY
        Resort.ResortName
    ORDER BY Resort.ResortName ASC";
    $results = DB::getInstance()->query($allResortValues, array($tripDate, 
        $userLocation, 
        $tripDate, 
        $tripDate, 
        $tripDuration));
    //printf("<br>The total count of resorts is %d",$results->count());
    return $results;
}

function GetLikeResortValues($userLocation, $tripDate, $tripDuration, $username) {
    
    $allResortValues = @"
    SELECT
        Resort.ResortName,
        Flight.Price + SUM(StayPricing.StayPrice + StayPricing.LiftTicketPrice) AS TotalPrice,
        Resort.rating AS RateValue,
        Resort.difficulty AS DifficultyValue
    FROM
        Flight
    JOIN Resort ON Resort.ResortName = Flight.ResortName
    JOIN StayPricing ON StayPricing.ResortName = Resort.ResortName
    JOIN UserHistory ON Resort.ResortName = UserHistory.ResortName
    WHERE
        Flight.Date = ?
        AND Flight.StartCity = ?
        AND StayPricing.Date >= ?
        AND StayPricing.Date <= DATE_ADD(?, INTERVAL ? DAY)
        AND UserHistory.username = ?
        AND UserHistory.LikeByUser = 1
    GROUP BY
        Resort.ResortName
    ORDER BY Resort.ResortName ASC";
    $results = DB::getInstance()->query($allResortValues, array($tripDate,
        $userLocation,
        $tripDate,
        $tripDate,
        $tripDuration,
        $username));
    //printf("<br>The total count of liked resorts is %d",$results->count());
    return $results;
}

function GetDislikeResortValues($userLocation, $tripDate, $tripDuration, $username) {
    
    $allResortValues = @"
    SELECT
        Resort.ResortName,
        Flight.Price + SUM(StayPricing.StayPrice + StayPricing.LiftTicketPrice) AS TotalPrice,
        Resort.rating AS RateValue,
        Resort.difficulty AS DifficultyValue
    FROM
        Flight
    JOIN Resort ON Resort.ResortName = Flight.ResortName
    JOIN StayPricing ON StayPricing.ResortName = Resort.ResortName
    JOIN UserHistory ON Resort.ResortName = UserHistory.ResortName
    WHERE
        Flight.Date = ?
        AND Flight.StartCity = ?
        AND StayPricing.Date >= ?
        AND StayPricing.Date <= DATE_ADD(?, INTERVAL ? DAY)
        AND UserHistory.username = ?
        AND UserHistory.LikeByUser = 0
    GROUP BY
        Resort.ResortName
    ORDER BY Resort.ResortName ASC";
    $results = DB::getInstance()->query($allResortValues, array($tripDate,
        $userLocation,
        $tripDate,
        $tripDate,
        $tripDuration,
        $username));
    //printf("<br>The total count of disliked resorts is %d",$results->count());
    return $results;
}

// This function computes the average value for each quantity (rating, price and difficulty and tries to
// estimate the average the user has preferred or not preferred
function GetComputedHistoryValue($resortAvgs, $likeValues, $disLikeValues,$avgString, $resultString) {
    
    $average = $resortAvgs[$avgString];
    // Sum over likes and take difference of average
    $i = 0;
    $likeRatings = $likeValues->getResults($resultString);
    $sumLikeRatings = 0.0;
    $totalLikes = $likeValues->count();
    //printf("<br> like count = %d",$totalLikes);
    while($i < $totalLikes) {
        $sumLikeRatings = $sumLikeRatings + $likeRatings[$i] - $average;
        $i = $i + 1;
    }
    //printf("<br> Sum like ratings = %1f",$sumLikeRatings);
    $i = 0;
    $disLikeRatings = $disLikeValues->getResults($resultString);
    //printf("<br> In computed history");
    $sumdisLikeRatings = 0.0;
    $totalDisLikes = $disLikeValues->count();
    //printf("<br> dislike count = %d",$totalDisLikes);
    while($i < $totalDisLikes) {
        $sumdisLikeRatings = $sumdisLikeRatings - $disLikeRatings[$i] + $average;
        $i = $i + 1;
    }
    //printf("<br> Sum dislike ratings = %1f",$sumdisLikeRatings);
    $resultPref = ($sumLikeRatings - $sumdisLikeRatings)/ ($totalLikes + $totalDisLikes);
    //printf("<br> Here is the result pref %1f", $resultPref);
    return $average - $resultPref;
}

function GetScore($values,$numVals,$historyAvg,$userPref,$voteWeight,$prefWeight) {
    // Compute a list of scores over the inputted variable keeping the resort list in order
    $i = 0;
    $output = array();
    while($i < $numVals) {
        $result = $prefWeight * abs($userPref - $values[$i]) + $voteWeight * abs($historyAvg - $values[$i]);
        array_push($output, $result);
        $i = $i + 1;
    }
    return $output;
}

function ScoreToRank($score,$count) {
    
    $i = 0;
    $j = 0;
    $rank = 1;
    $output = array();
    while($i < $count) {
        while($j < count) {
            if ($i != $j) {
                if($score[$i] > $score[$j])
                    $rank = $rank + 1;
            }
            $j = $j + 1;
        }
        $j = 0;        
        array_push($output,$rank);
        $rank = 1;
        $i = $i + 1;
    }
    return $output;
}

function SumScores($rankRating, $rankPrice, $rankDifficulty, $count) {
    
    $i = 0;
    $output = array();
    while($i < $count) {
        $sumOut = $rankRating[$i] + $rankPrice[$i] + $rankDifficulty[$i];
        array_push($output,$sumOut);
        $i = $i + 1;
    }
    return $output;
}

function FindIndex($listValues, $value, $size) {
    
    $i = 0;
    while($i < $size) {
        if($listValues[$i] == $value)
            break;
        $i = $i + 1;
    }
    return $i;
}

// This is one of the optimization functions
function GetVotingResorts($username,$userLocation, $tripDate, $tripDuration) {
    
    // Compute weights for computing score
    $totalNumResorts = GetTotalNumResorts();
    $totalNumVotedResorts = GetTotalVotes($username);
    $voteWeight = $totalNumVotedResorts/$totalNumResorts;
    $prefWeight = 1.0 - $voteWeight;
    //printf("<br> The vote weight = %1.1f and the pref weight = %1.1f",$voteWeight, $prefWeight);

    // Get user preferences
    $pref = GetUserPreferences($username);
    $levelPref = $pref["level"];
    $budgetPref = $pref["budget"];
    // Note:  Need to use average since we forgot to include this in the getStarted (Oooops)
    $ratingPref = GetAvgRating();

    // Get resort values list for all resorts
    $resortVals = GetAllResortValues($userLocation, $tripDate, $tripDuration);
    //printf("<br> The name = %s and price = %1.1f",$resortVals->first()->ResortName, $resortVals->first()->TotalPrice);
    $resortAvgs = GetAvgResortValues($resortVals); // Works
    //printf("<br> The avg price = %1.1f",$resortAvgs["avgPrice"]);
    $likeValues = GetLikeResortValues($userLocation, $tripDate, $tripDuration, $username); // Gives good count
    $dislikeValues = GetDislikeResortValues($userLocation, $tripDate, $tripDuration, $username); // Gives good count
    $computedHistoryRating = GetComputedHistoryValue($resortAvgs, $likeValues, $dislikeValues,"avgRating","RateValue");
    $computedHistoryPrice = GetComputedHistoryValue($resortAvgs, $likeValues, $dislikeValues,"avgPrice","TotalPrice");
    $computedHistoryDifficulty = GetComputedHistoryValue($resortAvgs, $likeValues, $dislikeValues,"avgDifficulty","DifficultyValue");
    //printf("<br> The computed history (rating) is %1f",$computedHistoryRating);
    //printf("<br> The computed history (price) is %1f",$computedHistoryPrice);
    //printf("<br> The computed history (difficulty) is %1f",$computedHistoryDifficulty);
    $scoreRating = GetScore($resortVals->getResults("RateValue"),$resortVals->count(),$computedHistoryRating,$ratingPref,
        $voteWeight,$prefWeight);
    $scorePrice = GetScore($resortVals->getResults("TotalPrice"),$resortVals->count(),$computedHistoryPrice,$budgetPref,
        $voteWeight,$prefWeight);
    $scoreDifficulty = GetScore($resortVals->getResults("DifficultyValue"),$resortVals->count(),$computedHistoryDifficulty,$levelPref,
        $voteWeight,$prefWeight);
    //printf("<br> The score of first element is %1.1f",$scoreRating[0]);
    $rankRating = ScoreToRank($scoreRating,$resortVals->count());
    $rankPrice = ScoreToRank($scorePrice,$resortVals->count());
    $rankDifficulty = ScoreToRank($scoreDifficulty,$resortVals->count());
    //printf("<br> The rank (rating) of first element is %d",$rankRating[1]);
    //printf("<br> The price (rating) of first element is %d",$rankPrice[1]);
    //printf("<br> The diff (rating) of first element is %d",$rankDifficulty[1]);
    $sumScores = SumScores($rankRating, $rankPrice, $rankDifficulty,$resortVals->count());
    $finalRank = ScoreToRank($sumScores,$resortVals->count());
    // Get index of first and second winning resorts and return their names
    $firstIndex = FindIndex($finalRank,1,$resortVals->count());
    $secondIndex = FindIndex($finalRank,2,$resortVals->count());
    //printf("<br>First index = %d and Second index = %d",$firstIndex,$secondIndex);
    // Get names of resorts
    $resortVals = GetAllResortValues($userLocation, $tripDate, $tripDuration);
    $names = $resortVals->getResults("ResName");
    $prices = $resortVals->getResults("TotalPrice");
    //printf("<br> First price = %1f", prices[0]);
    //printf("<br>First resort = %s and Second resort = %s",$names[0],$names[1]);
    $returnResort[0] = $names[$firstIndex];
    $returnResort[1] = $names[$secondIndex];
    //printf("<br>First resort = %s and Second resort = %s",$returnResort[0],$returnResort[1]);
    return $returnResort;

}

function GetCountOfResortsBelowBudget($conn, $userLocation, $tripDate, $tripDuration, $userBudget) {
    //
    // Makes the following assumptions to be accurate:
    // Flight price is calculated on the date of the trip as a round-trip cost.
    // Stay price and lift ticket pricing is available for each date within the trip dates.
    //
    $totalPriceQuery = @"
    SELECT
        Resort.ResortName,
        Flight.Price + SUM(StayPricing.StayPrice + StayPricing.LiftTicketPrice) AS TotalPrice
    FROM
        Flight
    JOIN Resort ON Resort.ResortName = Flight.ResortName
    JOIN StayPricing ON StayPricing.ResortName = Resort.ResortName
    WHERE
        Flight.Date = ?
        AND Flight.StartCity = ?
        AND StayPricing.Date >= ?
        AND StayPricing.Date <= DATE_ADD(?, INTERVAL ? DAY)
    GROUP BY
        Resort.ResortName,
        Flight.Price
    HAVING
        TotalPrice <= ?";
    
    $results = DB::getInstance()->query($totalPriceQuery, array(
        $tripDate,
        $userLocation,
        $tripDate,
        $tripDate,
        $tripDuration,
        $userBudget
    ));
    $total_results = $results->count();
    //printf("<br> The total results = %d", $total_results);
    
    return $total_results;
}

function GetNextHighestPrice($userLocation, $tripDate, $tripDuration, $userBudget) {
    //
    // Makes the following assumptions to be accurate:
    // Flight price is calculated on the date of the trip as a round-trip cost.
    // Stay price and lift ticket pricing is available for each date within the trip dates.
    //
    $totalPriceQuery = @"
    SELECT
        Resort.ResortName,
        Flight.Price + SUM(StayPricing.StayPrice + StayPricing.LiftTicketPrice) AS TotalPrice
    FROM
        Flight
    JOIN Resort ON Resort.ResortName = Flight.ResortName
    JOIN StayPricing ON StayPricing.ResortName = Resort.ResortName
    WHERE
        Flight.Date = ?
        AND Flight.StartCity = ?
        AND StayPricing.Date >= ?
        AND StayPricing.Date <= DATE_ADD(?, INTERVAL ? DAY)
    GROUP BY
        Resort.ResortName,
        Flight.Price
    HAVING
        TotalPrice > ?
    ORDER BY TotalPrice ASC";
    
    $results = DB::getInstance()->query($totalPriceQuery, array(
        $tripDate,
        $userLocation,
        $tripDate,
        $tripDate,
        $tripDuration,
        $userBudget
    ));

    if ($results->count())
    {
        return $results->first()->TotalPrice;
    }
    else
    {
        return -1;
    }
}

function GetValidResorts($conn, $n, $userLocation, $tripDate, $tripDuration, $userBudget, $budgetRange) {

    $resortPriceProj = "ST.ResortName, StayPrice, LiftTicketPrice, Price";
    $resortPriceTable = "StayPricing AS ST, Flight AS FL";
    $resortPriceWhere = "ST.Date <= '" . $tripDate . "' AND DATE_ADD(ST.Date, INTERVAL 7 DAY) > '" . $tripDate . "'";
    $resortPriceWhere = $resortPriceWhere . " AND FL.Date <= '" . $tripDate . "' AND DATE_ADD(FL.Date, INTERVAL 7 DAY) > '" . $tripDate . "'";
    $resortPriceWhere = $resortPriceWhere . " AND ST.ResortName = FL.ResortName AND StartCity = '" . $userLocation . "'";

    $resortPriceRes = GetQuery($conn, $resortPriceProj, $resortPriceTable, $resortPriceWhere);
    $i = 0;
    //$row = $resortPriceRes->fetch_assoc();
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

function GetRandomResorts($conn, $username) {
    
    //$resortProj = "Res.ResortName";
    //$resortTable = "Resort AS Res, UserHistory AS Hist";
    //$resortWhere = "Res.ResortName = Hist.ResortName AND LikedByUser <> 0 AND Hist.username = '" . $username . "'";
    $sql =  "SELECT ResortName FROM Resort WHERE ResortName NOT IN (SELECT ResortName FROM UserHistory WHERE LikeByUser = 0 AND username = '" . $username ."')";
    //printf("<br>The resort query = %s",$sql);
    $resortRes = $conn->query($sql);
    //$resortRes = GetQueryNoWhere($conn, $resortProj, $resortTable);
    //$resortRes = GetQuery($conn, $resortProj, $resortTable, $resortWhere);
    //printf("<br> The size of the query is %d",$resortRes->num_rows);
    $i = 0;
    //printf("<br> Made to resort query");
    while($row = $resortRes->fetch_assoc()) {
        $resort = $row["ResortName"];
        $resortList[$i] = $resort;
        $i = $i + 1;
    }
    //printf("<br> Outside loop");
    // Choose resort randomly excluding disliked resorts
    $int0 = rand(0, $i - 1);
    $returnResort[0] = $resortList[$int0];
    //printf("<br> Random int computed");
    $int1 = rand(0, $i - 1);
    $returnResort[1] = $resortList[$int1];
    //printf("<br> The random indexes are %d and %d from an array of size %d",$int0, $int1, count($resortList));
    //printf("<br> Selected %s and %s",$returnResort[0],$returnResort[1]);
    while(!strcmp($returnResort[0],$returnResort[1]))
        $returnResort[1] = $resortList[random_int(0, $i - 1)];
    return $returnResort;
}

function GetResortImage($conn,$resort) {
    
    $project = "ImageURL";
    $table = "Resort";
    $whereCond = "ResortName = '" . $resort . "'";
    $result = GetQuery($conn, $project, $table, $whereCond);
    $row = $result->fetch_assoc();
    $image = $row["ImageURL"];
    //printf("<br> The return value is %s",$image);
    return $image;
}

function GetResortInfo($conn,$resort) {
    
    $project = "*";
    $table = "Resort";
    $whereCond = "ResortName = '" . $resort . "'";
    $result = GetQuery($conn, $project, $table, $whereCond);
    return $result->fetch_assoc();
}

function InsertHistoryRecord($conn, $UserName, $ResortName, $LikedByUser) {
    $sql = "INSERT INTO UserHistory (username, ResortName, LikeByUser) VALUES ('" . $UserName . "', '" . $ResortName . "', " . $LikedByUser . ")";
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
    $result = GetQuery($conn, "username", "UserHistory", $whereCond);
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
    $result = GetQuery($conn, $resortProj, $resortTable, $resortWhere);
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
    
    // Adding various adjectives
    $adjects = array(0 => "beautiful", 1 => "mysterious", 2 => "exuberant", 3 => "wonderful", 4 => "massive", 5 => "exclusive",
        6 => "incomparable", 7 => "luxurious");
    $numEntry = count($adjects);
    $index = rand(0, $numEntry - 1);
    $adject1 = $adjects[$index];
    if($index == ($numEntry - 1))
        $index = 0;
    else
        $index =  $index + 1;
    $adject2 = $adjects[$index];
    
    
    // Place two images in table rows
    // TODO:  Get all the info on the screen
    printf("<td><img src='%s' alt='Left Resort' style=\"width:500px;height:375px;\"></td>",$leftResortImage);
    printf("<td><img src='%s' alt='Right Resort' style=\"width:500px;height:375px;\"></td></tr>",$rightResortImage);
    printf("<tr><td>The %s %s resort in %s, %s</td>",$adject1,$leftResortName,$leftResortCity,ucwords($leftResortState));
    printf("<td>The %s %s resort in %s, %s</td></tr>",$adject2,$rightResortName,$rightResortCity,ucwords($rightResortState));
}

function DisplayChosenImage($chosenResortInfo) {

    $leftResortImage = $chosenResortInfo["ImageURL"];
    
    // Get the resort name, cities and states
    $leftResortName = $chosenResortInfo["ResortName"];
    $leftResortCity = $chosenResortInfo["City"];
    $leftResortState = $chosenResortInfo["State"];
    printf("<td><img src='%s' alt='Left Resort' style=\"width:500px;height:375px;\"></td>",$leftResortImage);
    printf("<tr><td>Excellent choice of %s resort in %s, %s</td>",$leftResortName,$leftResortCity,ucwords($leftResortState));
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

function AddUserInfo($conn,$username,$password,$tripDate,$tripDuration,$level,$location,$budget) {
    //printf("<br> Adding user info here");
    $sql = "SELECT * FROM UserInfo WHERE username =\"" .  $username . "\"";
    //printf("<br> The userinfo sql command is %s",$sql);
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        //printf("<br> Found user info");
        return "Found user info";
    }
    else {
        //printf("<br> Adding user info");
        $sql = "INSERT INTO UserInfo (username, password, tripDate, tripDuration, level, location, budget) VALUES
         ('" . $username . "', '" . $password . "', '" . $tripDate . "', " . $tripDuration . ", " . $level .
         ", '" . $location . "', " . $budget . ")";
        //printf("<br> The add userinfo sql command is %s",$sql);
        //printf("<br>Here is the insertion string %s", $sql);
        if ($conn->query($sql) === TRUE) {
            $returnVal = "New record created successfully.";
        } else {
            $returnVal = "Error: " . $sql . "<br>" . $conn->error;
        }
        return $returnVal;
    }    
}

function UpdateUserInfo($conn,$username,$tripDate,$tripDuration,$level,$location,$budget) {
    $sql = "UPDATE UserInfo SET tripDate = \"" . $tripDate . "\", tripDuration = " . $tripDuration . ", level = " . 
           $level . ", location = \"" . $location . "\", budget = " . $budget . 
           " WHERE username =\"" .  $username . "\"";
    //printf("<br> The userinfo sql command is %s",$sql);
    return $conn->query($sql);

}

function DefaultTableResponse($leftResort, $rightResort) {
/*  </tr>
    <tr>
    <form action="/optimize.php">
    <td>
    <input type="radio" name="leftfeedback" value="like" checked> Like<br>
    <input type="radio" name="leftfeedback" value="dislike"> Dislike<br>
    <input type="radio" name="leftfeedback" value="want"> I want this!!<br><br>
    <input name="_leftresort" type="hidden" value="<?php echo $twoResorts[0]?>">
    </td>
    <td>
    <input type="radio" name="rightfeedback" value="like" checked> Like<br>
    <input type="radio" name="rightfeedback" value="dislike"> Dislike<br>
    <input type="radio" name="rightfeedback" value="want"> I want this!!<br><br>
    <input name="_rightresort" type="hidden" value="<?php echo $twoResorts[1]?>">
    <input type="submit">
    </td>
    </form>
    </tr>
    </table>"*/
    //printf("<br>The username = %s",$username);
    //printf("<br>The left resort = %s and the right resort = %s",$leftResort,$rightResort);
    $str = "</tr><tr><form action=\"/optimize.php\"><td> <input type=\"radio\" name=\"leftfeedback\" value=\"like\" 
           checked> Like<br><input type=\"radio\" name=\"leftfeedback\" value=\"dislike\"> Dislike<br>
           <input type=\"radio\" name=\"leftfeedback\" value=\"want\"> I want this!!<br><br>
           <input name=\"_leftresort\" type=\"hidden\" value=\"" . $leftResort . "\">
           </td><td><input type=\"radio\" name=\"rightfeedback\" value=\"like\" checked> Like<br>
           <input type=\"radio\" name=\"rightfeedback\" value=\"dislike\"> Dislike<br>
           <input type=\"radio\" name=\"rightfeedback\" value=\"want\"> I want this!!<br><br>
           <input name=\"_rightresort\" type=\"hidden\" value=\"" . $rightResort . "\">
    	   <input type=\"submit\"></td></form></tr></table>";
    return $str;
}

?>