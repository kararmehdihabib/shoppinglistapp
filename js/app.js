var app = angular.module('Shoppinglist', ['ngRoute']);
//Routing config
app.config(['$routeProvider', function($routeProvider) {
 $routeProvider.
 when('/', {
  templateUrl: 'directives/lists.html',
  controller: 'ProductListCtrl'
 }).
 when('/NewProduct', {
  templateUrl: 'directives/new.html',
  controller: 'ProductAddCtrl'
 }).
 when('/UpdateProduct/:id', {
  templateUrl: 'directives/edit.html',
  controller: 'ProductEditCtrl'
 }).
 when('/NewShopList', {
  templateUrl: 'directives/shopListAdd.html',
  controller: 'ShopListAddCtrl'
 }).
 when('/Shoppinglist', {
  templateUrl: 'directives/shoplist.html',
  controller: 'ShoppingListCtrl'
 }).
 otherwise({
  redirectTo: '/'
 });
}]);

//Controller for listing products
app.controller('ProductListCtrl', [
  '$scope', '$http', '$location',
  function($scope, $http, $location) {
   $http.get('api/Products').success(function(data) {
    $scope.products = data;
   });

   //Retrieving shoppinglist names to view it in the dropdown
   $http.get('api/ShoppinglistsName').success(function(data) {
    var shoppinglistName = data;
    var shoppingListName = [];
    for (i = 0; i < data.length; i++) {
     shoppingListName.push(shoppinglistName[i].list_name);
    }
    $scope.shoppingListName = shoppingListName;
   });

   $scope.master = {};
   $scope.activePath = null;
   $scope.done = false;
   //function for adding product to a list
   $scope.AddToCart = function(selectedProduct, id) {
    if (selectedProduct === undefined || selectedProduct.selectedList === null) {
     alert("Please Select Shopping List");
    } else {

     selectedProduct.id = id;
     $http.post('api/addToList', selectedProduct).success(function() {
      $scope.done = true;
      $scope.activePath = $location.path('/');
     });
    }

   };
  }
 ]),

 //Controller for listing shopping lists
 app.controller('ShoppingListCtrl', [
  '$scope', '$http', '$location', '$route',
  function($scope, $http, $location, $route) {
   //Retrieving shopping list names
   $http.get('api/ShoppinglistsName').success(function(data) {
    var shoppinglistName = data;
    var shoppingListName = [];
    for (i = 0; i < data.length; i++) {
     shoppingListName.push(shoppinglistName[i].list_name);
    }
    $scope.shoppingListName = shoppingListName;
   });
   //Retrieving shopping lists with added product
   $http.get('api/Shoppinglists').success(function(data) {
    $scope.shoppinglists = data;
    $scope.listWithItem = _.groupBy($scope.shoppinglists, function(item) {
     return item["shoplist_name"]
    });
   });
   //function to delete a shopping list entirely
   $scope.Delete_List = function(name) {
    var deleteList = confirm('Are you absolutely sure you want to delete?');
    if (deleteList) {
     $http.delete('api/ShoppinglistsName/' + name);
     $route.reload();
    }
   };
   //function to delete a product from a shopping list
   $scope.Delete_Prod = function(pid, shoplist_name) {
    var deleteProd = confirm('Are you absolutely sure you want to delete?');
    if (deleteProd) {
     $http.delete('api/ShoppinglistsName/' + pid + '/' + shoplist_name);
     $route.reload();
    }
   };
  }
 ]),
 //Controller for adding product to products list
 app.controller('ProductAddCtrl', [
  '$scope', '$http', '$location',
  function($scope, $http, $location) {

   $scope.master = {};
   $scope.activePath = null;

   $scope.New_Product = function(product, AddNewForm) {

    $http.post('api/New_Product', product).success(function() {
     $scope.reset();
     $scope.activePath = $location.path('/');
    });

    $scope.reset = function() {
     $scope.product = angular.copy($scope.master);
    };

    $scope.reset();
   };

  }
 ]),
 //Controller for adding a new shopping list
 app.controller('ShopListAddCtrl', [
  '$scope', '$http', '$location',
  function($scope, $http, $location) {

   $scope.master = {};
   $scope.activePath = null;

   $scope.New_ShopList = function(shoplist, AddNewShopForm) {
    alert("New Shopping List Created");
    $scope.activePath = $location.path('/');
    $http.post('api/New_ShoppingList', shoplist).success(function() {

     $scope.reset();

    });

    $scope.reset = function() {
     $scope.shoplist = angular.copy($scope.master);
    };

    $scope.reset();
   };

  }
 ]),
 //Controller for editing a product's details
 app.controller('ProductEditCtrl', [

  '$scope', '$http', '$location', '$routeParams',
  function($scope, $http, $location, $routeParams) {

   var id = $routeParams.id;
   $scope.activePath = null;

   $http.get('api/Products/' + id).success(function(data) {
    $scope.ProductDetail = data;
   });
   //function for updating the product detaile to the database
   $scope.Update_Product = function(product) {
    $scope.activePath = $location.path('/');
    $http.put('api/Products/' + id, product).success(function(data) {
     $scope.ProductDetail = data;

    });

   };
   //function for deleting the product from the database
   $scope.Delete_Product = function(product) {
    var deleteProduct = confirm('Are you absolutely sure you want to delete?');
    if (deleteProduct) {
     $http.delete('api/Products/' + product.id);
     $scope.activePath = $location.path('/');
    }
   };

  }
 ]);