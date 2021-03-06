<!doctype html>
<html ng-app="app">
	<head>

        <meta charset="utf-8">
		<title>VANS X DEERSTREET</title>
        <link href='http://fonts.googleapis.com/css?family=Lato:100,400,700,900' rel='stylesheet' type='text/css'>

        <?php
            echo HTML::style('app/css/layout.css');
            echo HTML::style('bower_components/bootstrap/dist/css/bootstrap.css');
            echo HTML::style('bower_components/angular-chart.js/dist/angular-chart.css');
        ?>

        <script>
            window.data = <?php echo $data ?> ;
            console.log(window.data);
        </script>

	</head>
    <body ng-controller="dataPreparation">

    <div class="container-fluid">

        <!-- ****************** HEADER ****************** -->

        <div class="row">
            <div class="col-lg-12 col-md-12" id="header">
                <h1 class="pull-left"> DEERSTREET </h1>

                    <span class="pull-right" id="header-left-holder">
                        <p class="inline-element"> {{client}} </p> &nbsp; &nbsp; &nbsp;
                        <h3 class="inline-element"> {{dayNr}} | {{currentMonthNr}} | {{yearNr}} </h3>
                    </span>
            </div>
        </div>

        <!-- ***************** LINE CHART ******************** -->

        <div class="row chart-holder" id="lineChartHolder">
            <div class="col-lg-12 col-md-12">
                <span ng-click="prevMonth()" ng-show="ifPrevMonth" class="glyphicon glyphicon-menu-left arrow"></span>&nbsp; &nbsp;
                <h2 class="inline-element" id="lineChartHeader"> {{month}} {{yearNr}} </h2> <p class="inline-element" id="lineChartHeader"> |&nbsp; &nbsp;{{PO}} &nbsp; &nbsp;|&nbsp; &nbsp; {{rPerson | uppercase}} </p>&nbsp; &nbsp;
                <span ng-click="nextMonth()" ng-show="ifNextMonth" class="glyphicon glyphicon-menu-right arrow"></span>
                    <span class="pull-right" id="searchBox">
                        <input type="text" id="search" ng-model="searchString" placeholder="Project name:" />
                        <ul class="list-group" id="projectList">
                            <a href="/{{key}}" class="list-group-item" ng-repeat="(key, value) in projects | search:searchString"> {{key}} </a>
                        </ul>
                    </span>
            </div>
            <dr/>
            <div class="col-lg-12 col-md-12" id="lineChart">
                <canvas id="canvas" class="chart chart-line" chart-data="lineData" chart-labels="lineLabels"  chart-legend="true" chart-series="lineSeries" chart-click="lineOnClick" height="1" width="8"></canvas>
            </div>
        </div>


        <!-- ***************** DONUT CHARTS ************************ -->

        <div class="row chart-holder" id="donutChartHolder">
            <div class="col-lg-3 col-md-6 donutCharts" id="totalHours">
                <h3> TOTAL HOURS </h3><br>

                    <span ng-repeat="(key, value) in totalRoleHours">
                        <p> <label>{{key}}:</label> {{value | setDecimal:2}} HRS </p>
                    </span>

                <h3> <label>TOTAL:</label> {{totalHours | setDecimal:2}} HRS </h3>
            </div>
            <div class="col-lg-3 col-md-6 donutCharts" id="usedHours">
                <h3> USED HOURS THIS MONTH </h3><br>

                <canvas id="pie" class="chart chart-doughnut" chart-data="monthData" chart-labels="monthLabels" chart-options="monthOptions"></canvas>

                    <span style="display:block; margin-left:180px; margin-top:-180px;">
                        <h1>{{ 100 * hrsThisMonth/totalMonth | setDecimal:0 }} %  </h1>
                        <p> {{ hrsThisMonth | setDecimal:2 }} HRS </p>
                    </span>



            </div>
            <div class="col-lg-3 col-md-6 donutCharts" id="availableOverall">
                <h3> STILL AVAILABLE OVERALL </h3><br>

                <canvas id="pie2" class="chart chart-doughnut" chart-data="overallData" chart-labels="overallLabels" chart-options="overallOptions" ></canvas>

                     <span style="display:block; margin-left:180px; margin-top:-180px;">
                        <h1>{{ 100 * (overallTotal - totHrs) / overallTotal | setDecimal:0 }} %  </h1>

                        <span ng-repeat="(key, value) in overallRoles">
                            <p> <label>{{key}}:</label> {{value | setDecimal:2}} HRS </p>
                        </span>

                    </span>

            </div>
            <dr/>
            <div class="col-lg-3 col-md-6 donutCharts" id="costThisMonth">

                <h3> COST THIS MONTH </h3><br>

                    <span ng-repeat="(key, value) in costs">
                        <p> <label>{{key}}:</label> {{value | setDecimal:2}} &euro; </p>
                    </span>

                <h3> <label>TOTAL:</label> {{totalCosts | setDecimal:2}}  &euro; </h3>

            </div>

        </div>


        <!-- ***************** Footer ************************ -->

        <div class="col-lg-12 col-md-12 navbar-fixed-bottom footer">
            <span class="pull-left" id="footerItemLeft"><p> &copy; 2015 deerstreet-experience GmbH</p></span>
            <span class="pull-right" id="footerItemRight"><a href="mailto:{{contact}}"  class="inline-element">contact</a>&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp;<a href="http://deerstreet.de/" class="inline-element"> www.deerstreet.de <a> </span>
        </div>

    </div>

    <?php

        echo HTML::script('bower_components/Chart.js/Chart.min.js');
        echo HTML::script('bower_components/angular/angular.min.js');
        echo HTML::script('bower_components/angular-chart.js/dist/angular-chart.js');
        echo HTML::script('bower_components/jquery/dist/jquery.min.js');
        echo HTML::script('bower_components/bootstrap/dist/js/bootstrap.min.js');
        echo HTML::script('app/src/app.js');
        echo HTML::script('app/js/main.js');

    ?>

        <script>
        var ctx=document.getElementById("pie").getContext("3d");


        var ctx=document.getElementById("pie2").getContext("3d");


        </script>

	</body>
</html>
