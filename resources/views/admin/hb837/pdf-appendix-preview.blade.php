<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>HB 837 Photos - {{ $hb837->property_name }}</title>
    <style>
        @page {
            margin: 40px 40px;
        }

        body {
            font-family: "Arial", "Helvetica", sans-serif;
            font-size: 11px;
            line-height: 1.25;
            color: #333;
        }

        h1,
        h2,
        h3,
        h4 {
            margin: 0;
            padding: 0;
            font-weight: bold;
        }

        .section-title {
            font-size: 13px;
            margin: 0 0 8px 0;
        }

        .small {
            font-size: 10px;
            color: #6c757d;
        }

        .img-placeholder {
            border: 1px solid #dee2e6;
            padding: 18px;
            text-align: center;
            margin: 6px 0 10px 0;
            page-break-inside: avoid;
        }

        .img-placeholder .ph-title {
            font-weight: bold;
            margin-bottom: 6px;
        }

        .avoid-break {
            page-break-inside: avoid;
        }

        .pdf-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .photo-img {
            display: block;
            width: 100%;
            max-width: 100%;
            border: 1px solid #999;
        }

        .photo-small {
            height: 140px;
            object-fit: cover;
        }

        /* Page 3 (T2) layout styles */
        .t2-caption {
            margin-top: 6px;
        }

        .t2-rule {
            border-top: 1px solid #444;
            margin: 16px 0 12px 0;
        }

        .t2-text {
            font-size: 10px;
            line-height: 1.35;
        }

        .t2-text .title {
            font-weight: bold;
            margin-bottom: 6px;
        }
    </style>
</head>

