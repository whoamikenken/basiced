<?php 
/**
 * @author Kennedy Hipolito
 * @copyright Bente-Bente
 * @copyright Coffee + Memes = Creativity ^_^
 */
?>	

<style type="text/css">
.btn-success:active, .btn-success.active, .open > .dropdown-toggle.btn-success {
    border: solid 4px #337ab7;
}

.btn-danger:active, .btn-danger.active, .open > .dropdown-toggle.btn-danger {
    border: solid 4px #337ab7;
}
</style>
<div class="col-md-12">
	<form>
		<?php foreach (explode("/", substr($record[0]["questions"], 1)) as $key => $value): 
			$info = explode("*", $value);
			?>
			<?php if ($info[0] == "TEXT"){ ?>
				<div class="form-group">
					<label><?= $info[2] ?></label>
					<input type="text" class="form-control" value="<?= $info[1] ?>">
				</div>
			<?php }elseif ($info[0] == "TIME") { ?>
				<div class="form-group">
					<label><?= $info[2] ?></label>
					<div class='input-group time'>
	                    <input type='text' class="form-control" value="<?= $info[1] ?>"/>
	                    <span class="input-group-addon">
	                        <span class="glyphicon glyphicon-time"></span>
	                    </span>
	                </div>
				</div>
			<?php }elseif ($info[0] == "DATE") { ?>
				<div class="form-group">
					<label><?= $info[2] ?></label>
					<div class='input-group date'>
	                    <input type='text' class="form-control" value="<?= $info[1] ?>"/>
	                    <span class="input-group-addon">
	                        <span class="glyphicon glyphicon-calendar"></span>
	                    </span>
	                </div>
				</div>
			<?php }elseif ($info[0] == "NUMBER") { ?>
				<div class="form-group">
					<label><?= $info[2] ?></label>
					<input type="number" class="form-control" value="<?= $info[1] ?>">
				</div>
			<?php }elseif ($info[0] == "YN") { ?>
				<div class="form-group">
					<label><?= $info[2] ?></label><br>
					<div data-toggle="buttons">
						<label class="btn btn-success <?= ($info[1] == "Yes")? 'active':'' ?>">
						<input type="radio" val="Yes" autocomplete="off"> Yes
						</label>
						<label class="btn btn-danger <?= ($info[1] == "No")? 'active':'' ?>">
						<input type="radio" val="No" autocomplete="off"> No
						</label>
					</div>
				</div>
			<?php } ?>
		<?php endforeach ?>
	</form>
</div>

<script type="text/javascript">
	var count = 1;
	$(document).ready(function(){
		$('.time').datetimepicker({
            format: 'LT'
        });

        $(".date").datetimepicker({
		    format: "YYYY-MM-DD"
		});
	});
</script>