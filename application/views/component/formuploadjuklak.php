<div class="modal fade" id="modal-upload-juklak" aria-hidden="true" aria-labelledby="modal-upload-juklakLabel" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Master Juklak</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url("upload_juklak") ?>" method="post" id="form-upload-juklak" data-clear="no" data-page="juklak">
                    <div class="input-group">
                        <input class="form-control" type="file" id="upload-juklak" name="upload-juklak" accept=".xlsx,.xls">
                    </div>
					<p class="mb-1">Download template juklak <a href="<?= base_url("assets/templates/Master Juklak.xlsx"); ?>" target="_blank">disini</a></p>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btn-upload-juklak">Upload Now</button>
                <button type="button" class="btn btn-info" id="btn-upload-juklak-clear">Clear & Upload</button>
            </div>
        </div>
    </div>
</div>