<body>
    @php
        $filesByPosition = ($hb837->relationLoaded('files') ? $hb837->files : $hb837->files()->get())
            ->whereNotNull('file_position')
            ->keyBy('file_position');

        $appendixMap = $filesByPosition->get('appendix_a_map');
        $appendixPhoto1 = $filesByPosition->get('appendix_a_photo_1');
        $appendixPhoto2 = $filesByPosition->get('appendix_a_photo_2');
        $appendixPhoto3 = $filesByPosition->get('appendix_a_photo_3');

        $appendixBPhotos = [];
        for ($i = 1; $i <= 13; $i++) {
            $appendixBPhotos[$i] = $filesByPosition->get('appendix_b_photo_' . $i);
        }

        $fileImgSrc = function ($file) {
            if (!$file || empty($file->file_path)) {
                return null;
            }

            $relative = ltrim((string) $file->file_path, '/');

            // Prefer the real storage path (works even if public/storage symlink is missing).
            $fsPath = storage_path('app/public/' . $relative);
            if (!is_file($fsPath)) {
                $fsPath = public_path('storage/' . $relative);
            }

            if (!is_file($fsPath)) {
                return null;
            }

            // Most reliable approach for DomPDF across environments: embed local images
            // as base64 data URIs. This avoids Windows file:// quirks and chroot issues.
            try {
                $bytes = @file_get_contents($fsPath);
                if ($bytes !== false && $bytes !== '') {
                    $mime = null;

                    if (function_exists('mime_content_type')) {
                        $mime = @mime_content_type($fsPath) ?: null;
                    }

                    if (!$mime) {
                        $ext = strtolower(pathinfo($fsPath, PATHINFO_EXTENSION));
                        $mime = match ($ext) {
                            'jpg', 'jpeg' => 'image/jpeg',
                            'png' => 'image/png',
                            'gif' => 'image/gif',
                            'webp' => 'image/webp',
                            default => 'image/jpeg',
                        };
                    }

                    return 'data:' . $mime . ';base64,' . base64_encode($bytes);
                }
            } catch (\Throwable $e) {
                // Fall through to file path attempt.
            }

            // DomPDF on Windows is most reliable with a file:// URI, but filenames
            // with spaces must be URL-encoded.
            $path = str_replace('\\', '/', $fsPath);

            if (preg_match('/^[A-Za-z]:\//', $path)) {
                // Convert to file:///C:/... and encode segments after the drive.
                $drivePrefix = substr($path, 0, 3); // e.g. C:/
                $rest = substr($path, 3);
                $rest = ltrim($rest, '/');
                $segments = $rest === '' ? [] : explode('/', $rest);
                $encodedRest = implode('/', array_map('rawurlencode', $segments));

                return 'file:///' . $drivePrefix . $encodedRest;
            }

            // Non-Windows absolute path: encode each segment.
            $segments = explode('/', ltrim($path, '/'));
            return '/' . implode('/', array_map('rawurlencode', $segments));
        };
    @endphp

    <h2 class="section-title">Appendix A. Photos</h2>
    <div class="small" style="margin-bottom: 10px;">
        {{ $hb837->property_name }} &nbsp;|&nbsp; Generated {{ $generated_at ?? '' }}
    </div>

    <table class="pdf-table">
        <tr>
            <td style="width: 34%; vertical-align: top; padding-right: 10px;">
                <div class="small" style="line-height: 1.35;">
                    <div style="font-weight: bold; margin-bottom: 6px;">Map Notes</div>
                    @if ($appendixMap && $appendixMap->description)
                        {!! nl2br(e($appendixMap->description)) !!}
                    @else
                        <span class="small">(Optional) Add notes/labels in the file description for the map upload.</span>
                    @endif
                </div>
            </td>
            <td style="width: 66%; vertical-align: top;">
                @php $mapSrc = $fileImgSrc($appendixMap); @endphp
                @if ($mapSrc)
                    <img class="photo-img" src="{{ $mapSrc }}" alt="Appendix map" style="height: auto;">
                @else
                    <div class="img-placeholder" style="height: 320px;">
                        <div class="ph-title">[MAP IMAGE – upload with position Appendix A Map]</div>
                        <div class="small">Position: appendix_a_map</div>
                    </div>
                @endif
            </td>
        </tr>
    </table>

    <table class="pdf-table" style="margin-top: 14px;">
        <tr>
            @foreach ([$appendixPhoto1, $appendixPhoto2, $appendixPhoto3] as $i => $photo)
                <td style="width: 33.33%; vertical-align: top; padding-right: {{ $i < 2 ? '10px' : '0' }};">
                    @php $photoSrc = $fileImgSrc($photo); @endphp
                    @if ($photoSrc)
                        <img class="photo-img" src="{{ $photoSrc }}" alt="Appendix photo {{ $i + 1 }}" style="height: 140px; object-fit: cover;">
                    @else
                        <div class="img-placeholder" style="height: 140px;">
                            <div class="ph-title">[PHOTO {{ $i + 1 }} – upload with position]</div>
                            <div class="small">Position: appendix_a_photo_{{ $i + 1 }}</div>
                        </div>
                    @endif

                    @if ($photo && $photo->description)
                        <div class="small" style="margin-top: 6px;">{!! nl2br(e($photo->description)) !!}</div>
                    @endif
                </td>
            @endforeach
        </tr>
    </table>

    <div style="page-break-before: always;"></div>

    <h2 class="section-title">Photos</h2>
    <div class="small" style="margin-bottom: 10px;">
        {{ $hb837->property_name }} &nbsp;|&nbsp; Generated {{ $generated_at ?? '' }}
    </div>

    @php
        $topNotesText = null;
        for ($i = 1; $i <= 4; $i++) {
            if (!empty($appendixBPhotos[$i]?->description)) {
                $topNotesText = $appendixBPhotos[$i]->description;
                break;
            }
        }
    @endphp

    {{-- Top block with left notes column + 2x2 photo grid (Photos 1-4) --}}
    <table class="pdf-table">
        <tr>
            <td style="width: 34%; vertical-align: top; padding-right: 10px;">
                <div class="small" style="line-height: 1.35;">
                    <div style="font-weight: bold; margin-bottom: 6px;">Photo Notes</div>
                    @if ($topNotesText)
                        <div class="small">{!! nl2br(e($topNotesText)) !!}</div>
                    @else
                        <span class="small">(Optional) Add notes in the description of Photo 1–4.</span>
                    @endif
                </div>
            </td>
            <td style="width: 66%; vertical-align: top;">
                <table class="pdf-table">
                    <tr>
                        @for ($i = 1; $i <= 2; $i++)
                            @php $photo = $appendixBPhotos[$i] ?? null; $src = $fileImgSrc($photo); @endphp
                            <td style="width: 50%; vertical-align: top; padding-right: {{ $i === 1 ? '10px' : '0' }};">
                                @if ($src)
                                    <img class="photo-img" src="{{ $src }}" alt="Appendix B photo {{ $i }}" style="height: 155px; object-fit: cover;">
                                @else
                                    <div class="img-placeholder" style="height: 155px;">
                                        <div class="ph-title">[PHOTO – upload with position]</div>
                                        <div class="small">Position: appendix_b_photo_{{ $i }}</div>
                                    </div>
                                @endif
                            </td>
                        @endfor
                    </tr>
                    <tr>
                        @for ($i = 3; $i <= 4; $i++)
                            @php $photo = $appendixBPhotos[$i] ?? null; $src = $fileImgSrc($photo); @endphp
                            <td style="width: 50%; vertical-align: top; padding-right: {{ $i === 3 ? '10px' : '0' }};">
                                @if ($src)
                                    <img class="photo-img" src="{{ $src }}" alt="Appendix B photo {{ $i }}" style="height: 155px; object-fit: cover;">
                                @else
                                    <div class="img-placeholder" style="height: 155px;">
                                        <div class="ph-title">[PHOTO – upload with position]</div>
                                        <div class="small">Position: appendix_b_photo_{{ $i }}</div>
                                    </div>
                                @endif
                            </td>
                        @endfor
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Rows of 3 (Photos 5-13) --}}
    @for ($rowStart = 5; $rowStart <= 13; $rowStart += 3)
        @if ($rowStart === 11)
            @php
                $notesText = null;
                if (!empty($appendixBPhotos[11]?->description)) {
                    $notesText = $appendixBPhotos[11]->description;
                } elseif (!empty($appendixBPhotos[12]?->description)) {
                    $notesText = $appendixBPhotos[12]->description;
                }

                $photo11 = $appendixBPhotos[11] ?? null;
                $photo12 = $appendixBPhotos[12] ?? null;
                $src11 = $fileImgSrc($photo11);
                $src12 = $fileImgSrc($photo12);
            @endphp

            <table class="pdf-table" style="margin-top: 10px;">
                <tr>
                    <td style="width: 33.33%; vertical-align: top; padding-right: 10px;">
                        <div class="img-placeholder" style="height: 140px; padding: 12px; text-align: left;">
                            <div class="ph-title">Notes</div>
                            @if ($notesText)
                                <div class="small">{!! nl2br(e($notesText)) !!}</div>
                            @else
                                <div class="small">Add the notes for this row in the description of Photo 11 or Photo 12.</div>
                            @endif
                        </div>
                    </td>

                    <td style="width: 33.33%; vertical-align: top; padding-right: 10px;">
                        @if ($src11)
                            <img class="photo-img photo-small" src="{{ $src11 }}" alt="Appendix B photo 11">
                        @else
                            <div class="img-placeholder" style="height: 140px;">
                                <div class="ph-title">[PHOTO – upload with position]</div>
                                <div class="small">Position: appendix_b_photo_11</div>
                            </div>
                        @endif
                    </td>

                    <td style="width: 33.33%; vertical-align: top;">
                        @if ($src12)
                            <img class="photo-img photo-small" src="{{ $src12 }}" alt="Appendix B photo 12">
                        @else
                            <div class="img-placeholder" style="height: 140px;">
                                <div class="ph-title">[PHOTO – upload with position]</div>
                                <div class="small">Position: appendix_b_photo_12</div>
                            </div>
                        @endif
                    </td>
                </tr>
            </table>
        @else
            <table class="pdf-table" style="margin-top: 10px;">
                <tr>
                    @for ($i = $rowStart; $i <= min($rowStart + 2, 13); $i++)
                        @php $photo = $appendixBPhotos[$i] ?? null; $src = $fileImgSrc($photo); @endphp
                        <td style="width: 33.33%; vertical-align: top; padding-right: {{ $i < min($rowStart + 2, 13) ? '10px' : '0' }};">
                            @if ($src)
                                <img class="photo-img photo-small" src="{{ $src }}" alt="Appendix B photo {{ $i }}">
                            @else
                                <div class="img-placeholder" style="height: 140px;">
                                    <div class="ph-title">[PHOTO – upload with position]</div>
                                    <div class="small">Position: appendix_b_photo_{{ $i }}</div>
                                </div>
                            @endif

                            @if ($photo && $photo->description)
                                <div class="small" style="margin-top: 6px;">{!! nl2br(e($photo->description)) !!}</div>
                            @endif
                        </td>
                    @endfor
                </tr>
            </table>
        @endif
    @endfor

    {{-- Page 3 (T2) 8-slot layout --}}
    <div style="page-break-before: always;"></div>

    @php
        $page3 = [];
        for ($i = 1; $i <= 8; $i++) {
            $page3[$i] = $filesByPosition->get('page_3_slot_' . $i);
        }

        $t2RenderImage = function ($file, string $positionLabel, array $style = []) use ($fileImgSrc) {
            $src = $fileImgSrc($file);
            $height = $style['height'] ?? null;
            $fit = $style['fit'] ?? 'cover';

            if ($src) {
                $heightCss = $height ? 'height: ' . $height . ';' : 'height: auto;';
                return '<img class="photo-img" src="' . e($src) . '" alt="' . e($positionLabel) . '" style="' . $heightCss . ' object-fit: ' . e($fit) . ';">';
            }

            $phHeight = $height ? $height : '160px';
            return '<div class="img-placeholder" style="height: ' . e($phHeight) . ';">'
                . '<div class="ph-title">[IMAGE SLOT]</div>'
                . '<div class="small">Position: ' . e($positionLabel) . '</div>'
                . '</div>';
        };

        $t2CaptionHtml = function ($file, string $fallback) {
            $text = $file?->description;
            if (!$text) {
                return '<div class="small">' . e($fallback) . '</div>';
            }
            return '<div class="small">' . nl2br(e($text)) . '</div>';
        };
    @endphp

    <h2 class="section-title">Page 3</h2>
    <div class="small" style="margin-bottom: 10px;">
        {{ $hb837->property_name }} &nbsp;|&nbsp; Generated {{ $generated_at ?? '' }}
    </div>

    {{-- Top row: 3 slots with captions --}}
    <table class="pdf-table avoid-break">
        <tr>
            @for ($i = 1; $i <= 3; $i++)
                <td style="width: 33.33%; vertical-align: top; padding-right: {{ $i < 3 ? '10px' : '0' }};">
                    {!! $t2RenderImage($page3[$i], 'page_3_slot_' . $i, ['height' => '150px']) !!}
                    <div class="t2-caption">
                        {!! $t2CaptionHtml($page3[$i], 'Add a description for page_3_slot_' . $i . ' to appear here.') !!}
                    </div>
                </td>
            @endfor
        </tr>
    </table>

    {{-- Middle block: left caption + right image --}}
    <table class="pdf-table" style="margin-top: 10px;">
        <tr>
            <td style="width: 45%; vertical-align: top; padding-right: 10px;">
                <div class="t2-text">
                    {!! $t2CaptionHtml($page3[4], 'Add a description for page_3_slot_4 (this block) to match the template callouts.') !!}
                </div>
            </td>
            <td style="width: 55%; vertical-align: top;">
                {!! $t2RenderImage($page3[4], 'page_3_slot_4', ['height' => '150px']) !!}
            </td>
        </tr>
    </table>

    <div class="t2-rule"></div>

    {{-- Bottom block: left notes + center map/diagram + right image with caption --}}
    <table class="pdf-table avoid-break">
        <tr>
            <td style="width: 33.33%; vertical-align: top; padding-right: 10px;">
                <div class="t2-text">
                    <div class="title">Notes</div>
                    {!! $t2CaptionHtml($page3[5], 'Add a description for page_3_slot_5 (notes/callouts) to match the template.') !!}
                </div>
            </td>
            <td style="width: 33.34%; vertical-align: top; padding-right: 10px;">
                {!! $t2RenderImage($page3[5], 'page_3_slot_5', ['height' => '190px', 'fit' => 'contain']) !!}
            </td>
            <td style="width: 33.33%; vertical-align: top;">
                {!! $t2RenderImage($page3[6], 'page_3_slot_6', ['height' => '190px']) !!}
                <div class="t2-caption">
                    {!! $t2CaptionHtml($page3[6], 'Add a description for page_3_slot_6 to appear here.') !!}
                </div>
            </td>
        </tr>
    </table>

    {{-- Bottom row: 2 slots with captions --}}
    <table class="pdf-table" style="margin-top: 10px;">
        <tr>
            <td style="width: 50%; vertical-align: top; padding-right: 10px;">
                {!! $t2RenderImage($page3[7], 'page_3_slot_7', ['height' => '170px']) !!}
                <div class="t2-caption">
                    {!! $t2CaptionHtml($page3[7], 'Add a description for page_3_slot_7 to appear here.') !!}
                </div>
            </td>
            <td style="width: 50%; vertical-align: top;">
                {!! $t2RenderImage($page3[8], 'page_3_slot_8', ['height' => '170px']) !!}
                <div class="t2-caption">
                    {!! $t2CaptionHtml($page3[8], 'Add a description for page_3_slot_8 to appear here.') !!}
                </div>
            </td>
        </tr>
    </table>
</body>

</html>
