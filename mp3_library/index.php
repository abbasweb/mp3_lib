<html>
<body>

</body>
</html>
<?php
require_once ("includs/db.php");
$query="SELECT * FROM `tracks`";
$res=getData($con,$query,[]);
if (count($res)>0)
{
$query="SELECT  `title`, `track`, `token_audio` FROM `tracks` ";
$tracks=getData($con,$query,[]);


echo "
    <table>
    <tr style='background: yellow'><th>الاسم</th>
    ";
echo "<th>الــرابط"."</th>";
echo "</tr>";
foreach ($tracks as $track)
{
    echo "<tr style='background: #1c7430'>";
    echo "<td>".base64_decode($track['title'])."</td>";
    $url=url()."?file=".$track["token_audio"];
    echo "<td><a href='$url'>"."اضغط هنا"."</a></td>";
    echo "</tr>";
}
echo "</table>";


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
        echo "<p style='color:red '>"."كاعد تستنيج حتة تطلع
 <br>
 SQL error ?
         <br>
         ابلع يا طيز"."</p>";

    }


  }
}
else
{
    echo "<p style='color: red'>"."لا توجد  تراكات"."</p>";
}
?>