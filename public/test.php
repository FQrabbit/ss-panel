<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title></title>
<style>
    div{
        margin:0 auto;
        font-family: Microsoft Yahei;
    }
    table{
        border-collapse: collapse;
        margin-top: 30px;
    }
    td{
        border-bottom: 1px solid #EAE2E2;
        padding:9px;
    }
</style>
</head>
<body>
<?php
$day = 1*24*60*60;
$week = 7*24*3600;
$month = 30*24*3600;
$season = 3*30*24*3600;
$year = 12*30*24*3600;
$last_week = time() - $week;
$last_one_day = time() - 1*$day;
$last_two_days = time() - 2*$day;
$last_three_days = time() - 3*$day;

// $buydate = $oo->get_buy_date();
// $expired_user_array = $oo->get_expired_user_array();

// $buytime = strtotime($buydate);
// echo $buydate;
// echo $expiretime = strtotime("+1 Month",$buytime);
// echo $expiredate = date("Y-m-d H:i:s",$expiretime);
// $oo->resetExpiredUser();

// $ids = $oo->get_unexpire_user_array();
// print_r($ids);
// echo $ids[0];
// $oo->resetTransfer($ids[0]);
// foreach ($ids as $id) {
//     $oo->resetTransfer($id);
// }
// $oo->updateExpireDate("A");



?>
<div>
<table>
    <tr>
        <td>One day ago</td>
        <td><?php echo $last_one_day; ?></td>
                <td><?php echo date('y-m-d H:i:s',$last_one_day); ?></td>
        </tr>
        <tr>
                <td>Two days ago</td>
                <td><?php echo $last_two_days; ?></td>
                <td><?php echo date('y-m-d H:i:s',$last_two_days); ?></td>
        </tr>
        <tr>
                <td>Three days ago</td>
                <td><?php echo $last_three_days; ?></td>
                <td><?php echo date('y-m-d H:i:s',$last_three_days); ?></td>
        </tr>
        <tr>
                <td>One week ago</td>
                <td><?php echo $last_week; ?></td>
                <td><?php echo date('y-m-d H:i:s',$last_week); ?></td>
        </tr>
        <tr>
                <td>Seconds in a day</td>
                <td><?php echo $day; ?></td>
        </tr>

</table>
<table>
        <tr>
                <th>id</th>
                <th>user_name</th>
                <th>port</th>
                <th>email</th>
                <th>user_type</th>
                <th>buy_date</th>
                <th>expire_date</th>
                <th>isexpire</th>
                <th>plan</th>
        </tr>
        <tbody>
<?php 

$con = mysql_connect("db.shadowsky.site","root","zhWalker20");
if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  }
mysql_select_db("test", $con);
$sql = "SELECT * 
        FROM  `user` 
        WHERE  `expire_date` >  '0000-00-00 00:00:00'
        ORDER BY  `expire_date` DESC";
$result =  mysql_query($sql,$con);

while ($row = mysql_fetch_array($result)) {
    echo "<tr>";
            echo "<td>".$row["id"]."</td>";
            echo "<td>".$row["user_name"]."</td>";
            echo "<td>".$row["port"]."</td>";
            echo "<td>".$row["email"]."</td>";
            echo "<td>".$row["user_type"]."</td>";
            echo "<td>".$row["buy_date"]."</td>";
            echo "<td>".$row["expire_date"]."</td>";
            if ( $row["expire_date"] < date("Y-m-d H:i:s") ) {
                echo "<td>Yes</td>";
            }else{
                echo "<td>No</td>";
            }
            echo "<td>".$row["plan"]."</td>";
    echo "</tr>";
} 
?>
        </tbody>
        
</table>
</div>
</body>
</html>