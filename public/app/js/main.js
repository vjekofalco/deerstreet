angular.module("app", ["chart.js"])
.config(function($httpProvider) {

  $httpProvider.interceptors.push(function(){

    return { 

      response: function(req) {

        console.log("HTTP_Response fetch!" + req);
        return req;

      }

    }

  })

})
.service('getData', function ($http){

  var api = config.apiURL;

  this.serverData = function(endpoint){

    return $http.get(api + endpoint)
     .success(function (data, status, headers, config) {

              console.log(data);

          })
          .error(function (data, status, headers, config) {

              console.error("ERROR during data fetching: " + headers);

          });

  }

})
.controller("dataPreparation", ['$scope', 'getData', '$filter', function ($scope, getData, $filter){

  /*$scope.requestedFile = function(file){

  getData.serverData(file)
  .success(function(data){*/

        var data = window.data;

  /* ----------- DATA FOR DISPLAY ----------------- */  

    var months = ["January",  "February",  "March",  "April",  "May",  "June",  "July",  "August",  "September",  "October",  "November", "December"];

    var excelMonths = [];

    angular.forEach(data.calendar, function(value, key){

      excelMonths[excelMonths.length] = key;

    });

    console.log(excelMonths);

    $scope.searchString = '';

    $scope.client = data.ContractData.Client[0];
    $scope.rPerson = data.ContractData.Responsible_person[0];
    $scope.PO = data.ContractData.PO[0];
    $scope.hrsThisMonth = 0;
    $scope.projects = data.projects;
    $scope.contact = data.ContractData.Deerstreet_Contact[0];

    $scope.date = new Date();

    $scope.currentMonthNr = $filter('date')(new Date(), 'M');

    //$scope.monthNr = $filter('date')(new Date(), 'M');
    $scope.dayNr = $filter('date')(new Date(), 'd')
    $scope.yearNr = $filter('date')(new Date(), 'yyyy')

    //$scope.monthNr = $scope.monthNr - 1;
    var lastmonth = "";
    var lastmonthNr = "";
    
    angular.forEach(data.calendar, function(value, key){

      lastmonth = key;

    })

    $scope.monthNr = months.indexOf(lastmonth);
    var month = months[$scope.monthNr];

    console.log(lastmonth);

    $scope.month = month;


  /* ----------- END DATA FOR DISPLAY ----------------- */  



  /*
  |------------------------------------------------------
  | *****************  MAIN FUNCTION  ***************** |
  |------------------------------------------------------
  | 
  | Function is used for passing and displaying data for
  | the current and for the wanted month.
  |
  */


  /* ---------- Line chart $scope --------- */

  $scope.changeMonth = function(month){

    $scope.lineLabels = data.calendar[month].Days_in_month; // Putting Dates for the current month

    $scope.lineSeries = [];     
    $scope.lineData = [];

    angular.forEach(data.calendar[month].roles, function(value, key){

      $scope.lineSeries[$scope.lineSeries.length] = key; // Using keys as "Series values
     
      $scope.lineData[$scope.lineData.length] = data.calendar[month].roles[key].Log; 

    });

    /* --------- USED HOURS THIS MONTH donut chart $scope --------- */

    $scope.hrsThisMonth = 0;
    $scope.monthLabels = [];
    $scope.monthData = [];

    $scope.monthOptions={ 

      responsive: false,
      maintainAspectRatio: false

    }; 

    angular.forEach(data.calendar[month].roles, function(value, key){

      $scope.monthLabels[$scope.monthLabels.length] = key; // Using keys as "Series values
      $scope.monthData[$scope.monthData.length] = (data.calendar[month].roles[key].Used_HRS_This_Month).toFixed(2);

      $scope.hrsThisMonth = $scope.hrsThisMonth + data.calendar[month].roles[key].Used_HRS_This_Month;

    });

    $scope.monthLabels[$scope.monthLabels.length] = "Rest";
    
    $scope.totalMonth = 0;

    angular.forEach(data.ContractData.PDM, function(value, key){

      $scope.totalMonth = $scope.totalMonth + value;

    })

    $scope.monthData[$scope.monthData.length] = ($scope.totalMonth - $scope.hrsThisMonth).toFixed(2);


    /* ------------ End of USED HOURS THIS MONTH donut chart $scope ------------- */


    /* ---------------- Calculating costs -------------------- */

    $scope.costs = {};
    $scope.totalCosts = 0;
    var hoursByRole = 0;

    angular.forEach(data.calendar[month].roles, function(value, key){

      angular.forEach(data.calendar[month].roles[key].Log, function(value2, key2){

        hoursByRole = hoursByRole + value2;

      })

      //$scope.costs[key] = data.calendar[month].roles[key].Hourly_Rate * hoursByRole ;
        $scope.costs[key] = data.ContractData.Hourly_rate[key] * hoursByRole ;

        hoursByRole = 0;

    });

     angular.forEach($scope.costs, function(value, key){

      $scope.totalCosts = $scope.totalCosts + value;

     });

    /* ------------ End of Calculating costs ----------------- */

      /* ------------ Calculating hours ----------------- */

      $scope.totalRoleHours = {};
      $scope.totalHours = 0;

      angular.forEach(data.calendar[month].roles, function(value, key){

          $scope.totalRoleHours[key] = data.calendar[month].roles[key].Used_HRS_This_Month;

      })

      angular.forEach($scope.totalRoleHours, function(value, key){

          $scope.totalHours = $scope.totalHours + value;

      })

      /* ------------ End of Calculating hours ----------------- */


    $scope.month = month;

    if(excelMonths.indexOf(months[$scope.monthNr + 1]) != -1){

        $scope.ifNextMonth = true;

      }
      else{

       $scope.ifNextMonth = false;

      }


    if(excelMonths.indexOf(months[$scope.monthNr - 1]) != -1){

        $scope.ifPrevMonth = true;

      }
      else{

        $scope.ifPrevMonth = false;

      }

  }

  /* ---------- End of Line chart $scope ---------- */

  /*
  |-------------------------------------------------------------
  | *****************  END OF MAIN FUNCTION  ***************** |
  |-------------------------------------------------------------
  */

    $scope.changeMonth(month); // Calling the main function after initialization.


  /* ------------ Calculating hours ----------------- */

    /*$scope.totalRoleHours = {};
    $scope.totalHours = 0;

    angular.forEach(data.calendar, function(value, key){

      angular.forEach(data.calendar[key].roles, function(value2, key2){

        if($scope.totalRoleHours[key2] == undefined){

          $scope.totalRoleHours[key2] = 0;

        }

        angular.forEach(data.calendar[key].roles[key2].Log, function(value3, key3){

          $scope.totalRoleHours[key2] = $scope.totalRoleHours[key2] + value3;

        })

      })

    })

    angular.forEach($scope.totalRoleHours, function(value, key){

      $scope.totalHours = $scope.totalHours + value;

    })*/

    /* ------------ End of Calculating hours ----------------- */



    /*---- STILL AVAILABLE OVERALL donut chart $scope ----*/
    
   $scope.overallTotal = 0

   angular.forEach(data.ContractData.PDO, function(value, key){

    $scope.overallTotal = $scope.overallTotal + value;

   })

  $scope.totHrs = 0 ;

   angular.forEach(data.calendar, function(value, key){

       angular.forEach(data.calendar[key].roles, function(value, key2){

           $scope.totHrs = $scope.totHrs + data.calendar[key].roles[key2].Used_HRS_This_Month;

       })

   })

   var restOverall = $scope.overallTotal - $scope.totHrs;

    $scope.overallLabels = ["Overall", "Rest"];
    $scope.overallData = [($scope.totHrs).toFixed(2), (restOverall).toFixed(2)];
    $scope.overallOptions={ 
    
      responsive: false,
      maintainAspectRatio: false
    
    };

    var totRHrs = {};

    angular.forEach(data.calendar, function(value, key){
        angular.forEach(data.calendar[key].roles, function(value, key2){

            if(totRHrs[key2] != undefined && totRHrs[key2] != '') {
                totRHrs[key2] = totRHrs[key2] + data.calendar[key].roles[key2].Used_HRS_This_Month;
            }
            else{
                totRHrs[key2] = data.calendar[key].roles[key2].Used_HRS_This_Month;
            }

        })
    })

     $scope.overallRoles = {};

     angular.forEach(totRHrs, function(value, key){

      if(data.ContractData.PDO[key] != undefined){

        $scope.overallRoles[key] = data.ContractData.PDO[key] - value;

      }
      else{

        $scope.overallRoles[key] = value * -1;

      }

     })


    /*---- END OF STILL AVAILABLE OVERALL donut chart $scope ----*/


    $scope.prevMonth = function(){

      $scope.monthNr --;
      var prevMonth = $scope.monthNr;
      prevMonth = months[prevMonth];
      $scope.changeMonth(prevMonth);

    }

    $scope.nextMonth = function(){

      $scope.monthNr ++;
      var nextMonth = $scope.monthNr;
      nextMonth = months[nextMonth];
      $scope.changeMonth(nextMonth);

    }

 /* })
  
  .error(function(data, status){

    console.log("Error occured  " + status);

  })

} */

//$scope.requestedFile("0"); // Calling main function on first load with argument "0". (Requesting random file on first load)

}]).filter('setDecimal', function ($filter) { //Custom filter for setting up the 2 decimal numbers.

    return function (input, places) {

        if (isNaN(input)) return input;

        var factor = "1" + Array(+(places > 0 && places + 1)).join("0");

        return Math.round(input * factor).toFixed(2) / factor;
    
    };

}).filter('search', function(){ // Custom filter for project live search functionalitie

    return function(arr, searchString){

      if(!searchString){

        return false;

      }

      var searchResult = {};
      searchString = searchString.toLowerCase();

      angular.forEach(arr, function(value, key){

        if(key.toLowerCase().indexOf(searchString) > -1){
          
          searchResult[key] = value;

        }

      });

      return searchResult;

    }

  });
