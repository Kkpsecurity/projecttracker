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

                                    $categoryLabels = [
                                        'map_screenshot' => 'Site Map / Diagram (Screenshot)',
                                        'page_3' => 'Page 3 (8 Slots)',
                                    ];

                                    // These are system-generated/internal and should not be user-uploaded.
                                    $categories = array_values(array_diff($categories, [
                                        'generated_report',
                                        'report_template',
                                        'report_example',
                                    ]));
                                @endphp

                                @foreach($categories as $category)
                                    <option value="{{ $category }}">
                                        {{ $categoryLabels[$category] ?? ucwords(str_replace(['_', '-'], ' ', $category)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group" id="filePositionGroup" style="display:none;">
                            {{-- Backend uses a single `file_position` field. UI uses two dropdowns and writes into this hidden input. --}}
                            <input type="hidden" id="file_position" name="file_position" value="">

                            <div id="appendixPositionWrap" style="display:none;">
                                <label for="appendix_position_select">Appendix Slot <span class="text-danger" id="appendixPositionRequired" style="display:none;">*</span></label>
                                <select class="form-control" id="appendix_position_select" disabled>
                                    <option value="" selected>Select a slot...</option>
                                    @php
                                        $positions = (array) config('hb837.file_positions', []);
                                        $positions = array_values(array_unique($positions));
                                    @endphp
                                    @foreach($positions as $pos)
                                        @if (str_starts_with($pos, 'appendix_a_'))
                                            <option value="{{ $pos }}">{{ ucwords(str_replace(['_', '-'], ' ', $pos)) }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div id="photoPositionWrap" style="display:none;">
                                <label for="photo_position_select">Photo Slot <span class="text-danger" id="photoPositionRequired" style="display:none;">*</span></label>
                                <select class="form-control" id="photo_position_select" disabled>
                                    <option value="" selected>Select a slot...</option>
                                    @foreach($positions as $pos)
                                        @if (str_starts_with($pos, 'appendix_b_'))
                                            <option value="{{ $pos }}">{{ ucwords(str_replace(['_', '-'], ' ', $pos)) }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div id="page3PositionWrap" style="display:none;">
                                <label for="page3_position_select">Page 3 Slot <span class="text-danger" id="page3PositionRequired" style="display:none;">*</span></label>
                                <select class="form-control" id="page3_position_select" disabled>
                                    <option value="" selected>Select a slot...</option>
                                    @for ($i = 1; $i <= 8; $i++)
                                        <option value="page_3_slot_{{ $i }}">Slot {{ $i }}</option>
                                    @endfor
                                </select>
                                <small class="form-text text-muted">Slots map to positions <strong>page_3_slot_1</strong> through <strong>page_3_slot_8</strong>.</small>
                            </div>
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
            var isPhotoPage = (category === 'photo');
            var isPage3 = (category === 'page_3');
            var isCrimeReport = (category === 'crime_report');
            var isSlotBased = (isAppendix || isPhotoPage || isPage3);

            var $group = $('#filePositionGroup');
            var $hidden = $('#file_position');

            var $appendixWrap = $('#appendixPositionWrap');
            var $appendixSelect = $('#appendix_position_select');
            var $appendixReq = $('#appendixPositionRequired');

            var $photoWrap = $('#photoPositionWrap');
            var $photoSelect = $('#photo_position_select');
            var $photoReq = $('#photoPositionRequired');

            var $page3Wrap = $('#page3PositionWrap');
            var $page3Select = $('#page3_position_select');
            var $page3Req = $('#page3PositionRequired');

            if (isSlotBased) {
                $group.show();

                // Clear hidden position whenever switching pages.
                $hidden.val('');

                if (isAppendix) {
                    $appendixWrap.show();
                    $appendixSelect.prop('disabled', false).prop('required', true);
                    $appendixReq.show();

                    $photoWrap.hide();
                    $photoSelect.val('').prop('disabled', true).prop('required', false);
                    $photoReq.hide();

                    $page3Wrap.hide();
                    $page3Select.val('').prop('disabled', true).prop('required', false);
                    $page3Req.hide();
                } else if (isPage3) {
                    $page3Wrap.show();
                    $page3Select.prop('disabled', false).prop('required', true);
                    $page3Req.show();

                    $appendixWrap.hide();
                    $appendixSelect.val('').prop('disabled', true).prop('required', false);
                    $appendixReq.hide();

                    $photoWrap.hide();
                    $photoSelect.val('').prop('disabled', true).prop('required', false);
                    $photoReq.hide();
                } else {
                    // Photo page
                    $photoWrap.show();
                    $photoSelect.prop('disabled', false).prop('required', true);
                    $photoReq.show();

                    $appendixWrap.hide();
                    $appendixSelect.val('').prop('disabled', true).prop('required', false);
                    $appendixReq.hide();

                    $page3Wrap.hide();
                    $page3Select.val('').prop('disabled', true).prop('required', false);
                    $page3Req.hide();
                }
            } else {
                // Hide and clear any previous selection.
                $hidden.val('');

                $appendixSelect.val('').prop('required', false).prop('disabled', true);
                $appendixReq.hide();
                $appendixWrap.hide();

                $photoSelect.val('').prop('required', false).prop('disabled', true);
                $photoReq.hide();
                $photoWrap.hide();

                $page3Select.val('').prop('required', false).prop('disabled', true);
                $page3Req.hide();
                $page3Wrap.hide();

                $group.hide();
            }

            // Make the difference explicit:
            // - Crime Report: PDF upload (extracted)
            // - Appendix: image upload (placed into Appendix slot)
            // - Photo: image upload (placed into Photo slot)
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
            } else if (isPage3) {
                $fileInput.attr('accept', 'image/*');
                $hint.text('Page 3: upload an image and choose a slot (1–8) to place it in the Page 3 layout.');
                $title.text('Upload Page 3 Slot Image');
            } else if (isPhotoPage) {
                $fileInput.attr('accept', 'image/*');
                $hint.text('Photos: upload an image and choose a slot to place it in the PDF.');
                $title.text('Upload Photo Image');
            } else {
                $fileInput.removeAttr('accept');
                $hint.text('');
                $title.text('Upload File');
            }
        }

        $(function() {
            $('#file_category').on('change', updateFilePositionVisibility);
            $('#uploadModal').on('shown.bs.modal', updateFilePositionVisibility);
            $('#appendix_position_select').on('change', function() {
                $('#file_position').val(($('#appendix_position_select').val() || '').toString());
            });
            $('#photo_position_select').on('change', function() {
                $('#file_position').val(($('#photo_position_select').val() || '').toString());
            });
            $('#page3_position_select').on('change', function() {
                $('#file_position').val(($('#page3_position_select').val() || '').toString());
            });
            updateFilePositionVisibility();
        });
    })();
</script>