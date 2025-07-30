<div class="modal fade" id="modal-upload-sloc" aria-hidden="true" aria-labelledby="modal-upload-slocLabel" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Master sloc</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url("update_sloc") ?>" method="post" id="form-upload-sloc" data-clear="no">
					<p class="mb-1">Dept ID</p>
					<select name="id_dept" id="id_dept" class="form-control mb-2">
						<?php
						foreach (LIST_SHOP as $key => $value) {
							echo '<option value="'.$key.'">'.$value.'</option>';
						}
						?>
					</select>
					<p class="mb-1">SLOC</p>
					<input type="text" name="input-sloc" id="input-sloc" class="form-control">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btn-simpan-sloc">Simpan</button>
            </div>
        </div>
    </div>
</div>
