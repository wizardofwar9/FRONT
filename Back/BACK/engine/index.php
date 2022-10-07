<?php
require('./config.php');
use TeamTNTTNTSearchTNTSearch;

$articles = [];
if (!empty($_GET['q'])) {

$tnt = new TNTSearch;

$tnt-&gt;loadConfig([
'driver' =&gt; 'mysql',
'host' =&gt; 'localhost',
'database' =&gt; 'search_engine',
'username' =&gt; 'root',
'password' =&gt; 'root',
'storage' =&gt; '.',
'stemmer' =&gt; TeamTNTTNTSearchStemmerPorterStemmer::class
]);
$tnt-&gt;selectIndex(&quot;articles.index&quot;);

$searchResult = $tnt-&gt;search($_GET['q'], 10);
$ids = implode(&quot;, &quot;, $searchResult['ids']);

$q = $db-&gt;query(&quot;SELECT * FROM articles WHERE id IN ($ids) ORDER BY FIELD(id, $ids)&quot;);


$q = $db-&gt;query(&quot;SELECT * FROM articles WHERE CONCAT(title, content) LIKE '%&quot; . $_GET['q'] .
&quot;%'&quot;);
$articles = $q-&gt;fetchAll(PDO::FETCH_ASSOC);
}
?&gt;
&lt;!DOCTYPE html&gt;
&lt;html&gt;
&lt;head&gt;
&lt;meta charset=&quot;utf-8&quot;&gt;
&lt;meta http-equiv=&quot;X-UA-Compatible&quot; content=&quot;IE=edge&quot;&gt;
&lt;title&gt;Recherche&lt;/title&gt;
&lt;meta name=&quot;viewport&quot; content=&quot;width=device-width, initial-scale=1&quot;&gt;
&lt;/head&gt;
&lt;body&gt;

&lt;form method=&quot;GET&quot;&gt;
&lt;input type=&quot;search&quot; placeholder=&quot;Rechercher...&quot; name=&quot;q&quot;&gt;
&lt;button type=&quot;submit&quot;&gt;OK&lt;/button&gt;
&lt;/form&gt;

&lt;?php if ($articles): ?&gt;
&lt;h2&gt;
R&eacute;sultats&lt;br&gt;
&lt;small&gt;&lt;?= $searchResult['hits'] ?&gt; r&eacute;sultats en &lt;?= $searchResult['execution_time']
?&gt;&lt;/small&gt;
&lt;/h2&gt;

&lt;ul&gt;
&lt;?php foreach ($articles as $article): ?&gt;
&lt;li&gt;
&lt;h3&gt;[#&lt;?= $article['id'] ?&gt;] &lt;?= $article['title'] ?&gt;&lt;/h3&gt;
&lt;?= $article['content'] ?&gt;
&lt;/li&gt;
&lt;?php endforeach ?&gt;
&lt;/ul&gt;
&lt;?php endif ?&gt;

&lt;/body&gt;
&lt;/html&gt;