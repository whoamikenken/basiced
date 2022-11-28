<?
/**
* @author justin (with e)
* @copyright 2018
*/

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$count = 1;
foreach ($list as $info):
	if($count > 9) break;
	extract($info);
?>
<tr>
	<td><?=$date?></td>
	<td><?=$time?></td>
	<td><?=$name?></td>
	<td><?=$type?></td>
	<td><?=$user?></td>
</tr>
<?
	$count += 1;
endforeach;

if(!count($list)):
?>
<tr>
	<td colspan=5>No data available.</td>
</tr>
<?
endif;
?>
