<?php
    require("connection.php");

    if(isset($_GET['email']) && isset($_GET['v_code'])){
        $query = "SELECT * FROM `registered_user` WHERE `email`='$_GET[email]' AND `verification_code`='$_GET[v_code]'";
        $result = mysqli_query($con,$query);
        if($result){
            if(mysqli_num_rows($result)==1){
            }
            $result_fetch=mysqli_fetch_assoc($result);
            if($result_fetch['is_verify']==0){
                $update="UPDATE `registered_user` SET `is_verify`='1' WHERE `email`='$result_fetch[email]'";
                if(mysqli_query($con,$update)){
                    echo"
                    <script>
                        alert('email verification successful');
                        window.location.href='index.php'; 
                    </script>
                ";
                }
                else{
                    echo"
                    <script>
                        alert('error');
                        window.location.href='index.php'; 
                    </script>
                ";
                }
            }
            else{
                echo"
                    <script>
                        alert('email already registered');
                        window.location.href='index.php'; 
                    </script>
                ";
            }
            
        }
        else{
            echo"
                <script>
                    alert('server down..');
                    window.location.href='index.php'; 
                </script>
            ";
            
        }
    }
?>