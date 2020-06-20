

var app = angular.module('myApp', []);

app.controller('AppCtrl', ['$scope', '$http', '$timeout', function($scope, $http, $timeout) {
  
  // Load the data
  $http.get('http://www.corsproxy.com/loripsum.net/api/plaintext').then(function (res) {
		$scope.loremIpsum = res.data;
    $timeout(expand, 0);
  });
  
  $scope.autoExpand = function(e) {
        var element = typeof e === 'object' ? e.target : document.getElementById(e);
    		var scrollHeight = element.scrollHeight -60; // replace 60 by the sum of padding-top and padding-bottom
        element.style.height =  scrollHeight + "px";    
    };
  
  function expand() {
    $scope.autoExpand('TextArea');
  }
}]);