<?php include("path.php"); ?>
<?php session_start(); ?>
<?php
require_once (ROOT_PATH . '/app/database/connect.php');

$post_id = $_SESSION['postid'];
$memberId = $_SESSION['id'];
$sql = "SELECT tbl_comment.*,tbl_like_unlike.like_unlike 
FROM tbl_comment 
LEFT JOIN tbl_like_unlike 
ON tbl_comment.comment_id = tbl_like_unlike.comment_id 
AND member_id = " . $memberId . " WHERE post_id = $post_id ORDER BY parent_comment_id asc, comment_id asc";

$result = mysqli_query($conn, $sql);
$record_set = array();
while ($row = mysqli_fetch_assoc($result)) {
    array_push($record_set, $row);
}
mysqli_free_result($result);

mysqli_close($conn);
echo json_encode($record_set);
?>