<?php 
 
$name = $record['0']->name;
$image = $record['0']->content;
$id = $record['0']->title;

?>


<h5><?= $name ?></h5>
<br>
<h4><?= $id ?></h4>
<br>
<?php echo '<img src="data:image/jpeg;base64,'.base64_encode($image).'"/>'; ?>