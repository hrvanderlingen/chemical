var chemicalApp = angular.module('chemicalApp', ['ngSanitize', 'ngRoute']);

chemicalApp.controller('TableDataCtrl', function ($scope, TableData) {

    $scope.elements = [];

    TableData.periodicData(function (input) {
        data = input.data;
        var rows = [];
        var previousPosition;

        for (row in data.table) {
            var i = 0;
            var elems = [];
            elementRow = data.table[row];
            console.log(row);

            for (element in elementRow.elements) {

                elementObj = elementRow.elements[element];

                if (elementObj.small === '57-71') {
                    elementObj.small = '*';
                    elementObj.className = 'elements dummy placeholder';
                }

                else if (elementObj.small === '89-103') {
                    elementObj.small = '**';
                    elementObj.className = 'elements dummy placeholder';
                }

                else if (elementObj.position !== i) {

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
            .when('/tree', {
                templateUrl: 'pages/tree.html'
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
        periodicData: function (successCallback) {
            $http.get('periodicTable.json').then(successCallback);
        }
    };
});

chemicalApp.factory('SynonymData', function ($http) {
    return {
        getSynonym: function (inchi, successCallback) {
            $http.get('http://cts.fiehnlab.ucdavis.edu/service/synonyms/' + inchi).then(successCallback);
        }
    };
});

chemicalApp.factory('TemperatureData', function ($http) {
    return {
        getTemperature: function (kelvin, successCallback) {
            $http.post('http://orinoco.vander-lingen.nl/chemistry/rest/kelvin/200', {kelvin: kelvin}).then(successCallback);
        }
    };
});

chemicalApp.factory('TemperatureDataByGet', function ($http) {
    return {
        getTemperature: function (kelvin, successCallback) {
            $http.get('http://orinoco.vander-lingen.nl/chemistry/rest/kelvin/' + kelvin).then(successCallback);
        }
    };
});

chemicalApp.controller('SynonymCtrl', function ($scope, SynonymData) {
    $scope.message = 'The synonyms for ';
    $scope.inchi = "LFQSCWFLJHTTHZ-UHFFFAOYSA-N";

    $scope.submit = function (form) {

        if (form.$invalid) {
            $scope.errorMessage = 'This is not a valid InChi key';
            return false;
        }

        inchi = form.inchi.$viewValue;

        SynonymData.getSynonym(inchi, function (data) {
            $scope.synonyms = data;

        });
    }
});

chemicalApp.controller('TemperatureCtrl', function ($scope, TemperatureData) {

    $scope.kelvin = "200";

    $scope.submit = function (form) {

        kelvin = form.kelvin.$viewValue;

        TemperatureData.getTemperature(kelvin, function (results) {
            $scope.data = results.data;

        });
    }
});


chemicalApp.controller('TreeCtrl', function ($scope, TreeData) {


    $scope.node = "stereochemistry";

    $scope.submit = function (form) {

        node = form.node.$viewValue;

        TreeData.getTree(node, function (results) {
            $scope.data = JSON.stringify(results.data, undefined, 2);
            $scope.error = 0;
        }, function (results) {
            $scope.data = results.data;
            $scope.error = 1;
        });
    }

});


chemicalApp.factory('TreeData', function ($http) {
    return {
        getTree: function (node, successCallback, errorCallback) {
            $http.post('/chemistry/tree', {node: node}).then(successCallback).catch(errorCallback);
        }
    };
});






    