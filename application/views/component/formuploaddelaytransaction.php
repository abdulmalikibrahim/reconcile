<div class="modal fade" id="modal-upload-delaytransaction" aria-hidden="true" aria-labelledby="modal-upload-delaytransactionLabel" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload File Delay Transaction</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url("upload_delaytransaction") ?>" method="post" id="form-upload-delaytransaction" data-page="delaytransaction">
                    <div class="input-group">
                        <input class="form-control" type="file" id="upload-delaytransaction" name="upload-delaytransaction" accept=".xlsx,.xls">
                    </div>
					<p class="mt-2">Download template <a href="<?= base_url("assets/templates/template_delay_transaction.xlsx") ?>" target="_blank">disini</a></p>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btn-upload-delaytransaction">Upload Now</button>
            </div>
        </div>
    </div>
</div>
