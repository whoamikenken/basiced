<?php 
/**
 * @author Kennedy Hipolito
 * @copyright Bente-Bente
 * @copyright Coffee + Memes = Creativity ^_^
 */
?>	

<style type="text/css">
.btn-success:active, .btn-success.active, .open > .dropdown-toggle.btn-success {
    border: solid 2px #337ab7;
}

.btn-danger:active, .btn-danger.active, .open > .dropdown-toggle.btn-danger {
    border: solid 2px #337ab7;
}
</style>
<div class="col-md-12">
	<form>
		<?php foreach (explode("/", substr($record[0]["questions"], 1)) as $key => $value): 
			$info = explode("*", $value);
			// echo "<pre>";print_r(explode("/", substr($record[0]["questions"], 1)));die;
			?>
			<?php if ($info[1] == "TEXT"){ ?>
				<div class="form-group">
					<label><?= $info[0] ?></label>
					<input type="text" class="form-control" type="<?= $info[1] ?>">
				</div>
			<?php }elseif ($info[1] == "TIME") { ?>
				<div class="form-group">
					<label><?= $info[0] ?></label>
					<div class='input-group time'>
	                    <input type='text' class="form-control" type="<?= $info[1] ?>" />
	                    <span class="input-group-addon">
	                        <span class="glyphicon glyphicon-time"></span>
	                    </span>
	                </div>
				</div>
			<?php }elseif ($info[1] == "DATE") { ?>
				<div class="form-group">
					<label><?= $info[0] ?></label>
					<div class='input-group date'>
	                    <input type='text' class="form-control" type="<?= $info[1] ?>"/>
	                    <span class="input-group-addon">
	                        <span class="glyphicon glyphicon-calendar"></span>
	                    </span>
	                </div>
				</div>
			<?php }elseif ($info[1] == "NUMBER") { ?>
				<div class="form-group">
					<label><?= $info[0] ?></label>
					<input type="number" class="form-control" type="<?= $info[1] ?>">
				</div>
			<?php }elseif ($info[1] == "YN") { ?>
				<div class="form-group">
					<label><?= $info[0] ?></label><br>
					<div data-toggle="buttons">
						<label class="btn btn-success">
						<input type="radio" type="<?= $info[1] ?>" val="Yes" autocomplete="off"> Yes
						</label>
						<label class="btn btn-danger">
						<input type="radio" type="<?= $info[1] ?>" val="No" autocomplete="off"> No
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