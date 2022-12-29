<?php
session_start();
include 'koneksi.php';
$msg='';
if(isset($_POST['submit'])){
	$waktuunsetsesi=time()-30;
	$ip_address=getIpAddr();
// mendapatkan total klik submit berdasarkan ip
	$query=mysqli_query($koneksi,"select count(*) as total_count from loginlogs where TryTime > $waktuunsetsesi and IpAddress='$ip_address'");
	$r=mysqli_fetch_assoc($query);
	$total_count=$r['total_count'];
  //Checking if the attempt 3, or youcan set the no of attempt her. For now we taking only 3 fail attempted
	if($total_count==3){
		$msg="Terlalu banyak percobaan login, coba lagi dalam 30 detik";
	}else{
    //Getting Post Values
		$username = mysqli_real_escape_string($koneksi, $_POST['username']) ;
		$password = mysqli_real_escape_string($koneksi, md5($_POST['password'])) ;
    // Coding for login
		$res=mysqli_query($koneksi,"select * from tbl_admin where username='$username' and  password='$password'");
		if(mysqli_num_rows($res)){
			$_SESSION['IS_LOGIN']='yes';
			$_SESSION['namaadmin']  = $r['nama_admin'];
			$_SESSION['username']   = $r['username'];
			$_SESSION['password']   = $r['password'];
			$_SESSION['idadmin']    = $r['id_admin'];
			mysqli_query($koneksi,"delete from loginlogs where IpAddress='$ip_address'");
			if (!empty($_POST["remember"])) {
				setcookie("username", $_POST["username"], time() + (60 * 60 * 24 * 5));
				setcookie("password", $_POST["password"], time() + (60 * 60 * 24 * 5));
			} else {
				setcookie("username", "");
				setcookie("password", "");
			}

			echo "<script>window.location.href='dashboard.php';</script>";

		}else{
			$total_count++;
			$rem_attm=3-$total_count;
			if($rem_attm==0){
				$msg="Terlalu banyak percobaan login, coba lagi dalam 30 detik";
			}else{
				$msg="username atau password salah<br/>$rem_attm kali kesempatan tersisa";
			}
			$try_time=time();
			mysqli_query($koneksi,"insert into loginlogs(IpAddress,TryTime) values('$ip_address','$try_time')");
		}
	}
}

// Getting IP Address
function getIpAddr(){
	if (!empty($_SERVER['HTTP_CLIENT_IP'])){
		$ipAddr=$_SERVER['HTTP_CLIENT_IP'];
	}elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
		$ipAddr=$_SERVER['HTTP_X_FORWARDED_FOR'];
	}else{
		$ipAddr=$_SERVER['REMOTE_ADDR'];
	}
	return $ipAddr;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="robots" content="noindex, nofollow">
	<title>Login Form</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="css/bootstrap.css" rel='stylesheet' type='text/css' />
	
</head>
<body>
	<div class="container" >
		
		<center>
			<div class="card col-md-8" style="margin-top: 30px;">
				<h3 class="text-center  pt-3 pb-1">Login form</h3>
				<div id="login-row" class="row justify-content-center align-items-center">
					<div id="login-column" class="col-md-6">
						<div id="login-box" class="col-md-12">
							<form id="login-form" class="form" method="post">
								<div class="form-group">
									<input type="text" name="username" placeholder="Username" id="username" class="form-control" required value="<?php echo (isset($_COOKIE["username"])) ? $_COOKIE['username']:'' ?>" >
								</div>
								<div class="form-group">
									<input  type="password" name="password" placeholder="Password" id="password" class="form-control" required value="<?php echo (isset($_COOKIE['password'])) ? $_COOKIE['password']:'' ?>">
								</div>
								<div class="form-group">
									<input type="submit" name="submit" class="btn btn-info btn-md" value="Submit">
								</div>
								<div class="form-group form-check">
                                <input type="checkbox" class="form-check-input" id="exampleCheck1" name="remember"
                                <?php echo ((isset($_COOKIE["username"])) and (isset($_COOKIE["password"]))) ? "checked":"" ?> >
                                <label class="form-check-label" for="exampleCheck1">Remember Me</label>
                            </div>
								<div id="result" style="color: red;"><?php echo $msg?></div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</center>
	</div>
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
</body>
</html>