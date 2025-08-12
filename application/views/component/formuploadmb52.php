<div class="modal fade" tabindex="-1" id="modal-upload-mb52">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload File MB52</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url("upload_mb52") ?>" method="post" id="form-upload-mb52" data-page="mb52">
                    <div class="input-group">
                        <input class="form-control" type="file" id="upload-mb52" name="upload-mb52" accept=".xlsx,.xls">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btn-upload-mb52">Upload Now</button>
            </div>
        </div>
    </div>
</div>
