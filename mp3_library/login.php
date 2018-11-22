<!DOCTYPE html>
<html>
<head>

    <title>Login Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--===============================================================================================-->

    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="login/css/util.css">
    <link rel="stylesheet" type="text/css" href="login/css/main.css">
    <!--===============================================================================================-->
</head>
<body>
<div class="limiter" style="background: #00c7fc">
    <div class="container-login100">
        <div class="wrap-login100">
            <div class="login100-pic js-tilt" data-tilt>
                <img src="login/images/img-01.png" alt="IMG">
            </div>

            <form class="login100-form validate-form" method="post"  >
					<span class="login100-form-title">
						Admin Login
					</span>

                <div class="wrap-input100 validate-input">
                    <input class="input100" type="text" name="user" placeholder="Username" required>
                    <span class="focus-input100"></span>
                    <span class="symbol-input100">
							<i class="fa fa-envelope" aria-hidden="true"></i>
						</span>
                </div>

                <div class="wrap-input100 validate-input" data-validate = "Password is required">
                    <input class="input100" type="password" name="pass" placeholder="Password" required>
                    <span class="focus-input100"></span>
                    <span class="symbol-input100">
							<i class="fa fa-lock" aria-hidden="true"></i>
						</span>
                </div>

                <div class="container-login100-form-btn">

                    <input type="submit" name="sub" class="login100-form-btn" value="Login">
                </div>
            </form>

            <?php
            require_once ("includs/db.php");

            if (isset($_POST['sub']))
            {
                $user=filter_var($_POST['user'],FILTER_SANITIZE_STRING);
                $pass=filter_var($_POST['pass'],FILTER_SANITIZE_STRING);
                if (!empty($user))
                {
                    if (!empty($pass))
                    {
                        $query="SELECT   `id`,`password` FROM `admin` where `username`=?";
                        $res=getData($con,$query,[$user]);
                        if (count($res)>0)
                        {
                            foreach ($res as $re)
                            {
                                if (password_verify($pass,$re['password']))
                                {
                                    if (!isset($_SESSION))
                                    {
                                        session_start();
                                        session_regenerate_id();
                                    }
                                    header("Location:Panel.php");
                                    $_SESSION['id']=$re['id'];
                                    $_SESSION['user']=$user;

                                }
                                else
                                {
                                    echo "<p style='color: red'>"."الباسوورد خطأ"."</p>";

                                }
                            }


                        }
                        else
                        {
                            echo "<p style='color: red'>"."لايوجد يوزر بهذا الاسم"."</p>";
                        }
                    }
                    else
                    {
                        echo "<p style='color: red'>"."لا يمكن ان يكون الحقل فارغ"."</p>";
                    }
                }
                else
                {
                    echo "<p style='color: red'>"."لا يمكن ان يكون الحقل فارغ"."</p>";
                }
            }
            ?>
        </div>

    </div>
</div>
</body>
</html>
