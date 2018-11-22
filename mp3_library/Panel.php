<html>
<head>
    <META charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap-grid.css">
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap-reboot.css">
    <title>مكتبة راب العراق الـــصوتية</title>
    <style>
        #upFile
        {
            margin-left: 40px;
        }
        #track_id
        {
            margin-left: 45px;
        }
        #btnnnnnn
        {
            margin-left: 45px ;
        }

    </style>
</head>
<body>

<form  method="post" enctype="multipart/form-data">
    <input type="text" name="trackTitle" placeholder="اسم التراك" id="track_id">
    <input type="file" name="upFile" id="upFile" class="btn-outline-secondary">
    <input type="submit" value="رفع التراك" name="sub" class="rounded-bottom btn-outline-success" id="btnnnnnn">
</form>

<?php
if (!isset($_SESSION))
{

    session_start();
    session_regenerate_id();
}
error_reporting(0);
require_once ("includs/db.php");
require_once ("includs/CheckLogin.php");

$uploader=$_SESSION['user'];

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{

    $up_dir = "tracks" . "/";
    $upFile = basename($_FILES['upFile']['name']);
// checking the extn.
    $checkExtn = end(explode(".", $upFile));
// changing name
    $final_name = $up_dir . "audioClip-" . rand(0, 9 * 1000000) . "." . $checkExtn;
// $Allowed_extns to upload
    $Alloweded_extns = array("mp3", "m4a");
// Alowed MIME TYPE
    $Alloweded_mime = array("audio/mpeg" ,"audio/mp3","audio/m4a");
    // filtring the title
    $filtered_title = filter_var($_POST['trackTitle'], FILTER_SANITIZE_STRING);
    // token for track
    $Token = rand(0, 10 * 100000);
    // size limit
    $size=$_FILES['upFile']['size'];
    // accept the upload || by defualt
    $up_accept=null;
// check Empty
    if (!empty($filtered_title))
    {
        if (!empty($upFile))
        {
            if (in_array($checkExtn,$Alloweded_extns))
            {
                if (in_array($_FILES['upFile']['type'],$Alloweded_mime))
                {
                    $up_accept=true;
                }
                else
                {
                    echo "<p style='color: red'>"."هذا ليس ملفا صوتيا"."</p>";
                    $up_accept=false;
                }
            }
            else
            {
                $up_accept=false;
                echo "<p style='color: red'>"."الصيغ المدعومة هي:mp3,m4a فقط"."</p>";
            }
        }
        else
        {
            $up_accept=false;
            echo "<p style='color: red'>"."لايمكن ان تكون خانة التحميل فارغة"."</p>";
        }
    }
    else
    {
        $up_accept=false;
        echo "<p style='color: red'>"."لايمكن ان يكون هذا الحقل فارغا"."</p>";
    }
    if ($size > 40000000)
    {
        $up_accept=false;
        echo "<p style='color: red'>"."حجم الملف كبير جدا"."</p>";
    }
    if ($up_accept == true)
    {
        if (move_uploaded_file($_FILES['upFile']['tmp_name'],$final_name))
        {

            $query="INSERT INTO `tracks`( `title`, `track`, `token_audio`, `uploader`) VALUES (?,?,?,?)";
            $res=setData($con,$query,[
                    base64_encode($filtered_title),
                $final_name,
                $Token,
                $_SESSION['user']
            ]);
            if (count($res)>0)
            {
                echo "<p style='color: #1c7430'>"."تــــم"."</p>";
                unset($filtered_title);
                unset($upFile);
            }
            else
            {
                echo "<p style='color: #1c7430'>"."لم يتم تحميل الملف"."</p>";
                unset($filtered_title);
                unset($upFile);

            }

        }
    }

}
// Get the tracks
$query2="SELECT * FROM `tracks`";
$res=getData($con,$query2,[]);
if (count($res)>0)
{
    $query="SELECT  `title`, `track`, `token_audio`,`uploader` FROM `tracks` ";
    $tracks=getData($con,$query,[]);


    echo "
    <table>
    <tr style='background: yellow'><th>الاسم</th>
    ";
    echo "<th>الــرابط"."</th>";
    echo "<th>الحــــدث"."</th>";
    echo "<th>الرافع"."</th>";
    echo "</tr>";


    foreach ($tracks as $track)
    {
        echo "<tr style='background: #1c7430'>";
        $track_tok=filter_var($track['token_audio'],FILTER_SANITIZE_NUMBER_INT);
        echo "<td>".base64_decode($track['title'])."</td>";
        $url=url()."?file=".$track_tok;
        echo "<td><a href='$url'>"."اضغط هنا"."</a></td>";

        echo "<td>
<form method='post'>
<input type='hidden' value='$track_tok' name='track_rm'>
<input type='submit' value='حذف' name='del' class='btn-danger'>
</form>
</td>";
        echo "<td>".$track['uploader']."</td>";
        echo "</tr>";
    }
    echo "</table>";
    $track_rm=filter_var($_POST['track_rm'],FILTER_SANITIZE_NUMBER_INT);
    $qus = "SELECT   `track` FROM `tracks` where `token_audio`=?";
    $rs = getData($con, $qus,[$track_rm]);
    if (isset($_POST['del']))
    {

        foreach ($rs as $rg)
        {
            if (file_exists($rg['track']))
            {
                $qu = "DELETE FROM `tracks` WHERE `token_audio` = $track_rm";
                $r = setData($con, $qu, [
                    $track_rm
                ]);
                if (count($r)>0)
                {
                    unlink($rg['track']);
                    echo "<p style='color: green'>"."تم حذف التراك "."</p>";
                }
                else
                {
                    echo "<p style='color: red'>"."لم يتم حذف التراك "."</p>";
                }

            }
            else
            {
                echo "<p style='color: red'>"."لايوجد تراك كهذا "."</p>";
            }
        }


    }


    if (isset($_GET['file']))
    {
        $filtered_id=filter_var($_GET['file'],FILTER_SANITIZE_NUMBER_INT);
        if (!empty($filtered_id))
        {
            $query="SELECT   `track` FROM `tracks` WHERE `token_audio`=$filtered_id";
            $downloads=getData($con,$query,[$filtered_id]);
            foreach ($downloads as $download)
            {
                $file_location = $download['track'];
                if (file_exists($file_location))
                {

                    header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
                    header("Content-Type: application/zip");
                    header("Content-Transfer-Encoding: Binary");
                    header("Content-Length:".filesize($file_location));
                    header("Content-Disposition: attachment; filename=$file_location");
                    readfile($file_location);
                    die();
                }

            }
        }
        else
        {
            echo "<p style='color:red '>"."تريد تطلع 
 <br>
 SQL error ?
         <br>
         ابلع يا طيز"."</p>";
            die();
        }



    }


}
else
{
    echo "<p style='color: red'>"."لا توجد  تراكات"."</p>";
}
for ($i=0;$i<2;++$i)
{
    echo "<br>";
}
?>

<form method="post">
    <button name="log_out" type="submit" class="btn-outline-danger">تسجيل خـــروج</button>
</form>
<?php

if (isset($_POST['log_out'])) {
    if (isset($_SESSION))
    {
        session_destroy();

    }
    header("Location:index.php");
}


?>
</body>
</html>