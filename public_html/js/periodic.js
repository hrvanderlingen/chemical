var chemicalApp = angular.module('chemicalApp', ['schemaForm', 'ngSanitize', 'ngRoute', 'periodic.config']);


chemicalApp.controller('TableDataCtrl', function ($scope, TableData) {

    // populate the form with data
    //$scope.model = {"element" :"Ne"};

    $scope.elements = [];


    TableData.periodicData(function (data) {

        var rows = [];
        var previousPosition;

        for (row in data.table) {
            var i = 0;
            var elems = [];
            elementRow = data.table[row];

            for (element in elementRow.elements) {

                elementObj = elementRow.elements[element];

                if (elementObj.small === '57-71') {
                    elementObj.small = '*';
                    elementObj.className = 'elements dummy placeholder';
                } else if (elementObj.small === '89-103') {
                    elementObj.small = '**';
                    elementObj.className = 'elements dummy placeholder';
                } else if (elementObj.position !== i) {

                    var currentPosition = elementObj.position;

                    for (j = previousPosition; j < currentPosition - 1; j++) {
                        var dummy = {name: "dummy", order: previousPosition, className: "elements dummy"};
                        elems.push(dummy);
                    }
                    elementObj.className = 'elements';
                } else {
                    elementObj.className = 'elements';
                }
                elems.push(elementObj);
                i++;
                previousPosition = elementObj.position;
            }
            rows.push(elems);
        }


        $scope.elements = rows;
    });
});


/*
 * Source code for this directive: https://docs.angularjs.org/guide/compiler
 */

chemicalApp.directive('draggable', function ($document) {
    return function (scope, element, attr) {
        var startX = 0, startY = 0, x = 0, y = 0;

        element.on('mousedown', function (event) {

            event.preventDefault();
            startX = event.screenX - x;
            startY = event.screenY - y;
            $document.on('mousemove', mousemove);
            $document.on('mouseup', mouseup);
        });

        function mousemove(event) {
            y = event.screenY - startY;
            x = event.screenX - startX;
            element.css({
                top: y + 'px',
                left: x + 'px'
            });
        }

        function mouseup() {
            $document.off('mousemove', mousemove);
            $document.off('mouseup', mouseup);
        }
    };
});


chemicalApp.config(function ($routeProvider) {
    $routeProvider

            .when('/home', {
                templateUrl: 'pages/home.html',
                controller: 'HomeCtrl'
            })

            .when('/table', {
                templateUrl: 'pages/table.html',
                controller: 'TableDataCtrl'
            })

            .when('/synonym', {
                templateUrl: 'pages/synonym.html',
                controller: 'SynonymCtrl'
            })
            .when('/temperature', {
                templateUrl: 'pages/temperature.html',
                controller: 'TemperatureCtrl'
            })
            .otherwise({
                redirectTo: '/pages/home'
            });
});

chemicalApp.controller('HomeCtrl', function ($scope) {
    $scope.message = 'hi there';
});


chemicalApp.factory('TableData', function ($http) {
    return {
        periodicData: function (callback) {
            $http.get('periodicTable.json').success(callback);
        }
    };
});

chemicalApp.factory('SynonymData', function ($http) {
    return {
        getSynonym: function (inchi, callback) {
            $http.get('http://cts.fiehnlab.ucdavis.edu/service/synonyms/' + inchi).success(callback);
        }
    };
});

chemicalApp.factory('TemperatureData', function ($http) {
    return {
        getTemperature: function (kelvin, callback) {
            
            var req = {
                method: 'POST',
                url: 'http://orinoco.localhost/chemistry/rest/kelvin',
                headers: {'content-type': 'application/x-www-form-urlencoded'},
                data: 'kelvin=' + kelvin
            }
                        
            $http(req).success(callback);
        }
    };
});

chemicalApp.factory('TemperatureDataByGet', function ($http) {
    return {
        getTemperature: function (kelvin, callback) {
            $http.get('http://orinoco.vander-lingen.nl/chemistry/rest/kelvin/' + kelvin).success(callback);
        }
    };
});

chemicalApp.controller('SynonymCtrl', ['$scope', 'SynonymData', 'messages', function  ($scope, SynonymData, messages) {
    $scope.message = 'The synonyms for ';
    $scope.inchi = "LFQSCWFLJHTTHZ-UHFFFAOYSA-N";

$scope.sfOptions = { validationMessage: messages};
  
    $scope.schema = {
        type: "object",
        properties: {
            inchi: {
                type: "string",
                minLength: 10,

                maxLength: 50,

                title: "Inchi",
                description: "Inchi"},
        }
    };


    $scope.form = [

        "inchi",
        {
            type: "submit",
            title: "Go",

        }
    ];

    $scope.model = {inchi: "LFQSCWFLJHTTHZ-UHFFFAOYSA-N"};

    $scope.onSubmit = function (form) {
        $scope.$broadcast('schemaFormValidate');
        if (form.$valid) {
            inchi = $scope.model.inchi;

            SynonymData.getSynonym(inchi, function (data) {
                $scope.synonyms = data;

            });

        }
    }
}]);


chemicalApp.controller('TemperatureCtrl', function ($scope, TemperatureData) {

    $scope.schema = {
        type: "object",
        properties: {
            kelvin: {type: "integer", minLength: 1, maxLength: 5, title: "Kelvin", description: "Temperature in Kelvin"},
        }
    };

    $scope.form = [
        "kelvin",
        {
            type: "submit",
            title: "Go"
        }
    ];

    $scope.model = {kelvin: 900};

    $scope.onSubmit = function (form) {
        // First we broadcast an event so all fields validate themselves
        $scope.$broadcast('schemaFormValidate');

        // Then we check if the form is valid
        if (form.$valid) {

            kelvin = $scope.model.kelvin;

            TemperatureData.getTemperature(kelvin, function (results) {
                $scope.data = results.data;

            });
        }
    };



});






    