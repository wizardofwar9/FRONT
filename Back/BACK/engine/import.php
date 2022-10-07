<?php require('./config.php'); $apiKey="VOTRE_CLE_API" ; $categories=['technology', 'sports' , 'science' , 'business'
, 'entertainment' ]; foreach ($categories as $category) {
$endpoint=&quot;https://newsapi.org/v2/top-headlines?category=$category&amp;pageSize=100&amp;country=fr&amp;apiKey=$apiKey&quot;;
$response=file_get_contents($endpoint); $response=json_decode($response); var_dump($response); foreach
($response-&gt;articles as $article) { $q=$db-&gt;prepare('INSERT INTO articles (title, author, content,
description, imageUrl, publishedAt) VALUES (:title, :author, :content, :description, :imageUrl, :publishedAt)');
$q-&gt;bindValue('title', $article-&gt;title); $q-&gt;bindValue('author', $article-&gt;author);
$q-&gt;bindValue('content', $article-&gt;content); $q-&gt;bindValue('description', $article-&gt;description);
$q-&gt;bindValue('imageUrl', $article-&gt;urlToImage); $q-&gt;bindValue('publishedAt', date(&quot;Y-m-d H:i:s&quot;,
strtotime($article-&gt;publishedAt))); $q-&gt;execute(); } }