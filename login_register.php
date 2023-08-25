<?php 
require('connection.php');
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
function sendMail($email,$v_code){
    require ("phpmailer/PHPMailer.php");
    require ("phpmailer/Exception.php");
    require ("phpmailer/SMTP.php");

    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = 'edrago8018@gmail.com';                     //SMTP username
        $mail->Password   = 'cucdccizonzlrytc';                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
    
        //Recipients
        $mail->setFrom('edrago8018@gmail.com', 'MJ-site');
        $mail->addAddress($email);     //Add a recipient
        
    
        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = 'Email verification from MJ-site';
        $mail->Body    = "thanks for verification!
            click the link below to verify your email address
            <a href='http://localhost/project/register/verify.php?email=$email&v_code=$v_code'>verify</a>";
       
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

if(isset($_POST['login'])){
    $query="SELECT * FROM `registered_user` WHERE `email`='$_POST[email_username]' OR `username`='$_POST[email_username]'";
    $result=mysqli_query($con,$query);
    if($result){
        if(mysqli_num_rows($result)==1){
            $result_fetch=mysqli_fetch_assoc($result);
            if($result_fetch('is_verify')==1){
                if(password_verify($_POST['password'],$result_fetch['password'])){
                    $_SESSION['LOGGED_IN']=TRUE;    
                    $_SESSION['username']=$result_fetch['username'];
    
                    header("location:index.php");
                }else{
                    echo"
                        <script>
                            alert('Incorrect Password');
                            window.location.href='index.php'; 
                        </script>
                    ";
                }
            }
            else{
                echo"
                    <script>
                        alert('email not verified');
                        window.location.href='index.php'; 
                    </script>
                ";
            }
            
        }else{
            echo"
                    <script>
                        alert('Invalid Username or email');
                        window.location.href='index.php'; 
                    </script>
                ";
        }
    }else{
        echo"
            <script>
                alert('cannot run query');
                window.location.href='index.php'; 
            </script>
        ";
    }
}

if(isset($_POST['register'])){  
    $user_exist_query = "SELECT * FROM `registered_user` WHERE `username`='$_POST[username]' OR `email`='$_POST[email]'";
    $result=mysqli_query($con,$user_exist_query);
    if($result){
        if(mysqli_num_rows($result)>0){
            $result_fetch=mysqli_fetch_assoc($result);
            if($result_fetch['username']==$_POST['username']){
                echo"
                    <script>
                        alert('$result_fetch[username] - username already exist');
                        window.location.href='index.php'; 
                    </script>
                ";
            }else{
                echo"
                <script>
                    alert('$result_fetch[email] - email already exist');
                    window.location.href='index.php'; 
                </script>
            ";
            }
        }else{
            $password=password_hash($_POST['password'],PASSWORD_BCRYPT);
            $v_code=bin2hex(random_bytes(16));
            $query="INSERT INTO `registered_user`(`full_name`, `username`, `email`, `password`,`verification_code`, `is_verify`) VALUES ('$_POST[fullname]','$_POST[username]','$_POST[email]','$password','$v_code','0')";
            
            if(mysqli_query($con,$query) && sendMail($_POST['email'],$v_code)){
                echo"
                    <script>
                        alert('Registration Successful');
                        window.location.href='index.php'; 
                    </script>
                ";
            }else{
                echo"
                    <script>
                        alert('server down..');
                        window.location.href='index.php'; 
                    </script>
                ";
            }


        }
    }else{
        echo"
        <script>
            alert('cannot run query');
            window.location.href='index.php'; 
        </script>
        ";
    }
}



?>