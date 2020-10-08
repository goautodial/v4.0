
<?php
class Chat{
    private $host  = 'localhost';
    private $user  = 'goautodialu';
    private $password   = "goautodialu1234";
    private $database  = "chat_demo";
    private $chatTable = 'chat';
    private $chatUsersTable = 'chat_users';
    private $chatLoginDetailsTable = 'chat_login_details';
    private $dbConnect = false;
    
    public function __construct(){
        if(!$this->dbConnect){
            $conn = new mysqli($this->host, $this->user, $this->password, $this->database);
            if($conn->connect_error){
                die("Error failed to connect to MySQL: " . $conn->connect_error);
            }else{
                $this->dbConnect = $conn;
            }
        }
    }
    private function getData($sqlQuery) {
            $result = mysqli_query($this->dbConnect, $sqlQuery);
            if(!$result){
                    die('Error in query: '. mysqli_error());
            }
            $data= array();
            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                    $data[]=$row;
            }
            return $data;
    }
    private function getNumRows($sqlQuery) {
            $result = mysqli_query($this->dbConnect, $sqlQuery);
            if(!$result){
                    die('Error in query: '. mysqli_error());
            }
            $numRows = mysqli_num_rows($result);
            return $numRows;
    }
    public function loginUsers($username, $password){
            $sqlQuery = "
                    SELECT userid, username
                    FROM ".$this->chatUsersTable."
                    WHERE username='".$username."' AND password='".$password."'";
    return  $this->getData($sqlQuery);
    }
    public function chatUsers($userid){
            $sqlQuery = "
                    SELECT * FROM ".$this->chatUsersTable."
                    WHERE userid != '$userid'";
            return  $this->getData($sqlQuery);
    }
    public function getUserDetails($userid){
            $sqlQuery = "
                    SELECT * FROM ".$this->chatUsersTable."
                    WHERE userid = '$userid'";
            return  $this->getData($sqlQuery);
    }
    public function getUserAvatar($userid){
        $sqlQuery = "
                SELECT avatar
                FROM ".$this->chatUsersTable."
                WHERE userid = '$userid'";
        $userResult = $this->getData($sqlQuery);
        $userAvatar = '';
        foreach ($userResult as $user) {
                $userAvatar = $user['avatar'];
        }
        return $userAvatar;
    }
    public function updateUserOnline($userId, $online) {
            $sqlUserUpdate = "
                    UPDATE ".$this->chatUsersTable."
                    SET online = '".$online."'
                    WHERE userid = '".$userId."'";
            mysqli_query($this->dbConnect, $sqlUserUpdate);
    }
    public function insertChat($reciever_userid, $user_id, $chat_message) {
            $sqlInsert = "
                    INSERT INTO ".$this->chatTable."
                    (reciever_userid, sender_userid, message, status)
                    VALUES ('".$reciever_userid."', '".$user_id."', '".$chat_message."', '1')";
            $result = mysqli_query($this->dbConnect, $sqlInsert);
            if(!$result){
                    return ('Error in query: '. mysqli_error());
            } else {
                    $conversation = $this->getUserChat($user_id, $reciever_userid);
                    $data = array(
                            "conversation" => $conversation
                    );
                    echo json_encode($data);
            }
    }
    public function getUserChat($from_user_id, $to_user_id) {
            $fromUserAvatar = $this->getUserAvatar($from_user_id);
            $toUserAvatar = $this->getUserAvatar($to_user_id);
            $sqlQuery = "
                    SELECT * FROM ".$this->chatTable."
                    WHERE (sender_userid = '".$from_user_id."'
                    AND reciever_userid = '".$to_user_id."')
                    OR (sender_userid = '".$to_user_id."'
                    AND reciever_userid = '".$from_user_id."')
                    ORDER BY timestamp ASC";
            $userChat = $this->getData($sqlQuery);
            $conversation = '<ul>';
            foreach($userChat as $chat){
                    $user_name = '';
                    if($chat["sender_userid"] == $from_user_id) {
                            $conversation .= '<li class="sent">';
                            $conversation .= '<img width="22px" height="22px" src="userpics/'.$fromUserAvatar.'" alt="" />';
                    } else {
                            $conversation .= '<li class="replies">';
                            $conversation .= '<img width="22px" height="22px" src="userpics/'.$toUserAvatar.'" alt="" />';
                    }
                    $conversation .= '<p>'.$chat["message"].'</p>';
                    $conversation .= '</li>';
            }
            $conversation .= '</ul>';
            return $conversation;
    }
    public function showUserChat($from_user_id, $to_user_id) {
        $userDetails = $this->getUserDetails($to_user_id);
        $toUserAvatar = '';
        foreach ($userDetails as $user) {
                $toUserAvatar = $user['avatar'];
                $userSection = '<img src="userpics/'.$user['avatar'].'" alt="" />
                        <p>'.$user['username'].'</p>
                        <div class="social-media">
                                <i class="fa fa-facebook" aria-hidden="true"></i>
                                <i class="fa fa-twitter" aria-hidden="true"></i>
                                 <i class="fa fa-instagram" aria-hidden="true"></i>
                        </div>';
        }
        // get user conversation
        $conversation = $this->getUserChat($from_user_id, $to_user_id);
        // update chat user read status
        $sqlUpdate = "
                UPDATE ".$this->chatTable."
                SET status = '0'
                WHERE sender_userid = '".$to_user_id."' AND reciever_userid = '".$from_user_id."' AND status = '1'";
        mysqli_query($this->dbConnect, $sqlUpdate);
        // update users current chat session
        $sqlUserUpdate = "
                UPDATE ".$this->chatUsersTable."
                SET current_session = '".$to_user_id."'
                WHERE userid = '".$from_user_id."'";
        mysqli_query($this->dbConnect, $sqlUserUpdate);
        $data = array(
                "userSection" => $userSection,
                "conversation" => $conversation
        );
        echo json_encode($data);
    }
    public function getUnreadMessageCount($senderUserid, $recieverUserid) {
            $sqlQuery = "
                    SELECT * FROM ".$this->chatTable."
                    WHERE sender_userid = '$senderUserid' AND reciever_userid = '$recieverUserid' AND status = '1'";
            $numRows = $this->getNumRows($sqlQuery);
            $output = '';
            if($numRows > 0){
                    $output = $numRows;
            }
            return $output;
    }
    public function updateTypingStatus($is_type, $loginDetailsId) {
            $sqlUpdate = "
                    UPDATE ".$this->chatLoginDetailsTable."
                    SET is_typing = '".$is_type."'
                    WHERE id = '".$loginDetailsId."'";
            mysqli_query($this->dbConnect, $sqlUpdate);
    }
    public function fetchIsTypeStatus($userId){
        $sqlQuery = "
        SELECT is_typing FROM ".$this->chatLoginDetailsTable."
        WHERE userid = '".$userId."' ORDER BY last_activity DESC LIMIT 1";
        $result =  $this->getData($sqlQuery);
        $output = '';
        foreach($result as $row) {
            if($row["is_typing"] == 'yes'){
                    $output = ' - <small><em>Typing...</em></small>';
                }
            }
            return $output;
    }
    public function insertUserLoginDetails($userId) {
        $sqlInsert = "
                INSERT INTO ".$this->chatLoginDetailsTable."(userid)
                VALUES ('".$userId."')";
        mysqli_query($this->dbConnect, $sqlInsert);
        $lastInsertId = mysqli_insert_id($this->dbConnect);
        return $lastInsertId;
    }
    public function updateLastActivity($loginDetailsId) {
        $sqlUpdate = "
                UPDATE ".$this->chatLoginDetailsTable."
                SET last_activity = now()
                WHERE id = '".$loginDetailsId."'";
        mysqli_query($this->dbConnect, $sqlUpdate);
    }
    public function getUserLastActivity($userId) {
        $sqlQuery = "
                SELECT last_activity FROM ".$this->chatLoginDetailsTable."
                WHERE userid = '$userId' ORDER BY last_activity DESC LIMIT 1";
        $result =  $this->getData($sqlQuery);
        foreach($result as $row) {
                return $row['last_activity'];
        }
    }
}
?>

