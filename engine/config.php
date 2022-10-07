&lt;?php
require('./vendor/autoload.php');

/**
* SQL permettant de cr&eacute;er la table &quot;articles&quot; :
* create table articles (id int primary key auto_increment, title varchar(255), author varchar(255), content text,
description text, imageUrl text, publishedAt datetime);
*/

try {
$db = new PDO(&quot;mysql:host=localhost;dbname=search_engine&quot;, 'root', 'root');
$db-&gt;setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
echo &quot;Connection failed: &quot; . $e-&gt;getMessage();
}