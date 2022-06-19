<?php

require_once 'dbclass.php';

class USER
{	

	private $conn;
	
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
	
	public function lasdID()
	{
		$stmt = $this->conn->lastInsertId();
		return $stmt;
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
	
		
	public function monitor($activities)
	{
		try
		{	
		//$at = date("Y-m-d","h:i:sa") ;
		   
			$stmt = $this->conn->prepare("INSERT INTO monitor(activities) 
		VALUES(:fn)");
		    $stmt->bindparam(":fn",$activities);
			//$stmt->bindparam(":at",$at);
			$stmt->execute();	
			return $stmt;
		}
		catch(PDOException $ex)
		{
			echo $ex->getMessage();
		}
	}
		
	public function updateadmin($usa,$level,$sno)
	{
		try
		{	
		  
			$stmt = $this->conn->prepare("update admin set LEVEL = :lv, USERNAME = :us where SN =:sno");
		    $stmt->bindparam(":lv",$level);
		   $stmt->bindparam(":sno",$sno);
		    $stmt->bindparam(":us",$usa);
			$stmt->execute();	
			return $stmt;
		}
		catch(PDOException $ex)
		{
			echo $ex->getMessage();
		}
	}
	
    	public function changepassword($user,$pwd)
	{
		try
		{	
		$rp = md5($pwd) ;
		  
		    $stmt = $this->conn->prepare("update stalldata set pwd = :pd where user_id =:u");
		    $stmt->bindparam(":pd",$rp);
		   $stmt->bindparam(":u",$user);
		    //$stmt->bindparam(":us",$usa);
			$stmt->execute();	
			return $stmt;
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
	
		public function loginuser($user,$upass)
	{
		try
		{
			$stmt = $this->conn->prepare("SELECT * FROM customers WHERE cemails =:email_id");
			$stmt->execute(array(":email_id"=>$user));
			$userRow=$stmt->fetch(PDO::FETCH_ASSOC);
			
			if($stmt->rowCount() == 1)
			{
				//if($userRow['userStatus']=="Y")
				//{
					if($userRow['pwd']==md5($upass))
					{
						$_SESSION['customer'] = $userRow['fname'];
						$_SESSION['usermail'] = $userRow['cemails'];
						//$_SESSION['lv'] = $userRow['LEVEL'];
						return true;
					}
					else
					{
						header("Location: login.php?errorp");
						exit;
					}
			}
			else
			{
				header("Location: login.php?errore");
				exit;
			}		
		}
		     catch(PDOException $ex)
		     {
			  echo $ex->getMessage();
		     }
	}
	
	
	public function neworder($orderno,$item,$desc,$quant,$origin,$dest)
	{
		try
		{	
		   $tdate = date("Y/m/d") ;
		   $sdate = date("l") ;
		   $ldate = $sdate.'--'.$tdate ;
		   $sts = 'NOT DELIVERD YET' ;
		   $cl = 'OFFICE' ;
		   $ttm = date("h:i");
			$stmt = $this->conn->prepare("INSERT INTO trans(longsdate,transdate,order_no,items,descripts,quantity,origin,destination,curent_location,status,transtime) 
		VALUES(:lt,:td,:odn,:itm,:ds,:qun,:org,:dst,:cln,:stu,:tt)");
		    $stmt->bindparam(":lt",$ldate);
		    $stmt->bindparam(":td",$tdate);
			$stmt->bindparam(":odn",$orderno);
			$stmt->bindparam(":itm",$item);
			$stmt->bindparam(":ds",$desc);
			$stmt->bindparam(":qun",$quant);
			$stmt->bindparam(":org",$origin);
			$stmt->bindparam(":dst",$dest);
			$stmt->bindparam(":cln",$cl);
			$stmt->bindparam(":stu",$sts);
			$stmt->bindparam(":tt",$ttm);
			$stmt->execute();	
			return $stmt;
		}
		catch(PDOException $ex)
		{
			echo $ex->getMessage();
		}
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
		$mail->Username="info@ansmaportal.com";  
		$mail->Password="ansma@2020";            
		$mail->SetFrom('info@ansmaportal.com','MARKET_DEVELOPEMENT');
		$mail->AddReplyTo("info@ansmaportal.com","MARKET_DEVELOPEMENT");
		$mail->Subject    = $subject;
		$mail->MsgHTML($message);
		$mail->Send();
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
	
	
	
	
	
	
	
	
		
}

	