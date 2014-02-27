<?php
//connect to db
$db_resource = mysql_connect();
//if it doesn't connect, BAIL BAIL BAIL!!
if (!$db_resource) { echo '<strong>Warning: Could not connect to MySQL database.</strong>'; exit;}
echo 'Database connection made...<br>';
$select = mysql_select_db('tabularasa');
echo 'Database chosen...<br>';
if (!$select) { echo '<strong>Warning: Could not choose correct database.</strong>'; exit;}
//$query = 'CREATE TABLE boardlist (ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY, Name CHAR(50), Description VARCHAR(100), Rights TINYINT, Posts INT, Topics INT, Latest INT, Owner INT, Members TEXT, Style CHAR(20), Priority TINYINT);';
//$query = 'INSERT INTO boardlist (Name) VALUES ("Announcements");';
//$query = 'CREATE TABLE topiclist (ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY, BoardID INT, Title VARCHAR(60), Posts SMALLINT, Latest SMALLINT, Creator CHAR(20), Status TINYINT);';
//$query = 'INSERT INTO topiclist (BoardID, Title, Posts, Latest, Creator) VALUES (1, "Last test.", 1, 1204300, "Kyle");';
//$query = 'CREATE TABLE messagelist (ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY, TopicID INT, MessageNumber SMALLINT, PosterName CHAR(20), PosterID INT, PostTime INT, Visibility TINYINT, MessageText TEXT);';
//$query = 'INSERT INTO messagelist (TopicID, MessageNumber, PosterName, PosterID, PostTime , MessageText) VALUES (3, 1, "Kyle", 1, 8532458, "For serious this time.");';
//$query = 'CREATE TABLE categories (ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY, Name VARCHAR(20));';
//$query = 'INSERT INTO userlist (Username, UserLevel, Password, Email) VALUES ("Kyle", 99, "841f7d45bb4dea7396b787f99ee53d48aa98cfa7", "k.j.wilcox@gmail.com");';
//$query = 'CREATE TABLE reports (ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY, MessageID INT, Reason TINYINT, Reporter INT);';
//$query = 'CREATE TABLE pms (ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY, SenderID INT, SenderName VARCHAR(20), RecipientID INT, RecipientName VARCHAR(20), SendDate INT, Subject VARCHAR(50), Text TEXT, Status TINYINT);';




echo $query, '<br>';

$results = mysql_query($query);
echo $results;
print mysql_error();
?>
