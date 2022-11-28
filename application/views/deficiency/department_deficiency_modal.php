<form id="info_form">
    <input type="hidden" id="deptCode" value="<?=$id;?>" />
    <div class="form_row" style="margin-bottom: 0px; margin-top: 20px;">
      <label class="field_name align_right">Concerned Office</label>
      <div class="field">
        <div class="col-md-12">
          <select class="chosen" id="deptidx">
            <option value="">Select Deparment <?= isset($deptid) ? $deptid : ''; ?></option>
            <?php foreach ($departmentlist as $value): ?>
              <option value="<?= $value['code'] ?>" <?= ($deptid === $value['code']) ? 'selected' : '' ?>><?= $value['description'] ?></option>
            <?php endforeach ?>
          </select>  
        </div>
      </div>
    </div>
</form>
<script type="text/javascript">
  $(".chosen").chosen();
</script>