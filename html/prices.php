<?php
    $base = $_SERVER['DOCUMENT_ROOT'];
    require_once $base . '/core/init.php';

    if (Input::get('ResortName'))
    {
        $resort = Input::get('ResortName');
        $db = DB::getInstance();
        $query = $db->query("SELECT * FROM StayPricing JOIN Resort ON StayPricing.ResortName = Resort.ResortName WHERE Resort.ResortName = ? ORDER BY StayPricing.DATE DESC LIMIT 20", array($resort));
        $stayPricing = $query->results();

        $query = $db->query("SELECT * FROM Flight JOIN Resort ON Flight.ResortName = Resort.ResortName WHERE Resort.ResortName = ? ORDER BY Flight.DATE DESC LIMIT 20", array($resort));
        $flightPricing = $query->results();

        $renderStayPricingHeader = !$query->count() ? '' : '
            <tr>
                <th scope="col">Date</th>
                <th scope="col">Stay price</th>
                <th scope="col">Lift ticket price</th>
            </tr>';

        $renderStayPricingBody = '';
        foreach ($stayPricing as $stayPrice)
        {
            $renderStayPricingBody .= '<tr>
                <td>' . $stayPrice->Date . '</td>
                <td>' . $stayPrice->StayPrice . '</td>
                <td>' . $stayPrice->LiftTicketPrice . '</td>
            </tr>';
        }

        $renderFlightPricingHeader .= '
                    <tr>
                        <th scope="col">Date</th>
                        <th scope="col">Price</th>
                        <th scope="col">Airline</th>
                    </tr>';

        $renderFlightPricingBody = '';
        foreach ($flightPricing as $flightPrice)
        {
            $renderFlightPricingBody .= '<tr>
                <td>' . $flightPrice->Date . '</td>
                <td>' . $flightPrice->Price . '</td>
                <td>' . $flightPrice->Airline . '</td>
            </tr>';
        }
    }
    else
    {
        Redirect::to(502);
    }
?>

<html>
<?php
    PageHeader::render('VacaFun Ski Planning');
?>


<body>
  <!--Navigation Bar-->
  <?php NavBar::render(); ?>

<div class="container main-content">
    <h1 class="content-title">Prices for <?php echo $resort ?></h1>
    <div class="container row">
        <div>
            <h2>Stay pricing</h2>
            <table class="table">
                <thead>
                    <?php echo $renderStayPricingHeader ?>
                </thead>
                <tbody>
                    <?php echo $renderStayPricingBody ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="container row">
        <h2>Flight pricing</h2>
        <table class="table">
            <thead>
                <?php echo $renderFlightPricingHeader ?>
            </thead>
            <tbody>
                <?php echo $renderFlightPricingBody ?>
            </tbody>
        </table>
    </div>
</div>
</body>

</html>