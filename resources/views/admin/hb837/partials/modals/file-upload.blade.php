 <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">Upload File</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="fileUploadForm" action="{{ route('admin.hb837.files.upload', $hb837->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="file">Select File</label>
                            <input type="file" class="form-control-file" id="file" name="file" required>
                            <small class="form-text text-muted" id="fileTypeHint"></small>
                        </div>
                        <div class="form-group">
                            <label for="file_category">Page</label>
                            <select class="form-control" id="file_category" name="file_category" required>
                                <option value="" selected disabled>Select a page...</option>
                                @php
                                    $categories = (array) config('hb837.file_categories', []);
                                    $categories = array_values(array_unique($categories));

                                    // These are system-generated/internal and should not be user-uploaded.
                                    $categories = array_values(array_diff($categories, [
                                        'generated_report',
                                        'report_template',
                                        'report_example',
                                    ]));
                                @endphp

                                @foreach($categories as $category)
                                    <option value="{{ $category }}">
                                        {{ ucwords(str_replace(['_', '-'], ' ', $category)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group" id="filePositionGroup" style="display:none;">
                            <label for="file_position">Appendix Slot <span class="text-danger" id="filePositionRequired" style="display:none;">*</span></label>
                            <select class="form-control" id="file_position" name="file_position" disabled>
                                <option value="" id="filePositionPlaceholder" selected>Select a slot...</option>
                                @php
                                    $positions = (array) config('hb837.file_positions', []);
                                    $positions = array_values(array_unique($positions));
                                @endphp
                                @foreach($positions as $pos)
                                    <option value="{{ $pos }}">{{ ucwords(str_replace(['_', '-'], ' ', $pos)) }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">
                                Use a position to automatically place images in the PDF (e.g., Appendix A map/photos).
                            </small>
                        </div>
                        <div class="form-group">
                            <label for="description">Description (Optional)</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" form="fileUploadForm" class="btn btn-primary">Upload File</button>
                </div>
            </div>
        </div>
    </div>

<script>
    (function initFileUploadSlotUI(attempt) {
        attempt = attempt || 0;

        // This partial is rendered in-page; depending on layout ordering, jQuery may load after it.
        // Avoid referencing `$` until jQuery exists to prevent "ReferenceError: $ is not defined".
        if (!window.jQuery) {
            if (attempt < 100) {
                return window.setTimeout(function() { initFileUploadSlotUI(attempt + 1); }, 50);
            }
            return; // Give up quietly if jQuery never loads.
        }

        var $ = window.jQuery;

        function updateFilePositionVisibility() {
            var category = ($('#file_category').val() || '').toString();
            var isAppendix = (category === 'appendix');
            var isCrimeReport = (category === 'crime_report');

            var $group = $('#filePositionGroup');
            var $select = $('#file_position');
            var $req = $('#filePositionRequired');
            var $placeholder = $('#filePositionPlaceholder');

            if (isAppendix) {
                $group.show();
                $select.prop('disabled', false);
                $select.prop('required', true);
                $req.show();

                // Keep placeholder but make it unusable so browser required validation triggers.
                $placeholder.prop('disabled', true);
            } else {
                // Hide and clear any previous selection.
                $select.val('');
                $select.prop('required', false);
                $select.prop('disabled', true);
                $req.hide();
                $placeholder.prop('disabled', false);
                $group.hide();
            }

            // Make the difference explicit:
            // - Crime Report: PDF upload (extracted)
            // - Appendix: image upload (placed into slot)
            var $fileInput = $('#file');
            var $hint = $('#fileTypeHint');
            var $title = $('#uploadModalLabel');

            if (isCrimeReport) {
                $fileInput.attr('accept', 'application/pdf,.pdf');
                $hint.text('Crime Report: upload the PDF to extract crime stats.');
                $title.text('Upload Crime Report (PDF)');
            } else if (isAppendix) {
                $fileInput.attr('accept', 'image/*');
                $hint.text('Appendix: upload an image and choose a slot to place it in the PDF.');
                $title.text('Upload Appendix Image');
            } else {
                $fileInput.removeAttr('accept');
                $hint.text('');
                $title.text('Upload File');
            }
        }

        $(function() {
            $('#file_category').on('change', updateFilePositionVisibility);
            $('#uploadModal').on('shown.bs.modal', updateFilePositionVisibility);
            updateFilePositionVisibility();
        });
    })();
</script>