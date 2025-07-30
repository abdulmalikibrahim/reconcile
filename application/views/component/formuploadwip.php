<div class="modal fade" id="modal-upload-wip" aria-hidden="true" aria-labelledby="modal-upload-wipLabel" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload File WIP</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url("upload_wip") ?>" method="post" id="form-upload-wip">
                    <div class="input-group">
                        <input class="form-control" type="file" id="upload-wip" name="upload-wip" accept=".xlsx,.xls">
                    </div>
					<p class="mt-2">Download template <a href="<?= base_url("assets/templates/template_wip.xlsx") ?>" target="_blank">disini</a></p>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btn-upload-wip">Upload Now</button>
            </div>
        </div>
    </div>
</div>
