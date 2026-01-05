<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>HB 837 Appendix (T2) - {{ $hb837->property_name }}</title>
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

        .small {
            font-size: 10px;
            color: #6c757d;
        }

        .pdf-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .img-placeholder {
            border: 1px solid #dee2e6;
            padding: 18px;
            text-align: center;
            margin: 0;
            page-break-inside: avoid;
        }

        .img-placeholder .ph-title {
            font-weight: bold;
            margin-bottom: 6px;
        }

        .photo-img {
            display: block;
            width: 100%;
            max-width: 100%;
            border: 1px solid #999;
        }

        .caption {
            margin-top: 6px;
        }

        .rule {
            border-top: 1px solid #444;
            margin: 16px 0 12px 0;
        }

        .text-block {
            font-size: 10px;
            line-height: 1.35;
        }

        .text-block .title {
            font-weight: bold;
            margin-bottom: 6px;
        }

        .avoid-break {
            page-break-inside: avoid;
        }
    </style>
</head>

<body>
    @php
        $filesByPosition = ($hb837->relationLoaded('files') ? $hb837->files : $hb837->files()->get())
            ->whereNotNull('file_position')
            ->keyBy('file_position');

        // T2 uses dedicated Page 3 slots:
        // 1-3 = top row, 4 = mid image, 5 = map/diagram, 6 = right image, 7-8 = bottom row
        $slot = [];
        for ($i = 1; $i <= 8; $i++) {
            $slot[$i] = $filesByPosition->get('page_3_slot_' . $i);
        }

        $fileImgSrc = function ($file) {
            if (!$file || empty($file->file_path)) {
                return null;
            }

            $relative = ltrim((string) $file->file_path, '/');

            $fsPath = storage_path('app/public/' . $relative);
            if (!is_file($fsPath)) {
                $fsPath = public_path('storage/' . $relative);
            }

            if (!is_file($fsPath)) {
                return null;
            }

            // Most reliable approach for DomPDF across environments: embed local images
            // as base64 data URIs.
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
                return null;
            }

            return null;
        };

        $renderImage = function ($file, string $positionLabel, array $style = []) use ($fileImgSrc) {
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

        $captionHtml = function ($file, string $fallback) {
            $text = $file?->description;
            if (!$text) {
                return '<div class="small">' . e($fallback) . '</div>';
            }
            return '<div class="small">' . nl2br(e($text)) . '</div>';
        };
    @endphp

    {{-- Top row: 3 slots with captions --}}
    <table class="pdf-table avoid-break">
        <tr>
            @for ($i = 1; $i <= 3; $i++)
                <td style="width: 33.33%; vertical-align: top; padding-right: {{ $i < 3 ? '10px' : '0' }};">
                    {!! $renderImage($slot[$i], 'page_3_slot_' . $i, ['height' => '150px']) !!}
                    <div class="caption">
                        {!! $captionHtml($slot[$i], 'Add a description for page_3_slot_' . $i . ' to appear here.') !!}
                    </div>
                </td>
            @endfor
        </tr>
    </table>

    {{-- Middle block: left caption + right image --}}
    <table class="pdf-table" style="margin-top: 10px;">
        <tr>
            <td style="width: 45%; vertical-align: top; padding-right: 10px;">
                <div class="text-block">
                    {!! $captionHtml($slot[4], 'Add a description for page_3_slot_4 (this block) to match the template callouts.') !!}
                </div>
            </td>
            <td style="width: 55%; vertical-align: top;">
                {!! $renderImage($slot[4], 'page_3_slot_4', ['height' => '150px']) !!}
            </td>
        </tr>
    </table>

    <div class="rule"></div>

    {{-- Bottom block: left notes + center map/diagram + right image with caption --}}
    <table class="pdf-table avoid-break">
        <tr>
            <td style="width: 33.33%; vertical-align: top; padding-right: 10px;">
                <div class="text-block">
                    <div class="title">Notes</div>
                    {!! $captionHtml($slot[5], 'Add a description for page_3_slot_5 (notes/callouts) to match the template.') !!}
                </div>
            </td>
            <td style="width: 33.34%; vertical-align: top; padding-right: 10px;">
                {!! $renderImage($slot[5], 'page_3_slot_5', ['height' => '190px', 'fit' => 'contain']) !!}
            </td>
            <td style="width: 33.33%; vertical-align: top;">
                {!! $renderImage($slot[6], 'page_3_slot_6', ['height' => '190px']) !!}
                <div class="caption">
                    {!! $captionHtml($slot[6], 'Add a description for page_3_slot_6 to appear here.') !!}
                </div>
            </td>
        </tr>
    </table>

    {{-- Bottom row: 2 slots with captions --}}
    <table class="pdf-table" style="margin-top: 10px;">
        <tr>
            <td style="width: 50%; vertical-align: top; padding-right: 10px;">
                {!! $renderImage($slot[7], 'page_3_slot_7', ['height' => '170px']) !!}
                <div class="caption">
                    {!! $captionHtml($slot[7], 'Add a description for page_3_slot_7 to appear here.') !!}
                </div>
            </td>
            <td style="width: 50%; vertical-align: top;">
                {!! $renderImage($slot[8], 'page_3_slot_8', ['height' => '170px']) !!}
                <div class="caption">
                    {!! $captionHtml($slot[8], 'Add a description for page_3_slot_8 to appear here.') !!}
                </div>
            </td>
        </tr>
    </table>
</body>

</html>
