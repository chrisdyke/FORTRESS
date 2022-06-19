<?php
require_once 'dbclass.php';

class ECOMMERCE
{	

	private $conn;
	// Stores the visitor's Cart ID
    private static $_mCartId;
	
	public function __construct()
	{
		$database = new Database();
		$db = $database->dbConnection();
		$this->conn = $db;
    }
	
	public function runQuery($sql)
	{
		$stmt = $this->conn->prepare($sql);
		return $stmt;
	}
	
	public function is_logged_in()
	{
		if(isset($_SESSION['userSession']))
		{
			return true;
		}
	}
	
	public function redirect($url)
	{
		header("Location: $url");
	}
	
	public function logout()
	{
		session_destroy();
		$_SESSION['userSession'] = false;
	}
	
	public function lasdID()
	{
		$stmt = $this->conn->lastInsertId();
		return $stmt;
	}
	
public static function SetCartId()
{
// If the cart ID hasn't already been set ...
if (self::$_mCartId == '')
{
// If the visitor's cart ID is in the session, get it from there
if (isset ($_SESSION['cart_id']))
{
self::$_mCartId = $_SESSION['cart_id'];
}
// If not, check whether the cart ID was saved as a cookie
elseif (isset ($_COOKIE['cart_id']))
{
// Save the cart ID from the cookie
self::$_mCartId = $_COOKIE['cart_id'];
$_SESSION['cart_id'] = self::$_mCartId;
// Regenerate cookie to be valid for 7 days (604800 seconds)
setcookie('cart_id', self::$_mCartId, time() + 604800);
}
else
{
/* Generate cart id and save it to the $_mCartId class member,
the session and a cookie (on subsequent requests $_mCartId
will be populated from the session) */
self::$_mCartId = md5(uniqid(rand(), true));
// Store cart id in session
$_SESSION['cart_id'] = self::$_mCartId;
// Cookie will be valid for 7 days (604800 seconds)
setcookie('cart_id', self::$_mCartId, time() + 604800);
}
}
}

// Returns the current visitor's card id
public function GetCartId()
{
// Ensure we have a cart id for the current visitor
if (!isset (self::$_mCartId))
self::SetCartId();
return self::$_mCartId;
}
	
public function add_product_to_cart($cartid,$productid,$productquantity)
{
	try
	{
		// check if the item has been added before and update the quantity if yes or add it as a new cart item if no
		
		$stmt = $this->conn->prepare("SELECT * FROM shopping_cart WHERE (product_id =:pid and cart_id =:cid)");
		$stmt->bindparam(":pid",$productid) ;
		$stmt->bindparam(":cid",$cartid) ;
		$stmt->execute() ;
		$cartrow =$stmt->fetch(PDO::FETCH_ASSOC);
		if($stmt->rowCount() == 1)
		{
			// update the cart with the current product quantity
   $updatecart = $this->conn->prepare("update shopping_cart set quantity = :qt where (cart_id =:cid and product_id =:pid)") ;
	$updatecart->bindparam(":pid",$productid) ;
	$updatecart->bindparam(":cid",$cartid) ;
	$updatecart->bindparam(":qt",$productquantity) ;
	$updatecart->execute() ;
	return $updatecart ;
	
		}
		else
		{
			// add the item as a new item to the cart
			$buy = 1 ;
			$tdate = date("Y/m/d") ;
$additem = $this->conn->prepare("insert into shopping_cart(cart_id, product_id,quantity,buy_now, trans_date) values(:cid,:pid,:qt,:bn,:td)") ;
		$additem->bindparam(":cid", $cartid) ;
		$additem->bindparam(":pid", $productid) ;
		$additem->bindparam(":qt", $productquantity) ;
		$additem->bindparam(":bn", $buy) ;
		$additem->bindparam(":td", $tdate) ;
		$additem->execute() ;
		return $additem ;
		}
	}
	catch(PDOException $ex)
    {
	echo $ex->getMessage();
    }
}

public function remove_item_from_cart($cartid,$product_id)
{
	try
	{
	$remove = $this->conn->prepare("delete from shopping_cart where (cart_id =:cid and product_id =:pid)") ;
	$remove->bindparam(":cid",$cartid) ;
	$remove->bindparam(":pid",$product_id) ;
	$remove->execute() ;
	return $remove ;
	}
	catch(PDOException $ex)
	{
	echo $ex->getMessage();
	}
}

public function update_cart_item($cartid, $productid,$quantity)
{
try
{
			// update the cart with the current product quantity
   $updatecart = $this->conn->prepare("update shopping_cart set quantity = :qt where (cart_id =:cid and product_id =:pid)") ;
	$updatecart->bindparam(":pid",$productid) ;
	$updatecart->bindparam(":cid",$cartid) ;
	$updatecart->bindparam(":qt",$quantity) ;
	$updatecart->execute() ;
	return $updatecart ;	
}
catch(PDOException $ex)
    {
	echo $ex->getMessage();
    }


}

public function get_cart_item ($cartid)
{
	try
	{
		$getitem = $this->conn->prepare("select * from shopping_cart where cart_id = :cid") ;
		$getitem->bindparam(":cid",$cartid) ;
		$getitem->execute() ;
		return $getitem ;
	}
	catch(PDOException $ex)
    {
	echo $ex->getMessage();
    }
	
}

public function get_no_of_item_inmycart($cartid)
{
	try
	{
		$itemno = $this->conn->prepare("select * from shopping_cart where cart_id = '$cartid'") ;
		$itemno->execute() ;
		$rows = $itemno->rowCount() ;
		return $rows ;
	}
	catch(PDOException $ex)
    {
	echo $ex->getMessage();
    }
	
}

public function create($user,$pwd,$mail,$level,$code)
{
		try
		{	
		  					
			$password = md5($pwd);
			$stmt = $this->conn->prepare("INSERT INTO admin(USERNAME,PASSWORD,EMAILS,LEVEL,tokenCode) 
		VALUES(:un,:pw,:mls,:lev,:cd)");
		    $stmt->bindparam(":un",$user);
			$stmt->bindparam(":pw",$password);
			$stmt->bindparam(":mls",$mail);
			$stmt->bindparam(":lev",$level);
			$stmt->bindparam(":cd",$code);
			$stmt->execute();	
			return $stmt;
		}
		catch(PDOException $ex)
		{
			echo $ex->getMessage();
		}
	}

public function cat($name)
{
		try
		{	
		  					
		$stmt = $this->conn->prepare("INSERT INTO category(category_name) VALUES (:un)");
		 $stmt->bindparam(":un",$name);
		 $stmt->execute();	
		 return $stmt;
		}
		catch(PDOException $ex)
		{
			echo $ex->getMessage();
		}
	}
		
	
public function loginadmin($user,$upass)
{
	try
	{
			$stmt = $this->conn->prepare("SELECT * FROM admin WHERE USERNAME =:us");
			$stmt->execute(array(":us"=>$user));
			$userRow=$stmt->fetch(PDO::FETCH_ASSOC);
			
			if($stmt->rowCount() == 1)
			{
				//if($userRow['userStatus']=="Y")
				//{
					if($userRow['PASSWORD'] == md5($upass))
					{
						$_SESSION['userSession'] = $userRow['USERNAME'];
						$_SESSION['user'] = $userRow['EMAILS'];
						$_SESSION['lv'] = $userRow['LEVEL'];
						return true;
					}
					else
					{
						header("Location: index.php?errorp");
						exit;
					}
			}
			else
			{
				header("Location: index.php?errore");
				exit;
			}		
}
catch(PDOException $ex)
{
echo $ex->getMessage();
}
}
			
//}
	
function send_mail($email,$message,$subject)
{						
		require_once('mailer/class.phpmailer.php');
		$mail = new PHPMailer();
		$mail->IsSMTP(); 
		$mail->SMTPDebug  = 0;                     
		$mail->SMTPAuth   = true;                  
		$mail->SMTPSecure = "ssl";                 
		$mail->Host       = "titano.protonhosting.com";      
		$mail->Port       = 465;             
		$mail->AddAddress($email);
		$mail->Username="info@chisadomusic.com";  
		$mail->Password="chisado@2021";            
		$mail->SetFrom('info@chisadomusic.com','CHISADO MUSIC');
		$mail->AddReplyTo("info@chisadomusic.com","CHISADO MUSIC");
		$mail->Subject    = $subject;
		$mail->MsgHTML($message);
		$mail->Send();
	}

public function customers($fname,$lname,$emails,$gender,$phone,$city,$state,$pwd,$rdate,$code)
{
		try
		{	
		$password = md5($pwd);
		   
			$stmt = $this->conn->prepare("INSERT INTO customers(first_name,last_name,cemails,gender,phone,city,cstate,pwd,rdate,tokencode) 
		VALUES(:fn,:ln,:cm,:gn,:ph,:ct,:cs,:pd,:rd,:cd)");
		    $stmt->bindparam(":fn",$fname);
			$stmt->bindparam(":ln",$lname);
			$stmt->bindparam(":cm",$emails);
			$stmt->bindparam(":gn",$gender);
			$stmt->bindparam(":ph",$phone);
			$stmt->bindparam(":ct",$city);
			$stmt->bindparam(":cs",$state);
			$stmt->bindparam(":pd",$password);
			$stmt->bindparam(":rd",$rdate);
			$stmt->bindparam(":cd",$code);
			$stmt->execute();	
			return $stmt;
		}
		catch(PDOException $ex)
		{
			echo $ex->getMessage();
		}
	}
	
public function addmusic($cat,$title,$author,$price,$ds)
{
		try
		{
			$intro = "null" ;
			$full = "null" ;
			$pix = "null" ;	
			$pdate = date("Y/m/d") ;
				   
$stmt = $this->conn->prepare("INSERT INTO products(title,author,category,price,intro,full,pix,ds,pubdate) 
		VALUES(:a,:b,:c,:d,:e,:f,:g,:h,:i)");
		    $stmt->bindparam(":a",$title);
			$stmt->bindparam(":b",$author);
			$stmt->bindparam(":c",$cat);
			$stmt->bindparam(":d",$price);
			$stmt->bindparam(":e",$intro);
			$stmt->bindparam(":f",$full);
			$stmt->bindparam(":g",$pix);
			$stmt->bindparam(":h",$ds);
			$stmt->bindparam(":i",$pdate);
			//$stmt->bindparam(":cd",$code);
			$stmt->execute();	
			return $stmt;
		}
		catch(PDOException $ex)
		{
			echo $ex->getMessage();
		}
	}
			
}
?>