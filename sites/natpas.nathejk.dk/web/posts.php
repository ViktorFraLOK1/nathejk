<?php

$query = new Nathejk_Post;
$query->columnIn('typeName', array('post', 'post1', 'post2', 'oplev', 'start', 'slut'));
$posts = $query->findAll();

$query = new Nathejk_Post;
//$query->typeName = 'post';
$query->id = isset($_GET['postId']) ? $_GET['postId'] : 0;
$post = $query->findOne();

$PAGE = new Nathejk_Page();
$PAGE->assign('activePost', true);
$PAGE->assign('posts', $posts);
$PAGE->assign('post', $post);
$PAGE->display();
?>
