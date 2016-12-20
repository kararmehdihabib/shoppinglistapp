<?php
require 'vendor/autoload.php';
require 'Slim/Slim.php';

$app = new Slim();
$app->get('/Products', 'getProducts');
$app->get('/ShoppinglistsName', 'getShopListsName');
$app->get('/Shoppinglists', 'getShopLists');
$app->get('/Products/:id', 'getProduct');
$app->post('/New_Product', 'addProduct');
$app->post('/New_ShoppingList', 'addShopList');
$app->post('/addToList', 'addToList');
$app->put('/Products/:id', 'updateProduct');
$app->delete('/Products/:id', 'deleteProduct');
$app->delete('/ShoppinglistsName/:name', 'deleteList');
$app->delete('/ShoppinglistsName/:pid/:shoplist_name', 'deleteListProduct');
$app->run();

// Get Database Connection

function DB_Connection()
	{
	$dbhost = "localhost";
	$dbuser = "root";
	$dbpass = "";
	$dbname = "shopping";
	$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $dbh;
	}

// Get Product Details

function getProducts()
	{
	$sql = "select id, product_name,information FROM products";
	try
		{
		$db = DB_Connection();
		$stmt = $db->query($sql);
		$list = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo json_encode($list);
		}

	catch(PDOException $e)
		{
		echo '{"error":{"text":' . $e->getMessage() . '}}';
		}
	}

// Get shopping list names

function getShopListsName()
	{
	$sql = "select id,list_name FROM shoppinglistnames";
	try
		{
		$db = DB_Connection();
		$stmt = $db->query($sql);
		$list = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo json_encode($list);
		}

	catch(PDOException $e)
		{
		echo '{"error":{"text":' . $e->getMessage() . '}}';
		}
	}

// Get shopping lists

function getShopLists()
	{
	$sql = "SELECT shoplist_name ,productid, product_name, information FROM shoppinglists, products WHERE shoppinglists.productid = products.id";
	try
		{
		$db = DB_Connection();
		$stmt = $db->query($sql);
		$list = json_encode($stmt->fetchAll(PDO::FETCH_OBJ));
		$db = null;
		echo $list;
		}

	catch(PDOException $e)
		{
		echo '{"error":{"text":' . $e->getMessage() . '}}';
		}
	}

// Add products to list

function addToList()
	{
	$request = Slim::getInstance()->request();
	$cus = json_decode($request->getBody());
	$name = $cus->selectedList;
	$sql = "INSERT INTO shoppinglists (shoplist_name, productid) VALUES (:name, :productid)";
	try
		{
		$db = DB_Connection();
		$stmt = $db->prepare($sql);
		$stmt->bindParam("name", $cus->selectedList);
		$stmt->bindParam("productid", $cus->id);
		$stmt->execute();
		$db = null;
		echo json_encode($cus);
		}

	catch(PDOException $e)
		{
		echo '{"error":{"text":' . $e->getMessage() . '}}';
		}
	}

// Add new Product to the Database

function addProduct()
	{
	$request = Slim::getInstance()->request();
	$cus = json_decode($request->getBody());
	$sql = "INSERT INTO products (product_name, information) VALUES (:p_name, :info)";
	try
		{
		$db = DB_Connection();
		$stmt = $db->prepare($sql);
		$stmt->bindParam("p_name", $cus->product_name);
		$stmt->bindParam("info", $cus->information);
		$stmt->execute();
		$cus->id = $db->lastInsertId();
		$db = null;
		echo json_encode($cus);
		}

	catch(PDOException $e)
		{
		echo '{"error":{"text":' . $e->getMessage() . '}}';
		}
	}

// Add new shopping list to the Database

function addShopList()
	{
	$request = Slim::getInstance()->request();
	$cus = json_decode($request->getBody());
	$table = $cus->Name;
	$sql = "INSERT INTO shoppinglistnames (list_name) VALUES (:name)";
	try
		{
		$db = DB_Connection();
		$stmt = $db->prepare($sql);
		$stmt->bindParam("name", $cus->Name);
		$stmt->execute();
		$db = null;
		echo json_encode($cus);
		}

	catch(PDOException $e)
		{
		echo '{"error":{"text":' . $e->getMessage() . '}}';
		}
	}

// GET a Product Details

function getProduct($id)
	{
	$sql = "select id,product_name,information FROM products WHERE id=" . $id . " ORDER BY id";
	try
		{
		$db = DB_Connection();
		$stmt = $db->query($sql);
		$list = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo json_encode($list);
		}

	catch(PDOException $e)
		{
		echo '{"error":{"text":' . $e->getMessage() . '}}';
		}
	}

// Update Product Details

function updateProduct($id)
	{
	$request = Slim::getInstance()->request();
	$cus = json_decode($request->getBody());
	$sql = "UPDATE products SET product_name=:p_name,information=:info WHERE id=:id";
	try
		{
		$db = DB_Connection();
		$stmt = $db->prepare($sql);
		$stmt->bindParam("p_name", $cus->product_name);
		$stmt->bindParam("info", $cus->information);
		$stmt->bindParam("id", $id);
		$stmt->execute();
		$db = null;
		echo json_encode($cus);
		}

	catch(PDOException $e)
		{
		echo '{"error":{"text":' . $e->getMessage() . '}}';
		}
	}

// DELETE Product From the Database

function deleteProduct($id)
	{
	$sql = "DELETE FROM products WHERE id=" . $id;
	try
		{
		$db = DB_Connection();
		$stmt = $db->query($sql);
		$list = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo json_encode($list);
		}

	catch(PDOException $e)
		{
		echo '{"error":{"text":' . $e->getMessage() . '}}';
		}
	}

// DELETE shoppinglist From the Database

function deleteList($name)
	{
	$sql = "DELETE FROM shoppinglistnames WHERE list_name='" . $name . "' LIMIT 1";
	try
		{
		$db = DB_Connection();
		$stmt = $db->query($sql);
		$list = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo json_encode($list);
		}

	catch(PDOException $e)
		{
		echo '{"error":{"text":' . $e->getMessage() . '}}';
		}
	}

// DELETE product From shoppinglist

function deleteListProduct($pid, $shoplist_name)
	{
	$sql = "DELETE FROM shoppinglists WHERE shoppinglists.productid=" . $pid . " AND shoppinglists.shoplist_name='" . $shoplist_name . "' LIMIT 1";
	try
		{
		$db = DB_Connection();
		$stmt = $db->query($sql);
		$list = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo json_encode($list);
		}

	catch(PDOException $e)
		{
		echo '{"error":{"text":' . $e->getMessage() . '}}';
		}
	}

?>