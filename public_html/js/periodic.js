var periodicalApp = angular.module('periodicalApp', []);

periodicalApp.controller('TableDataCtrl', function ($scope, $http) {

$scope.elements= [];
$http.get('periodicTable.json').success(function(data) {
    var rows = [];
    var previousPosition;

    for(row in data.table){
        var i = 0;
         var elems = [];
        elementRow = data.table[row];

        for(element in elementRow.elements){

            elementObj = elementRow.elements[element];

            if(elementObj.small == '57-71'){
                elementObj.small = '*';
                elementObj.className = 'elements dummy placeholder';
            }

            else if(elementObj.small == '89-103'){
                elementObj.small = '**';
                elementObj.className = 'elements dummy placeholder';
            }

            else if(elementObj.position != i){

               var currentPosition = elementObj.position;

               for(j = previousPosition;j < currentPosition - 1;j++){
                   var dummy = {name:"dummy", order:previousPosition, className:"elements dummy"};
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

periodicalApp.directive('draggable', function($document) {
  return function(scope, element, attr) {
    var startX = 0, startY = 0, x = 0, y = 0;

    element.on('mousedown', function(event) {

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
        left:  x + 'px'
      });
    }

    function mouseup() {
      $document.off('mousemove', mousemove);
      $document.off('mouseup', mouseup);
    }
  };
});

