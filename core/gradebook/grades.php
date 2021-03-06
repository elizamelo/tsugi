<?php
require_once "../../config.php";
require_once $CFG->dirroot."/pdo.php";
require_once $CFG->dirroot."/lib/lms_lib.php";
require_once "lib.php";

session_start();

// Sanity checks
$LTI = requireData(array('user_id', 'link_id', 'role','context_id', "result_id"));
$instructor = isInstructor($LTI);
if ( ! $instructor ) die("Requires instructor role");
$p = $CFG->dbprefix;

// Get basic grade data
$query_parms = array(":LID" => $LTI['link_id']);
$orderfields =  array("R.updated_at", "displayname", "email", "grade");
$searchfields = $orderfields;
$sql = 
    "SELECT R.user_id AS user_id, displayname, email,
        grade, note, R.updated_at AS updated_at
    FROM {$p}lti_result AS R
    JOIN {$p}lti_user AS U ON R.user_id = U.user_id
    WHERE R.link_id = :LID";

// View 
headerContent();
startBody();
flashMessages();
welcomeUserCourse($LTI);

if ( isset($GRADE_DETAIL_CLASS) && is_object($GRADE_DETAIL_CLASS) ) {
    $detail = $GRADE_DETAIL_CLASS;
} else {
    $detail = false;
}

pagedPDO($pdo, $sql, $query_parms, $searchfields, $orderfields, "grade-detail.php");

footerContent();
